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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\CommandBus;

use Phalcon\Api\Domain\Infrastructure\CommandBus\ContainerHandlerLocator;
use Phalcon\Api\Domain\Infrastructure\Exceptions\HandlerRuntimeException;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Api\Tests\Fixtures\Domain\Application\Command\CommandFixture;
use Phalcon\Api\Tests\Fixtures\Domain\Application\Handler\HandlerFixture;

use function uniqid;

final class ContainerHandlerLocatorTest extends AbstractUnitTestCase
{
    public function testDispatch(): void
    {
        $name    = uniqid('name-');
        $command = new CommandFixture($name);
        /**
         * Register services
         */
        $this->container->set(HandlerFixture::class, HandlerFixture::class);

        /** @var ContainerHandlerLocator $locator */
        $locator = $this->container->get(ContainerHandlerLocator::class);

        $result = $locator->resolve($command);

        $expected = HandlerFixture::class;
        $actual   = $result;
        $this->assertInstanceOf($expected, $actual);
    }

    public function testDispatchError(): void
    {
        $this->expectException(HandlerRuntimeException::class);
        $this->expectExceptionMessage(
            'No handler configured for ' . CommandFixture::class
        );

        $name    = uniqid('name-');
        $command = new CommandFixture($name);

        /** @var ContainerHandlerLocator $locator */
        $locator = $this->container->get(ContainerHandlerLocator::class);

        $locator->resolve($command);
    }
}
