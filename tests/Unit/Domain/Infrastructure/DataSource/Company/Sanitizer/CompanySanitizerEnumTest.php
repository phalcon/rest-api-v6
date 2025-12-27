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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\DataSource\Company\Sanitizer;

use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Sanitizer\CompanySanitizerEnum;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Filter\Filter;

final class CompanySanitizerEnumTest extends AbstractUnitTestCase
{
    public function testEnum(): void
    {
        $expected = 17;
        $actual   = CompanySanitizerEnum::cases();
        $this->assertCount($expected, $actual);

        $expected = Filter::FILTER_ABSINT;
        $actual   = CompanySanitizerEnum::id->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = 0;
        $actual   = CompanySanitizerEnum::id->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_STRIPTAGS;
        $actual   = CompanySanitizerEnum::name->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = CompanySanitizerEnum::name->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_STRIPTAGS;
        $actual   = CompanySanitizerEnum::phone->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = CompanySanitizerEnum::phone->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_EMAIL;
        $actual   = CompanySanitizerEnum::email->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = CompanySanitizerEnum::email->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_STRIPTAGS;
        $actual   = CompanySanitizerEnum::website->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = CompanySanitizerEnum::website->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_STRIPTAGS;
        $actual   = CompanySanitizerEnum::addressLine1->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = CompanySanitizerEnum::addressLine1->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_STRIPTAGS;
        $actual   = CompanySanitizerEnum::addressLine2->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = CompanySanitizerEnum::addressLine2->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_STRIPTAGS;
        $actual   = CompanySanitizerEnum::city->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = CompanySanitizerEnum::city->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_STRIPTAGS;
        $actual   = CompanySanitizerEnum::stateProvince->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = CompanySanitizerEnum::stateProvince->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_STRIPTAGS;
        $actual   = CompanySanitizerEnum::zipCode->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = CompanySanitizerEnum::zipCode->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_STRIPTAGS;
        $actual   = CompanySanitizerEnum::country->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = CompanySanitizerEnum::country->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_STRIPTAGS;
        $actual   = CompanySanitizerEnum::createdDate->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = CompanySanitizerEnum::createdDate->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_ABSINT;
        $actual   = CompanySanitizerEnum::createdUserId->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = 0;
        $actual   = CompanySanitizerEnum::createdUserId->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_STRIPTAGS;
        $actual   = CompanySanitizerEnum::updatedDate->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = CompanySanitizerEnum::updatedDate->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_ABSINT;
        $actual   = CompanySanitizerEnum::updatedUserId->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = 0;
        $actual   = CompanySanitizerEnum::updatedUserId->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_ABSINT;
        $actual   = CompanySanitizerEnum::page->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = 1;
        $actual   = CompanySanitizerEnum::page->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_ABSINT;
        $actual   = CompanySanitizerEnum::perPage->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = 10;
        $actual   = CompanySanitizerEnum::perPage->default();
        $this->assertSame($expected, $actual);
    }
}
