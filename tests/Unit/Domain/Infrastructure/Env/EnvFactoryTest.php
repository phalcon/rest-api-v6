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

use Phalcon\Api\Domain\Infrastructure\Env\Adapters\DotEnv;
use Phalcon\Api\Domain\Infrastructure\Env\EnvFactory;
use Phalcon\Api\Domain\Infrastructure\Exceptions\InvalidConfigurationArgumentException;
use Phalcon\Api\Tests\AbstractUnitTestCase;

final class EnvFactoryTest extends AbstractUnitTestCase
{
    public function testLoad(): void
    {
        $factory = new EnvFactory();
        $dotEnv  = $factory->newInstance('dotenv');

        $class = DotEnv::class;
        $this->assertInstanceOf($class, $dotEnv);
    }

    public function testUnknownService(): void
    {
        $this->expectException(InvalidConfigurationArgumentException::class);
        $this->expectExceptionMessage(
            'Service unknown is not registered'
        );

        $factory = new EnvFactory();
        $factory->newInstance('unknown');
    }
}
