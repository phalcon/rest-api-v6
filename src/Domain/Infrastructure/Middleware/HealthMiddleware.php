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

use Phalcon\Api\Domain\Infrastructure\Container;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Events\Exception as EventsException;
use Phalcon\Http\RequestInterface;
use Phalcon\Http\Response\Exception;
use Phalcon\Mvc\Micro;

final class HealthMiddleware extends AbstractMiddleware
{
    /**
     * @param Micro $application
     *
     * @return true
     * @throws EventsException
     * @throws Exception
     */
    public function call(Micro $application): bool
    {
        /** @var RequestInterface $request */
        $request = $application->getSharedService(Container::REQUEST);

        if (
            '/health' === $request->getURI() &&
            true === $request->isGet()
        ) {
            $payload = [
                'status'  => 'ok',
                'message' => 'service operational',
            ];

            $this->halt(
                $application,
                HttpCodesEnum::OK->value,
                HttpCodesEnum::OK->text(),
                $payload
            );
        }

        return true;
    }
}
