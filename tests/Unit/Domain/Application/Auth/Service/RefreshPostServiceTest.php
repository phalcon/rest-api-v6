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
use Phalcon\Api\Domain\Application\Auth\Service\AuthRefreshPostService;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Mapper\UserMapper;
use Phalcon\Api\Domain\Infrastructure\Encryption\JWTToken;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Api\Tests\Fixtures\Domain\Migrations\UsersMigration;
use Phalcon\Encryption\Security\JWT\Token\Item;
use Phalcon\Encryption\Security\JWT\Token\Token;
use PHPUnit\Framework\Attributes\BackupGlobals;

#[BackupGlobals(true)]
final class RefreshPostServiceTest extends AbstractUnitTestCase
{
    public function testServiceEmptyToken(): void
    {
        /** @var AuthRefreshPostService $service */
        $service = $this->container->get(AuthRefreshPostService::class);

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

        /** @var AuthRefreshPostService $service */
        $service = $this->container->get(AuthRefreshPostService::class);

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

        /** @var AuthRefreshPostService $service */
        $service = $this->container->get(AuthRefreshPostService::class);

        $payload = $service->__invoke(['token' => '1234']);

        $expected = DomainStatus::UNAUTHORIZED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('errors', $actual);

        $expected = HttpCodesEnum::AppTokenNotValid->error();
        $actual   = $actual['errors'][0];
        $this->assertSame($expected, $actual);
    }

    public function testServiceWithCredentials(): void
    {
        /** @var AuthLoginPostService $service */
        $loginService = $this->container->get(AuthLoginPostService::class);
        /** @var AuthRefreshPostService $service */
        $service   = $this->container->get(AuthRefreshPostService::class);
        $migration = new UsersMigration($this->getConnection());

        /**
         * Setting the password to something we know
         */
        $password = 'password';

        $dbUser  = $this->getNewUser($migration, ['usr_password' => $password]);
        $email   = $dbUser['usr_email'];
        $payload = [
            'email'    => $email,
            'password' => $password,
        ];

        $payload = $loginService->__invoke($payload);

        $expected = DomainStatus::SUCCESS;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $result = $payload->getResult();
        $jwt    = $result['data']['jwt'];

        $payload = $service->__invoke(['token' => $jwt['refreshToken']]);

        $expected = DomainStatus::SUCCESS;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('data', $actual);

        $data = $actual['data'];

        $this->assertArrayHasKey('token', $data);
        $this->assertArrayHasKey('refreshToken', $data);

        $this->assertIsString($data['token']);
        $this->assertIsString($data['refreshToken']);
        $this->assertNotEmpty($data['token']);
        $this->assertNotEmpty($data['refreshToken']);
    }

    public function testServiceWrongUser(): void
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

        /** @var AuthRefreshPostService $service */
        $service = $this->container->get(AuthRefreshPostService::class);

        $payload = $service->__invoke(['token' => '1234']);

        $expected = DomainStatus::UNAUTHORIZED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('errors', $actual);

        $expected = HttpCodesEnum::AppTokenInvalidUser->error();
        $actual   = $actual['errors'][0];
        $this->assertSame($expected, $actual);
    }
}
