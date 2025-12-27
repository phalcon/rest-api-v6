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

use Faker\Factory;
use PayloadInterop\DomainStatus;
use Phalcon\Api\Domain\Application\Auth\Command\AuthCommandFactory;
use Phalcon\Api\Domain\Application\Auth\Handler\AuthLoginPostHandler;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Api\Tests\Fixtures\Domain\Migrations\UsersMigration;

final class LoginPostHandlerTest extends AbstractUnitTestCase
{
    public function testHandlerEmptyCredentials(): void
    {
        /** @var AuthLoginPostHandler $handler */
        $handler = $this->container->get(AuthLoginPostHandler::class);
        /** @var AuthCommandFactory $factory */
        $factory = $this->container->get(AuthCommandFactory::class);

        $command = $factory->authenticate([]);
        $payload = $handler->__invoke($command);

        $expected = DomainStatus::UNAUTHORIZED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('errors', $actual);

        $expected = HttpCodesEnum::AppIncorrectCredentials->error();
        $actual   = $actual['errors'][0];
        $this->assertSame($expected, $actual);
    }

    public function testHandlerWithCredentials(): void
    {
        /** @var AuthLoginPostHandler $handler */
        $handler = $this->container->get(AuthLoginPostHandler::class);
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
        $payload = $handler->__invoke($command);

        $expected = DomainStatus::SUCCESS;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('data', $actual);

        $data = $actual['data'];

        $this->assertArrayHasKey('authenticated', $data);
        $this->assertArrayHasKey('user', $data);
        $this->assertArrayHasKey('jwt', $data);

        $authenticated = $data['authenticated'];
        $user          = $data['user'];
        $jwt           = $data['jwt'];

        $actual = $authenticated;
        $this->assertTrue($actual);

        $expected = $dbUser['usr_id'];
        $actual   = $user['id'];
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_email'];
        $actual   = $user['email'];
        $this->assertSame($expected, $actual);

        $expected = trim(
            $dbUser['usr_name_last']
            . ', '
            . $dbUser['usr_name_first']
            . ' '
            . $dbUser['usr_name_middle']
        );

        $actual = $user['name'];
        $this->assertSame($expected, $actual);

        $this->assertArrayHasKey('token', $jwt);
        $this->assertArrayHasKey('refreshToken', $jwt);

        $this->assertNotEmpty($jwt['token']);
        $this->assertNotEmpty($jwt['refreshToken']);
    }

    public function testHandlerWrongCredentials(): void
    {
        /** @var AuthLoginPostHandler $handler */
        $handler = $this->container->get(AuthLoginPostHandler::class);
        /** @var AuthCommandFactory $factory */
        $factory = $this->container->get(AuthCommandFactory::class);
        $faker   = Factory::create();

        /**
         * Issue a wrong password
         */
        $payload = [
            'email'    => $faker->email(),
            'password' => $faker->password(),
        ];
        $command = $factory->authenticate($payload);
        $payload = $handler->__invoke($command);

        $expected = DomainStatus::UNAUTHORIZED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('errors', $actual);

        $expected = HttpCodesEnum::AppIncorrectCredentials->error();
        $actual   = $actual['errors'][0];
        $this->assertSame($expected, $actual);
    }

    public function testHandlerWrongCredentialsForUser(): void
    {
        /** @var AuthLoginPostHandler $handler */
        $handler = $this->container->get(AuthLoginPostHandler::class);
        /** @var AuthCommandFactory $factory */
        $factory   = $this->container->get(AuthCommandFactory::class);
        $migration = new UsersMigration($this->getConnection());

        $password = 'password';

        /**
         * Issue a wrong password
         */
        $dbUser  = $this->getNewUser($migration, ['usr_password' => $password]);
        $email   = $dbUser['usr_email'];
        $payload = [
            'email'    => $email,
            'password' => $password . '2',
        ];

        $command = $factory->authenticate($payload);
        $payload = $handler->__invoke($command);

        $expected = DomainStatus::UNAUTHORIZED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('errors', $actual);

        $expected = HttpCodesEnum::AppIncorrectCredentials->error();
        $actual   = $actual['errors'][0];
        $this->assertSame($expected, $actual);
    }
}
