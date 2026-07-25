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

use Phalcon\Api\Domain\Infrastructure\Exceptions\HandlerRuntimeException;
use Phalcon\Contracts\Container\Service\Collection;
use Phalcon\Di\DiInterface;

final readonly class ContainerHandlerLocator implements HandlerLocatorInterface
{
    /**
     * Accepts either container. Phalcon\Di\Di is what the application boots
     * on today; Phalcon\Container\Container is what the ADR layer uses. Both
     * expose has() and get(), which is all this class needs, so the union
     * lets the two coexist until the switchover retires the former.
     *
     * @param DiInterface|Collection $container
     */
    public function __construct(
        private DiInterface|Collection $container,
    ) {
    }

    /**
     * @param CommandInterface $command
     *
     * @return HandlerInterface
     */
    public function resolve(CommandInterface $command): HandlerInterface
    {
        $commandClass = get_class($command);

        /**
         * Phalcon\Api\Domain\Application\User\Command\UserGetCommand
         *
         * becomes
         *
         * Phalcon\Api\Domain\Application\User\Handler\UserGetHandler
         *
         * The location of files DOES matter
         */
        $handlerClass = str_replace(
            'Command',
            'Handler',
            $commandClass
        );

        if (true !== $this->container->has($handlerClass)) {
            throw HandlerRuntimeException::new(
                "No handler configured for $commandClass"
            );
        }

        /** @var HandlerInterface $handler */
        $handler = $this->container->get($handlerClass);

        return $handler;
    }
}
