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

use Phalcon\Api\Domain\ADR\Payload;
use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandBus;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Api\Tests\Fixtures\Domain\Application\Command\CommandFixture;
use Phalcon\Api\Tests\Fixtures\Domain\Application\Handler\HandlerFixture;

use function uniqid;

final class CommandBusTest extends AbstractUnitTestCase
{
    public function testDispatch(): void
    {
        $name    = uniqid('name-');
        $command = new CommandFixture($name);
        /**
         * Register services
         */
        $this->container->set(HandlerFixture::class, HandlerFixture::class);

        $bus = $this->container->get(CommandBus::class);

        $result = $bus->dispatch($command);

        $expected = Payload::class;
        $actual   = $result;
        $this->assertInstanceOf($expected, $actual);

        $expected = [
            'data' => [
                "Name: $name",
            ],
        ];
        $actual   = $result->getResult();
        $this->assertSame($expected, $actual);
    }
}
