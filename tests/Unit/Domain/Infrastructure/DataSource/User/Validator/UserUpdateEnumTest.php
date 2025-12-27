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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\DataSource\User\Validator;

use Phalcon\Api\Domain\Infrastructure\DataSource\User\Validator\UserUpdateEnum;
use Phalcon\Api\Domain\Infrastructure\DataSource\Validation\AbsInt;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Filter\Validation\Validator\Email;
use Phalcon\Filter\Validation\Validator\PresenceOf;

final class UserUpdateEnumTest extends AbstractUnitTestCase
{
    public function testEnum(): void
    {
        $expected = 6;
        $actual   = UserUpdateEnum::cases();
        $this->assertCount($expected, $actual);

        $expected = [
            PresenceOf::class,
            AbsInt::class,
        ];
        $actual   = UserUpdateEnum::id->validators();
        $this->assertSame($expected, $actual);

        $expected = false;
        $actual   = UserUpdateEnum::id->allowEmpty();
        $this->assertSame($expected, $actual);

        $expected = [
            Email::class,
        ];
        $actual   = UserUpdateEnum::email->validators();
        $this->assertSame($expected, $actual);

        $expected = true;
        $actual   = UserUpdateEnum::email->allowEmpty();
        $this->assertSame($expected, $actual);

        $expected = [];
        $actual   = UserUpdateEnum::password->validators();
        $this->assertSame($expected, $actual);

        $expected = true;
        $actual   = UserUpdateEnum::password->allowEmpty();
        $this->assertSame($expected, $actual);

        $expected = [];
        $actual   = UserUpdateEnum::issuer->validators();
        $this->assertSame($expected, $actual);

        $expected = true;
        $actual   = UserUpdateEnum::issuer->allowEmpty();
        $this->assertSame($expected, $actual);

        $expected = [];
        $actual   = UserUpdateEnum::tokenPassword->validators();
        $this->assertSame($expected, $actual);

        $expected = true;
        $actual   = UserUpdateEnum::tokenPassword->allowEmpty();
        $this->assertSame($expected, $actual);

        $expected = [];
        $actual   = UserUpdateEnum::tokenId->validators();
        $this->assertSame($expected, $actual);

        $expected = true;
        $actual   = UserUpdateEnum::tokenId->allowEmpty();
        $this->assertSame($expected, $actual);
    }
}
