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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\Env;

use Phalcon\Api\Domain\Infrastructure\Env\EnvManager;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\BackupGlobals;

#[BackupGlobals(true)]
final class EnvManagerTest extends AbstractUnitTestCase
{
    public function testAppEnvReturnsDefault(): void
    {
        /** @var EnvManager $env */
        $env = $this->container->getShared(EnvManager::class);

        $expected = 'development';
        $actual   = $env->appEnv();
        $this->assertSame($expected, $actual);
    }

    public function testAppEnvReturnsValue(): void
    {
        $_ENV = ['APP_ENV' => 'production'];

        /** @var EnvManager $env */
        $env = $this->container->getShared(EnvManager::class);

        $expected = 'production';
        $actual   = $env->appEnv();
        $this->assertSame($expected, $actual);
    }

    public function testAppLogLevelReturnsDefault(): void
    {
        /** @var EnvManager $env */
        $env = $this->container->getShared(EnvManager::class);

        $expected = 1;
        $actual   = $env->appLogLevel();
        $this->assertSame($expected, $actual);
    }

    public function testAppLogLevelReturnsValue(): void
    {
        $_ENV = ['APP_LOG_LEVEL' => 5];

        /** @var EnvManager $env */
        $env = $this->container->getShared(EnvManager::class);

        $expected = 5;
        $actual   = $env->appLogLevel();
        $this->assertSame($expected, $actual);
    }

    public function testAppPathReturnsRoot(): void
    {
        /** @var EnvManager $env */
        $env = $this->container->getShared(EnvManager::class);

        $expected = dirname(__DIR__, 5);
        $actual   = $env->appPath();
        $this->assertSame($expected, $actual);
    }

    public function testAppTimezoneReturnsDefault(): void
    {
        /** @var EnvManager $env */
        $env = $this->container->getShared(EnvManager::class);

        $expected = 'UTC';
        $actual   = $env->appTimezone();
        $this->assertSame($expected, $actual);
    }

    public function testAppTimezoneReturnsValue(): void
    {
        $_ENV = ['APP_TIMEZONE' => 'America/Los_Angeles'];

        /** @var EnvManager $env */
        $env = $this->container->getShared(EnvManager::class);

        $expected = 'America/Los_Angeles';
        $actual   = $env->appTimezone();
        $this->assertSame($expected, $actual);
    }

    public function testGetFromDotEnvLoad(): void
    {
        /** @var EnvManager $env */
        $env = $this->container->getShared(EnvManager::class);

        $_ENV = [
            'APP_ENV_ADAPTER'   => 'dotenv',
            'APP_ENV_FILE_PATH' => $env->appPath()
                . '/tests/Fixtures/Domain/Infrastructure/Env/',
        ];

        $env->load();

        $values = [
            'SAMPLE_STRING' => 'sample_value',
            'SAMPLE_INT'    => '1',
            'SAMPLE_TRUE'   => true,
            'SAMPLE_FALSE'  => false,
        ];

        $expected = 'default_value';
        $actual   = $env->get('NON_EXISTENT', 'default_value');
        $this->assertSame($expected, $actual);

        $expected = $values['SAMPLE_STRING'];
        $actual   = $env->get('SAMPLE_STRING');
        $this->assertSame($expected, $actual);

        $expected = $values['SAMPLE_INT'];
        $actual   = $env->get('SAMPLE_INT');
        $this->assertSame($expected, $actual);

        $expected = $values['SAMPLE_TRUE'];
        $actual   = $env->get('SAMPLE_TRUE');
        $this->assertSame($expected, $actual);

        $expected = $values['SAMPLE_FALSE'];
        $actual   = $env->get('SAMPLE_FALSE');
        $this->assertSame($expected, $actual);
    }
}
