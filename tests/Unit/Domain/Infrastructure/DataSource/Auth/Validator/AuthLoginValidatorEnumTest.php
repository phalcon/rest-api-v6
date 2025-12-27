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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\DataSource\Auth\Validator;

use Phalcon\Api\Domain\Infrastructure\DataSource\Auth\Validator\AuthLoginValidatorEnum;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Filter\Validation\Validator\Email;
use Phalcon\Filter\Validation\Validator\PresenceOf;

final class AuthLoginValidatorEnumTest extends AbstractUnitTestCase
{
    public function testEnum(): void
    {
        $expected = 2;
        $actual   = AuthLoginValidatorEnum::cases();
        $this->assertCount($expected, $actual);

        $expected = [
            PresenceOf::class,
            Email::class,
        ];
        $actual   = AuthLoginValidatorEnum::email->validators();
        $this->assertSame($expected, $actual);

        $expected = false;
        $actual   = AuthLoginValidatorEnum::email->allowEmpty();
        $this->assertSame($expected, $actual);

        $expected = [
            PresenceOf::class,
        ];
        $actual   = AuthLoginValidatorEnum::password->validators();
        $this->assertSame($expected, $actual);

        $expected = false;
        $actual   = AuthLoginValidatorEnum::password->allowEmpty();
        $this->assertSame($expected, $actual);
    }
}
