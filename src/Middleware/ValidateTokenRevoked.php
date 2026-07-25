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

use Phalcon\Api\Domain\Infrastructure\Constants\Cache as CacheConstants;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\DTO\User;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Api\Domain\Infrastructure\Env\EnvManager;
use Phalcon\Cache\Cache;
use Phalcon\Contracts\ADR\Handler;
use Phalcon\Contracts\ADR\Responder\Responder;
use Phalcon\Contracts\Http\AttributeRequest;
use Phalcon\Http\ResponseInterface;
use Phalcon\Support\Registry;

/**
 * Last guard: has this token been revoked since it was issued.
 */
final class ValidateTokenRevoked extends AbstractGuard
{
    public function __construct(
        EnvManager $env,
        Responder $responder,
        ResponseInterface $response,
        private readonly Cache $cache,
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
        /** @var User $domainUser */
        $domainUser = $this->registry->get('user');

        $cacheKey = CacheConstants::getCacheTokenKey(
            $domainUser,
            $this->bearerToken($request)
        );

        if (true !== $this->cache->has($cacheKey)) {
            return $this->reject(
                $request,
                [HttpCodesEnum::AppTokenNotValid->error()]
            );
        }

        return $next($request);
    }
}
