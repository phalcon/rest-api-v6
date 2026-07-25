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

namespace Phalcon\Api\Action\Health;

use Phalcon\ADR\Payload\Payload;
use Phalcon\Contracts\ADR\Action;
use Phalcon\Contracts\ADR\Responder\Responder;
use Phalcon\Contracts\Http\AttributeRequest;
use Phalcon\Http\ResponseInterface;

/**
 * Replaces HealthMiddleware, which answered /health from inside the
 * middleware chain. A liveness probe is a resource, not a cross-cutting
 * concern, so under ADR it is simply an action.
 */
final readonly class GetHealth implements Action
{
    public function __construct(
        private Responder $responder,
        private ResponseInterface $response,
    ) {
    }

    /**
     * @param AttributeRequest $request
     *
     * @return ResponseInterface
     */
    public function __invoke(AttributeRequest $request): ResponseInterface
    {
        $payload = Payload::success(
            [
                'status'  => 'ok',
                'message' => 'service operational',
            ]
        );

        return ($this->responder)($request, $this->response, $payload);
    }
}
