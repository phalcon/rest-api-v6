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

use Phalcon\ADR\Payload\Status;
use Phalcon\Api\Domain\Application\User\Command\UserCommandFactory;
use Phalcon\Api\Domain\Application\User\Handler\UserDeleteHandler;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Api\Tests\Fixtures\Domain\Migrations\UsersMigration;

final class UserHandlerDeleteTest extends AbstractUnitTestCase
{
    public function testHandlerWithUserId(): void
    {
        /** @var UserDeleteHandler $handler */
        $handler = $this->container->get(UserDeleteHandler::class);
        /** @var UserCommandFactory $factory */
        $factory = $this->container->get(UserCommandFactory::class);
        /**
         * We need to ask for a user to be deleted with an ID that does not
         * exist in the database. To ensure that, we will create a user,
         * delete it and then try to delete the same user with that ID
         */
        $migration = new UsersMigration($this->getConnection());
        $dbUser    = $this->getNewUser($migration);
        $userId    = $dbUser['usr_id'];

        $command = $factory->delete(['id' => $userId]);
        $payload = $handler->__invoke($command);

        $expected = Status::DELETED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $data = $payload->getResult();

        $expected = [
            'Record deleted successfully [#' . $userId . '].',
        ];
        $actual   = $data;
        $this->assertSame($expected, $actual);

        /**
         * Now deleting it again, will result in a 404
         */
        $payload = $handler->__invoke($command);

        $expected = Status::NOT_FOUND;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $errors = $payload->getMessages();

        $expected = [['Record(s) not found']];
        $actual   = $errors;
        $this->assertSame($expected, $actual);
    }

    public function testHandlerZeroUserId(): void
    {
        /** @var UserDeleteHandler $handler */
        $handler = $this->container->get(UserDeleteHandler::class);
        /** @var UserCommandFactory $factory */
        $factory = $this->container->get(UserCommandFactory::class);

        $command = $factory->delete(['id' => 0]);
        $payload = $handler->__invoke($command);

        $expected = Status::NOT_FOUND;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $errors = $payload->getMessages();

        $expected = [['Record(s) not found']];
        $actual   = $errors;
        $this->assertSame($expected, $actual);
    }
}
