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

use Phalcon\Api\Domain\Infrastructure\DataSource\User\Repository\UserRepositoryInterface;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenManagerInterface;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Api\Domain\Infrastructure\Env\EnvManager;
use Phalcon\Contracts\ADR\Handler;
use Phalcon\Contracts\ADR\Responder\Responder;
use Phalcon\Contracts\Http\AttributeRequest;
use Phalcon\Encryption\Security\JWT\Token\Token;
use Phalcon\Http\ResponseInterface;
use Phalcon\Support\Registry;

/**
 * Resolves the token's user and puts it in the registry.
 */
final class ValidateTokenUser extends AbstractGuard
{
    public function __construct(
        EnvManager $env,
        Responder $responder,
        ResponseInterface $response,
        private readonly TokenManagerInterface $tokenManager,
        private readonly UserRepositoryInterface $repository,
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

        $domainUser = $this->tokenManager->getUser($this->repository, $tokenObject);

        if (null === $domainUser) {
            return $this->reject(
                $request,
                [HttpCodesEnum::AppTokenInvalidUser->error()]
            );
        }

        $this->registry->set('user', $domainUser);

        return $next($request);
    }
}
