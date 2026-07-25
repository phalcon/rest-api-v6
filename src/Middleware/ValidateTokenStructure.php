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

use Phalcon\Api\Domain\Infrastructure\Encryption\TokenManagerInterface;
use Phalcon\Api\Domain\Infrastructure\Env\EnvManager;
use Phalcon\Contracts\ADR\Handler;
use Phalcon\Contracts\ADR\Responder\Responder;
use Phalcon\Contracts\Http\AttributeRequest;
use Phalcon\Http\ResponseInterface;
use Phalcon\Support\Registry;

/**
 * Parses the token and puts it in the registry for the guards downstream.
 */
final class ValidateTokenStructure extends AbstractGuard
{
    public function __construct(
        EnvManager $env,
        Responder $responder,
        ResponseInterface $response,
        private readonly TokenManagerInterface $tokenManager,
        private readonly Registry $registry,
    ) {
        parent::__construct($env, $responder, $response);
    }

    /**
     * @param AttributeRequest $request
     * @param Handler          $next
     *
     * @return ResponseInterface
     */
    public function __invoke(AttributeRequest $request, Handler $next): ResponseInterface
    {
        $tokenObject = $this->tokenManager->getObject($this->bearerToken($request));

        if (null === $tokenObject) {
            return $this->reject(
                $request,
                [[$this->tokenManager->getErrorMessage()]]
            );
        }

        $this->registry->set('token', $tokenObject);

        return $next($request);
    }
}
