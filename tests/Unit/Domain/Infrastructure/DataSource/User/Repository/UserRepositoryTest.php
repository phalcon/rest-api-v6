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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\DataSource\User\Repository;

use Phalcon\Api\Domain\Infrastructure\DataSource\User\DTO\User;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Repository\UserRepository;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Api\Tests\Fixtures\Domain\Migrations\UsersMigration;

final class UserRepositoryTest extends AbstractUnitTestCase
{
    public function testFindByEmail(): void
    {
        /** @var UserRepository $repository */
        $repository = $this->container->get(UserRepository::class);

        $migration = new UsersMigration($this->getConnection());

        $repositoryUser = $repository->findByEmail('');
        $this->assertEmpty($repositoryUser);

        $migrationUser = $this->getNewUser($migration);
        $email         = $migrationUser['usr_email'];

        $repositoryUser = $repository->findByEmail($email);

        $this->runAssertions($migrationUser, $repositoryUser);
    }

    public function testFindById(): void
    {
        /** @var UserRepository $repository */
        $repository = $this->container->get(UserRepository::class);

        $migration = new UsersMigration($this->getConnection());

        $repositoryUser = $repository->findById(0);
        $this->assertEmpty($repositoryUser);

        $migrationUser = $this->getNewUser($migration);
        $userId        = $migrationUser['usr_id'];

        $repositoryUser = $repository->findById($userId);

        $this->runAssertions($migrationUser, $repositoryUser);
    }

    private function runAssertions(array $dbUser, User $user): void
    {
        $expected = $dbUser['usr_id'];
        $actual   = $user->id;
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_status_flag'];
        $actual   = $user->status;
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_email'];
        $actual   = $user->email;
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_password'];
        $actual   = $user->password;
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_name_prefix'];
        $actual   = $user->namePrefix;
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_name_first'];
        $actual   = $user->nameFirst;
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_name_middle'];
        $actual   = $user->nameMiddle;
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_name_last'];
        $actual   = $user->nameLast;
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_name_suffix'];
        $actual   = $user->nameSuffix;
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_issuer'];
        $actual   = $user->issuer;
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_token_password'];
        $actual   = $user->tokenPassword;
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_token_id'];
        $actual   = $user->tokenId;
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_preferences'];
        $actual   = $user->preferences;
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_created_date'];
        $actual   = $user->createdDate;
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_created_usr_id'];
        $actual   = $user->createdUserId;
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_updated_date'];
        $actual   = $user->updatedDate;
        $this->assertSame($expected, $actual);

        $expected = $dbUser['usr_updated_usr_id'];
        $actual   = $user->updatedUserId;
        $this->assertSame($expected, $actual);
    }
}
