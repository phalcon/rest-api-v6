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

use Phalcon\Api\Domain\Application\User\Command\UserCommandFactory;
use Phalcon\Api\Domain\Application\User\Facade\UserFacade;
use Phalcon\Api\Domain\Application\User\Handler\UserDeleteHandler;
use Phalcon\Api\Domain\Application\User\Handler\UserGetHandler;
use Phalcon\Api\Domain\Application\User\Handler\UserPostHandler;
use Phalcon\Api\Domain\Application\User\Handler\UserPutHandler;
use Phalcon\Api\Domain\Application\User\Service\UserDeleteService;
use Phalcon\Api\Domain\Application\User\Service\UserGetService;
use Phalcon\Api\Domain\Application\User\Service\UserPostService;
use Phalcon\Api\Domain\Application\User\Service\UserPutService;
use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandBus;
use Phalcon\Api\Domain\Infrastructure\Container;
use Phalcon\Api\Domain\Infrastructure\DataSource\Transformer\Transformer;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Mapper\UserMapper;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Repository\UserRepository;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Sanitizer\UserSanitizer;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Validator\UserValidator;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Validator\UserValidatorUpdate;
use Phalcon\Api\Domain\Infrastructure\Encryption\Security;
use Phalcon\DataMapper\Pdo\Connection;
use Phalcon\Filter\Validation;
use Phalcon\Support\Registry;

/**
 * @phpstan-import-type TService from Container
 */
enum UserDefinitionsEnum: string implements DefinitionsEnumInterface
{
    case UserCommandFactory  = UserCommandFactory::class;
    case UserDelete          = UserDeleteService::class;
    case UserGet             = UserGetService::class;
    case UserPost            = UserPostService::class;
    case UserPut             = UserPutService::class;
    case UserFacade          = UserFacade::class;
    case UserDeleteHandler   = UserDeleteHandler::class;
    case UserGetHandler      = UserGetHandler::class;
    case UserPostHandler     = UserPostHandler::class;
    case UserPutHandler      = UserPutHandler::class;
    case UserMapper          = UserMapper::class;
    case UserRepository      = UserRepository::class;
    case UserSanitizer       = UserSanitizer::class;
    case UserValidator       = UserValidator::class;
    case UserValidatorUpdate = UserValidatorUpdate::class;

    /**
     * @return TService
     */
    public function definition(): array
    {
        return match ($this) {
            self::UserCommandFactory  => [
                'className' => $this->value,
                'arguments' => [
                    [
                        'type' => 'service',
                        'name' => UserSanitizer::class,
                    ],
                ],
            ],
            self::UserDelete,
            self::UserGet,
            self::UserPost,
            self::UserPut             => [
                'className' => $this->value,
                'arguments' => [
                    [
                        'type' => 'service',
                        'name' => UserFacade::class,
                    ],
                ],
            ],
            self::UserFacade          => [
                'className' => $this->value,
                'arguments' => [
                    [
                        'type' => 'service',
                        'name' => CommandBus::class,
                    ],
                    [
                        'type' => 'service',
                        'name' => UserCommandFactory::class,
                    ],
                ],
            ],
            self::UserDeleteHandler,
            self::UserGetHandler      => [
                'className' => $this->value,
                'arguments' => [
                    [
                        'type' => 'service',
                        'name' => UserRepository::class,
                    ],
                    [
                        'type' => 'service',
                        'name' => Transformer::class,
                    ],
                ],
            ],
            self::UserPostHandler     => $this->getServicePutPost(
                $this->value,
                UserValidator::class
            ),
            self::UserPutHandler      => $this->getServicePutPost(
                $this->value,
                UserValidatorUpdate::class
            ),
            self::UserMapper          => [
                'className' => $this->value,
            ],
            self::UserRepository      => [
                'className' => $this->value,
                'arguments' => [
                    [
                        'type' => 'service',
                        'name' => Connection::class,
                    ],
                    [
                        'type' => 'service',
                        'name' => UserMapper::class,
                    ],
                ],
            ],
            self::UserSanitizer       => [
                'className' => $this->value,
                'arguments' => [
                    [
                        'type' => 'service',
                        'name' => Container::FILTER,
                    ],
                ],
            ],
            self::UserValidator,
            self::UserValidatorUpdate => [
                'className' => $this->value,
                'arguments' => [
                    [
                        'type' => 'service',
                        'name' => Validation::class,
                    ],
                ],
            ],
        };
    }

    public function isShared(): bool
    {
        return match ($this) {
            self::UserMapper,
            self::UserRepository,
            self::UserSanitizer => true,
            default             => false,
        };
    }

    /**
     * @param class-string $className
     * @param class-string $validatorName
     *
     * @return TService
     */
    private function getServicePutPost(string $className, string $validatorName): array
    {
        return [
            'className' => $className,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => $validatorName,
                ],
                [
                    'type' => 'service',
                    'name' => UserMapper::class,
                ],
                [
                    'type' => 'service',
                    'name' => UserRepository::class,
                ],
                [
                    'type' => 'service',
                    'name' => Container::EVENTS_MANAGER,
                ],
                [
                    'type' => 'service',
                    'name' => Transformer::class,
                ],
                [
                    'type' => 'service',
                    'name' => Registry::class,
                ],
                [
                    'type' => 'service',
                    'name' => Security::class,
                ],
            ],
        ];
    }
}
