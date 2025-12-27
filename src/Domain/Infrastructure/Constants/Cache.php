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

namespace Phalcon\Api\Domain\Infrastructure\Constants;

use Phalcon\Api\Domain\Infrastructure\DataSource\User\DTO\User;

use function sha1;

class Cache
{
    /** @var int */
    public const CACHE_LIFETIME_DAY = 86400;
    /** @var int */
    public const CACHE_LIFETIME_HOUR = 3600;
    /**
     * Cache Timeouts
     */
    /** @var int */
    public const CACHE_LIFETIME_MINUTE = 60;
    /** @var int */
    public const CACHE_LIFETIME_MONTH = 2592000;
    /**
     * Default token expiry - 4 hours
     */
    /** @var int */
    public const CACHE_TOKEN_EXPIRY = 14400;
    /**
     * Cache masks
     */
    /** @var string */
    private const MASK_TOKEN_USER = 'tk-%s-%s';

    /**
     * @param User   $domainUser
     * @param string $token
     *
     * @return string
     */
    public static function getCacheTokenKey(User $domainUser, string $token): string
    {
        $tokenString = '';
        if (true !== empty($token)) {
            $tokenString = sha1($token);
        }

        return sprintf(
            self::MASK_TOKEN_USER,
            $domainUser->id,
            $tokenString
        );
    }
}
