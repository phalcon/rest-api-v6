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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\Constants;

use DateTimeImmutable;
use Phalcon\Api\Domain\Infrastructure\Constants\Dates;
use Phalcon\Api\Tests\AbstractUnitTestCase;

final class DatesTest extends AbstractUnitTestCase
{
    public function testConstants(): void
    {
        $expected = 'Y-m-d';
        $actual   = Dates::DATE_FORMAT;
        $this->assertSame($expected, $actual);

        $expected = 'NOW()';
        $actual   = Dates::DATE_NOW;
        $this->assertSame($expected, $actual);

        $expected = 'Y-m-d H:i:s';
        $actual   = Dates::DATE_TIME_FORMAT;
        $this->assertSame($expected, $actual);

        $expected = 'Y-m-d\\TH:i:sp';
        $actual   = Dates::DATE_TIME_UTC_FORMAT;
        $this->assertSame($expected, $actual);

        $expected = 'UTC';
        $actual   = Dates::DATE_TIME_ZONE;
        $this->assertSame($expected, $actual);

        $now      = new DateTimeImmutable();
        $expected = $now->format('Y-m-d');

        $actual = Dates::toUTC(format: 'Y-m-d');
        $this->assertSame($expected, $actual);
    }
}
