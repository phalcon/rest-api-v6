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
use Phalcon\Api\Domain\Application\Company\Service\CompanyDeleteService;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Api\Tests\Fixtures\Domain\Migrations\CompaniesMigration;

final class CompanyServiceDeleteTest extends AbstractUnitTestCase
{
    public function testServiceWithCompanyId(): void
    {
        /** @var CompanyDeleteService $service */
        $service = $this->container->get(CompanyDeleteService::class);

        /**
         * We need to ask for a company to be deleted with an ID that does not
         * exist in the database. To ensure that, we will create a company,
         * delete it and then try to delete the same company with that ID
         */
        $migration = new CompaniesMigration($this->getConnection());
        $dbCompany = $this->getNewCompany($migration);
        $companyId = $dbCompany['com_id'];

        $payload = $service->__invoke(
            [
                'id' => $companyId,
            ]
        );

        $expected = DomainStatus::DELETED;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('data', $actual);

        $data = $actual['data'];

        $expected = [
            'Record deleted successfully [#' . $companyId . '].',
        ];
        $actual   = $data;
        $this->assertSame($expected, $actual);

        /**
         * Now deleting it again, will result in a 404
         */
        $payload = $service->__invoke(
            [
                'id' => $companyId,
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

    public function testServiceZeroCompanyId(): void
    {
        /** @var CompanyDeleteService $service */
        $service = $this->container->get(CompanyDeleteService::class);

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
