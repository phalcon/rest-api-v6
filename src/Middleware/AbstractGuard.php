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

use Phalcon\ADR\Payload\Payload;
use Phalcon\Api\Domain\Infrastructure\Env\EnvManager;
use Phalcon\Contracts\ADR\Middleware;
use Phalcon\Contracts\ADR\Responder\Responder;
use Phalcon\Contracts\Http\AttributeRequest;
use Phalcon\Http\ResponseInterface;

use function str_replace;

/**
 * Shared plumbing for the token guards.
 *
 * Rejection is a plain return: a guard that does not call $next has halted
 * the pipeline, and the layers outside it unwind normally. Nothing here
 * stops the application or emits a response itself, which is what the old
 * AbstractMiddleware::halt() had to do.
 */
abstract class AbstractGuard implements Middleware
{
    public function __construct(
        protected readonly EnvManager $env,
        protected readonly Responder $responder,
        protected readonly ResponseInterface $response,
    ) {
    }

    /**
     * Some local environments do not forward the Authorization header, so
     * the header name stays configurable.
     *
     * @param AttributeRequest $request
     *
     * @return string
     */
    protected function bearerToken(AttributeRequest $request): string
    {
        /** @var string $header */
        $header = $this->env->get('API_HEADER_AUTH', 'Authorization');

        return str_replace('Bearer ', '', $request->getHeader($header));
    }

    /**
     * @param AttributeRequest        $request
     * @param array<array-key, mixed> $errors
     *
     * @return ResponseInterface
     */
    protected function reject(AttributeRequest $request, array $errors): ResponseInterface
    {
        return ($this->responder)(
            $request,
            $this->response,
            Payload::unauthenticated($errors)
        );
    }
}
