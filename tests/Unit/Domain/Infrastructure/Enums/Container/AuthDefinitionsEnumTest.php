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
use Phalcon\Api\Domain\Infrastructure\Enums\Container\AuthDefinitionsEnum;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Filter\Validation;

final class AuthDefinitionsEnumTest extends AbstractUnitTestCase
{
    public function testDefinition(): void
    {
        $expected = [
            'className' => AuthCommandFactory::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => AuthSanitizer::class,
                ],
            ],
        ];
        $actual   = AuthDefinitionsEnum::AuthCommandFactory->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => AuthLoginPostService::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => AuthFacade::class,
                ],
            ],
        ];
        $actual   = AuthDefinitionsEnum::AuthLoginPost->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => AuthLogoutPostService::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => AuthFacade::class,
                ],
            ],
        ];
        $actual   = AuthDefinitionsEnum::AuthLogoutPost->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => AuthRefreshPostService::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => AuthFacade::class,
                ],
            ],
        ];
        $actual   = AuthDefinitionsEnum::AuthRefreshPost->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => AuthFacade::class,
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
        ];
        $actual   = AuthDefinitionsEnum::AuthFacade->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => AuthLoginPostHandler::class,
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
        ];
        $actual   = AuthDefinitionsEnum::AuthLoginHandler->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => AuthLogoutPostHandler::class,
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
        ];
        $actual   = AuthDefinitionsEnum::AuthLogoutHandler->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => AuthRefreshPostHandler::class,
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
        ];
        $actual   = AuthDefinitionsEnum::AuthRefreshHandler->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => AuthSanitizer::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => Container::FILTER,
                ],
            ],
        ];
        $actual   = AuthDefinitionsEnum::AuthSanitizer->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => AuthLoginValidator::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => Validation::class,
                ],
            ],
        ];
        $actual   = AuthDefinitionsEnum::AuthLoginValidator->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => AuthTokenValidator::class,
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
        ];
        $actual   = AuthDefinitionsEnum::AuthTokenValidator->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => AuthTransformer::class,
        ];
        $actual   = AuthDefinitionsEnum::AuthTransformer->definition();
        $this->assertSame($expected, $actual);
    }

    public function testIsShared(): void
    {
        $actual = AuthDefinitionsEnum::AuthCommandFactory->isShared();
        $this->assertFalse($actual);

        $actual = AuthDefinitionsEnum::AuthLoginPost->isShared();
        $this->assertFalse($actual);

        $actual = AuthDefinitionsEnum::AuthLogoutPost->isShared();
        $this->assertFalse($actual);

        $actual = AuthDefinitionsEnum::AuthRefreshPost->isShared();
        $this->assertFalse($actual);

        $actual = AuthDefinitionsEnum::AuthFacade->isShared();
        $this->assertFalse($actual);

        $actual = AuthDefinitionsEnum::AuthLoginHandler->isShared();
        $this->assertFalse($actual);

        $actual = AuthDefinitionsEnum::AuthLogoutHandler->isShared();
        $this->assertFalse($actual);

        $actual = AuthDefinitionsEnum::AuthRefreshHandler->isShared();
        $this->assertFalse($actual);

        $actual = AuthDefinitionsEnum::AuthSanitizer->isShared();
        $this->assertTrue($actual);

        $actual = AuthDefinitionsEnum::AuthLoginValidator->isShared();
        $this->assertFalse($actual);

        $actual = AuthDefinitionsEnum::AuthTokenValidator->isShared();
        $this->assertFalse($actual);

        $actual = AuthDefinitionsEnum::AuthTransformer->isShared();
        $this->assertFalse($actual);
    }

    public function testValues(): void
    {
        $expected = AuthCommandFactory::class;
        $actual   = AuthDefinitionsEnum::AuthCommandFactory->value;
        $this->assertSame($expected, $actual);

        $expected = AuthLoginPostService::class;
        $actual   = AuthDefinitionsEnum::AuthLoginPost->value;
        $this->assertSame($expected, $actual);

        $expected = AuthLogoutPostService::class;
        $actual   = AuthDefinitionsEnum::AuthLogoutPost->value;
        $this->assertSame($expected, $actual);

        $expected = AuthRefreshPostService::class;
        $actual   = AuthDefinitionsEnum::AuthRefreshPost->value;
        $this->assertSame($expected, $actual);

        $expected = AuthFacade::class;
        $actual   = AuthDefinitionsEnum::AuthFacade->value;
        $this->assertSame($expected, $actual);

        $expected = AuthLoginPostHandler::class;
        $actual   = AuthDefinitionsEnum::AuthLoginHandler->value;
        $this->assertSame($expected, $actual);

        $expected = AuthLogoutPostHandler::class;
        $actual   = AuthDefinitionsEnum::AuthLogoutHandler->value;
        $this->assertSame($expected, $actual);

        $expected = AuthRefreshPostHandler::class;
        $actual   = AuthDefinitionsEnum::AuthRefreshHandler->value;
        $this->assertSame($expected, $actual);

        $expected = AuthSanitizer::class;
        $actual   = AuthDefinitionsEnum::AuthSanitizer->value;
        $this->assertSame($expected, $actual);

        $expected = AuthLoginValidator::class;
        $actual   = AuthDefinitionsEnum::AuthLoginValidator->value;
        $this->assertSame($expected, $actual);

        $expected = AuthTokenValidator::class;
        $actual   = AuthDefinitionsEnum::AuthTokenValidator->value;
        $this->assertSame($expected, $actual);

        $expected = AuthTransformer::class;
        $actual   = AuthDefinitionsEnum::AuthTransformer->value;
        $this->assertSame($expected, $actual);
    }
}
