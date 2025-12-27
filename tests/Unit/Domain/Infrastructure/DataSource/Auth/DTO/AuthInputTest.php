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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\DataSource\Auth\DTO;

use Faker\Factory as FakerFactory;
use Phalcon\Api\Domain\Infrastructure\DataSource\Auth\DTO\AuthInput;
use Phalcon\Api\Domain\Infrastructure\DataSource\Auth\Sanitizer\AuthSanitizer;
use Phalcon\Api\Tests\AbstractUnitTestCase;

final class AuthInputTest extends AbstractUnitTestCase
{
    public function testObject(): void
    {
        /** @var AuthSanitizer $sanitizer */
        $sanitizer = $this->container->get(AuthSanitizer::class);
        $faker     = FakerFactory::create();

        // Build an input with many fields present
        $input = [
            'email'    => "  Foo.Bar+tag@Example.COM  ",
            'password' => $faker->password(),
            'token'    => $faker->password(),
        ];

        $sanitized = $sanitizer->sanitize($input);
        $authInput = AuthInput::new($sanitizer, $input);

        $expected = $sanitized['email'];
        $actual   = $authInput->email;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['password'];
        $actual   = $authInput->password;
        $this->assertSame($expected, $actual);

        $expected = $sanitized['token'];
        $actual   = $authInput->token;
        $this->assertSame($expected, $actual);
    }
}
