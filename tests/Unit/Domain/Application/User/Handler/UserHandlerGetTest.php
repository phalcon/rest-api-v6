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

namespace Phalcon\Api\Tests\Unit\Domain\Application\User\Handler;

use PayloadInterop\DomainStatus;
use Phalcon\Api\Domain\Application\User\Command\UserCommandFactory;
use Phalcon\Api\Domain\Application\User\Handler\UserGetHandler;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Api\Tests\Fixtures\Domain\Migrations\UsersMigration;

final class UserHandlerGetTest extends AbstractUnitTestCase
{
    public function testHandlerEmptyUserId(): void
    {
        /** @var UserGetHandler $handler */
        $handler = $this->container->get(UserGetHandler::class);
        /** @var UserCommandFactory $factory */
        $factory = $this->container->get(UserCommandFactory::class);

        $command = $factory->get([]);
        $payload = $handler->__invoke($command);

        $expected = DomainStatus::NOT_FOUND;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('errors', $actual);

        $errors = $actual['errors'];

        $expected = [['Record(s) not found']];
        $actual   = $errors;
        $this->assertSame($expected, $actual);
    }

    public function testHandlerWithUserId(): void
    {
        /** @var UserGetHandler $handler */
        $handler = $this->container->get(UserGetHandler::class);
        /** @var UserCommandFactory $factory */
        $factory = $this->container->get(UserCommandFactory::class);

        $migration = new UsersMigration($this->getConnection());
        $dbUser    = $this->getNewUser($migration);
        $userId    = $dbUser['usr_id'];

        $command = $factory->get(['id' => $userId]);
        $payload = $handler->__invoke($command);

        $expected = DomainStatus::SUCCESS;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('data', $actual);

        $user = $actual['data'];
        $key  = array_key_first($user);
        $user = $user[$key];

        $expected = $dbUser['usr_id'];
        $actual   = $user['id'];
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_status_flag'];
        $actual   = $user['status'];
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_email'];
        $actual   = $user['email'];
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_password'];
        $actual   = $user['password'];
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_name_prefix'];
        $actual   = $user['namePrefix'];
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_name_first'];
        $actual   = $user['nameFirst'];
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_name_middle'];
        $actual   = $user['nameMiddle'];
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_name_last'];
        $actual   = $user['nameLast'];
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_name_suffix'];
        $actual   = $user['nameSuffix'];
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_issuer'];
        $actual   = $user['issuer'];
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_token_password'];
        $actual   = $user['tokenPassword'];
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_token_id'];
        $actual   = $user['tokenId'];
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_preferences'];
        $actual   = $user['preferences'];
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_created_date'];
        $actual   = $user['createdDate'];
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_created_usr_id'];
        $actual   = $user['createdUserId'];
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_updated_date'];
        $actual   = $user['updatedDate'];
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_updated_usr_id'];
        $actual   = $user['updatedUserId'];
        $this->assertSame($expected, $actual);
    }

    public function testHandlerWrongUserId(): void
    {
        /** @var UserGetHandler $handler */
        $handler = $this->container->get(UserGetHandler::class);
        /** @var UserCommandFactory $factory */
        $factory = $this->container->get(UserCommandFactory::class);

        $command = $factory->get(['id' => 999999]);
        $payload = $handler->__invoke($command);

        $expected = DomainStatus::NOT_FOUND;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('errors', $actual);

        $errors = $actual['errors'];

        $expected = [['Record(s) not found']];
        $actual   = $errors;
        $this->assertSame($expected, $actual);
    }
}
