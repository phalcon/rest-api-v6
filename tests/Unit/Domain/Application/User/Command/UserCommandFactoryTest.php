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

namespace Phalcon\Api\Tests\Unit\Domain\Application\User\Command;

use Faker\Factory as FakerFactory;
use Phalcon\Api\Domain\Application\User\Command\UserCommandFactory;
use Phalcon\Api\Domain\Application\User\Command\UserDeleteCommand;
use Phalcon\Api\Domain\Application\User\Command\UserGetCommand;
use Phalcon\Api\Domain\Application\User\Command\UserPostCommand;
use Phalcon\Api\Domain\Application\User\Command\UserPutCommand;
use Phalcon\Api\Domain\Infrastructure\Container;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Sanitizer\UserSanitizer;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Filter\Filter;

use function json_encode;

final class UserCommandFactoryTest extends AbstractUnitTestCase
{
    public function testDelete(): void
    {
        $sanitizer = $this->container->get(UserSanitizer::class);
        $factory   = new UserCommandFactory($sanitizer);
        $faker     = FakerFactory::create();

        $input = [
            'id'            => $faker->numberBetween(1, 1000),
            'status'        => $faker->numberBetween(0, 9),
            'email'         => "  Foo.Bar+tag@Example.COM  ",
            'password'      => $faker->password(),
            'namePrefix'    => $faker->title(),
            'nameFirst'     => $faker->firstName(),
            'nameMiddle'    => $faker->word(),
            'nameLast'      => $faker->lastName(),
            'nameSuffix'    => $faker->suffix(),
            'issuer'        => $faker->company(),
            'tokenPassword' => $faker->password(),
            'tokenId'       => $faker->uuid(),
            'preferences'   => json_encode(['k' => $faker->word()]),
            'createdDate'   => $faker->date(),
            'createdUserId' => (string)$faker->numberBetween(1, 1000), // string to ensure ABSINT cast
            'updatedDate'   => $faker->date(),
            'updatedUserId' => (string)$faker->numberBetween(1, 1000), // string to ensure ABSINT cast
        ];

        /** @var UserDeleteCommand $command */
        $command = $factory->delete($input);

        $this->assertInstanceOf(UserDeleteCommand::class, $command);

        $expected = $input['id'];
        $actual   = $command->id;
        $this->assertSame($expected, $actual);
    }

    public function testGet(): void
    {
        $sanitizer = $this->container->get(UserSanitizer::class);
        $factory   = new UserCommandFactory($sanitizer);
        $faker     = FakerFactory::create();
        $input     = [
            'id'            => $faker->numberBetween(1, 1000),
            'status'        => $faker->numberBetween(0, 9),
            'email'         => "  Foo.Bar+tag@Example.COM  ",
            'password'      => $faker->password(),
            'namePrefix'    => $faker->title(),
            'nameFirst'     => $faker->firstName(),
            'nameMiddle'    => $faker->word(),
            'nameLast'      => $faker->lastName(),
            'nameSuffix'    => $faker->suffix(),
            'issuer'        => $faker->company(),
            'tokenPassword' => $faker->password(),
            'tokenId'       => $faker->uuid(),
            'preferences'   => json_encode(['k' => $faker->word()]),
            'createdDate'   => $faker->date(),
            'createdUserId' => (string)$faker->numberBetween(1, 1000), // string to ensure ABSINT cast
            'updatedDate'   => $faker->date(),
            'updatedUserId' => (string)$faker->numberBetween(1, 1000), // string to ensure ABSINT cast
        ];

        $command = $factory->get($input);

        $this->assertInstanceOf(UserGetCommand::class, $command);
        $expected = $input['id'];
        $actual   = $command->id;
        $this->assertSame($expected, $actual);
    }

    public function testInsert(): void
    {
        $sanitizer = $this->container->get(UserSanitizer::class);
        /** @var Filter $filter */
        $filter  = $this->container->get(Container::FILTER);
        $factory = new UserCommandFactory($sanitizer);
        $faker   = FakerFactory::create();

        $input = [
            'id'            => $faker->numberBetween(1, 1000),
            'status'        => $faker->numberBetween(0, 9),
            'email'         => "  Foo.Bar+tag@Example.COM  ",
            'password'      => $faker->password(),
            'namePrefix'    => $faker->title(),
            'nameFirst'     => $faker->firstName(),
            'nameMiddle'    => $faker->word(),
            'nameLast'      => $faker->lastName(),
            'nameSuffix'    => $faker->suffix(),
            'issuer'        => $faker->company(),
            'tokenPassword' => $faker->password(),
            'tokenId'       => $faker->uuid(),
            'preferences'   => json_encode(['k' => $faker->word()]),
            'createdDate'   => $faker->date(),
            'createdUserId' => (string)$faker->numberBetween(1, 1000), // string to ensure ABSINT cast
            'updatedDate'   => $faker->date(),
            'updatedUserId' => (string)$faker->numberBetween(1, 1000), // string to ensure ABSINT cast
        ];

        /** @var UserPostCommand $command */
        $command = $factory->insert($input);

        $this->assertInstanceOf(UserPostCommand::class, $command);

        $expected = $input['status'];
        $actual   = $command->status;
        $this->assertSame($expected, $actual);
        $expected = $filter->email($input['email']);
        $actual   = $command->email;
        $this->assertSame($expected, $actual);
        $expected = $input['password'];
        $actual   = $command->password;
        $this->assertSame($expected, $actual);
        $expected = $filter->striptags($input['namePrefix']);
        $actual   = $command->namePrefix;
        $this->assertSame($expected, $actual);
        $expected = $filter->striptags($input['nameFirst']);
        $actual   = $command->nameFirst;
        $this->assertSame($expected, $actual);
        $expected = $filter->striptags($input['nameMiddle']);
        $actual   = $command->nameMiddle;
        $this->assertSame($expected, $actual);
        $expected = $filter->striptags($input['nameLast']);
        $actual   = $command->nameLast;
        $this->assertSame($expected, $actual);
        $expected = $filter->striptags($input['nameSuffix']);
        $actual   = $command->nameSuffix;
        $this->assertSame($expected, $actual);
        $expected = $input['issuer'];
        $actual   = $command->issuer;
        $this->assertSame($expected, $actual);
        $expected = $input['tokenPassword'];
        $actual   = $command->tokenPassword;
        $this->assertSame($expected, $actual);
        $expected = $input['tokenId'];
        $actual   = $command->tokenId;
        $this->assertSame($expected, $actual);
        $expected = $filter->striptags($input['preferences']);
        $actual   = $command->preferences;
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
        $sanitizer = $this->container->get(UserSanitizer::class);
        /** @var Filter $filter */
        $filter  = $this->container->get(Container::FILTER);
        $factory = new UserCommandFactory($sanitizer);
        $faker   = FakerFactory::create();

        $input = [
            'id'            => $faker->numberBetween(1, 1000),
            'status'        => $faker->numberBetween(0, 9),
            'email'         => "  Foo.Bar+tag@Example.COM  ",
            'password'      => $faker->password(),
            'namePrefix'    => $faker->title(),
            'nameFirst'     => $faker->firstName(),
            'nameMiddle'    => $faker->word(),
            'nameLast'      => $faker->lastName(),
            'nameSuffix'    => $faker->suffix(),
            'issuer'        => $faker->company(),
            'tokenPassword' => $faker->password(),
            'tokenId'       => $faker->uuid(),
            'preferences'   => json_encode(['k' => $faker->word()]),
            'createdDate'   => $faker->date(),
            'createdUserId' => (string)$faker->numberBetween(1, 1000), // string to ensure ABSINT cast
            'updatedDate'   => $faker->date(),
            'updatedUserId' => (string)$faker->numberBetween(1, 1000), // string to ensure ABSINT cast
        ];

        /** @var UserPutCommand $command */
        $command = $factory->update($input);

        $this->assertInstanceOf(UserPutCommand::class, $command);
        $expected = $input['id'];
        $actual   = $command->id;
        $this->assertSame($expected, $actual);
        $expected = $input['status'];
        $actual   = $command->status;
        $this->assertSame($expected, $actual);
        $expected = $filter->email($input['email']);
        $actual   = $command->email;
        $this->assertSame($expected, $actual);
        $expected = $input['password'];
        $actual   = $command->password;
        $this->assertSame($expected, $actual);
        $expected = $filter->striptags($input['namePrefix']);
        $actual   = $command->namePrefix;
        $this->assertSame($expected, $actual);
        $expected = $filter->striptags($input['nameFirst']);
        $actual   = $command->nameFirst;
        $this->assertSame($expected, $actual);
        $expected = $filter->striptags($input['nameMiddle']);
        $actual   = $command->nameMiddle;
        $this->assertSame($expected, $actual);
        $expected = $filter->striptags($input['nameLast']);
        $actual   = $command->nameLast;
        $this->assertSame($expected, $actual);
        $expected = $filter->striptags($input['nameSuffix']);
        $actual   = $command->nameSuffix;
        $this->assertSame($expected, $actual);
        $expected = $input['issuer'];
        $actual   = $command->issuer;
        $this->assertSame($expected, $actual);
        $expected = $input['tokenPassword'];
        $actual   = $command->tokenPassword;
        $this->assertSame($expected, $actual);
        $expected = $input['tokenId'];
        $actual   = $command->tokenId;
        $this->assertSame($expected, $actual);
        $expected = $filter->striptags($input['preferences']);
        $actual   = $command->preferences;
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
