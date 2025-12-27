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

use Phalcon\Api\Domain\Application\Auth\Command\AuthCommandFactory;
use Phalcon\Api\Domain\Application\Auth\Facade\AuthFacade;
use Phalcon\Api\Domain\Application\Auth\Handler\AuthLoginPostHandler;
use Phalcon\Api\Domain\Application\Auth\Handler\AuthLogoutPostHandler;
use Phalcon\Api\Domain\Application\Auth\Handler\AuthRefreshPostHandler;
use Phalcon\Api\Domain\Application\Auth\Service\AuthLoginPostService;
use Phalcon\Api\Domain\Application\Auth\Service\AuthLogoutPostService;
use Phalcon\Api\Domain\Application\Auth\Service\AuthRefreshPostService;
use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandBus;
use Phalcon\Api\Domain\Infrastructure\Container;
use Phalcon\Api\Domain\Infrastructure\DataSource\Auth\Sanitizer\AuthSanitizer;
use Phalcon\Api\Domain\Infrastructure\DataSource\Auth\Transformer\AuthTransformer;
use Phalcon\Api\Domain\Infrastructure\DataSource\Auth\Validator\AuthLoginValidator;
use Phalcon\Api\Domain\Infrastructure\DataSource\Auth\Validator\AuthTokenValidator;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Repository\UserRepository;
use Phalcon\Api\Domain\Infrastructure\Encryption\Security;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenManager;
use Phalcon\Filter\Validation;

/**
 * @phpstan-import-type TService from Container
 */
enum AuthDefinitionsEnum: string implements DefinitionsEnumInterface
{
    case AuthCommandFactory = AuthCommandFactory::class;
    case AuthLoginPost      = AuthLoginPostService::class;
    case AuthLogoutPost     = AuthLogoutPostService::class;
    case AuthRefreshPost    = AuthRefreshPostService::class;
    case AuthFacade         = AuthFacade::class;
    case AuthLoginHandler   = AuthLoginPostHandler::class;
    case AuthLogoutHandler  = AuthLogoutPostHandler::class;
    case AuthRefreshHandler = AuthRefreshPostHandler::class;
    case AuthSanitizer      = AuthSanitizer::class;
    case AuthLoginValidator = AuthLoginValidator::class;
    case AuthTokenValidator = AuthTokenValidator::class;
    case AuthTransformer    = AuthTransformer::class;

    /**
     * @return TService
     */
    public function definition(): array
    {
        return match ($this) {
            self::AuthCommandFactory => [
                'className' => $this->value,
                'arguments' => [
                    [
                        'type' => 'service',
                        'name' => AuthSanitizer::class,
                    ],
                ],
            ],
            self::AuthLoginPost,
            self::AuthLogoutPost,
            self::AuthRefreshPost    => [
                'className' => $this->value,
                'arguments' => [
                    [
                        'type' => 'service',
                        'name' => AuthFacade::class,
                    ],
                ],
            ],
            self::AuthFacade         => [
                'className' => $this->value,
                'arguments' => [
                    [
                        'type' => 'service',
                        'name' => CommandBus::class,
                    ],
                    [
                        'type' => 'service',
                        'name' => AuthCommandFactory::class,
                    ],
                ],
            ],
            self::AuthLoginHandler   => [
                'className' => $this->value,
                'arguments' => [
                    [
                        'type' => 'service',
                        'name' => UserRepository::class,
                    ],
                    [
                        'type' => 'service',
                        'name' => TokenManager::class,
                    ],
                    [
                        'type' => 'service',
                        'name' => AuthTransformer::class,
                    ],
                    [
                        'type' => 'service',
                        'name' => Security::class,
                    ],
                    [
                        'type' => 'service',
                        'name' => AuthLoginValidator::class,
                    ],
                ],
            ],
            self::AuthLogoutHandler,
            self::AuthRefreshHandler => [
                'className' => $this->value,
                'arguments' => [
                    [
                        'type' => 'service',
                        'name' => TokenManager::class,
                    ],
                    [
                        'type' => 'service',
                        'name' => AuthTransformer::class,
                    ],
                    [
                        'type' => 'service',
                        'name' => AuthTokenValidator::class,
                    ],
                ],
            ],
            self::AuthSanitizer      => [
                'className' => $this->value,
                'arguments' => [
                    [
                        'type' => 'service',
                        'name' => Container::FILTER,
                    ],
                ],
            ],
            self::AuthLoginValidator => [
                'className' => $this->value,
                'arguments' => [
                    [
                        'type' => 'service',
                        'name' => Validation::class,
                    ],
                ],
            ],
            self::AuthTokenValidator => [
                'className' => $this->value,
                'arguments' => [
                    [
                        'type' => 'service',
                        'name' => TokenManager::class,
                    ],
                    [
                        'type' => 'service',
                        'name' => UserRepository::class,
                    ],
                    [
                        'type' => 'service',
                        'name' => Validation::class,
                    ],
                ],
            ],
            self::AuthTransformer    => [
                'className' => $this->value,
            ],
        };
    }

    public function isShared(): bool
    {
        return match ($this) {
            self::AuthSanitizer => true,
            default             => false,
        };
    }
}
