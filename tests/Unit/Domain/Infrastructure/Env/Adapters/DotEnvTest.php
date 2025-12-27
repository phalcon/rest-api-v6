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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\Env\Adapters;

use Phalcon\Api\Domain\Infrastructure\Env\Adapters\DotEnv;
use Phalcon\Api\Domain\Infrastructure\Env\EnvManager;
use Phalcon\Api\Domain\Infrastructure\Exceptions\InvalidConfigurationArgumentException;
use Phalcon\Api\Tests\AbstractUnitTestCase;

final class DotEnvTest extends AbstractUnitTestCase
{
    private string $envFile;

    public function setUp(): void
    {
        parent::setUp();

        /** @var EnvManager $env */
        $env = $this->container->get(EnvManager::class);

        $this->envFile = $env->appPath()
            . '/tests/Fixtures/Domain/Infrastructure/Env/';
    }

    public function testLoadExceptionForEmptyFilePath(): void
    {
        $this->expectException(InvalidConfigurationArgumentException::class);
        $this->expectExceptionMessage(
            'The .env file does not exist at the specified path'
        );

        $dotEnv  = new DotEnv();
        $options = [
            'filePath' => '',
        ];

        $dotEnv->load($options);
    }

    public function testLoadExceptionForMissingFile(): void
    {
        $this->expectException(InvalidConfigurationArgumentException::class);
        $this->expectExceptionMessage(
            'The .env file does not exist at the specified path'
        );

        $dotEnv  = new DotEnv();
        $options = [
            'filePath' => '/does/not/exist/',
        ];

        $dotEnv->load($options);
    }

    public function testLoadSuccess(): void
    {
        $dotEnv  = new DotEnv();
        $options = [
            'filePath' => $this->envFile,
        ];

        $expected = [
            'SAMPLE_STRING' => 'sample_value',
            'SAMPLE_INT'    => '1',
            'SAMPLE_TRUE'   => 'true',
            'SAMPLE_FALSE'  => 'false',
        ];
        $actual   = $dotEnv->load($options);

        $this->assertArrayHasKey('SAMPLE_STRING', $actual);
        $this->assertArrayHasKey('SAMPLE_INT', $actual);
        $this->assertArrayHasKey('SAMPLE_TRUE', $actual);
        $this->assertArrayHasKey('SAMPLE_FALSE', $actual);

        $actualArray = [
            'SAMPLE_STRING' => $actual['SAMPLE_STRING'],
            'SAMPLE_INT'    => $actual['SAMPLE_INT'],
            'SAMPLE_TRUE'   => $actual['SAMPLE_TRUE'],
            'SAMPLE_FALSE'  => $actual['SAMPLE_FALSE'],
        ];

        $this->assertSame($expected, $actualArray);
    }
}
