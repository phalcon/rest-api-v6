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
use Phalcon\Api\Domain\Application\Company\Handler\CompanyGetManyHandler;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Api\Tests\Fixtures\Domain\Migrations\CompaniesMigration;

use function count;
use function uniqid;

final class CompanyHandlerGetManyTest extends AbstractUnitTestCase
{
    public function testHandlerSuccess(): void
    {
        /** @var CompanyGetManyHandler $handler */
        $handler = $this->container->get(CompanyGetManyHandler::class);
        /** @var CompanyCommandFactory $factory */
        $factory = $this->container->get(CompanyCommandFactory::class);

        $companyMigration = new CompaniesMigration($this->getConnection());
        $companyName1 = uniqid('companyName');
        $companyName2 = uniqid('companyName');

        $company1 = $this->getNewCompany($companyMigration, ['com_name' => $companyName1]);
        $company2 = $this->getNewCompany($companyMigration, ['com_name' => $companyName1]);
        $company3 = $this->getNewCompany($companyMigration, ['com_name' => $companyName2]);
        $company4 = $this->getNewCompany($companyMigration, ['com_name' => $companyName2]);
        $company5 = $this->getNewCompany($companyMigration, ['com_name' => $companyName2]);

        /**
         * Make the call
         */
        $command = $factory->getMany([]);
        $payload = $handler->__invoke($command);

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

        /**
         * Make the call for paginated
         */
        $command = $factory->getMany(['page' => 2, 'perPage' => 2]);
        $payload = $handler->__invoke($command);

        $expected = DomainStatus::SUCCESS;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('data', $actual);

        $data = $actual['data'];

        $expected = 2;
        $actual   = count($data);
        $this->assertSame($expected, $actual);

        $this->assertArrayHasKey($company3['com_id'], $data);
        $this->assertArrayHasKey($company4['com_id'], $data);

        $expected = $company3['com_name'];
        $actual   = $data[$company3['com_id']]['name'];
        $this->assertSame($expected, $actual);

        $expected = $company4['com_name'];
        $actual   = $data[$company4['com_id']]['name'];
        $this->assertSame($expected, $actual);

        /**
         * Make the call for name
         */
        $command = $factory->getMany(['name' => $companyName1]);
        $payload = $handler->__invoke($command);

        $expected = DomainStatus::SUCCESS;
        $actual   = $payload->getStatus();
        $this->assertSame($expected, $actual);

        $actual = $payload->getResult();
        $this->assertArrayHasKey('data', $actual);

        $data = $actual['data'];

        $expected = 2;
        $actual   = count($data);
        $this->assertSame($expected, $actual);

        $this->assertArrayHasKey($company1['com_id'], $data);
        $this->assertArrayHasKey($company2['com_id'], $data);

        $expected = $company1['com_name'];
        $actual   = $data[$company1['com_id']]['name'];
        $this->assertSame($expected, $actual);

        $expected = $company2['com_name'];
        $actual   = $data[$company2['com_id']]['name'];
        $this->assertSame($expected, $actual);
    }
}
