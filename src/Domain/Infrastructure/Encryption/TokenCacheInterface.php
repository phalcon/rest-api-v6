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

namespace Phalcon\Api\Domain\Infrastructure\Encryption;

use Phalcon\Api\Domain\Infrastructure\DataSource\User\DTO\User;
use Phalcon\Api\Domain\Infrastructure\Env\EnvManager;
use Psr\SimpleCache\InvalidArgumentException;

interface TokenCacheInterface
{
    /**
     * @param EnvManager $env
     * @param User       $domainUser
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function invalidateForUser(
        EnvManager $env,
        User $domainUser
    ): bool;

    /**
     * @param EnvManager $env
     * @param User       $domainUser
     * @param string     $token
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function storeTokenInCache(
        EnvManager $env,
        User $domainUser,
        string $token
    ): bool;
}
