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

use Phalcon\Api\Domain\Infrastructure\DataSource\User\Validator\UserInsertEnum;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Filter\Validation\Validator\Email;
use Phalcon\Filter\Validation\Validator\PresenceOf;

final class UserInsertEnumTest extends AbstractUnitTestCase
{
    public function testEnum(): void
    {
        $expected = 5;
        $actual   = UserInsertEnum::cases();
        $this->assertCount($expected, $actual);

        $expected = [
            PresenceOf::class,
            Email::class,
        ];
        $actual   = UserInsertEnum::email->validators();
        $this->assertSame($expected, $actual);

        $expected = false;
        $actual   = UserInsertEnum::email->allowEmpty();
        $this->assertSame($expected, $actual);

        $expected = [
            PresenceOf::class,
        ];
        $actual   = UserInsertEnum::password->validators();
        $this->assertSame($expected, $actual);

        $expected = false;
        $actual   = UserInsertEnum::password->allowEmpty();
        $this->assertSame($expected, $actual);

        $expected = [
            PresenceOf::class,
        ];
        $actual   = UserInsertEnum::issuer->validators();
        $this->assertSame($expected, $actual);

        $expected = false;
        $actual   = UserInsertEnum::issuer->allowEmpty();
        $this->assertSame($expected, $actual);

        $expected = [
            PresenceOf::class,
        ];
        $actual   = UserInsertEnum::tokenPassword->validators();
        $this->assertSame($expected, $actual);

        $expected = false;
        $actual   = UserInsertEnum::tokenPassword->allowEmpty();
        $this->assertSame($expected, $actual);

        $expected = [
            PresenceOf::class,
        ];
        $actual   = UserInsertEnum::tokenId->validators();
        $this->assertSame($expected, $actual);

        $expected = false;
        $actual   = UserInsertEnum::tokenId->allowEmpty();
        $this->assertSame($expected, $actual);
    }
}
