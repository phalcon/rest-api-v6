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

namespace Phalcon\Api\Domain\Infrastructure\Middleware;

use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Events\Event;
use Phalcon\Events\Exception as EventsException;
use Phalcon\Http\Response\Exception;
use Phalcon\Mvc\Micro;

final class NotFoundMiddleware extends AbstractMiddleware
{
    /**
     * @param Event $event
     * @param Micro $application
     *
     * @return bool
     * @throws EventsException
     * @throws Exception
     */
    public function beforeNotFound(Event $event, Micro $application): bool
    {
        $this->halt(
            $application,
            HttpCodesEnum::NotFound->value,
            'error',
            [],
            [HttpCodesEnum::AppResourceNotFound->error()]
        );

        return false;
    }

    /**
     * @param Micro $application
     *
     * @return true
     */
    public function call(Micro $application): bool
    {
        return true;
    }
}
