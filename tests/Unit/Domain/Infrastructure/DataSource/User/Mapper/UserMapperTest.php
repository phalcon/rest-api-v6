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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\DataSource\User\Mapper;

use Faker\Factory as FakerFactory;
use Phalcon\Api\Domain\Application\User\Command\UserCommandFactory;
use Phalcon\Api\Domain\Infrastructure\Constants\Dates;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Mapper\UserMapper;
use Phalcon\Api\Tests\AbstractUnitTestCase;

use function json_encode;

final class UserMapperTest extends AbstractUnitTestCase
{
    public function testDb(): void
    {
        /** @var UserCommandFactory $factory */
        $factory = $this->container->get(UserCommandFactory::class);
        $faker   = FakerFactory::create();

        $preferences     = [
            'theme' => $faker->randomElement(['dark', 'light']),
            'lang'  => $faker->languageCode(),
        ];
        $preferencesJson = json_encode($preferences);

        $createdDate = $faker->date(Dates::DATE_TIME_FORMAT);
        $updatedDate = $faker->date(Dates::DATE_TIME_FORMAT);

        $input = [
            'id'            => $faker->numberBetween(1, 1000),
            'status'        => $faker->numberBetween(0, 9),
            'email'         => $faker->safeEmail(),
            'password'      => $faker->password(),
            'namePrefix'    => $faker->optional()->title(),
            'nameFirst'     => $faker->firstName(),
            'nameMiddle'    => $faker->optional()->word(),
            'nameLast'      => $faker->lastName(),
            'nameSuffix'    => $faker->optional()->suffix(),
            'issuer'        => $faker->company(),
            'tokenPassword' => $faker->optional()->password(),
            'tokenId'       => $faker->uuid(),
            'preferences'   => $preferencesJson,
            'createdDate'   => $createdDate,
            'createdUserId' => $faker->numberBetween(1, 1000),
            'updatedDate'   => $updatedDate,
            'updatedUserId' => $faker->numberBetween(1, 1000),
        ];

        $user = $factory->update($input);

        /** @var UserMapper $mapper */
        $mapper = $this->container->get(UserMapper::class);
        $row    = $mapper->db($user);

        $expected = [
            'usr_id'             => $user->id,
            'usr_status_flag'    => $user->status,
            'usr_email'          => $user->email,
            'usr_password'       => $user->password,
            'usr_name_prefix'    => $user->namePrefix,
            'usr_name_first'     => $user->nameFirst,
            'usr_name_middle'    => $user->nameMiddle,
            'usr_name_last'      => $user->nameLast,
            'usr_name_suffix'    => $user->nameSuffix,
            'usr_issuer'         => $user->issuer,
            'usr_token_password' => $user->tokenPassword,
            'usr_token_id'       => $user->tokenId,
            'usr_preferences'    => $user->preferences,
            'usr_created_date'   => $user->createdDate,
            'usr_created_usr_id' => $user->createdUserId,
            'usr_updated_date'   => $user->updatedDate,
            'usr_updated_usr_id' => $user->updatedUserId,
        ];

        $actual = $row;
        $this->assertSame($expected, $actual);
    }

    public function testDomain(): void
    {
        $faker  = FakerFactory::create();
        $mapper = $this->container->get(UserMapper::class);

        // Empty row: defaults should be applied
        $emptyUser = $mapper->domain([]);

        $expected = 0;
        $actual   = $emptyUser->id;
        $this->assertSame($expected, $actual);

        $expected = 0;
        $actual   = $emptyUser->status;
        $this->assertSame($expected, $actual);

        $expected = '';
        $actual   = $emptyUser->email;
        $this->assertSame($expected, $actual);

        $expected = '';
        $actual   = $emptyUser->password;
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = $emptyUser->preferences;
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = $emptyUser->createdDate;
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = $emptyUser->createdUserId;
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = $emptyUser->updatedDate;
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = $emptyUser->updatedUserId;
        $this->assertSame($expected, $actual);

        // Row with present created/updated user ids as strings should be cast to int
        $row = [
            'usr_id'             => (string)$faker->numberBetween(1, 1000),
            'usr_status_flag'    => (string)$faker->numberBetween(0, 9),
            'usr_email'          => $faker->safeEmail(),
            'usr_created_usr_id' => (string)$faker->numberBetween(1, 1000),
            'usr_updated_usr_id' => (string)$faker->numberBetween(1, 1000),
        ];

        $user = $mapper->domain($row);

        $expected = (int)$row['usr_id'];
        $actual   = $user->id;
        $this->assertSame($expected, $actual);

        $expected = (int)$row['usr_status_flag'];
        $actual   = $user->status;
        $this->assertSame($expected, $actual);

        $expected = $row['usr_email'];
        $actual   = $user->email;
        $this->assertSame($expected, $actual);

        $expected = (int)$row['usr_created_usr_id'];
        $actual   = $user->createdUserId;
        $this->assertSame($expected, $actual);

        $expected = (int)$row['usr_updated_usr_id'];
        $actual   = $user->updatedUserId;
        $this->assertSame($expected, $actual);
    }
}
