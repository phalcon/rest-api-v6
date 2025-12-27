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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\Middleware;

use Phalcon\Api\Domain\Infrastructure\Constants\Cache as CacheConstants;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Mapper\UserMapper;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Api\Domain\Infrastructure\Middleware\ValidateTokenRevokedMiddleware;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Api\Tests\Fixtures\Domain\Migrations\UsersMigration;
use Phalcon\Cache\Cache;
use Phalcon\Mvc\Micro;
use Phalcon\Support\Registry;
use PHPUnit\Framework\Attributes\BackupGlobals;

#[BackupGlobals(true)]
final class ValidateTokenRevokedMiddlewareTest extends AbstractUnitTestCase
{
    public function testValidateTokenRevokedFailureInvalidToken(): void
    {
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        $migration  = new UsersMigration($this->getConnection());
        $user       = $this->getNewUser($migration);
        $tokenUser  = $userMapper->domain($user);

        [$micro, $middleware] = $this->setupTest();

        $token = $this->getUserToken($user);

        /**
         * Store the user in the registry
         */
        /** @var Registry $registry */
        $registry = $this->container->get(Registry::class);
        $registry->set('user', $tokenUser);

        // There is no entry in the cache for this token, so this should fail.
        $time    = $_SERVER['REQUEST_TIME_FLOAT'] ?? time();
        $_SERVER = [
            'REQUEST_METHOD'     => 'GET',
            'REQUEST_TIME_FLOAT' => $time,
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'REQUEST_URI'        => '/user?id=1234',
        ];

        ob_start();
        $middleware->call($micro);
        $contents = ob_get_clean();

        $contents = json_decode($contents, true);

        $data   = $contents['data'];
        $errors = $contents['errors'];

        $this->assertSame([], $data);

        $expected = [HttpCodesEnum::AppTokenNotValid->error()];
        $this->assertSame($expected, $errors);
    }

    public function testValidateTokenRevokedSuccess(): void
    {
        /** @var UserMapper $userMapper */
        $userMapper = $this->container->get(UserMapper::class);
        $migration  = new UsersMigration($this->getConnection());
        $user       = $this->getNewUser($migration);
        $tokenUser  = $userMapper->domain($user);

        [$micro, $middleware] = $this->setupTest();

        $token = $this->getUserToken($user);

        /**
         * Store the user in the registry
         */
        /** @var Registry $registry */
        $registry = $this->container->get(Registry::class);
        $registry->set('user', $tokenUser);

        /** @var Cache $cache */
        $cache       = $micro->getSharedService(Cache::class);
        $sessionUser = $registry->get('user');
        $cacheKey    = CacheConstants::getCacheTokenKey($sessionUser, $token);
        $payload     = [
            'token' => $token,
        ];
        $actual      = $cache->set($cacheKey, $payload, 2);
        $this->assertTrue($actual);

        // There is no entry in the cache for this token, so this should fail.
        $time    = $_SERVER['REQUEST_TIME_FLOAT'] ?? time();
        $_SERVER = [
            'REQUEST_METHOD'     => 'GET',
            'REQUEST_TIME_FLOAT' => $time,
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'REQUEST_URI'        => '/user?id=1234',
        ];

        $contents = $middleware->call($micro);

        $this->assertTrue($contents);
    }

    /**
     * @return array
     */
    private function setupTest(): array
    {
        $micro      = new Micro($this->container);
        $middleware = $this->container->get(ValidateTokenRevokedMiddleware::class);

        return [$micro, $middleware];
    }
}
