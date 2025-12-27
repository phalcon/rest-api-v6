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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure;

use Phalcon\Api\Domain\Infrastructure\Container;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Filter\Filter;

final class ContainerTest extends AbstractUnitTestCase
{
    public function testContainerEventManager(): void
    {
        $container = new Container();

        $actual = $container->has(Container::EVENTS_MANAGER);
        $this->assertTrue($actual);

        $eventsManager = $container->getShared(Container::EVENTS_MANAGER);
        $this->assertInstanceOf(EventsManager::class, $eventsManager);
    }

    public function testContainerFilter(): void
    {
        $container = new Container();

        $actual = $container->has(Container::FILTER);
        $this->assertTrue($actual);

        $eventsManager = $container->getShared(Container::FILTER);
        $this->assertInstanceOf(Filter::class, $eventsManager);
    }
}
