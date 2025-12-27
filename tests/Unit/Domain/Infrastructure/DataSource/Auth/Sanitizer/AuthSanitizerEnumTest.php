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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\DataSource\Auth\Sanitizer;

use Phalcon\Api\Domain\Infrastructure\DataSource\Auth\Sanitizer\AuthSanitizerEnum;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Filter\Filter;

final class AuthSanitizerEnumTest extends AbstractUnitTestCase
{
    public function testEnum(): void
    {
        $expected = 3;
        $actual   = AuthSanitizerEnum::cases();
        $this->assertCount($expected, $actual);

        $expected = Filter::FILTER_EMAIL;
        $actual   = AuthSanitizerEnum::email->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = AuthSanitizerEnum::email->default();
        $this->assertSame($expected, $actual);

        $expected = '';
        $actual   = AuthSanitizerEnum::password->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = AuthSanitizerEnum::password->default();
        $this->assertSame($expected, $actual);

        $expected = '';
        $actual   = AuthSanitizerEnum::token->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = AuthSanitizerEnum::token->default();
        $this->assertSame($expected, $actual);
    }
}
