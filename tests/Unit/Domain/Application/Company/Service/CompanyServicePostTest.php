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

namespace Phalcon\Api\Tests\Unit\Domain\Application\Company\Service;

use DateTimeImmutable;
use PayloadInterop\DomainStatus;
use PDOException;
use Phalcon\Api\Domain\Application\Company\Service\CompanyPostService;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Mapper\CompanyMapper;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Mapper\CompanyMapperInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Repository\CompanyRepository;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Mapper\UserMapper;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Mapper\UserMapperInterface;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Support\Registry;

use function array_key_first;
use function uniqid;

final class CompanyServicePostTest extends AbstractUnitTestCase
{
    public function testServiceFailureNoIdReturned(): void
    {
        $companyRepository = $this
            ->getMockBuilder(CompanyRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'insert',
                ]
            )
            ->getMock()
        ;
        $companyRepository->method('insert')->willReturn(0);

        $this->container->setShared(CompanyRepository::class, $companyRepository);

        /** @var CompanyPostService $service */
        $service = $this->container->get(CompanyPostService::class);
        /** @var CompanyMapper $companyMapper */
        $companyMapper = $this->container->get(CompanyMapper::class);
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        /** @var Registry $registry */
        $registry = $this->container->get(Registry::class);

        $userData              = $this->getNewUserData();
        $userData['usr_id']    = 1;
        $companyData           = $this->getNewCompanyData();
        $companyData['com_id'] = 1;

        /**
         * $companyData is a db record. We need a domain object here
         */
        $domainCompany = $companyMapper->domain($companyData);
        $domainUser = $userMapper->domain($userData);
        /**
         * Store the domain user in the registry - to be used for *_com_id
         * field updates
         */
        $registry->set('user', $domainUser);

        $domainData = $domainCompany->toArray();

        $payload = $service->__invoke($domainData);

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

    public function testServiceFailurePdoError(): void
    {
        $message           = uniqid('pdo-error-');
        $companyRepository = $this
            ->getMockBuilder(CompanyRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'insert',
                ]
            )
            ->getMock()
        ;
        $companyRepository
            ->method('insert')
            ->willThrowException(new PDOException($message))
        ;

        $this->container->setShared(CompanyRepository::class, $companyRepository);

        /** @var CompanyPostService $service */
        $service = $this->container->get(CompanyPostService::class);
        /** @var CompanyMapper $companyMapper */
        $companyMapper = $this->container->get(CompanyMapper::class);
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        /** @var Registry $registry */
        $registry = $this->container->get(Registry::class);

        $userData              = $this->getNewUserData();
        $userData['usr_id']    = 1;
        $companyData           = $this->getNewCompanyData();
        $companyData['com_id'] = 1;

        /**
         * $companyData is a db record. We need a domain object here
         */
        $domainCompany = $companyMapper->domain($companyData);
        $domainUser = $userMapper->domain($userData);
        /**
         * Store the domain user in the registry - to be used for *_com_id
         * field updates
         */
        $registry->set('user', $domainUser);

        $domainData = $domainCompany->toArray();

        $payload = $service->__invoke($domainData);

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

    public function testServiceFailureValidation(): void
    {
        /** @var CompanyPostService $service */
        $service = $this->container->get(CompanyPostService::class);
        /** @var CompanyMapper $companyMapper */
        $companyMapper = $this->container->get(CompanyMapper::class);

        $companyData = $this->getNewCompanyData();

        unset(
            $companyData['com_name'],
            $companyData['com_email'],
        );

        /**
         * $companyData is a db record. We need a domain object here
         */
        $domainCompany = $companyMapper->domain($companyData);
        $domainData    = $domainCompany->toArray();

        $payload = $service->__invoke($domainData);

        $expected = DomainStatus::INVALID;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('errors', $actual);

        $errors = $actual['errors'];

        $expected = [
            ['Field name is required'],
            ['Field email is required'],
            ['Field email must be an email address'],
        ];
        $actual   = $errors;
        $this->assertSame($expected, $actual);
    }

    public function testServiceSuccess(): void
    {
        /** @var CompanyPostService $service */
        $service = $this->container->get(CompanyPostService::class);
        /** @var UserMapperInterface $companyMapper */
        $userMapper = $this->container->get(UserMapper::class);
        /** @var CompanyMapperInterface $companyMapper */
        $companyMapper = $this->container->get(CompanyMapper::class);
        /** @var Registry $registry */
        $registry = $this->container->get(Registry::class);

        $userData              = $this->getNewUserData();
        $userData['usr_id']    = 1;
        $companyData           = $this->getNewCompanyData();
        $companyData['com_id'] = 1;

        /**
         * $companyData is a db record. We need a domain object here
         */
        $domainCompany = $companyMapper->domain($companyData);
        $domainUser = $userMapper->domain($userData);
        /**
         * Store the domain user in the registry - to be used for *_com_id
         * field updates
         */
        $registry->set('user', $domainUser);

        $domainCompany = $companyMapper->domain($companyData);
        $domainData    = $domainCompany->toArray();

        $payload = $service->__invoke($domainData);

        $expected = DomainStatus::CREATED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('data', $actual);

        $data = $actual['data'];

        $companyId = array_key_first($data);

        $this->assertGreaterThan(0, $companyId);

        $data = $data[$companyId];

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

    public function testServiceSuccessEmptyDates(): void
    {
        $now   = new DateTimeImmutable();
        $today = $now->format('Y-m-d');
        /** @var CompanyPostService $service */
        $service = $this->container->get(CompanyPostService::class);
        /** @var CompanyMapperInterface $companyMapper */
        $companyMapper = $this->container->get(CompanyMapper::class);
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        /** @var Registry $registry */
        $registry = $this->container->get(Registry::class);

        $userData              = $this->getNewUserData();
        $userData['usr_id']    = 1;
        $companyData           = $this->getNewCompanyData();
        $companyData['com_id'] = 1;

        /**
         * $companyData is a db record. We need a domain object here
         */
        $domainCompany = $companyMapper->domain($companyData);
        $domainUser = $userMapper->domain($userData);
        /**
         * Store the domain user in the registry - to be used for *_com_id
         * field updates
         */
        $registry->set('user', $domainUser);

        unset(
            $companyData['com_created_date'],
            $companyData['com_updated_date'],
        );

        /**
         * $companyData is a db record. We need a domain object here
         */
        $domainCompany = $companyMapper->domain($companyData);
        $domainData    = $domainCompany->toArray();
        $payload       = $service->__invoke($domainData);

        $expected = DomainStatus::CREATED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('data', $actual);

        $data = $actual['data'];

        $companyId = array_key_first($data);

        $this->assertGreaterThan(0, $companyId);

        $data = $data[$companyId];

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

        $actual = $data['createdDate'];
        $this->assertStringContainsString($today, $actual);

        $expected = 1;
        $actual   = $data['createdUserId'];
        $this->assertSame($expected, $actual);

        $actual = $data['updatedDate'];
        $this->assertStringContainsString($today, $actual);

        $expected = 1;
        $actual   = $data['updatedUserId'];
        $this->assertSame($expected, $actual);
    }
}
