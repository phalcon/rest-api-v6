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

use Faker\Factory;
use PayloadInterop\DomainStatus;
use Phalcon\Api\Domain\Application\Auth\Service\AuthLoginPostService;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Api\Tests\Fixtures\Domain\Migrations\UsersMigration;
use PHPUnit\Framework\Attributes\BackupGlobals;

#[BackupGlobals(true)]
final class LoginPostServiceTest extends AbstractUnitTestCase
{
    public function testServiceEmptyCredentials(): void
    {
        /** @var AuthLoginPostService $service */
        $service = $this->container->get(AuthLoginPostService::class);

        $payload = $service->__invoke([]);

        $expected = DomainStatus::UNAUTHORIZED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('errors', $actual);

        $expected = HttpCodesEnum::AppIncorrectCredentials->error();
        $actual   = $actual['errors'][0];
        $this->assertSame($expected, $actual);
    }

    public function testServiceWithCredentials(): void
    {
        /** @var AuthLoginPostService $service */
        $service   = $this->container->get(AuthLoginPostService::class);
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

        $payload = $service->__invoke($payload);

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

    public function testServiceWrongCredentials(): void
    {
        $faker = Factory::create();
        /** @var AuthLoginPostService $service */
        $service = $this->container->get(AuthLoginPostService::class);

        /**
         * Issue a wrong password
         */
        $payload = [
            'email'    => $faker->email(),
            'password' => $faker->password(),
        ];

        $payload = $service->__invoke($payload);

        $expected = DomainStatus::UNAUTHORIZED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('errors', $actual);

        $expected = HttpCodesEnum::AppIncorrectCredentials->error();
        $actual   = $actual['errors'][0];
        $this->assertSame($expected, $actual);
    }

    public function testServiceWrongCredentialsForUser(): void
    {
        /** @var AuthLoginPostService $service */
        $service   = $this->container->get(AuthLoginPostService::class);
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

        $payload = $service->__invoke($payload);

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
