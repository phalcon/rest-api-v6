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

use Exception;
use Phalcon\Api\Domain\Infrastructure\Encryption\JWTToken;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenCache;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenCacheInterface;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenManager;
use Phalcon\Api\Domain\Infrastructure\Env\EnvManager;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Encryption\Security\JWT\Token\Token;

use function rand;

final class TokenManagerTest extends AbstractUnitTestCase
{
    public function testGetObjectSuccess(): void
    {
        /** @var EnvManager $env */
        $env = $this->container->get(EnvManager::class);
        /** @var TokenCacheInterface $tokenCache */
        $tokenCache = $this->container->get(TokenCache::class);
        /** @var JWTToken $jwtToken */
        $jwtToken           = $this->container->get(JwtToken::class);
        $userData           = $this->getNewUserData();
        $userData['usr_id'] = rand(1, 100);

        $token = $this->getUserToken($userData);

        $manager = new TokenManager($tokenCache, $env, $jwtToken);

        $expected = Token::class;
        $actual   = $manager->getObject($token);
        $this->assertInstanceOf($expected, $actual);
    }

    public function testGetObjectTokenEmpty(): void
    {
        /** @var EnvManager $env */
        $env = $this->container->get(EnvManager::class);
        /** @var TokenCacheInterface $tokenCache */
        $tokenCache = $this->container->get(TokenCache::class);
        /** @var JWTToken $jwtToken */
        $jwtToken = $this->container->get(JwtToken::class);

        $manager = new TokenManager($tokenCache, $env, $jwtToken);

        $expected = null;
        $actual   = $manager->getObject('');
        $this->assertSame($expected, $actual);

        $actual = $manager->getObject(null);
        $this->assertSame($expected, $actual);
    }

    public function testGetObjectWithException(): void
    {
        /** @var EnvManager $env */
        $env = $this->container->get(EnvManager::class);
        /** @var TokenCacheInterface $tokenCache */
        $tokenCache   = $this->container->get(TokenCache::class);
        $mockJWTToken = $this
            ->getMockBuilder(JWTToken::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'getObject',
                ]
            )
            ->getMock()
        ;
        $mockJWTToken
            ->method('getObject')
            ->willThrowException(new Exception('error'))
        ;

        $manager = new TokenManager($tokenCache, $env, $mockJWTToken);

        $expected = null;
        $actual   = $manager->getObject('');
        $this->assertSame($expected, $actual);

        $actual = $manager->getObject(null);
        $this->assertSame($expected, $actual);
    }
}
