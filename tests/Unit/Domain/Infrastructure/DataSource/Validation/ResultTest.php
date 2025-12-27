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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\DataSource\Validation;

use Phalcon\Api\Domain\Infrastructure\DataSource\Validation\Result;
use Phalcon\Api\Tests\AbstractUnitTestCase;

final class ResultTest extends AbstractUnitTestCase
{
    public function testErrorIsInvalidAndReturnsErrors(): void
    {
        $errors = ['field' => ['must not be empty']];
        $result = Result::error($errors);

        $expected = $errors;
        $actual   = $result->getErrors();
        $this->assertSame($expected, $actual);
    }

    public function testGetMetaReturnsDefaultWhenMissing(): void
    {
        $result = new Result([], ['present' => 123]);

        $expected = 123;
        $actual   = $result->getMeta('present');
        $this->assertSame($expected, $actual);

        $actual = $result->getMeta('uknown');
        $this->assertNull($actual);

        $expected = 'default';
        $actual   = $result->getMeta('unknown', 'default');
        $this->assertSame($expected, $actual);
    }

    public function testSetMetaAddsOrUpdatesMeta(): void
    {
        $result = new Result();

        $actual = $result->getMeta('key');
        $this->assertNull($actual);

        $result->setMeta('key', 'value');

        $expected = 'value';
        $actual   = $result->getMeta('key');
        $this->assertSame($expected, $actual);

        $result->setMeta('key', 42);

        $expected = 42;
        $actual   = $result->getMeta('key');
        $this->assertSame($expected, $actual);
    }

    public function testSuccessIsValidAndHasNoErrors(): void
    {
        $result = Result::success();

        $actual = $result->isValid();
        $this->assertTrue($actual);

        $expected = [];
        $actual   = $result->getErrors();
        $this->assertSame($expected, $actual);
    }
}
