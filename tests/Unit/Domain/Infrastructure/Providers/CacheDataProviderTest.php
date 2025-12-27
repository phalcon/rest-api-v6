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

use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Cache\Cache;

final class CacheDataProviderTest extends AbstractUnitTestCase
{
    public function testCheckRegistration(): void
    {
        $expected = Cache::class;
        $actual   = $this->container->get(Cache::class);
        $this->assertInstanceOf($expected, $actual);
    }
}
