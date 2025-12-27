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

namespace Phalcon\Api\Tests\Unit\Domain\Application\Auth\Handler;

use PayloadInterop\DomainStatus;
use Phalcon\Api\Domain\Application\Auth\Command\AuthCommandFactory;
use Phalcon\Api\Domain\Application\Auth\Handler\AuthLoginPostHandler;
use Phalcon\Api\Domain\Application\Auth\Handler\AuthRefreshPostHandler;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Mapper\UserMapper;
use Phalcon\Api\Domain\Infrastructure\Encryption\JWTToken;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Api\Tests\Fixtures\Domain\Migrations\UsersMigration;
use Phalcon\Encryption\Security\JWT\Token\Item;
use Phalcon\Encryption\Security\JWT\Token\Token;

final class RefreshPostHandlerTest extends AbstractUnitTestCase
{
    public function testHandlerEmptyToken(): void
    {
        /** @var AuthRefreshPostHandler $handler */
        $handler = $this->container->get(AuthRefreshPostHandler::class);
        /** @var AuthCommandFactory $factory */
        $factory = $this->container->get(AuthCommandFactory::class);

        $command = $factory->refresh([]);
        $payload = $handler->__invoke($command);

        $expected = DomainStatus::UNAUTHORIZED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('errors', $actual);

        $expected = HttpCodesEnum::AppTokenNotPresent->error();
        $actual   = $actual['errors'][0];
        $this->assertSame($expected, $actual);
    }

    public function testHandlerInvalidToken(): void
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
         * Set up mock handlers
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
         * Replace the handler with the mocked one
         */
        $this->container->set(JWTToken::class, $mockJWT);

        /** @var AuthRefreshPostHandler $handler */
        $handler = $this->container->get(AuthRefreshPostHandler::class);
        /** @var AuthCommandFactory $factory */
        $factory = $this->container->get(AuthCommandFactory::class);

        $command = $factory->refresh(['token' => '1234']);
        $payload = $handler->__invoke($command);

        $expected = DomainStatus::UNAUTHORIZED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('errors', $actual);

        $expected = ['Incorrect token data'];
        $actual   = $actual['errors'][0];
        $this->assertSame($expected, $actual);
    }

    public function testHandlerNotRefreshToken(): void
    {
        /**
         * Set up mock handlers
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
         * Replace the handler with the mocked one
         */
        $this->container->set(JWTToken::class, $mockJWT);

        /** @var AuthRefreshPostHandler $handler */
        $handler = $this->container->get(AuthRefreshPostHandler::class);
        /** @var AuthCommandFactory $factory */
        $factory = $this->container->get(AuthCommandFactory::class);

        $command = $factory->refresh(['token' => '1234']);
        $payload = $handler->__invoke($command);

        $expected = DomainStatus::UNAUTHORIZED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('errors', $actual);

        $expected = HttpCodesEnum::AppTokenNotValid->error();
        $actual   = $actual['errors'][0];
        $this->assertSame($expected, $actual);
    }

    public function testHandlerWithCredentials(): void
    {
        /** @var AuthLoginPostHandler $handler */
        $loginHandler = $this->container->get(AuthLoginPostHandler::class);
        /** @var AuthRefreshPostHandler $handler */
        $handler = $this->container->get(AuthRefreshPostHandler::class);
        /** @var AuthCommandFactory $factory */
        $factory = $this->container->get(AuthCommandFactory::class);

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

        $command = $factory->authenticate($payload);
        $payload = $loginHandler->__invoke($command);

        $expected = DomainStatus::SUCCESS;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $result = $payload->getResult();
        $jwt    = $result['data']['jwt'];

        $command = $factory->refresh(['token' => $jwt['refreshToken']]);
        $payload = $handler->__invoke($command);

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

    public function testHandlerWrongUser(): void
    {
        /**
         * Set up mock handlers
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
         * Replace the handler with the mocked one
         */
        $this->container->set(JWTToken::class, $mockJWT);

        /** @var AuthRefreshPostHandler $handler */
        $handler = $this->container->get(AuthRefreshPostHandler::class);
        /** @var AuthCommandFactory $factory */
        $factory = $this->container->get(AuthCommandFactory::class);

        $command = $factory->refresh(['token' => '1234']);
        $payload = $handler->__invoke($command);

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
