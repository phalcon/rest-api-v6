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

namespace Phalcon\Api\Tests\Unit\Domain\Application\Company\Command;

use Faker\Factory as FakerFactory;
use Phalcon\Api\Domain\Application\Company\Command\CompanyCommandFactory;
use Phalcon\Api\Domain\Application\Company\Command\CompanyDeleteCommand;
use Phalcon\Api\Domain\Application\Company\Command\CompanyGetCommand;
use Phalcon\Api\Domain\Application\Company\Command\CompanyPostCommand;
use Phalcon\Api\Domain\Application\Company\Command\CompanyPutCommand;
use Phalcon\Api\Domain\Infrastructure\Container;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Sanitizer\CompanySanitizer;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Filter\Filter;

final class CompanyCommandFactoryTest extends AbstractUnitTestCase
{
    public function testDelete(): void
    {
        $sanitizer = $this->container->get(CompanySanitizer::class);
        $factory   = new CompanyCommandFactory($sanitizer);
        $faker     = FakerFactory::create();

        $input = [
            'id'            => $faker->numberBetween(1, 1000),
            'name'          => $faker->company(),
            'email'         => "  Foo.Bar+tag@Example.COM  ",
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

        /** @var CompanyDeleteCommand $command */
        $command = $factory->delete($input);

        $this->assertInstanceOf(CompanyDeleteCommand::class, $command);

        $expected = $input['id'];
        $actual   = $command->id;
        $this->assertSame($expected, $actual);
    }

    public function testGet(): void
    {
        $sanitizer = $this->container->get(CompanySanitizer::class);
        $factory   = new CompanyCommandFactory($sanitizer);
        $faker     = FakerFactory::create();
        $input     = [
            'id'            => $faker->numberBetween(1, 1000),
            'name'          => $faker->company(),
            'email'         => "  Foo.Bar+tag@Example.COM  ",
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

        $command = $factory->get($input);

        $this->assertInstanceOf(CompanyGetCommand::class, $command);
        $expected = $input['id'];
        $actual   = $command->id;
        $this->assertSame($expected, $actual);
    }

    public function testInsert(): void
    {
        $sanitizer = $this->container->get(CompanySanitizer::class);
        /** @var Filter $filter */
        $filter  = $this->container->get(Container::FILTER);
        $factory = new CompanyCommandFactory($sanitizer);
        $faker   = FakerFactory::create();

        $input = [
            'id'            => (string)$faker->numberBetween(1, 1000),
            'name'          => $faker->company(),
            'email'         => "  Foo.Bar+tag@Example.COM  ",
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

        /** @var CompanyPostCommand $command */
        $command = $factory->insert($input);

        $this->assertInstanceOf(CompanyPostCommand::class, $command);

        $expected = null;
        $actual   = $command->id;
        $this->assertSame($expected, $actual);

        $expected = $input['name'];
        $actual   = $command->name;
        $this->assertSame($expected, $actual);

        $expected = $filter->email($input['email']);
        $actual   = $command->email;
        $this->assertSame($expected, $actual);

        $expected = $input['phone'];
        $actual   = $command->phone;
        $this->assertSame($expected, $actual);

        $expected = $input['website'];
        $actual   = $command->website;
        $this->assertSame($expected, $actual);

        $expected = $input['addressLine1'];
        $actual   = $command->addressLine1;
        $this->assertSame($expected, $actual);

        $expected = $input['addressLine2'];
        $actual   = $command->addressLine2;
        $this->assertSame($expected, $actual);

        $expected = $input['city'];
        $actual   = $command->city;
        $this->assertSame($expected, $actual);

        $expected = $input['stateProvince'];
        $actual   = $command->stateProvince;
        $this->assertSame($expected, $actual);

        $expected = $input['zipCode'];
        $actual   = $command->zipCode;
        $this->assertSame($expected, $actual);

        $expected = $input['country'];
        $actual   = $command->country;
        $this->assertSame($expected, $actual);

        $expected = $input['createdDate'];
        $actual   = $command->createdDate;
        $this->assertSame($expected, $actual);

        $expected = $filter->absint($input['createdUserId']);
        $actual   = $command->createdUserId;
        $this->assertSame($expected, $actual);

        $expected = $input['updatedDate'];
        $actual   = $command->updatedDate;
        $this->assertSame($expected, $actual);

        $expected = $filter->absint($input['updatedUserId']);
        $actual   = $command->updatedUserId;
        $this->assertSame($expected, $actual);
    }

    public function testUpdate(): void
    {
        $sanitizer = $this->container->get(CompanySanitizer::class);
        /** @var Filter $filter */
        $filter  = $this->container->get(Container::FILTER);
        $factory = new CompanyCommandFactory($sanitizer);
        $faker   = FakerFactory::create();

        $input = [
            'id'            => (string)$faker->numberBetween(1, 1000),
            'name'          => $faker->company(),
            'email'         => "  Foo.Bar+tag@Example.COM  ",
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

        /** @var CompanyPutCommand $command */
        $command = $factory->update($input);

        $this->assertInstanceOf(CompanyPutCommand::class, $command);

        $expected = $filter->absint($input['id']);
        $actual   = $command->id;
        $this->assertSame($expected, $actual);

        $expected = $input['name'];
        $actual   = $command->name;
        $this->assertSame($expected, $actual);

        $expected = $filter->email($input['email']);
        $actual   = $command->email;
        $this->assertSame($expected, $actual);

        $expected = $input['phone'];
        $actual   = $command->phone;
        $this->assertSame($expected, $actual);

        $expected = $input['website'];
        $actual   = $command->website;
        $this->assertSame($expected, $actual);

        $expected = $input['addressLine1'];
        $actual   = $command->addressLine1;
        $this->assertSame($expected, $actual);

        $expected = $input['addressLine2'];
        $actual   = $command->addressLine2;
        $this->assertSame($expected, $actual);

        $expected = $input['city'];
        $actual   = $command->city;
        $this->assertSame($expected, $actual);

        $expected = $input['stateProvince'];
        $actual   = $command->stateProvince;
        $this->assertSame($expected, $actual);

        $expected = $input['zipCode'];
        $actual   = $command->zipCode;
        $this->assertSame($expected, $actual);

        $expected = $input['country'];
        $actual   = $command->country;
        $this->assertSame($expected, $actual);

        $expected = $input['createdDate'];
        $actual   = $command->createdDate;
        $this->assertSame($expected, $actual);

        $expected = $filter->absint($input['createdUserId']);
        $actual   = $command->createdUserId;
        $this->assertSame($expected, $actual);

        $expected = $input['updatedDate'];
        $actual   = $command->updatedDate;
        $this->assertSame($expected, $actual);

        $expected = $filter->absint($input['updatedUserId']);
        $actual   = $command->updatedUserId;
        $this->assertSame($expected, $actual);
    }
}
