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

namespace Phalcon\Api\Tests\Unit\Domain\ADR;

use Faker\Factory;
use Phalcon\Api\Domain\ADR\Input;
use Phalcon\Http\Request;
use PHPUnit\Framework\Attributes\BackupGlobals;
use PHPUnit\Framework\TestCase;

#[BackupGlobals(true)]
final class InputTest extends TestCase
{
    public function testInvoke(): void
    {
        $faker    = Factory::create();
        $postData = [
            'post1' => $faker->word(),
            'post2' => $faker->word(),
        ];
        $getData  = [
            'get1' => $faker->word(),
            'get2' => $faker->word(),
        ];
        $putData  = [
            'put1' => $faker->word(),
            'put2' => $faker->word(),
        ];

        $mockRequest = $this
            ->getMockBuilder(Request::class)
            ->onlyMethods(
                [
                    'getQuery',
                    'getPost',
                    'getPut',
                ]
            )
            ->getMock()
        ;
        $mockRequest->method('getQuery')->willReturn($getData);
        $mockRequest->method('getPost')->willReturn($postData);
        $mockRequest->method('getPut')->willReturn($putData);

        $input = new Input();

        $expected = [
            'get1'  => $getData['get1'],
            'get2'  => $getData['get2'],
            'post1' => $postData['post1'],
            'post2' => $postData['post2'],
            'put1'  => $putData['put1'],
            'put2'  => $putData['put2'],
        ];
        $actual   = $input->__invoke($mockRequest);
        $this->assertSame($expected, $actual);
    }
}
