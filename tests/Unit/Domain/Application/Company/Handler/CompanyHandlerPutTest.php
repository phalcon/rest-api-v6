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

namespace Phalcon\Api\Tests\Unit\Domain\Application\Company\Handler;

use DateTimeImmutable;
use Faker\Factory;
use PayloadInterop\DomainStatus;
use PDOException;
use Phalcon\Api\Domain\Application\Company\Command\CompanyCommandFactory;
use Phalcon\Api\Domain\Application\Company\Handler\CompanyPutHandler;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Mapper\CompanyMapper;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Repository\CompanyRepository;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Mapper\UserMapper;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Api\Tests\Fixtures\Domain\Migrations\CompaniesMigration;
use Phalcon\Support\Registry;

use function uniqid;

final class CompanyHandlerPutTest extends AbstractUnitTestCase
{
    public function testServiceFailureNoIdReturned(): void
    {
        /** @var CompanyMapper $companyMapper */
        $companyMapper = $this->container->get(CompanyMapper::class);
        $companyData   = $this->getNewCompanyData();

        $companyData['com_id'] = 1;

        $findByCompany = $companyMapper->domain($companyData);

        $userRepository = $this
            ->getMockBuilder(CompanyRepository::class)
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
        $userRepository->method('findById')->willReturn($findByCompany);

        $this->container->setShared(CompanyRepository::class, $userRepository);

        /**
         * Add a user in the repository for the session
         */
        /** @var Registry $registry */
        $registry = $this->container->get(Registry::class);
        /** @var UserMapper $userMapper */
        $userMapper  = $this->container->get(UserMapper::class);
        $sessionUser = $userMapper->domain(['id' => 1]);
        $registry->set('user', $sessionUser);

        /** @var CompanyPutHandler $handler */
        $handler = $this->container->get(CompanyPutHandler::class);
        /** @var CompanyCommandFactory $factory */
        $factory = $this->container->get(CompanyCommandFactory::class);

        /**
         * Update company
         */
        $companyData           = $this->getNewCompanyData();
        $companyData['com_id'] = 1;

        $updateCompany = $companyMapper->domain($companyData);
        $updateCompany = $updateCompany->toArray();

        $command = $factory->update($updateCompany);
        $payload = $handler->__invoke($command);

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

    public function testServiceFailurePdoError(): void
    {
        $message = uniqid('pdo-error-');
        /** @var CompanyMapper $companyMapper */
        $companyMapper = $this->container->get(CompanyMapper::class);
        $companyData   = $this->getNewCompanyData();

        $companyData['com_id'] = 1;

        $findByCompany = $companyMapper->domain($companyData);

        $userRepository = $this
            ->getMockBuilder(CompanyRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'update',
                    'findById',
                ]
            )
            ->getMock()
        ;
        $userRepository->method('findById')->willReturn($findByCompany);
        $userRepository
            ->method('update')
            ->willThrowException(new PDOException($message))
        ;

        $this->container->setShared(CompanyRepository::class, $userRepository);

        /**
         * Add a user in the repository for the session
         */
        /** @var Registry $registry */
        $registry = $this->container->get(Registry::class);
        /** @var UserMapper $userMapper */
        $userMapper  = $this->container->get(UserMapper::class);
        $sessionUser = $userMapper->domain(['id' => 1]);
        $registry->set('user', $sessionUser);

        /** @var CompanyPutHandler $handler */
        $handler = $this->container->get(CompanyPutHandler::class);
        /** @var CompanyCommandFactory $factory */
        $factory = $this->container->get(CompanyCommandFactory::class);

        $companyData           = $this->getNewCompanyData();
        $companyData['com_id'] = 1;

        /**
         * $companyData is a db record. We need a domain object here
         */
        $updateCompany = $companyMapper->domain($companyData);
        $updateCompany = $updateCompany->toArray();

        $command = $factory->update($updateCompany);
        $payload = $handler->__invoke($command);

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

    public function testServiceFailureRecordNotFound(): void
    {
        /** @var CompanyMapper $companyMapper */
        $companyMapper  = $this->container->get(CompanyMapper::class);
        $userRepository = $this
            ->getMockBuilder(CompanyRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'findById',
                ]
            )
            ->getMock()
        ;
        $userRepository->method('findById')->willReturn(null);

        $this->container->setShared(CompanyRepository::class, $userRepository);

        /** @var CompanyPutHandler $handler */
        $handler = $this->container->get(CompanyPutHandler::class);
        /** @var CompanyCommandFactory $factory */
        $factory = $this->container->get(CompanyCommandFactory::class);

        /**
         * Update user
         */
        $companyData           = $this->getNewCompanyData();
        $companyData['com_id'] = 1;

        $updateCompany = $companyMapper->domain($companyData);
        $updateCompany = $updateCompany->toArray();

        $command = $factory->update($updateCompany);
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

    public function testServiceFailureValidation(): void
    {
        /** @var CompanyPutHandler $handler */
        $handler = $this->container->get(CompanyPutHandler::class);
        /** @var CompanyCommandFactory $factory */
        $factory = $this->container->get(CompanyCommandFactory::class);
        /** @var CompanyMapper $companyMapper */
        $companyMapper = $this->container->get(CompanyMapper::class);
        $companyData   = $this->getNewCompanyData();

        $companyData['com_email'] = 'wrong-email';
        unset($companyData['com_id']);

        /**
         * $companyData is a db record. We need a domain object here
         */
        $updateCompany = $companyMapper->domain($companyData);
        $updateCompany = $updateCompany->toArray();

        $command = $factory->update($updateCompany);
        $payload = $handler->__invoke($command);

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

    public function testServiceSuccess(): void
    {
        $faker = Factory::create();
        /** @var CompanyPutHandler $handler */
        $handler = $this->container->get(CompanyPutHandler::class);
        /** @var CompanyCommandFactory $factory */
        $factory = $this->container->get(CompanyCommandFactory::class);
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        /** @var CompanyMapper $companyMapper */
        $companyMapper = $this->container->get(CompanyMapper::class);
        /** @var Registry $registry */
        $registry = $this->container->get(Registry::class);

        $migration          = new CompaniesMigration($this->getConnection());
        $dbCompany          = $this->getNewCompany($migration);
        $companyId          = $dbCompany['com_id'];
        $userData           = $this->getNewUserData();
        $userId             = $faker->numberBetween(1, 10);
        $userData['usr_id'] = $userId;

        /**
         * $userData is a db record. We need a domain object here
         */
        $domainUser = $userMapper->domain($userData);
        /**
         * Store the domain user in the registry - to be used for *_com_id
         * field updates
         */
        $registry->set('user', $domainUser);

        $domainCompany = $companyMapper->domain($dbCompany);
        $domainData    = $domainCompany->toArray();
        $command       = $factory->update($domainData);
        $payload       = $handler->__invoke($command);

        $expected = DomainStatus::UPDATED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('data', $actual);

        $data = $actual['data'];

        $this->assertArrayHasKey($companyId, $data);

        $data = $data[$companyId];

        $expected = $domainData['id'];
        $actual   = $data['id'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['name'];
        $actual   = $data['name'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['email'];
        $actual   = $data['email'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['phone'];
        $actual   = $data['phone'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['website'];
        $actual   = $data['website'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['addressLine1'];
        $actual   = $data['addressLine1'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['addressLine2'];
        $actual   = $data['addressLine2'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['city'];
        $actual   = $data['city'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['stateProvince'];
        $actual   = $data['stateProvince'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['zipCode'];
        $actual   = $data['zipCode'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['country'];
        $actual   = $data['country'];
        $this->assertSame($expected, $actual);

        /**
         * These have to be the same on an update
         */
        $expected = $domainData['createdDate'];
        $actual   = $data['createdDate'];
        $this->assertSame($expected, $actual);

        $expected = 0;
        $actual   = $data['createdUserId'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['updatedDate'];
        $actual   = $data['updatedDate'];
        $this->assertSame($expected, $actual);

        $expected = $userId;
        $actual   = $data['updatedUserId'];
        $this->assertSame($expected, $actual);
    }

    public function testServiceSuccessEmptyDates(): void
    {
        $faker = Factory::create();
        $now   = new DateTimeImmutable();
        $today = $now->format('Y-m-d');
        /** @var CompanyPutHandler $handler */
        $handler = $this->container->get(CompanyPutHandler::class);
        /** @var CompanyCommandFactory $factory */
        $factory = $this->container->get(CompanyCommandFactory::class);
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        /** @var CompanyMapper $companyMapper */
        $companyMapper = $this->container->get(CompanyMapper::class);
        /** @var Registry $registry */
        $registry = $this->container->get(Registry::class);

        $migration = new CompaniesMigration($this->getConnection());
        $dbCompany = $this->getNewCompany($migration);

        $companyId          = $dbCompany['com_id'];
        $userData           = $this->getNewUserData();
        $userId             = $faker->numberBetween(1, 10);
        $userData['usr_id'] = $userId;

        unset(
            $dbCompany['com_updated_date'],
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

        $domainCompany = $companyMapper->domain($dbCompany);
        $domainData    = $domainCompany->toArray();
        $command       = $factory->update($domainData);
        $payload       = $handler->__invoke($command);

        $expected = DomainStatus::UPDATED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('data', $actual);

        $data = $actual['data'];

        $this->assertArrayHasKey($companyId, $data);

        $data = $data[$companyId];

        $expected = $domainData['id'];
        $actual   = $data['id'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['name'];
        $actual   = $data['name'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['email'];
        $actual   = $data['email'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['phone'];
        $actual   = $data['phone'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['website'];
        $actual   = $data['website'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['addressLine1'];
        $actual   = $data['addressLine1'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['addressLine2'];
        $actual   = $data['addressLine2'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['city'];
        $actual   = $data['city'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['stateProvince'];
        $actual   = $data['stateProvince'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['zipCode'];
        $actual   = $data['zipCode'];
        $this->assertSame($expected, $actual);

        $expected = $domainData['country'];
        $actual   = $data['country'];
        $this->assertSame($expected, $actual);

        /**
         * These have to be the same on an update
         */
        $expected = $dbCompany['com_created_date'];
        $actual   = $data['createdDate'];
        $this->assertSame($expected, $actual);

        /**
         * These have to be the same on an update
         */
        $expected = 0;
        $actual   = $data['createdUserId'];
        $this->assertSame($expected, $actual);

        $actual = $data['updatedDate'];
        $this->assertStringContainsString($today, $actual);

        $expected = $userId;
        $actual   = $data['updatedUserId'];
        $this->assertSame($expected, $actual);
    }
}
