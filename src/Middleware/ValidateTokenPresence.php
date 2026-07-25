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

namespace Phalcon\Api\Middleware;

use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Contracts\ADR\Handler;
use Phalcon\Contracts\Http\AttributeRequest;
use Phalcon\Http\ResponseInterface;

/**
 * First guard in the chain: is there a bearer token at all.
 */
final class ValidateTokenPresence extends AbstractGuard
{
    /**
     * @param AttributeRequest $request
     * @param Handler          $next
     *
     * @return ResponseInterface
     */
    public function __invoke(AttributeRequest $request, Handler $next): ResponseInterface
    {
        if ('' === $this->bearerToken($request)) {
            return $this->reject(
                $request,
                [HttpCodesEnum::AppTokenNotPresent->error()]
            );
        }

        return $next($request);
    }
}
