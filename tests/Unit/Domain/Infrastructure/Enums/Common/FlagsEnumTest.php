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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\Enums\Common;

use Phalcon\Api\Domain\Infrastructure\Enums\Common\FlagsEnum;
use Phalcon\Api\Tests\AbstractUnitTestCase;

final class FlagsEnumTest extends AbstractUnitTestCase
{
    public function testCheckCount(): void
    {
        $expected = 3;
        $actual   = FlagsEnum::cases();
        $this->assertCount($expected, $actual);
    }

    public function testCheckText(): void
    {
        $expected = 'Active';
        $actual   = FlagsEnum::Active->text();
        $this->assertSame($expected, $actual);

        $expected = 'Disabled';
        $actual   = FlagsEnum::Disabled->text();
        $this->assertSame($expected, $actual);

        $expected = 'Inactive';
        $actual   = FlagsEnum::Inactive->text();
        $this->assertSame($expected, $actual);
    }

    public function testCheckValues(): void
    {
        $expected = 1;
        $actual   = FlagsEnum::Active->value;
        $this->assertSame($expected, $actual);

        $expected = 2;
        $actual   = FlagsEnum::Disabled->value;
        $this->assertSame($expected, $actual);

        $expected = 3;
        $actual   = FlagsEnum::Inactive->value;
        $this->assertSame($expected, $actual);
    }
}
