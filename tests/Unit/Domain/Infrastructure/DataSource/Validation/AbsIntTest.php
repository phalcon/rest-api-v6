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

use Phalcon\Api\Domain\Infrastructure\DataSource\Validation\AbsInt;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Filter\Validation;
use PHPUnit\Framework\Attributes\DataProvider;

final class AbsIntTest extends AbstractUnitTestCase
{
    /**
     * @return array
     */
    public static function getExamples(): array
    {
        return [
            [
                'amount'   => 123,
                'expected' => 0,
            ],
            [
                'amount'   => 123.12,
                'expected' => 0,
            ],
            [
                'amount'   => '-12,000',
                'expected' => 1,
            ],
            [
                'amount'   => '-12,0@0',
                'expected' => 1,
            ],
            [
                'amount'   => '-12,0@@0',
                'expected' => 1,
            ],
            [
                'amount'   => '123abc',
                'expected' => 1,
            ],
            [
                'amount'   => '123.12e3',
                'expected' => 1,
            ],
        ];
    }

    #[DataProvider('getExamples')]
    public function testValidator(
        mixed $amount,
        int $expected
    ): void {
        $validation = new Validation();
        $validation->add('amount', new AbsInt());

        $messages = $validation->validate(
            [
                'amount' => $amount,
            ]
        );

        $actual = $messages->count();
        $this->assertSame($expected, $actual);
    }
}
