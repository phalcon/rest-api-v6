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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\DataSource\Company\Validator;

use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Validator\CompanyUpdateEnum;
use Phalcon\Api\Domain\Infrastructure\DataSource\Validation\AbsInt;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Filter\Validation\Validator\Email;
use Phalcon\Filter\Validation\Validator\PresenceOf;

final class CompanyUpdateEnumTest extends AbstractUnitTestCase
{
    public function testEnum(): void
    {
        $expected = 2;
        $actual   = CompanyUpdateEnum::cases();
        $this->assertCount($expected, $actual);

        $expected = [
            PresenceOf::class,
            AbsInt::class,
        ];
        $actual   = CompanyUpdateEnum::id->validators();
        $this->assertSame($expected, $actual);

        $expected = false;
        $actual   = CompanyUpdateEnum::id->allowEmpty();
        $this->assertSame($expected, $actual);

        $expected = [
            Email::class,
        ];
        $actual   = CompanyUpdateEnum::email->validators();
        $this->assertSame($expected, $actual);

        $expected = true;
        $actual   = CompanyUpdateEnum::email->allowEmpty();
        $this->assertSame($expected, $actual);
    }
}
