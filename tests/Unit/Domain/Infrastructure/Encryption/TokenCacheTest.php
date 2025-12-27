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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\Encryption;

use Phalcon\Api\Domain\Infrastructure\Constants\Cache as CacheConstants;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\DTO\User;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenCache;
use Phalcon\Api\Domain\Infrastructure\Env\EnvManager;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Cache\Cache;

use function rand;
use function uniqid;

final class TokenCacheTest extends AbstractUnitTestCase
{
    public function testStoreAndInvalidateForUser(): void
    {
        /** @var EnvManager $env */
        $env = $this->container->get(EnvManager::class);
        /** @var Cache $cache */
        $cache = $this->container->get(Cache::class);
        /** @var TokenCache $tokenCache */
        $tokenCache = $this->container->get(TokenCache::class);

        /**
         * Empty cache
         */
        $cache->getAdapter()->clear();

        /**
         * Create user data
         */
        $newUser           = $this->getNewUserData();
        $newUser['usr_id'] = rand(1, 100);
        $domainUser        = new User(
            $newUser['usr_id'],
            $newUser['usr_status_flag'],
            $newUser['usr_email'],
            $newUser['usr_password'],
            $newUser['usr_name_prefix'],
            $newUser['usr_name_first'],
            $newUser['usr_name_middle'],
            $newUser['usr_name_last'],
            $newUser['usr_name_suffix'],
            $newUser['usr_issuer'],
            $newUser['usr_token_password'],
            $newUser['usr_token_id'],
            $newUser['usr_preferences'],
            $newUser['usr_created_date'],
            $newUser['usr_created_usr_id'],
            $newUser['usr_updated_date'],
            $newUser['usr_updated_usr_id']
        );

        $token1 = uniqid('tok-');
        $token2 = uniqid('tok-');

        $tokenKey1 = CacheConstants::getCacheTokenKey($domainUser, $token1);
        $tokenKey2 = CacheConstants::getCacheTokenKey($domainUser, $token2);

        /**
         * Store
         */
        $actual = $tokenCache->storeTokenInCache($env, $domainUser, $token1);
        $this->assertTrue($actual);
        $actual = $tokenCache->storeTokenInCache($env, $domainUser, $token2);
        $this->assertTrue($actual);

        /**
         * Check the cache
         */
        $actual = $cache->has($tokenKey1);
        $this->assertTrue($actual);
        $actual = $cache->has($tokenKey2);
        $this->assertTrue($actual);

        /**
         * Invalidate
         */
        $actual = $tokenCache->invalidateForUser($env, $domainUser);
        $this->assertTrue($actual);

        $actual = $cache->has($tokenKey1);
        $this->assertFalse($actual);
        $actual = $cache->has($tokenKey2);
        $this->assertFalse($actual);
    }
}
