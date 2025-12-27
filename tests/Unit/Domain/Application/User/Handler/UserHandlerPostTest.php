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
use Phalcon\Api\Domain\Application\User\Handler\UserPostHandler;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Mapper\UserMapper;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Repository\UserRepository;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Support\Registry;

use function strip_tags;
use function uniqid;

final class UserHandlerPostTest extends AbstractUnitTestCase
{
    public function testHandlerFailureNoIdReturned(): void
    {
        $userRepository = $this
            ->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'insert',
                ]
            )
            ->getMock()
        ;
        $userRepository->method('insert')->willReturn(0);

        $this->container->setShared(UserRepository::class, $userRepository);

        /** @var UserPostHandler $handler */
        $handler = $this->container->get(UserPostHandler::class);
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        /** @var Registry $registry */
        $registry = $this->container->get(Registry::class);
        /** @var UserCommandFactory $factory */
        $factory = $this->container->get(UserCommandFactory::class);

        $userData           = $this->getNewUserData();
        $userData['usr_id'] = 1;

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
        $command    = $factory->insert($domainData);

        $payload = $handler->__invoke($command);

        $expected = DomainStatus::ERROR;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('errors', $actual);

        $errors = $actual['errors'];

        $expected = [['Cannot create database record: No id returned']];
        $actual   = $errors;
        $this->assertSame($expected, $actual);
    }

    public function testHandlerFailurePdoError(): void
    {
        $message        = uniqid('pdo-error-');
        $userRepository = $this
            ->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'insert',
                ]
            )
            ->getMock()
        ;
        $userRepository
            ->method('insert')
            ->willThrowException(new PDOException($message))
        ;

        $this->container->setShared(UserRepository::class, $userRepository);

        /** @var UserPostHandler $handler */
        $handler = $this->container->get(UserPostHandler::class);
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        /** @var Registry $registry */
        $registry = $this->container->get(Registry::class);
        /** @var UserCommandFactory $factory */
        $factory = $this->container->get(UserCommandFactory::class);

        $userData           = $this->getNewUserData();
        $userData['usr_id'] = 1;

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
        $command    = $factory->insert($domainData);
        $payload    = $handler->__invoke($command);

        $expected = DomainStatus::ERROR;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('errors', $actual);

        $errors = $actual['errors'];

        $expected = [['Cannot create database record: ' . $message]];
        $actual   = $errors;
        $this->assertSame($expected, $actual);
    }

    public function testHandlerFailureValidation(): void
    {
        /** @var UserPostHandler $handler */
        $handler = $this->container->get(UserPostHandler::class);
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        /** @var UserCommandFactory $factory */
        $factory = $this->container->get(UserCommandFactory::class);

        $userData = $this->getNewUserData();

        unset(
            $userData['usr_email'],
            $userData['usr_password'],
            $userData['usr_issuer'],
            $userData['usr_token_password'],
            $userData['usr_token_id']
        );

        /**
         * $userData is a db record. We need a domain object here
         */
        $domainUser = $userMapper->domain($userData);
        $domainData = $domainUser->toArray();
        $command    = $factory->insert($domainData);
        $payload    = $handler->__invoke($command);

        $expected = DomainStatus::INVALID;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('errors', $actual);

        $errors = $actual['errors'];

        $expected = [
            ['Field email is required'],
            ['Field email must be an email address'],
            ['Field password is required'],
            ['Field issuer is required'],
            ['Field tokenPassword is required'],
            ['Field tokenId is required'],
        ];
        $actual   = $errors;
        $this->assertSame($expected, $actual);
    }

    public function testHandlerSuccess(): void
    {
        /** @var UserPostHandler $handler */
        $handler = $this->container->get(UserPostHandler::class);
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        /** @var Registry $registry */
        $registry = $this->container->get(Registry::class);
        /** @var UserCommandFactory $factory */
        $factory = $this->container->get(UserCommandFactory::class);

        $userData = $this->getNewUserData();

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
        $command    = $factory->insert($domainData);
        $payload    = $handler->__invoke($command);

        $expected = DomainStatus::CREATED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('data', $actual);

        $data = $actual['data'];

        $userId = array_key_first($data);

        $this->assertGreaterThan(0, $userId);

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

        $actual = $data['preferences'];
        $this->assertNull($actual);

        $expected = $domainData['createdDate'];
        $actual   = $data['createdDate'];
        $this->assertSame($expected, $actual);

        $expected = $domainUser->id;
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
        /** @var UserPostHandler $handler */
        $handler = $this->container->get(UserPostHandler::class);
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        /** @var Registry $registry */
        $registry = $this->container->get(Registry::class);
        /** @var UserCommandFactory $factory */
        $factory = $this->container->get(UserCommandFactory::class);

        $userData = $this->getNewUserData();
        unset(
            $userData['usr_created_date'],
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
        $command    = $factory->insert($domainData);

        $payload = $handler->__invoke($command);

        $expected = DomainStatus::CREATED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('data', $actual);

        $data = $actual['data'];

        $userId = array_key_first($data);

        $this->assertGreaterThan(0, $userId);

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

        $actual = $data['preferences'];
        $this->assertNull($actual);

        $actual = $data['createdDate'];
        $this->assertStringContainsString($today, $actual);

        $expected = 0;
        $actual   = $data['createdUserId'];
        $this->assertSame($expected, $actual);

        $actual = $data['updatedDate'];
        $this->assertStringContainsString($today, $actual);

        $expected = 0;
        $actual   = $data['updatedUserId'];
        $this->assertSame($expected, $actual);
    }
}
