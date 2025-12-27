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
use Phalcon\Api\Domain\Application\Auth\Handler\AuthLogoutPostHandler;
use Phalcon\Api\Domain\Infrastructure\Constants\Cache as CacheConstants;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Mapper\UserMapper;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Api\Tests\Fixtures\Domain\Migrations\UsersMigration;
use Phalcon\Cache\Cache;

final class LogoutPostHandlerTest extends AbstractUnitTestCase
{
    public function testHandlerEmptyToken(): void
    {
        /** @var AuthLogoutPostHandler $handler */
        $handler = $this->container->get(AuthLogoutPostHandler::class);
        /** @var AuthCommandFactory $factory */
        $factory = $this->container->get(AuthCommandFactory::class);

        $command = $factory->authenticate([]);
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

    public function testHandlerSuccess(): void
    {
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        /** @var AuthLoginPostHandler $handler */
        $loginHandler = $this->container->get(AuthLoginPostHandler::class);
        /** @var Cache $cache */
        $cache = $this->container->getShared(Cache::class);
        /** @var AuthLogoutPostHandler $handler */
        $handler = $this->container->get(AuthLogoutPostHandler::class);
        /** @var AuthCommandFactory $factory */
        $factory   = $this->container->get(AuthCommandFactory::class);
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

        $command = $factory->authenticate($input);
        $payload = $loginHandler->__invoke($command);

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
        $command = $factory->logout($input);
        $payload = $handler->__invoke($command);

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
}
