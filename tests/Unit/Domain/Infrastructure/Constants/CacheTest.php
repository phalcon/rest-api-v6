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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\Constants;

use Phalcon\Api\Domain\Infrastructure\Constants\Cache;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\DTO\User;
use Phalcon\Api\Tests\AbstractUnitTestCase;

use function sha1;
use function uniqid;

final class CacheTest extends AbstractUnitTestCase
{
    public function testConstants(): void
    {
        $expected = 86400;
        $actual   = Cache::CACHE_LIFETIME_DAY;
        $this->assertSame($expected, $actual);

        $expected = 3600;
        $actual   = Cache::CACHE_LIFETIME_HOUR;
        $this->assertSame($expected, $actual);

        $expected = 60;
        $actual   = Cache::CACHE_LIFETIME_MINUTE;
        $this->assertSame($expected, $actual);

        $expected = 2592000;
        $actual   = Cache::CACHE_LIFETIME_MONTH;
        $this->assertSame($expected, $actual);

        $expected = 14400;
        $actual   = Cache::CACHE_TOKEN_EXPIRY;
        $this->assertSame($expected, $actual);

        $token    = uniqid('tok-');
        $shaToken = sha1($token);

        $newUser           = $this->getNewUserData();
        $newUser['usr_id'] = 1;
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

        $expected = 'tk-' . $domainUser->id . '-' . $shaToken;
        $actual   = Cache::getCacheTokenKey($domainUser, $token);
        $this->assertSame($expected, $actual);
    }
}
