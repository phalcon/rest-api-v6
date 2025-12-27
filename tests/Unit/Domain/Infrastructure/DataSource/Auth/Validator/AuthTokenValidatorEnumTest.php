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

use Phalcon\Api\Domain\Infrastructure\DataSource\Auth\Validator\AuthTokenValidatorEnum;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Filter\Validation\Validator\PresenceOf;

final class AuthTokenValidatorEnumTest extends AbstractUnitTestCase
{
    public function testEnum(): void
    {
        $expected = 1;
        $actual   = AuthTokenValidatorEnum::cases();
        $this->assertCount($expected, $actual);

        $expected = [
            PresenceOf::class,
        ];
        $actual   = AuthTokenValidatorEnum::token->validators();
        $this->assertSame($expected, $actual);

        $expected = false;
        $actual   = AuthTokenValidatorEnum::token->allowEmpty();
        $this->assertSame($expected, $actual);
    }
}
