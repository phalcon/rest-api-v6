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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\Enums\Container;

use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandBus;
use Phalcon\Api\Domain\Infrastructure\CommandBus\ContainerHandlerLocator;
use Phalcon\Api\Domain\Infrastructure\Container;
use Phalcon\Api\Domain\Infrastructure\Encryption\JWTToken;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenCache;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenManager;
use Phalcon\Api\Domain\Infrastructure\Enums\Container\CommonDefinitionsEnum;
use Phalcon\Api\Domain\Infrastructure\Env\EnvManager;
use Phalcon\Api\Responder\JsonResponder;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Cache\Cache;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Mvc\Router;
use Phalcon\Support\Registry;

final class CommonDefinitionsEnumTest extends AbstractUnitTestCase
{
    public function testDefinition(): void
    {
        $expected = [
            'className' => CommandBus::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => ContainerHandlerLocator::class,
                ],
            ],
        ];
        $actual   = CommonDefinitionsEnum::CommandBus->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => EnvManager::class,
        ];
        $actual   = CommonDefinitionsEnum::EnvManager->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => JsonResponder::class,
        ];
        $actual   = CommonDefinitionsEnum::JsonResponder->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => JWTToken::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => EnvManager::class,
                ],
            ],
        ];
        $actual   = CommonDefinitionsEnum::JWTToken->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => TokenCache::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => Cache::class,
                ],
            ],
        ];
        $actual   = CommonDefinitionsEnum::JWTTokenCache->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => TokenManager::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => TokenCache::class,
                ],
                [
                    'type' => 'service',
                    'name' => EnvManager::class,
                ],
                [
                    'type' => 'service',
                    'name' => JWTToken::class,
                ],
            ],
        ];
        $actual   = CommonDefinitionsEnum::JWTTokenManager->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => Registry::class,
        ];
        $actual   = CommonDefinitionsEnum::Registry->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => Request::class,
        ];
        $actual   = CommonDefinitionsEnum::Request->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => Response::class,
        ];
        $actual   = CommonDefinitionsEnum::Response->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => Router::class,
            'arguments' => [
                [
                    'type'  => 'parameter',
                    'value' => false,
                ],
            ],
        ];
        $actual   = CommonDefinitionsEnum::Router->definition();
        $this->assertSame($expected, $actual);
    }

    public function testIsShared(): void
    {
        $actual = CommonDefinitionsEnum::CommandBus->isShared();
        $this->assertTrue($actual);

        $actual = CommonDefinitionsEnum::EnvManager->isShared();
        $this->assertTrue($actual);

        $actual = CommonDefinitionsEnum::JsonResponder->isShared();
        $this->assertTrue($actual);

        $actual = CommonDefinitionsEnum::JWTToken->isShared();
        $this->assertTrue($actual);

        $actual = CommonDefinitionsEnum::JWTTokenCache->isShared();
        $this->assertTrue($actual);

        $actual = CommonDefinitionsEnum::JWTTokenManager->isShared();
        $this->assertTrue($actual);

        $actual = CommonDefinitionsEnum::Registry->isShared();
        $this->assertTrue($actual);

        $actual = CommonDefinitionsEnum::Request->isShared();
        $this->assertTrue($actual);

        $actual = CommonDefinitionsEnum::Response->isShared();
        $this->assertTrue($actual);

        $actual = CommonDefinitionsEnum::Router->isShared();
        $this->assertTrue($actual);
    }

    public function testValues(): void
    {
        $expected = CommandBus::class;
        $actual   = CommonDefinitionsEnum::CommandBus->value;
        $this->assertSame($expected, $actual);

        $expected = EnvManager::class;
        $actual   = CommonDefinitionsEnum::EnvManager->value;
        $this->assertSame($expected, $actual);

        $expected = JsonResponder::class;
        $actual   = CommonDefinitionsEnum::JsonResponder->value;
        $this->assertSame($expected, $actual);

        $expected = JWTToken::class;
        $actual   = CommonDefinitionsEnum::JWTToken->value;
        $this->assertSame($expected, $actual);

        $expected = TokenCache::class;
        $actual   = CommonDefinitionsEnum::JWTTokenCache->value;
        $this->assertSame($expected, $actual);

        $expected = TokenManager::class;
        $actual   = CommonDefinitionsEnum::JWTTokenManager->value;
        $this->assertSame($expected, $actual);

        $expected = Registry::class;
        $actual   = CommonDefinitionsEnum::Registry->value;
        $this->assertSame($expected, $actual);

        $expected = Container::REQUEST;
        $actual   = CommonDefinitionsEnum::Request->value;
        $this->assertSame($expected, $actual);

        $expected = Container::RESPONSE;
        $actual   = CommonDefinitionsEnum::Response->value;
        $this->assertSame($expected, $actual);

        $expected = Container::ROUTER;
        $actual   = CommonDefinitionsEnum::Router->value;
        $this->assertSame($expected, $actual);
    }
}
