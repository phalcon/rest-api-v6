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

namespace Phalcon\Api\Tests\Unit\Domain\Application\Auth\Service;

use PayloadInterop\DomainStatus;
use Phalcon\Api\Domain\Application\Auth\Service\AuthLoginPostService;
use Phalcon\Api\Domain\Application\Auth\Service\AuthLogoutPostService;
use Phalcon\Api\Domain\Infrastructure\Constants\Cache as CacheConstants;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Mapper\UserMapper;
use Phalcon\Api\Domain\Infrastructure\Encryption\JWTToken;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Api\Tests\Fixtures\Domain\Migrations\UsersMigration;
use Phalcon\Cache\Cache;
use Phalcon\Encryption\Security\JWT\Token\Item;
use Phalcon\Encryption\Security\JWT\Token\Token;
use PHPUnit\Framework\Attributes\BackupGlobals;

#[BackupGlobals(true)]
final class LogoutPostServiceTest extends AbstractUnitTestCase
{
    public function testServiceEmptyToken(): void
    {
        /** @var AuthLogoutPostService $service */
        $service = $this->container->get(AuthLogoutPostService::class);

        $payload = $service->__invoke([]);

        $expected = DomainStatus::UNAUTHORIZED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('errors', $actual);

        $expected = HttpCodesEnum::AppTokenNotPresent->error();
        $actual   = $actual['errors'][0];
        $this->assertSame($expected, $actual);
    }

    public function testServiceInvalidToken(): void
    {
        /** @var UserMapper $userMapper */
        $userMapper     = $this->container->get(UserMapper::class);
        $user           = $this->getNewUserData();
        $user['usr_id'] = 1;
        $domainUser     = $userMapper->domain($user);
        $errors         = [
            ['Incorrect token data'],
        ];

        /**
         * Set up mock services
         */
        $mockItem = $this
            ->getMockBuilder(Item::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'get',
                ]
            )
            ->getMock()
        ;
        $mockItem->method('get')->willReturn(true);

        $mockToken = $this
            ->getMockBuilder(Token::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'getClaims',
                ]
            )
            ->getMock()
        ;
        $mockToken->method('getClaims')->willReturn($mockItem);

        $mockJWT = $this
            ->getMockBuilder(JWTToken::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'getObject',
                    'getUser',
                    'validate',
                ]
            )
            ->getMock()
        ;
        $mockJWT->method('getObject')->willReturn($mockToken);
        $mockJWT->method('getUser')->willReturn($domainUser);
        $mockJWT->method('validate')->willReturn($errors);


        /**
         * Replace the service with the mocked one
         */
        $this->container->set(JWTToken::class, $mockJWT);

        /** @var AuthLogoutPostService $service */
        $service = $this->container->get(AuthLogoutPostService::class);

        $payload = $service->__invoke(['token' => '1234']);

        $expected = DomainStatus::UNAUTHORIZED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('errors', $actual);

        $expected = ['Incorrect token data'];
        $actual   = $actual['errors'][0];
        $this->assertSame($expected, $actual);
    }

    public function testServiceNotRefreshToken(): void
    {
        /**
         * Set up mock services
         */
        $mockItem = $this
            ->getMockBuilder(Item::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'get',
                ]
            )
            ->getMock()
        ;
        $mockItem->method('get')->willReturn(false);

        $mockToken = $this
            ->getMockBuilder(Token::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'getClaims',
                ]
            )
            ->getMock()
        ;
        $mockToken->method('getClaims')->willReturn($mockItem);

        $mockJWT = $this
            ->getMockBuilder(JWTToken::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'getObject',
                ]
            )
            ->getMock()
        ;
        $mockJWT->method('getObject')->willReturn($mockToken);

        /**
         * Replace the service with the mocked one
         */
        $this->container->set(JWTToken::class, $mockJWT);
        /** @var AuthLogoutPostService $service */
        $logoutService = $this->container->get(AuthLogoutPostService::class);

        /**
         * Logout now
         */
        $payload = $logoutService->__invoke(['token' => '1234']);

        $expected = DomainStatus::UNAUTHORIZED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('errors', $actual);

        $errors = $actual['errors'];

        $expected = [HttpCodesEnum::AppTokenNotValid->error()];
        $actual   = $errors;
        $this->assertSame($expected, $actual);
    }

    public function testServiceSuccess(): void
    {
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        /** @var AuthLogoutPostService $service */
        $logoutService = $this->container->get(AuthLogoutPostService::class);
        /** @var Cache $cache */
        $cache = $this->container->getShared(Cache::class);
        /** @var AuthLoginPostService $service */
        $service   = $this->container->get(AuthLoginPostService::class);
        $migration = new UsersMigration($this->getConnection());

        /**
         * Setting the password to something we know
         */
        $password = 'password';

        $dbUser = $this->getNewUser($migration, ['usr_password' => $password]);
        $email  = $dbUser['usr_email'];
        $input  = [
            'email'    => $email,
            'password' => $password,
        ];

        $payload = $service->__invoke($input);

        $expected = DomainStatus::SUCCESS;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('data', $actual);

        $data = $actual['data'];

        $this->assertArrayHasKey('authenticated', $data);

        $authenticated = $data['authenticated'];

        $actual = $authenticated;
        $this->assertTrue($actual);

        $token      = $data['jwt']['token'];
        $domainUser = $userMapper->domain($dbUser);
        $tokenKey   = CacheConstants::getCacheTokenKey($domainUser, $token);

        $actual = $cache->has($tokenKey);
        $this->assertTrue($actual);

        $refreshToken = $data['jwt']['refreshToken'];
        $tokenKey     = CacheConstants::getCacheTokenKey($domainUser, $refreshToken);

        $actual = $cache->has($tokenKey);
        $this->assertTrue($actual);

        $refreshToken = $data['jwt']['refreshToken'];

        /**
         * Logout now
         */
        $input   = [
            'token' => $refreshToken,
        ];
        $payload = $logoutService->__invoke($input);

        $expected = DomainStatus::SUCCESS;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();

        $this->assertArrayHasKey('data', $actual);

        $data = $actual['data'];

        $this->assertArrayHasKey('authenticated', $data);

        $authenticated = $data['authenticated'];

        $actual = $authenticated;
        $this->assertFalse($actual);

        $actual = $cache->has($tokenKey);
        $this->assertFalse($actual);
    }

    public function testServiceWrongUser(): void
    {
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        $domainUser = $userMapper->domain([]);

        /**
         * Set up mock services
         */
        $mockItem = $this
            ->getMockBuilder(Item::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'get',
                ]
            )
            ->getMock()
        ;
        $mockItem->method('get')->willReturn(true);

        $mockToken = $this
            ->getMockBuilder(Token::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'getClaims',
                ]
            )
            ->getMock()
        ;
        $mockToken->method('getClaims')->willReturn($mockItem);

        $mockJWT = $this
            ->getMockBuilder(JWTToken::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'getObject',
                    'getUser',
                ]
            )
            ->getMock()
        ;
        $mockJWT->method('getObject')->willReturn($mockToken);
        $mockJWT->method('getUser')->willReturn(null);


        /**
         * Replace the service with the mocked one
         */
        $this->container->set(JWTToken::class, $mockJWT);

        /** @var AuthLogoutPostService $service */
        $logoutService = $this->container->get(AuthLogoutPostService::class);

        /**
         * Logout now
         */
        $payload = $logoutService->__invoke(['token' => '1234']);

        $expected = DomainStatus::UNAUTHORIZED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('errors', $actual);

        $errors = $actual['errors'];

        $expected = [HttpCodesEnum::AppTokenInvalidUser->error()];
        $actual   = $errors;
        $this->assertSame($expected, $actual);
    }
}
