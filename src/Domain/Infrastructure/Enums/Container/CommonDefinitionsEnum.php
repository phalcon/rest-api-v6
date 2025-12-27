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

namespace Phalcon\Api\Domain\Infrastructure\Enums\Container;

use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandBus;
use Phalcon\Api\Domain\Infrastructure\CommandBus\ContainerHandlerLocator;
use Phalcon\Api\Domain\Infrastructure\Container;
use Phalcon\Api\Domain\Infrastructure\DataSource\Transformer\Transformer;
use Phalcon\Api\Domain\Infrastructure\Encryption\JWTToken;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenCache;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenManager;
use Phalcon\Api\Domain\Infrastructure\Env\EnvManager;
use Phalcon\Api\Responder\JsonResponder;
use Phalcon\Cache\Cache;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Mvc\Router;
use Phalcon\Support\Registry;

/**
 * @phpstan-import-type TService from Container
 */
enum CommonDefinitionsEnum: string implements DefinitionsEnumInterface
{
    case CommandBus      = CommandBus::class;
    case EnvManager      = EnvManager::class;
    case JsonResponder   = JsonResponder::class;
    case JWTToken        = JWTToken::class;
    case JWTTokenCache   = TokenCache::class;
    case JWTTokenManager = TokenManager::class;
    case Registry        = Registry::class;
    case Request         = Container::REQUEST;
    case Response        = Container::RESPONSE;
    case Router          = Container::ROUTER;
    case Transformer     = Transformer::class;

    /**
     * @return TService
     */
    public function definition(): array
    {
        return match ($this) {
            self::CommandBus      => [
                'className' => $this->value,
                'arguments' => [
                    [
                        'type' => 'service',
                        'name' => ContainerHandlerLocator::class,
                    ],
                ],
            ],
            self::EnvManager,
            self::JsonResponder,
            self::Registry,
            self::Transformer     => [
                'className' => $this->value,
            ],
            self::JWTToken        => [
                'className' => $this->value,
                'arguments' => [
                    [
                        'type' => 'service',
                        'name' => EnvManager::class,
                    ],
                ],
            ],
            self::JWTTokenCache   => [
                'className' => $this->value,
                'arguments' => [
                    [
                        'type' => 'service',
                        'name' => Cache::class,
                    ],
                ],
            ],
            self::JWTTokenManager => [
                'className' => $this->value,
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
            ],
            self::Request         => [
                'className' => Request::class,
            ],
            self::Response        => [
                'className' => Response::class,
            ],
            self::Router          => [
                'className' => Router::class,
                'arguments' => [
                    [
                        'type'  => 'parameter',
                        'value' => false,
                    ],
                ],
            ]
        };
    }

    public function isShared(): bool
    {
        return true;
    }
}
