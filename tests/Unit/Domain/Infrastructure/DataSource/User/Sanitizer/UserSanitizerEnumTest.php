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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\DataSource\User\Sanitizer;

use Phalcon\Api\Domain\Infrastructure\DataSource\User\Sanitizer\UserSanitizerEnum;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Filter\Filter;

final class UserSanitizerEnumTest extends AbstractUnitTestCase
{
    public function testEnum(): void
    {
        $expected = 17;
        $actual   = UserSanitizerEnum::cases();
        $this->assertCount($expected, $actual);

        $expected = Filter::FILTER_ABSINT;
        $actual   = UserSanitizerEnum::id->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = 0;
        $actual   = UserSanitizerEnum::id->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_EMAIL;
        $actual   = UserSanitizerEnum::email->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = UserSanitizerEnum::email->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_ABSINT;
        $actual   = UserSanitizerEnum::status->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = 0;
        $actual   = UserSanitizerEnum::status->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_EMAIL;
        $actual   = UserSanitizerEnum::email->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = UserSanitizerEnum::email->default();
        $this->assertSame($expected, $actual);

        $expected = '';
        $actual   = UserSanitizerEnum::password->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = UserSanitizerEnum::password->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_STRIPTAGS;
        $actual   = UserSanitizerEnum::namePrefix->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = UserSanitizerEnum::namePrefix->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_STRIPTAGS;
        $actual   = UserSanitizerEnum::nameFirst->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = UserSanitizerEnum::nameFirst->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_STRIPTAGS;
        $actual   = UserSanitizerEnum::nameLast->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = UserSanitizerEnum::nameLast->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_STRIPTAGS;
        $actual   = UserSanitizerEnum::nameMiddle->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = UserSanitizerEnum::nameMiddle->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_STRIPTAGS;
        $actual   = UserSanitizerEnum::nameSuffix->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = UserSanitizerEnum::nameSuffix->default();
        $this->assertSame($expected, $actual);

        $expected = '';
        $actual   = UserSanitizerEnum::issuer->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = UserSanitizerEnum::issuer->default();
        $this->assertSame($expected, $actual);

        $expected = '';
        $actual   = UserSanitizerEnum::tokenPassword->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = UserSanitizerEnum::tokenPassword->default();
        $this->assertSame($expected, $actual);

        $expected = '';
        $actual   = UserSanitizerEnum::tokenId->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = UserSanitizerEnum::tokenId->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_STRIPTAGS;
        $actual   = UserSanitizerEnum::preferences->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = UserSanitizerEnum::preferences->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_ABSINT;
        $actual   = UserSanitizerEnum::createdUserId->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = 0;
        $actual   = UserSanitizerEnum::createdUserId->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_STRIPTAGS;
        $actual   = UserSanitizerEnum::createdDate->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = UserSanitizerEnum::createdDate->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_ABSINT;
        $actual   = UserSanitizerEnum::updatedUserId->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = 0;
        $actual   = UserSanitizerEnum::updatedUserId->default();
        $this->assertSame($expected, $actual);

        $expected = Filter::FILTER_STRIPTAGS;
        $actual   = UserSanitizerEnum::updatedDate->sanitizer();
        $this->assertSame($expected, $actual);

        $expected = null;
        $actual   = UserSanitizerEnum::updatedDate->default();
        $this->assertSame($expected, $actual);
    }
}
