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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\DataSource\Company\DTO;

use Faker\Factory as FakerFactory;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\DTO\CompanyInput;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Sanitizer\CompanySanitizer;
use Phalcon\Api\Tests\AbstractUnitTestCase;

final class CompanyInputTest extends AbstractUnitTestCase
{
    public function testToArray(): void
    {
        /** @var CompanySanitizer $sanitizer */
        $sanitizer = $this->container->get(CompanySanitizer::class);
        $faker     = FakerFactory::create();

        $input = [
            'id'            => $faker->numberBetween(1, 1000),
            'name'          => $faker->company(),
            'email'         => $faker->safeEmail(),
            'phone'         => $faker->phoneNumber(),
            'website'       => $faker->domainName(),
            'addressLine1'  => $faker->streetAddress(),
            'addressLine2'  => $faker->streetAddress(),
            'city'          => $faker->city(),
            'stateProvince' => $faker->city(),
            'zipCode'       => $faker->postcode(),
            'country'       => $faker->countryISOAlpha3(),
            'createdDate'   => $faker->date(),
            'createdUserId' => (string)$faker->numberBetween(1, 1000),
            'updatedDate'   => $faker->date(),
            'updatedUserId' => (string)$faker->numberBetween(1, 1000),
        ];

        $sanitized    = $sanitizer->sanitize($input);
        $companyInput = CompanyInput::new($sanitizer, $input);

        $expected = $sanitized['id'];
        $actual   = $companyInput->id;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['name'];
        $actual   = $companyInput->name;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['email'];
        $actual   = $companyInput->email;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['phone'];
        $actual   = $companyInput->phone;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['website'];
        $actual   = $companyInput->website;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['addressLine1'];
        $actual   = $companyInput->addressLine1;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['addressLine2'];
        $actual   = $companyInput->addressLine2;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['city'];
        $actual   = $companyInput->city;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['stateProvince'];
        $actual   = $companyInput->stateProvince;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['zipCode'];
        $actual   = $companyInput->zipCode;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['country'];
        $actual   = $companyInput->country;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['createdDate'];
        $actual   = $companyInput->createdDate;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['createdUserId'];
        $actual   = $companyInput->createdUserId;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['updatedDate'];
        $actual   = $companyInput->updatedDate;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['updatedUserId'];
        $actual   = $companyInput->updatedUserId;
        $this->assertSame($expected, $actual);

        $expected = get_object_vars($companyInput);
        $actual   = $companyInput->toArray();
        $this->assertSame($expected, $actual);
    }
}
