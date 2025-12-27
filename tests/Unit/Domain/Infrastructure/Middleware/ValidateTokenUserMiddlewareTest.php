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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\Middleware;

use Phalcon\Api\Domain\Infrastructure\DataSource\User\Mapper\UserMapper;
use Phalcon\Api\Domain\Infrastructure\Encryption\JWTToken;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Api\Domain\Infrastructure\Middleware\ValidateTokenUserMiddleware;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Api\Tests\Fixtures\Domain\Migrations\UsersMigration;
use Phalcon\Mvc\Micro;
use Phalcon\Support\Registry;
use PHPUnit\Framework\Attributes\BackupGlobals;

#[BackupGlobals(true)]
final class ValidateTokenUserMiddlewareTest extends AbstractUnitTestCase
{
    public function testValidateTokenUserFailureRecordNotFound(): void
    {
        [$micro, $middleware, $jwtToken] = $this->setupTest();
        $userData           = $this->getNewUserData();
        $userData['usr_id'] = 1;
        $token              = $this->getUserToken($userData);
        $tokenObject        = $jwtToken->getObject($token);
        /** @var Registry $registry */
        $registry = $this->container->get(Registry::class);
        $registry->set('token', $tokenObject);

        $time    = $_SERVER['REQUEST_TIME_FLOAT'] ?? time();
        $_SERVER = [
            'REQUEST_METHOD'     => 'GET',
            'REQUEST_TIME_FLOAT' => $time,
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'REQUEST_URI'        => '/user?id=1234',
        ];

        ob_start();
        $middleware->call($micro);
        $contents = ob_get_clean();

        $contents = json_decode($contents, true);
        $data     = $contents['data'];
        $errors   = $contents['errors'];

        $this->assertSame([], $data);

        $expected = [HttpCodesEnum::AppTokenInvalidUser->error()];
        $actual   = $errors;
        $this->assertSame($expected, $actual);
    }

    public function testValidateTokenUserSuccess(): void
    {
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        $migration  = new UsersMigration($this->getConnection());
        $user       = $this->getNewUser($migration);
        $domainUser = $userMapper->domain($user);

        [$micro, $middleware, $jwtToken] = $this->setupTest();

        $token       = $jwtToken->getForUser($domainUser);
        $tokenObject = $jwtToken->getObject($token);
        /** @var Registry $registry */
        $registry = $this->container->get(Registry::class);
        $registry->set('token', $tokenObject);

        $time    = $_SERVER['REQUEST_TIME_FLOAT'] ?? time();
        $_SERVER = [
            'REQUEST_METHOD'     => 'GET',
            'REQUEST_TIME_FLOAT' => $time,
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'REQUEST_URI'        => '/user?id=1234',
        ];

        ob_start();
        $actual = $middleware->call($micro);
        ob_end_flush();

        $this->assertTrue($actual);
    }

    /**
     * @return array
     */
    private function setupTest(): array
    {
        $micro      = new Micro($this->container);
        $middleware = $this->container->get(ValidateTokenUserMiddleware::class);
        $jwtToken   = $this->container->get(JWTToken::class);

        return [$micro, $middleware, $jwtToken];
    }
}
