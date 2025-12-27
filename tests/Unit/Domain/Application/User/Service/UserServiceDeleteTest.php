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

namespace Phalcon\Api\Tests\Unit\Domain\Application\User\Service;

use PayloadInterop\DomainStatus;
use Phalcon\Api\Domain\Application\User\Service\UserDeleteService;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Api\Tests\Fixtures\Domain\Migrations\UsersMigration;

final class UserServiceDeleteTest extends AbstractUnitTestCase
{
    public function testServiceWithUserId(): void
    {
        /** @var UserDeleteService $service */
        $service = $this->container->get(UserDeleteService::class);

        /**
         * We need to ask for a user to be deleted with an ID that does not
         * exist in the database. To ensure that, we will create a user,
         * delete it and then try to delete the same user with that ID
         */
        $migration = new UsersMigration($this->getConnection());
        $dbUser    = $this->getNewUser($migration);
        $userId    = $dbUser['usr_id'];

        $payload = $service->__invoke(
            [
                'id' => $userId,
            ]
        );

        $expected = DomainStatus::DELETED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('data', $actual);

        $data = $actual['data'];

        $expected = [
            'Record deleted successfully [#' . $userId . '].',
        ];
        $actual   = $data;
        $this->assertSame($expected, $actual);

        /**
         * Now deleting it again, will result in a 404
         */
        $payload = $service->__invoke(
            [
                'id' => $userId,
            ]
        );

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

    public function testServiceZeroUserId(): void
    {
        /** @var UserDeleteService $service */
        $service = $this->container->get(UserDeleteService::class);

        $payload = $service->__invoke(
            [
                'id' => 0,
            ]
        );

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
