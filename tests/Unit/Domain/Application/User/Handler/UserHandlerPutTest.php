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

use DateTimeImmutable;
use PayloadInterop\DomainStatus;
use PDOException;
use Phalcon\Api\Domain\Application\User\Command\UserCommandFactory;
use Phalcon\Api\Domain\Application\User\Handler\UserPutHandler;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Mapper\UserMapper;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Repository\UserRepository;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Api\Tests\Fixtures\Domain\Migrations\UsersMigration;
use Phalcon\Support\Registry;

use function uniqid;

final class UserHandlerPutTest extends AbstractUnitTestCase
{
    public function testHandlerFailureNoIdReturned(): void
    {
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        /** @var Registry $registry */
        $registry = $this->container->get(Registry::class);
        /** @var UserCommandFactory $factory */
        $factory  = $this->container->get(UserCommandFactory::class);
        $userData = $this->getNewUserData();

        $userData['usr_id'] = 1;

        $findByUser = $userMapper->domain($userData);

        $userRepository = $this
            ->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'update',
                    'findById',
                ]
            )
            ->getMock()
        ;
        $userRepository->method('update')->willReturn(0);
        $userRepository->method('findById')->willReturn($findByUser);

        $this->container->setShared(UserRepository::class, $userRepository);

        /** @var UserPutHandler $handler */
        $handler = $this->container->get(UserPutHandler::class);

        /**
         * Update user
         */
        $userData           = $this->getNewUserData();
        $userData['usr_id'] = 1;

        $updateUser = $userMapper->domain($userData);
        /**
         * Store the domain user in the registry - to be used for *_usr_id
         * field updates
         */
        $registry->set('user', $updateUser);

        $updateUser = $updateUser->toArray();
        $command    = $factory->update($updateUser);
        $payload    = $handler->__invoke($command);

        $expected = DomainStatus::ERROR;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('errors', $actual);

        $errors = $actual['errors'];

        $expected = [['Cannot update database record: No id returned']];
        $actual   = $errors;
        $this->assertSame($expected, $actual);
    }

    public function testHandlerFailurePdoError(): void
    {
        $message = uniqid('pdo-error-');
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        /** @var Registry $registry */
        $registry = $this->container->get(Registry::class);
        /** @var UserCommandFactory $factory */
        $factory  = $this->container->get(UserCommandFactory::class);
        $userData = $this->getNewUserData();

        $userData['usr_id'] = 1;

        $findByUser = $userMapper->domain($userData);

        $userRepository = $this
            ->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'update',
                    'findById',
                ]
            )
            ->getMock()
        ;
        $userRepository->method('findById')->willReturn($findByUser);
        $userRepository
            ->method('update')
            ->willThrowException(new PDOException($message))
        ;

        $this->container->setShared(UserRepository::class, $userRepository);

        /** @var UserPutHandler $handler */
        $handler = $this->container->get(UserPutHandler::class);

        $userData           = $this->getNewUserData();
        $userData['usr_id'] = 1;

        /**
         * $userData is a db record. We need a domain object here
         */
        $updateUser = $userMapper->domain($userData);
        /**
         * Store the domain user in the registry - to be used for *_usr_id
         * field updates
         */
        $registry->set('user', $updateUser);

        $updateUser = $updateUser->toArray();
        $command    = $factory->update($updateUser);
        $payload    = $handler->__invoke($command);

        $expected = DomainStatus::ERROR;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('errors', $actual);

        $errors = $actual['errors'];

        $expected = [['Cannot update database record: ' . $message]];
        $actual   = $errors;
        $this->assertSame($expected, $actual);
    }

    public function testHandlerFailureRecordNotFound(): void
    {
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        /** @var Registry $registry */
        $registry = $this->container->get(Registry::class);
        /** @var UserCommandFactory $factory */
        $factory  = $this->container->get(UserCommandFactory::class);
        $userData = $this->getNewUserData();

        $userData['usr_id'] = 1;

        $userRepository = $this
            ->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'findById',
                ]
            )
            ->getMock()
        ;
        $userRepository->method('findById')->willReturn(null);

        $this->container->setShared(UserRepository::class, $userRepository);

        /** @var UserPutHandler $handler */
        $handler = $this->container->get(UserPutHandler::class);

        /**
         * Update user
         */
        $userData           = $this->getNewUserData();
        $userData['usr_id'] = 1;

        $updateUser = $userMapper->domain($userData);
        /**
         * Store the domain user in the registry - to be used for *_usr_id
         * field updates
         */
        $registry->set('user', $updateUser);

        $updateUser = $updateUser->toArray();
        $command    = $factory->update($updateUser);
        $payload    = $handler->__invoke($command);

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

    public function testHandlerFailureValidation(): void
    {
        /** @var UserPutHandler $handler */
        $handler = $this->container->get(UserPutHandler::class);
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        /** @var Registry $registry */
        $registry = $this->container->get(Registry::class);
        /** @var UserCommandFactory $factory */
        $factory  = $this->container->get(UserCommandFactory::class);
        $userData = $this->getNewUserData();

        $userData['usr_email'] = 'wrong-email';
        unset($userData['usr_id']);

        /**
         * $userData is a db record. We need a domain object here
         */
        $updateUser = $userMapper->domain($userData);
        /**
         * Store the domain user in the registry - to be used for *_usr_id
         * field updates
         */
        $registry->set('user', $updateUser);

        $updateUser = $updateUser->toArray();
        $command    = $factory->update($updateUser);
        $payload    = $handler->__invoke($command);

        $expected = DomainStatus::INVALID;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('errors', $actual);

        $errors = $actual['errors'];

        $expected = [
            ['Field id is not a valid absolute integer and greater than 0'],
            ['Field email must be an email address'],
        ];
        $actual   = $errors;
        $this->assertSame($expected, $actual);
    }

    public function testHandlerSuccess(): void
    {
        /** @var UserPutHandler $handler */
        $handler = $this->container->get(UserPutHandler::class);
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        /** @var Registry $registry */
        $registry = $this->container->get(Registry::class);
        /** @var UserCommandFactory $factory */
        $factory = $this->container->get(UserCommandFactory::class);

        $migration          = new UsersMigration($this->getConnection());
        $dbUser             = $this->getNewUser($migration);
        $userId             = $dbUser['usr_id'];
        $userData           = $this->getNewUserData();
        $userData['usr_id'] = $userId;
        /**
         * Don't hash the password
         */
        $userData['usr_password'] = $this->getStrongPassword();

        $userData['usr_created_usr_id'] = 4;

        /**
         * $userData is a db record. We need a domain object here
         */
        $domainUser = $userMapper->domain($userData);
        /**
         * Store the domain user in the registry - to be used for *_usr_id
         * field updates
         */
        $registry->set('user', $domainUser);

        $domainData = $domainUser->toArray();
        $command    = $factory->update($domainData);
        $payload    = $handler->__invoke($command);

        $expected = DomainStatus::UPDATED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('data', $actual);

        $data = $actual['data'];

        $this->assertArrayHasKey($userId, $data);

        $data = $data[$userId];

        $expected = $domainData['status'];
        $actual   = $data['status'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['email'];
        $actual   = $data['email'];
        $this->assertSame($expected, $actual);

        $actual = str_starts_with($data['password'], '$argon2i$');
        $this->assertTrue($actual);

        $expected = strip_tags($domainData['namePrefix']);
        $actual   = $data['namePrefix'];
        $this->assertSame($expected, $actual);

        $expected = strip_tags($domainData['nameFirst']);
        $actual   = $data['nameFirst'];
        $this->assertSame($expected, $actual);

        $expected = strip_tags($domainData['nameMiddle']);
        $actual   = $data['nameMiddle'];
        $this->assertSame($expected, $actual);

        $expected = strip_tags($domainData['nameLast']);
        $actual   = $data['nameLast'];
        $this->assertSame($expected, $actual);

        $expected = strip_tags($domainData['nameSuffix']);
        $actual   = $data['nameSuffix'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['issuer'];
        $actual   = $data['issuer'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['tokenPassword'];
        $actual   = $data['tokenPassword'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['tokenId'];
        $actual   = $data['tokenId'];
        $this->assertSame($expected, $actual);

        $expected = '';
        $actual   = $data['preferences'];
        $this->assertSame($expected, $actual);

        /**
         * These have to be the same on an update
         */
        $expected = $dbUser['usr_created_date'];
        $actual   = $data['createdDate'];
        $this->assertSame($expected, $actual);

        /**
         * These have to be the same on an update
         */
        $expected = 0;
        $actual   = $data['createdUserId'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['updatedDate'];
        $actual   = $data['updatedDate'];
        $this->assertSame($expected, $actual);

        $expected = $domainUser->id;
        $actual   = $data['updatedUserId'];
        $this->assertSame($expected, $actual);
    }

    public function testHandlerSuccessEmptyDates(): void
    {
        $now   = new DateTimeImmutable();
        $today = $now->format('Y-m-d');
        /** @var UserPutHandler $handler */
        $handler = $this->container->get(UserPutHandler::class);
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        /** @var Registry $registry */
        $registry = $this->container->get(Registry::class);
        /** @var UserCommandFactory $factory */
        $factory = $this->container->get(UserCommandFactory::class);

        $migration = new UsersMigration($this->getConnection());
        $dbUser    = $this->getNewUser($migration);

        $userId             = $dbUser['usr_id'];
        $userData           = $this->getNewUserData();
        $userData['usr_id'] = $userId;
        /**
         * Don't hash the password
         */
        $userData['usr_password'] = $this->getStrongPassword();

        unset(
            $userData['usr_updated_date'],
        );

        /**
         * $userData is a db record. We need a domain object here
         */
        $domainUser = $userMapper->domain($userData);
        /**
         * Store the domain user in the registry - to be used for *_usr_id
         * field updates
         */
        $registry->set('user', $domainUser);

        $domainData = $domainUser->toArray();
        $command    = $factory->update($domainData);
        $payload    = $handler->__invoke($command);

        $expected = DomainStatus::UPDATED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('data', $actual);

        $data = $actual['data'];

        $this->assertArrayHasKey($userId, $data);

        $data = $data[$userId];

        $expected = $domainData['status'];
        $actual   = $data['status'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['email'];
        $actual   = $data['email'];
        $this->assertSame($expected, $actual);

        $actual = str_starts_with($data['password'], '$argon2i$');
        $this->assertTrue($actual);

        $expected = strip_tags($domainData['namePrefix']);
        $actual   = $data['namePrefix'];
        $this->assertSame($expected, $actual);

        $expected = strip_tags($domainData['nameFirst']);
        $actual   = $data['nameFirst'];
        $this->assertSame($expected, $actual);

        $expected = strip_tags($domainData['nameMiddle']);
        $actual   = $data['nameMiddle'];
        $this->assertSame($expected, $actual);

        $expected = strip_tags($domainData['nameLast']);
        $actual   = $data['nameLast'];
        $this->assertSame($expected, $actual);

        $expected = strip_tags($domainData['nameSuffix']);
        $actual   = $data['nameSuffix'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['issuer'];
        $actual   = $data['issuer'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['tokenPassword'];
        $actual   = $data['tokenPassword'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['tokenId'];
        $actual   = $data['tokenId'];
        $this->assertSame($expected, $actual);

        $expected = '';
        $actual   = $data['preferences'];
        $this->assertSame($expected, $actual);

        /**
         * These have to be the same on an update
         */
        $expected = $dbUser['usr_created_date'];
        $actual   = $data['createdDate'];
        $this->assertSame($expected, $actual);

        /**
         * These have to be the same on an update
         */
        $expected = 0;
        $actual   = $data['createdUserId'];
        $this->assertSame($expected, $actual);

        $today  = date('Y-m-d ');
        $actual = $data['updatedDate'];
        $this->assertStringContainsString($today, $actual);

        $expected = $domainUser->id;
        $actual   = $data['updatedUserId'];
        $this->assertSame($expected, $actual);
    }
}
