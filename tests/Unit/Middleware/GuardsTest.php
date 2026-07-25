<?php

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Api\Tests\Unit\Middleware;

use Phalcon\Api\Domain\Infrastructure\DataSource\User\DTO\User;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Mapper\UserMapper;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Repository\UserRepository;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenManagerInterface;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Api\Domain\Infrastructure\Env\EnvManager;
use Phalcon\Api\Middleware\ValidateTokenClaims;
use Phalcon\Api\Middleware\ValidateTokenPresence;
use Phalcon\Api\Middleware\ValidateTokenRevoked;
use Phalcon\Api\Middleware\ValidateTokenStructure;
use Phalcon\Api\Middleware\ValidateTokenUser;
use Phalcon\Api\Responder\ApiResponder;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Cache\Cache;
use Phalcon\Contracts\ADR\Handler;
use Phalcon\Contracts\Http\AttributeRequest;
use Phalcon\Encryption\Security\JWT\Token\Token;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Support\Registry;
use PHPUnit\Framework\Attributes\BackupGlobals;

use function json_decode;

/**
 * The mechanism these tests pin: a guard that rejects returns a response
 * and never calls $next. That replaces the old halt(), which emitted the
 * response itself and told the application to stop.
 *
 * BackupGlobals because the guards read the Authorization header out of
 * $_SERVER, and a token set by one test would otherwise satisfy the next.
 */
#[BackupGlobals(true)]
final class GuardsTest extends AbstractUnitTestCase
{
    public function testClaimsGuardRejectsWhenValidationReturnsErrors(): void
    {
        $errors = [['Validation: incorrect Id']];

        $tokenManager = $this->createMock(TokenManagerInterface::class);
        $tokenManager->method('validate')->willReturn($errors);

        $registry = new Registry();
        $registry->set('token', $this->mockWithoutConstructor(Token::class));
        $registry->set('user', $this->domainUser());

        $response = new Response();
        $called   = false;

        $guard = new ValidateTokenClaims(
            $this->env(),
            new ApiResponder(),
            $response,
            $tokenManager,
            $registry
        );

        $guard->__invoke($this->request(), $this->spy($called));

        $body = json_decode($response->getContent(), true);

        $this->assertFalse($called);
        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame([$errors], $body['errors']);
    }

    public function testPresenceGuardPassesThroughWhenATokenIsPresent(): void
    {
        $_SERVER['HTTP_ACCEPT']        = 'application/json';
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer abc.def.ghi';

        $called = false;

        $guard = new ValidateTokenPresence(
            $this->env(),
            new ApiResponder(),
            new Response()
        );

        $guard->__invoke($this->request(), $this->spy($called));

        $this->assertTrue($called, 'an accepting guard must call $next');
    }

    public function testPresenceGuardRejectsWithoutCallingNext(): void
    {
        $response = new Response();
        $called   = false;

        $guard = new ValidateTokenPresence(
            $this->env(),
            new ApiResponder(),
            $response
        );

        $guard->__invoke($this->request(), $this->spy($called));

        $body = json_decode($response->getContent(), true);

        $this->assertFalse($called, 'a rejecting guard must not call $next');
        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame([HttpCodesEnum::AppTokenNotPresent->error()], $body['errors']);
    }

    public function testRevokedGuardRejectsWhenTheTokenIsNotInTheCache(): void
    {
        $cache = $this->createMock(Cache::class);
        $cache->method('has')->willReturn(false);

        $registry = new Registry();
        $registry->set('user', $this->domainUser());

        $response = new Response();
        $called   = false;

        $guard = new ValidateTokenRevoked(
            $this->env(),
            new ApiResponder(),
            $response,
            $cache,
            $registry
        );

        $guard->__invoke($this->request(), $this->spy($called));

        $body = json_decode($response->getContent(), true);

        $this->assertFalse($called);
        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame([HttpCodesEnum::AppTokenNotValid->error()], $body['errors']);
    }

    public function testStructureGuardRejectsAnUnparseableToken(): void
    {
        $tokenManager = $this->createMock(TokenManagerInterface::class);
        $tokenManager->method('getObject')->willReturn(null);
        $tokenManager->method('getErrorMessage')->willReturn('bad token');

        $response = new Response();
        $called   = false;

        $guard = new ValidateTokenStructure(
            $this->env(),
            new ApiResponder(),
            $response,
            $tokenManager,
            new Registry()
        );

        $guard->__invoke($this->request(), $this->spy($called));

        $body = json_decode($response->getContent(), true);

        $this->assertFalse($called);
        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame([['bad token']], $body['errors']);
    }

    public function testUserGuardRejectsWhenTheTokenResolvesToNobody(): void
    {
        $tokenManager = $this->createMock(TokenManagerInterface::class);
        $tokenManager->method('getUser')->willReturn(null);

        $registry = new Registry();
        $registry->set('token', $this->mockWithoutConstructor(Token::class));

        $response = new Response();
        $called   = false;

        $guard = new ValidateTokenUser(
            $this->env(),
            new ApiResponder(),
            $response,
            $tokenManager,
            $this->container->get(UserRepository::class),
            $registry
        );

        $guard->__invoke($this->request(), $this->spy($called));

        $body = json_decode($response->getContent(), true);

        $this->assertFalse($called);
        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame([HttpCodesEnum::AppTokenInvalidUser->error()], $body['errors']);
    }

    /**
     * A real DTO rather than a mock: the cache key reads typed properties,
     * which on a constructor-less mock are uninitialized.
     */
    private function domainUser(): User
    {
        /** @var UserMapper $mapper */
        $mapper = $this->container->get(UserMapper::class);

        $data           = $this->getNewUserData();
        $data['usr_id'] = 1;

        return $mapper->domain($data);
    }

    private function env(): EnvManager
    {
        $_SERVER['HTTP_ACCEPT'] = 'application/json';

        /** @var EnvManager $env */
        $env = $this->container->get(EnvManager::class);

        return $env;
    }

    private function request(): AttributeRequest
    {
        return new Request();
    }

    private function spy(bool &$called): Handler
    {
        return new class ($called) implements Handler {
            public function __construct(private bool &$called)
            {
            }

            public function __invoke(AttributeRequest $request): Response
            {
                $this->called = true;

                return new Response();
            }
        };
    }
}
