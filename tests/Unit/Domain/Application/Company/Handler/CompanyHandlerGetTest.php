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

use PayloadInterop\DomainStatus;
use Phalcon\Api\Domain\Application\Company\Command\CompanyCommandFactory;
use Phalcon\Api\Domain\Application\Company\Handler\CompanyGetHandler;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Api\Tests\Fixtures\Domain\Migrations\CompaniesMigration;

use function array_key_first;

final class CompanyHandlerGetTest extends AbstractUnitTestCase
{
    public function testHandlerEmptyCompanyId(): void
    {
        /** @var CompanyGetHandler $handler */
        $handler = $this->container->get(CompanyGetHandler::class);
        /** @var CompanyCommandFactory $factory */
        $factory = $this->container->get(CompanyCommandFactory::class);

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

    public function testHandlerWithCompanyId(): void
    {
        /** @var CompanyGetHandler $handler */
        $handler = $this->container->get(CompanyGetHandler::class);
        /** @var CompanyCommandFactory $factory */
        $factory = $this->container->get(CompanyCommandFactory::class);

        $companyMigration = new CompaniesMigration($this->getConnection());

        $dbCompany = $this->getNewCompany($companyMigration);
        $companyId = $dbCompany['com_id'];

        $command = $factory->get(['id' => $companyId]);
        $payload = $handler->__invoke($command);

        $expected = DomainStatus::SUCCESS;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('data', $actual);

        $company = $actual['data'];
        $key     = array_key_first($company);
        $company = $company[$key];

        $expected = $dbCompany['com_id'];
        $actual   = $company['id'];
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_name'];
        $actual   = $company['name'];
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_email'];
        $actual   = $company['email'];
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_phone'];
        $actual   = $company['phone'];
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_website'];
        $actual   = $company['website'];
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_address_line_1'];
        $actual   = $company['addressLine1'];
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_address_line_2'];
        $actual   = $company['addressLine2'];
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_city'];
        $actual   = $company['city'];
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_state_province'];
        $actual   = $company['stateProvince'];
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_zip_code'];
        $actual   = $company['zipCode'];
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_country'];
        $actual   = $company['country'];
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_created_date'];
        $actual   = $company['createdDate'];
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_created_usr_id'];
        $actual   = $company['createdUserId'];
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_updated_date'];
        $actual   = $company['updatedDate'];
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_updated_usr_id'];
        $actual   = $company['updatedUserId'];
        $this->assertSame($expected, $actual);
    }

    public function testHandlerWrongCompanyId(): void
    {
        /** @var CompanyGetHandler $handler */
        $handler = $this->container->get(CompanyGetHandler::class);
        /** @var CompanyCommandFactory $factory */
        $factory = $this->container->get(CompanyCommandFactory::class);

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
