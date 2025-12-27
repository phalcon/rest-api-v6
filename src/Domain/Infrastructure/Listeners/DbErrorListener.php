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

namespace Phalcon\Api\Domain\Infrastructure\Listeners;

use Phalcon\Api\Domain\Infrastructure\CommandBus\HandlerInterface;
use Phalcon\Events\Event;
use Psr\Log\LoggerInterface;

use function get_class;

final readonly class DbErrorListener
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @param Event            $event
     * @param HandlerInterface $handler
     *
     * @return void
     */
    public function pdoError(
        Event $event,
        HandlerInterface $handler
    ): void {
        $class = get_class($handler);
        /** @var string $data */
        $data = $event->getData();
        $this->logger->error($class . ': ' . $data);
    }
}
