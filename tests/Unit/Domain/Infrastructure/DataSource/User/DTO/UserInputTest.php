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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\DataSource\User\DTO;

use Faker\Factory as FakerFactory;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\DTO\UserInput;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Sanitizer\UserSanitizer;
use Phalcon\Api\Tests\AbstractUnitTestCase;

use function json_encode;

final class UserInputTest extends AbstractUnitTestCase
{
    public function testToArray(): void
    {
        /** @var UserSanitizer $sanitizer */
        $sanitizer = $this->container->get(UserSanitizer::class);
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
            'nameSuffix'    => $faker->randomElement([null, $faker->suffix()]),
            'issuer'        => $faker->company(),
            'tokenPassword' => $faker->password(),
            'tokenId'       => $faker->uuid(),
            'preferences'   => json_encode(['k' => $faker->word()]),
            'createdDate'   => $faker->date(),
            'createdUserId' => (string)$faker->numberBetween(1, 1000), // string to ensure ABSINT cast
            'updatedDate'   => $faker->date(),
            'updatedUserId' => (string)$faker->numberBetween(1, 1000), // string to ensure ABSINT cast
        ];

        $sanitized = $sanitizer->sanitize($input);
        $userInput = UserInput::new($sanitizer, $input);

        $expected = $sanitized['id'];
        $actual   = $userInput->id;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['status'];
        $actual   = $userInput->status;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['email'];
        $actual   = $userInput->email;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['password'];
        $actual   = $userInput->password;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['namePrefix'];
        $actual   = $userInput->namePrefix;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['nameFirst'];
        $actual   = $userInput->nameFirst;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['nameMiddle'];
        $actual   = $userInput->nameMiddle;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['nameLast'];
        $actual   = $userInput->nameLast;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['nameSuffix'];
        $actual   = $userInput->nameSuffix;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['issuer'];
        $actual   = $userInput->issuer;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['tokenPassword'];
        $actual   = $userInput->tokenPassword;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['tokenId'];
        $actual   = $userInput->tokenId;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['preferences'];
        $actual   = $userInput->preferences;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['createdDate'];
        $actual   = $userInput->createdDate;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['createdUserId'];
        $actual   = $userInput->createdUserId;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['updatedDate'];
        $actual   = $userInput->updatedDate;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['updatedUserId'];
        $actual   = $userInput->updatedUserId;
        $this->assertSame($expected, $actual);

        $expected = get_object_vars($userInput);
        $actual   = $userInput->toArray();
        $this->assertSame($expected, $actual);
    }
}
