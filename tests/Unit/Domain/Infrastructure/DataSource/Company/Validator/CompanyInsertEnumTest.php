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

use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Validator\CompanyInsertEnum;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Filter\Validation\Validator\Email;
use Phalcon\Filter\Validation\Validator\PresenceOf;

final class CompanyInsertEnumTest extends AbstractUnitTestCase
{
    public function testEnum(): void
    {
        $expected = 2;
        $actual   = CompanyInsertEnum::cases();
        $this->assertCount($expected, $actual);

        $expected = [
            PresenceOf::class,
            Email::class,
        ];
        $actual   = CompanyInsertEnum::email->validators();
        $this->assertSame($expected, $actual);

        $expected = false;
        $actual   = CompanyInsertEnum::email->allowEmpty();
        $this->assertSame($expected, $actual);

        $expected = [
            PresenceOf::class,
        ];
        $actual   = CompanyInsertEnum::name->validators();
        $this->assertSame($expected, $actual);

        $expected = false;
        $actual   = CompanyInsertEnum::name->allowEmpty();
        $this->assertSame($expected, $actual);
    }
}
