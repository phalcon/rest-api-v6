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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\DataSource\Company\Repository;

use Phalcon\Api\Domain\Application\Company\Command\CompanyCommandFactory;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\DTO\Company;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\DTO\CompanyCollection;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\DTO\CompanyCriteria;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Repository\CompanyRepository;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Api\Tests\Fixtures\Domain\Migrations\CompaniesMigration;

use function uniqid;

final class CompanyRepositoryTest extends AbstractUnitTestCase
{
    public function testFind(): void
    {
        /** @var CompanyCommandFactory $factory */
        $factory = $this->container->get(CompanyCommandFactory::class);
        /** @var CompanyRepository $repository */
        $repository = $this->container->get(CompanyRepository::class);

        $companyName = uniqid('name-');
        $migration   = new CompaniesMigration($this->getConnection());

        $company1 = $this->getNewCompany($migration, ['com_name' => $companyName]);
        $company2 = $this->getNewCompany($migration, ['com_name' => $companyName]);
        $company3 = $this->getNewCompany($migration, ['com_name' => $companyName]);
        $company4 = $this->getNewCompany($migration);
        $company5 = $this->getNewCompany($migration);

        $command  = $factory->getMany(
            [
                'page'    => 1,
                'perPage' => 10,
            ]
        );
        $criteria = CompanyCriteria::fromDto($command);

        $companies = $repository->find($criteria);

        $class = CompanyCollection::class;
        $this->assertInstanceOf($class, $companies);

        $expected = 5;
        $actual   = $companies->count();
        $this->assertSame($expected, $actual);

        $companies = $companies->toArray();

        $expected = $company1['com_id'];
        $actual   = $companies[$company1['com_id']]->id;
        $this->assertSame($expected, $actual);

        $expected = $company2['com_id'];
        $actual   = $companies[$company2['com_id']]->id;
        $this->assertSame($expected, $actual);

        $expected = $company3['com_id'];
        $actual   = $companies[$company3['com_id']]->id;
        $this->assertSame($expected, $actual);

        $expected = $company4['com_id'];
        $actual   = $companies[$company4['com_id']]->id;
        $this->assertSame($expected, $actual);

        $expected = $company5['com_id'];
        $actual   = $companies[$company5['com_id']]->id;
        $this->assertSame($expected, $actual);

        $command   = $factory->getMany(
            [
                'name'    => $companyName,
                'page'    => 1,
                'perPage' => 10,
            ]
        );
        $criteria  = CompanyCriteria::fromDto($command);
        $companies = $repository->find($criteria);

        $class = CompanyCollection::class;
        $this->assertInstanceOf($class, $companies);

        $expected = 3;
        $actual   = $companies->count();
        $this->assertSame($expected, $actual);

        $companyIds = [];
        foreach ($companies as $company) {
            $companyIds[] = $company->id;
        }

        $expected = [
            $company1['com_id'],
            $company2['com_id'],
            $company3['com_id'],
        ];
        $actual   = $companyIds;
        $this->assertSame($expected, $actual);
    }

    public function testFindById(): void
    {
        /** @var CompanyRepository $repository */
        $repository = $this->container->get(CompanyRepository::class);

        $migration = new CompaniesMigration($this->getConnection());

        $repositoryCompany = $repository->findById(0);
        $this->assertEmpty($repositoryCompany);

        $migrationCompany = $this->getNewCompany($migration);
        $companyId        = $migrationCompany['com_id'];

        $repositoryCompany = $repository->findById($companyId);

        $this->runAssertions($migrationCompany, $repositoryCompany);
    }

    private function runAssertions(array $dbCompany, Company $company): void
    {
        $expected = $dbCompany['com_id'];
        $actual   = $company->id;
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_name'];
        $actual   = $company->name;
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_email'];
        $actual   = $company->email;
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_phone'];
        $actual   = $company->phone;
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_website'];
        $actual   = $company->website;
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_address_line_1'];
        $actual   = $company->addressLine1;
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_address_line_2'];
        $actual   = $company->addressLine2;
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_city'];
        $actual   = $company->city;
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_state_province'];
        $actual   = $company->stateProvince;
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_zip_code'];
        $actual   = $company->zipCode;
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_country'];
        $actual   = $company->country;
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_created_date'];
        $actual   = $company->createdDate;
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_created_usr_id'];
        $actual   = $company->createdUserId;
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_updated_date'];
        $actual   = $company->updatedDate;
        $this->assertSame($expected, $actual);

        $expected = $dbCompany['com_updated_usr_id'];
        $actual   = $company->updatedUserId;
        $this->assertSame($expected, $actual);
    }
}
