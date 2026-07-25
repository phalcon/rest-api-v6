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

use Phalcon\Api\Domain\Infrastructure\DataSource\User\DTO\User;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenManagerInterface;
use Phalcon\Api\Domain\Infrastructure\Env\EnvManager;
use Phalcon\Contracts\ADR\Handler;
use Phalcon\Contracts\ADR\Responder\Responder;
use Phalcon\Contracts\Http\AttributeRequest;
use Phalcon\Encryption\Security\JWT\Token\Token;
use Phalcon\Http\ResponseInterface;
use Phalcon\Support\Registry;

/**
 * Validates the token's claims - expiry, not-before, issuer, audience.
 */
final class ValidateTokenClaims extends AbstractGuard
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
        /** @var Token $tokenObject */
        $tokenObject = $this->registry->get('token');
        /** @var User $sessionUser */
        $sessionUser = $this->registry->get('user');

        $errors = $this->tokenManager->validate($tokenObject, $sessionUser);

        if (true !== empty($errors)) {
            return $this->reject($request, [$errors]);
        }

        return $next($request);
    }
}
