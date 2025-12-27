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

use PayloadInterop\DomainStatus;
use Phalcon\Api\Domain\Application\Company\Service\CompanyGetService;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Api\Tests\Fixtures\Domain\Migrations\CompaniesMigration;

use function array_key_first;

final class CompanyServiceGetTest extends AbstractUnitTestCase
{
    public function testServiceEmptyCompanyId(): void
    {
        /** @var CompanyGetService $service */
        $service = $this->container->get(CompanyGetService::class);

        $payload = $service->__invoke([]);

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

    public function testServiceWithCompanyId(): void
    {
        /** @var CompanyGetService $service */
        $service = $this->container->get(CompanyGetService::class);

        $companyMigration = new CompaniesMigration($this->getConnection());

        $dbCompany = $this->getNewCompany($companyMigration);
        $companyId = $dbCompany['com_id'];

        $payload = $service->__invoke(
            [
                'id' => $companyId,
            ]
        );

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

    public function testServiceWrongCompanyId(): void
    {
        /** @var CompanyGetService $service */
        $service = $this->container->get(CompanyGetService::class);

        $payload = $service->__invoke(
            [
                'id' => 999999,
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
