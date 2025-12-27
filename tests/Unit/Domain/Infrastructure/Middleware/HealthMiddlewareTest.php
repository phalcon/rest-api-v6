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

use Phalcon\Api\Domain\Infrastructure\Middleware\HealthMiddleware;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Mvc\Micro;
use PHPUnit\Framework\Attributes\BackupGlobals;

use function ob_get_clean;
use function ob_start;

#[BackupGlobals(true)]
final class HealthMiddlewareTest extends AbstractUnitTestCase
{
    public function testCall(): void
    {
        $application = new Micro($this->container);
        $middleware  = $this->container->get(HealthMiddleware::class);

        $time    = $_SERVER['REQUEST_TIME_FLOAT'] ?? time();
        $_SERVER = [
            'REQUEST_METHOD'     => 'GET',
            'REQUEST_TIME_FLOAT' => $time,
            'REQUEST_URI'        => '/health',
        ];

        ob_start();
        $actual   = $middleware->call($application);
        $contents = ob_get_clean();

        $this->assertTrue($actual);

        $contents = json_decode($contents, true);

        $expected = [
            'status'  => 'ok',
            'message' => 'service operational',
        ];
        $actual   = $contents['data'];
        $this->assertSame($expected, $actual);
    }
}
