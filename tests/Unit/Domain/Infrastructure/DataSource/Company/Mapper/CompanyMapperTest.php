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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\DataSource\Company\Mapper;

use Faker\Factory as FakerFactory;
use Phalcon\Api\Domain\Application\Company\Command\CompanyCommandFactory;
use Phalcon\Api\Domain\Infrastructure\Constants\Dates;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Mapper\CompanyMapper;
use Phalcon\Api\Tests\AbstractUnitTestCase;

final class CompanyMapperTest extends AbstractUnitTestCase
{
    public function testDb(): void
    {
        /** @var CompanyCommandFactory $factory */
        $factory = $this->container->get(CompanyCommandFactory::class);
        $faker   = FakerFactory::create();

        $createdDate = $faker->date(Dates::DATE_TIME_FORMAT);
        $updatedDate = $faker->date(Dates::DATE_TIME_FORMAT);

        $input = [
            'id'            => $faker->numberBetween(1, 1000),
            'name'          => $faker->company(),
            'phone'         => $faker->safeEmail(),
            'email'         => $faker->phoneNumber(),
            'website'       => $faker->domainName(),
            'addressLine1'  => $faker->streetAddress(),
            'addressLine2'  => $faker->streetAddress(),
            'city'          => $faker->city(),
            'stateProvince' => $faker->city(),
            'zipCode'       => $faker->postcode(),
            'country'       => $faker->countryISOAlpha3(),
            'createdDate'   => $createdDate,
            'createdUserId' => $faker->numberBetween(1, 1000),
            'updatedDate'   => $updatedDate,
            'updatedUserId' => $faker->numberBetween(1, 1000),
        ];

        $company = $factory->update($input);

        /** @var CompanyMapper $mapper */
        $mapper = $this->container->get(CompanyMapper::class);
        $row    = $mapper->db($company);

        $expected = [
            'com_id'             => $company->id,
            'com_name'           => $company->name,
            'com_phone'          => $company->phone,
            'com_email'          => $company->email,
            'com_website'        => $company->website,
            'com_address_line_1' => $company->addressLine1,
            'com_address_line_2' => $company->addressLine2,
            'com_city'           => $company->city,
            'com_state_province' => $company->stateProvince,
            'com_zip_code'       => $company->zipCode,
            'com_country'        => $company->country,
            'com_created_date'   => $company->createdDate,
            'com_created_usr_id' => $company->createdUserId,
            'com_updated_date'   => $company->updatedDate,
            'com_updated_usr_id' => $company->updatedUserId,
        ];

        $actual = $row;
        $this->assertSame($expected, $actual);
    }

    public function testDomain(): void
    {
        $faker  = FakerFactory::create();
        $mapper = $this->container->get(CompanyMapper::class);

        // Empty row: defaults should be applied
        $emptyCompany = $mapper->domain([]);

        $expected = 0;
        $actual   = $emptyCompany->id;
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = $emptyCompany->name;
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = $emptyCompany->phone;
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = $emptyCompany->email;
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = $emptyCompany->website;
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = $emptyCompany->addressLine1;
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = $emptyCompany->addressLine2;
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = $emptyCompany->city;
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = $emptyCompany->stateProvince;
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = $emptyCompany->zipCode;
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = $emptyCompany->country;
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = $emptyCompany->createdDate;
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = $emptyCompany->createdUserId;
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = $emptyCompany->updatedDate;
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = $emptyCompany->updatedUserId;
        $this->assertSame($expected, $actual);

        // Row with present created/updated user ids as strings should be cast to int
        $row = [
            'com_id'             => (string)$faker->numberBetween(1, 1000),
            'com_name'           => $faker->company(),
            'com_email'          => $faker->safeEmail(),
            'com_created_usr_id' => (string)$faker->numberBetween(1, 1000),
            'com_updated_usr_id' => (string)$faker->numberBetween(1, 1000),
        ];

        $company = $mapper->domain($row);

        $expected = (int)$row['com_id'];
        $actual   = $company->id;
        $this->assertSame($expected, $actual);

        $expected = $row['com_name'];
        $actual   = $company->name;
        $this->assertSame($expected, $actual);

        $expected = $row['com_email'];
        $actual   = $company->email;
        $this->assertSame($expected, $actual);

        $expected = (int)$row['com_created_usr_id'];
        $actual   = $company->createdUserId;
        $this->assertSame($expected, $actual);

        $expected = (int)$row['com_updated_usr_id'];
        $actual   = $company->updatedUserId;
        $this->assertSame($expected, $actual);
    }
}
