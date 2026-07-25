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

namespace Phalcon\Api\Domain\Infrastructure\CommandBus;

use Phalcon\Contracts\ADR\Payload\Payload as PayloadContract;

final readonly class CommandBus implements CommandBusInterface
{
    /**
     * @param HandlerLocatorInterface $locator
     */
    public function __construct(
        private HandlerLocatorInterface $locator,
    ) {
    }

    /**
     * @param CommandInterface $command
     *
     * @return PayloadContract
     */
    public function dispatch(CommandInterface $command): PayloadContract
    {
        $handler = $this->locator->resolve($command);

        return $handler($command);
    }
}
