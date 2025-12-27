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
use Phalcon\Api\Domain\Infrastructure\Enums\Container\UserDefinitionsEnum;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\DataMapper\Pdo\Connection;
use Phalcon\Filter\Validation;
use Phalcon\Support\Registry;

final class UserDefinitionsEnumTest extends AbstractUnitTestCase
{
    public function testDefinition(): void
    {
        $expected = [
            'className' => UserCommandFactory::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => UserSanitizer::class,
                ],
            ],
        ];
        $actual   = UserDefinitionsEnum::UserCommandFactory->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => UserDeleteService::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => UserFacade::class,
                ],
            ],
        ];
        $actual   = UserDefinitionsEnum::UserDelete->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => UserGetService::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => UserFacade::class,
                ],
            ],
        ];
        $actual   = UserDefinitionsEnum::UserGet->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => UserPostService::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => UserFacade::class,
                ],
            ],
        ];
        $actual   = UserDefinitionsEnum::UserPost->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => UserPutService::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => UserFacade::class,
                ],
            ],
        ];
        $actual   = UserDefinitionsEnum::UserPut->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => UserFacade::class,
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
        ];
        $actual   = UserDefinitionsEnum::UserFacade->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => UserDeleteHandler::class,
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
        ];
        $actual   = UserDefinitionsEnum::UserDeleteHandler->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => UserGetHandler::class,
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
        ];
        $actual   = UserDefinitionsEnum::UserGetHandler->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => UserPostHandler::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => UserValidator::class,
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
        $actual   = UserDefinitionsEnum::UserPostHandler->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => UserPutHandler::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => UserValidatorUpdate::class,
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
        $actual   = UserDefinitionsEnum::UserPutHandler->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => UserMapper::class,
        ];
        $actual   = UserDefinitionsEnum::UserMapper->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => UserRepository::class,
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
        ];
        $actual   = UserDefinitionsEnum::UserRepository->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => UserSanitizer::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => Container::FILTER,
                ],
            ],
        ];
        $actual   = UserDefinitionsEnum::UserSanitizer->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => UserValidator::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => Validation::class,
                ],
            ],
        ];
        $actual   = UserDefinitionsEnum::UserValidator->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => UserValidatorUpdate::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => Validation::class,
                ],
            ],
        ];
        $actual   = UserDefinitionsEnum::UserValidatorUpdate->definition();
        $this->assertSame($expected, $actual);
    }

    public function testIsShared(): void
    {
        $actual = UserDefinitionsEnum::UserCommandFactory->isShared();
        $this->assertFalse($actual);

        $actual = UserDefinitionsEnum::UserDelete->isShared();
        $this->assertFalse($actual);

        $actual = UserDefinitionsEnum::UserGet->isShared();
        $this->assertFalse($actual);

        $actual = UserDefinitionsEnum::UserPost->isShared();
        $this->assertFalse($actual);

        $actual = UserDefinitionsEnum::UserPut->isShared();
        $this->assertFalse($actual);

        $actual = UserDefinitionsEnum::UserFacade->isShared();
        $this->assertFalse($actual);

        $actual = UserDefinitionsEnum::UserDeleteHandler->isShared();
        $this->assertFalse($actual);

        $actual = UserDefinitionsEnum::UserGetHandler->isShared();
        $this->assertFalse($actual);

        $actual = UserDefinitionsEnum::UserPostHandler->isShared();
        $this->assertFalse($actual);

        $actual = UserDefinitionsEnum::UserPutHandler->isShared();
        $this->assertFalse($actual);

        $actual = UserDefinitionsEnum::UserMapper->isShared();
        $this->assertTrue($actual);

        $actual = UserDefinitionsEnum::UserRepository->isShared();
        $this->assertTrue($actual);

        $actual = UserDefinitionsEnum::UserSanitizer->isShared();
        $this->assertTrue($actual);

        $actual = UserDefinitionsEnum::UserValidator->isShared();
        $this->assertFalse($actual);

        $actual = UserDefinitionsEnum::UserValidatorUpdate->isShared();
        $this->assertFalse($actual);
    }

    public function testValue(): void
    {
        $expected = UserCommandFactory::class;
        $actual   = UserDefinitionsEnum::UserCommandFactory->value;
        $this->assertSame($expected, $actual);

        $expected = UserDeleteService::class;
        $actual   = UserDefinitionsEnum::UserDelete->value;
        $this->assertSame($expected, $actual);

        $expected = UserGetService::class;
        $actual   = UserDefinitionsEnum::UserGet->value;
        $this->assertSame($expected, $actual);

        $expected = UserPostService::class;
        $actual   = UserDefinitionsEnum::UserPost->value;
        $this->assertSame($expected, $actual);

        $expected = UserPutService::class;
        $actual   = UserDefinitionsEnum::UserPut->value;
        $this->assertSame($expected, $actual);

        $expected = UserFacade::class;
        $actual   = UserDefinitionsEnum::UserFacade->value;
        $this->assertSame($expected, $actual);

        $expected = UserDeleteHandler::class;
        $actual   = UserDefinitionsEnum::UserDeleteHandler->value;
        $this->assertSame($expected, $actual);

        $expected = UserGetHandler::class;
        $actual   = UserDefinitionsEnum::UserGetHandler->value;
        $this->assertSame($expected, $actual);

        $expected = UserPostHandler::class;
        $actual   = UserDefinitionsEnum::UserPostHandler->value;
        $this->assertSame($expected, $actual);

        $expected = UserPutHandler::class;
        $actual   = UserDefinitionsEnum::UserPutHandler->value;
        $this->assertSame($expected, $actual);

        $expected = UserMapper::class;
        $actual   = UserDefinitionsEnum::UserMapper->value;
        $this->assertSame($expected, $actual);

        $expected = UserRepository::class;
        $actual   = UserDefinitionsEnum::UserRepository->value;
        $this->assertSame($expected, $actual);

        $expected = UserSanitizer::class;
        $actual   = UserDefinitionsEnum::UserSanitizer->value;
        $this->assertSame($expected, $actual);

        $expected = UserValidator::class;
        $actual   = UserDefinitionsEnum::UserValidator->value;
        $this->assertSame($expected, $actual);

        $expected = UserValidatorUpdate::class;
        $actual   = UserDefinitionsEnum::UserValidatorUpdate->value;
        $this->assertSame($expected, $actual);
    }
}
