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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\Providers;

use Phalcon\Api\Domain\Infrastructure\Container;
use Phalcon\Api\Domain\Infrastructure\Env\EnvManager;
use Phalcon\Api\Domain\Infrastructure\Providers\ErrorHandlerProvider;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Logger\Logger;
use PHPUnit\Framework\Attributes\BackupGlobals;
use ReflectionClass;

use function date_default_timezone_get;
use function hrtime;
use function restore_error_handler;
use function trigger_error;
use function uniqid;

use const E_ALL;

#[BackupGlobals(true)]
final class ErrorHandlerProviderTest extends AbstractUnitTestCase
{
    public function testCheckRegistration(): void
    {
        $now = hrtime(true);
        $this->container->set(
            Container::TIME,
            function () use ($now) {
                return $now;
            },
            true
        );

        /** @var EnvManager $env */
        $env = $this->container->getShared(EnvManager::class);

        $provider = new ErrorHandlerProvider();
        $provider->register($this->container);

        $expected = date_default_timezone_get();
        $actual   = $env->appTimezone();
        $this->assertSame($expected, $actual);

        $expected = 'On';
        $actual   = ini_get('display_errors');
        $this->assertSame($expected, $actual);

        $expected = E_ALL;
        $actual   = (int)ini_get('error_reporting');
        $this->assertSame($expected, $actual);

        restore_error_handler();
    }

    public function testRegisterSetsHandlersAndLogs(): void
    {
        $now = hrtime(true);
        $this->container->set(
            Container::TIME,
            function () use ($now) {
                return $now;
            },
            true
        );

        /** @var EnvManager $env */
        $env = $this->container->getShared(EnvManager::class);

        $provider = new ErrorHandlerProvider();
        $provider->register($this->container);

        $message = uniqid('msg-');
        // Trigger an error
        @trigger_error($message);

        restore_error_handler();

        /** @var string $logName */
        $logName = $env->get('LOG_FILENAME', 'rest-api');
        /** @var string $logPath */
        $logPath = $env->get('LOG_PATH', 'storage/logs/');
        $logFile = $env->appPath($logPath) . '/' . $logName . '.log';

        $this->assertFileContentsContains($logFile, $message);
    }

    public function testRegisterShutdown(): void
    {
        $now = hrtime(true);
        $this->container->set(
            Container::TIME,
            function () use ($now) {
                return $now;
            },
            true
        );

        $_ENV['APP_ENV']       = 'development';
        $_ENV['APP_LOG_LEVEL'] = 2;

        /** @var Logger $logger */
        $logger = $this->container->getShared(Logger::class);
        /** @var EnvManager $env */
        $env = $this->container->getShared(EnvManager::class);

        /** @var string $logName */
        $logName = $env->get('LOG_FILENAME', 'rest-api');
        /** @var string $logPath */
        $logPath = $env->get('LOG_PATH', 'storage/logs/');
        $logFile = $env->appPath($logPath) . '/' . $logName . '.log';

        $provider = new ErrorHandlerProvider();
        $provider->register($this->container);

        // Directly call shutdown for coverage and checking if it works
        $reflection = new ReflectionClass($provider);
        $method     = $reflection->getMethod('onShutdown');
        $method->setAccessible(true);
        $shutdown = $method->invoke(
            $provider,
            $logger,
            $env,
            $now
        );

        restore_error_handler();

        $this->assertTrue($shutdown);

        $this->assertFileExists($logFile);

        $message = 'Shutdown';
        $this->assertFileContentsContains($logFile, $message);
    }
}
