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
use Phalcon\Api\Domain\Application\Company\Service\CompanyGetManyService;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Api\Tests\Fixtures\Domain\Migrations\CompaniesMigration;

use function count;

final class CompanyServiceGetManyTest extends AbstractUnitTestCase
{
    public function testServiceWithCompanyId(): void
    {
        /** @var CompanyGetManyService $service */
        $service = $this->container->get(CompanyGetManyService::class);

        $companyMigration = new CompaniesMigration($this->getConnection());

        $company1 = $this->getNewCompany($companyMigration);
        $company2 = $this->getNewCompany($companyMigration);
        $company3 = $this->getNewCompany($companyMigration);
        $company4 = $this->getNewCompany($companyMigration);
        $company5 = $this->getNewCompany($companyMigration);

        $payload = $service->__invoke([]);

        $expected = DomainStatus::SUCCESS;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('data', $actual);

        $data = $actual['data'];

        $expected = 5;
        $actual   = count($data);
        $this->assertSame($expected, $actual);

        $this->assertArrayHasKey($company1['com_id'], $data);
        $this->assertArrayHasKey($company2['com_id'], $data);
        $this->assertArrayHasKey($company3['com_id'], $data);
        $this->assertArrayHasKey($company4['com_id'], $data);
        $this->assertArrayHasKey($company5['com_id'], $data);

        $expected = $company1['com_name'];
        $actual   = $data[$company1['com_id']]['name'];
        $this->assertSame($expected, $actual);

        $expected = $company2['com_name'];
        $actual   = $data[$company2['com_id']]['name'];
        $this->assertSame($expected, $actual);

        $expected = $company3['com_name'];
        $actual   = $data[$company3['com_id']]['name'];
        $this->assertSame($expected, $actual);

        $expected = $company4['com_name'];
        $actual   = $data[$company4['com_id']]['name'];
        $this->assertSame($expected, $actual);

        $expected = $company5['com_name'];
        $actual   = $data[$company5['com_id']]['name'];
        $this->assertSame($expected, $actual);
    }
}
