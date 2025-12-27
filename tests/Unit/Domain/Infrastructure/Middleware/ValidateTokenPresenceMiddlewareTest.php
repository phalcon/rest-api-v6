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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\Middleware;

use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Api\Domain\Infrastructure\Middleware\ValidateTokenPresenceMiddleware;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Mvc\Micro;
use PHPUnit\Framework\Attributes\BackupGlobals;

#[BackupGlobals(true)]
final class ValidateTokenPresenceMiddlewareTest extends AbstractUnitTestCase
{
    public function testValidateTokenPresenceFailure(): void
    {
        $micro      = new Micro($this->container);
        $middleware = $this->container->get(ValidateTokenPresenceMiddleware::class);

        /**
         * No token exception
         */
        $time    = $_SERVER['REQUEST_TIME_FLOAT'] ?? time();
        $_SERVER = [
            'REQUEST_METHOD'     => 'GET',
            'REQUEST_TIME_FLOAT' => $time,
            'REQUEST_URI'        => '/user?id=1234',
        ];

        ob_start();
        $middleware->call($micro);
        $contents = ob_get_clean();

        $contents = json_decode($contents, true);
        $data     = $contents['data'];
        $errors   = $contents['errors'];

        $this->assertSame([], $data);

        $expected = [HttpCodesEnum::AppTokenNotPresent->error()];
        $this->assertSame($expected, $errors);
    }

    public function testValidateTokenPresenceSuccess(): void
    {
        $micro      = new Micro($this->container);
        $middleware = $this->container->get(ValidateTokenPresenceMiddleware::class);

        /**
         * No token return
         */
        $time    = $_SERVER['REQUEST_TIME_FLOAT'] ?? time();
        $_SERVER = [
            'REQUEST_METHOD'     => 'GET',
            'REQUEST_TIME_FLOAT' => $time,
            'REQUEST_URI'        => '/user?id=1234',
        ];

        ob_start();
        $actual = $middleware->call($micro);
        ob_end_clean();
        $this->assertFalse($actual);

        /**
         * Token present
         */
        $time    = $_SERVER['REQUEST_TIME_FLOAT'] ?? time();
        $_SERVER = [
            'REQUEST_METHOD'     => 'GET',
            'REQUEST_TIME_FLOAT' => $time,
            'HTTP_AUTHORIZATION' => 'Bearer 123.456.789',
            'REQUEST_URI'        => '/user?id=1234',
        ];

        ob_start();
        $actual = $middleware->call($micro);
        ob_end_clean();
        $this->assertTrue($actual);
    }
}
