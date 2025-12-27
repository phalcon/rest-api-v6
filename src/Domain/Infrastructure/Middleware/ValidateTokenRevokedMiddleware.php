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

use Phalcon\Api\Domain\Infrastructure\Constants\Cache as CacheConstants;
use Phalcon\Api\Domain\Infrastructure\Container;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\DTO\User;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Api\Domain\Infrastructure\Env\EnvManager;
use Phalcon\Cache\Cache;
use Phalcon\Http\RequestInterface;
use Phalcon\Mvc\Micro;
use Phalcon\Support\Registry;
use Psr\SimpleCache\CacheInterface;

final class ValidateTokenRevokedMiddleware extends AbstractMiddleware
{
    /**
     * @param Micro $application
     *
     * @return bool
     */
    public function call(Micro $application): bool
    {
        /** @var RequestInterface $request */
        $request = $application->getSharedService(Container::REQUEST);
        /** @var CacheInterface $cache */
        $cache = $application->getSharedService(Cache::class);
        /** @var EnvManager $env */
        $env = $application->getSharedService(EnvManager::class);
        /** @var Registry $registry */
        $registry = $application->getSharedService(Registry::class);

        /** @var User $domainUser */
        $domainUser = $registry->get('user');

        /**
         * Get the token object
         */
        $token = $this->getBearerTokenFromHeader($request, $env);
        $cacheKey = CacheConstants::getCacheTokenKey($domainUser, $token);
        $exists = $cache->has($cacheKey);

        if (true !== $exists) {
            $this->halt(
                $application,
                HttpCodesEnum::Unauthorized->value,
                HttpCodesEnum::Unauthorized->text(),
                [],
                [HttpCodesEnum::AppTokenNotValid->error()]
            );

            return false;
        }

        return true;
    }
}
