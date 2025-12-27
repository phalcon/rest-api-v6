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

use Phalcon\Api\Domain\Application\Company\Command\CompanyCommandFactory;
use Phalcon\Api\Domain\Application\Company\Facade\CompanyFacade;
use Phalcon\Api\Domain\Application\Company\Handler\CompanyDeleteHandler;
use Phalcon\Api\Domain\Application\Company\Handler\CompanyGetHandler;
use Phalcon\Api\Domain\Application\Company\Handler\CompanyGetManyHandler;
use Phalcon\Api\Domain\Application\Company\Handler\CompanyPostHandler;
use Phalcon\Api\Domain\Application\Company\Handler\CompanyPutHandler;
use Phalcon\Api\Domain\Application\Company\Service\CompanyDeleteService;
use Phalcon\Api\Domain\Application\Company\Service\CompanyGetManyService;
use Phalcon\Api\Domain\Application\Company\Service\CompanyGetService;
use Phalcon\Api\Domain\Application\Company\Service\CompanyPostService;
use Phalcon\Api\Domain\Application\Company\Service\CompanyPutService;
use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandBus;
use Phalcon\Api\Domain\Infrastructure\Container;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Mapper\CompanyMapper;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Repository\CompanyRepository;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Sanitizer\CompanySanitizer;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Validator\CompanyValidator;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Validator\CompanyValidatorUpdate;
use Phalcon\Api\Domain\Infrastructure\DataSource\Transformer\Transformer;
use Phalcon\Api\Domain\Infrastructure\Encryption\Security;
use Phalcon\Api\Domain\Infrastructure\Enums\Container\CompanyDefinitionsEnum;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\DataMapper\Pdo\Connection;
use Phalcon\Filter\Validation;
use Phalcon\Support\Registry;

final class CompanyDefinitionsEnumTest extends AbstractUnitTestCase
{
    public function testDefinition(): void
    {
        $expected = [
            'className' => CompanyCommandFactory::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => CompanySanitizer::class,
                ],
            ],
        ];
        $actual   = CompanyDefinitionsEnum::CompanyCommandFactory->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => CompanyDeleteService::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => CompanyFacade::class,
                ],
            ],
        ];
        $actual   = CompanyDefinitionsEnum::CompanyDelete->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => CompanyGetService::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => CompanyFacade::class,
                ],
            ],
        ];
        $actual   = CompanyDefinitionsEnum::CompanyGet->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => CompanyGetManyService::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => CompanyFacade::class,
                ],
            ],
        ];
        $actual   = CompanyDefinitionsEnum::CompanyGetMany->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => CompanyPostService::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => CompanyFacade::class,
                ],
            ],
        ];
        $actual   = CompanyDefinitionsEnum::CompanyPost->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => CompanyPutService::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => CompanyFacade::class,
                ],
            ],
        ];
        $actual   = CompanyDefinitionsEnum::CompanyPut->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => CompanyFacade::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => CommandBus::class,
                ],
                [
                    'type' => 'service',
                    'name' => CompanyCommandFactory::class,
                ],
            ],
        ];
        $actual   = CompanyDefinitionsEnum::CompanyFacade->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => CompanyDeleteHandler::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => CompanyRepository::class,
                ],
                [
                    'type' => 'service',
                    'name' => Transformer::class,
                ],
            ],
        ];
        $actual   = CompanyDefinitionsEnum::CompanyDeleteHandler->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => CompanyGetHandler::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => CompanyRepository::class,
                ],
                [
                    'type' => 'service',
                    'name' => Transformer::class,
                ],
            ],
        ];
        $actual   = CompanyDefinitionsEnum::CompanyGetHandler->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => CompanyGetManyHandler::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => CompanyRepository::class,
                ],
                [
                    'type' => 'service',
                    'name' => Transformer::class,
                ],
            ],
        ];
        $actual   = CompanyDefinitionsEnum::CompanyGetManyHandler->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => CompanyPostHandler::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => CompanyValidator::class,
                ],
                [
                    'type' => 'service',
                    'name' => CompanyMapper::class,
                ],
                [
                    'type' => 'service',
                    'name' => CompanyRepository::class,
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
        $actual   = CompanyDefinitionsEnum::CompanyPostHandler->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => CompanyPutHandler::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => CompanyValidatorUpdate::class,
                ],
                [
                    'type' => 'service',
                    'name' => CompanyMapper::class,
                ],
                [
                    'type' => 'service',
                    'name' => CompanyRepository::class,
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
        $actual   = CompanyDefinitionsEnum::CompanyPutHandler->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => CompanyMapper::class,
        ];
        $actual   = CompanyDefinitionsEnum::CompanyMapper->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => CompanyRepository::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => Connection::class,
                ],
                [
                    'type' => 'service',
                    'name' => CompanyMapper::class,
                ],
            ],
        ];
        $actual   = CompanyDefinitionsEnum::CompanyRepository->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => CompanySanitizer::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => Container::FILTER,
                ],
            ],
        ];
        $actual   = CompanyDefinitionsEnum::CompanySanitizer->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => CompanyValidator::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => Validation::class,
                ],
            ],
        ];
        $actual   = CompanyDefinitionsEnum::CompanyValidator->definition();
        $this->assertSame($expected, $actual);

        $expected = [
            'className' => CompanyValidatorUpdate::class,
            'arguments' => [
                [
                    'type' => 'service',
                    'name' => Validation::class,
                ],
            ],
        ];
        $actual   = CompanyDefinitionsEnum::CompanyValidatorUpdate->definition();
        $this->assertSame($expected, $actual);
    }

    public function testIsShared(): void
    {
        $actual = CompanyDefinitionsEnum::CompanyCommandFactory->isShared();
        $this->assertFalse($actual);

        $actual = CompanyDefinitionsEnum::CompanyDelete->isShared();
        $this->assertFalse($actual);

        $actual = CompanyDefinitionsEnum::CompanyGet->isShared();
        $this->assertFalse($actual);

        $actual = CompanyDefinitionsEnum::CompanyGetMany->isShared();
        $this->assertFalse($actual);

        $actual = CompanyDefinitionsEnum::CompanyPost->isShared();
        $this->assertFalse($actual);

        $actual = CompanyDefinitionsEnum::CompanyPut->isShared();
        $this->assertFalse($actual);

        $actual = CompanyDefinitionsEnum::CompanyFacade->isShared();
        $this->assertFalse($actual);

        $actual = CompanyDefinitionsEnum::CompanyDeleteHandler->isShared();
        $this->assertFalse($actual);

        $actual = CompanyDefinitionsEnum::CompanyGetHandler->isShared();
        $this->assertFalse($actual);

        $actual = CompanyDefinitionsEnum::CompanyPostHandler->isShared();
        $this->assertFalse($actual);

        $actual = CompanyDefinitionsEnum::CompanyPutHandler->isShared();
        $this->assertFalse($actual);

        $actual = CompanyDefinitionsEnum::CompanyMapper->isShared();
        $this->assertTrue($actual);

        $actual = CompanyDefinitionsEnum::CompanyRepository->isShared();
        $this->assertTrue($actual);

        $actual = CompanyDefinitionsEnum::CompanySanitizer->isShared();
        $this->assertTrue($actual);

        $actual = CompanyDefinitionsEnum::CompanyValidator->isShared();
        $this->assertFalse($actual);

        $actual = CompanyDefinitionsEnum::CompanyValidatorUpdate->isShared();
        $this->assertFalse($actual);
    }

    public function testValue(): void
    {
        $expected = CompanyCommandFactory::class;
        $actual   = CompanyDefinitionsEnum::CompanyCommandFactory->value;
        $this->assertSame($expected, $actual);

        $expected = CompanyDeleteService::class;
        $actual   = CompanyDefinitionsEnum::CompanyDelete->value;
        $this->assertSame($expected, $actual);

        $expected = CompanyGetService::class;
        $actual   = CompanyDefinitionsEnum::CompanyGet->value;
        $this->assertSame($expected, $actual);

        $expected = CompanyGetManyService::class;
        $actual   = CompanyDefinitionsEnum::CompanyGetMany->value;
        $this->assertSame($expected, $actual);

        $expected = CompanyPostService::class;
        $actual   = CompanyDefinitionsEnum::CompanyPost->value;
        $this->assertSame($expected, $actual);

        $expected = CompanyPutService::class;
        $actual   = CompanyDefinitionsEnum::CompanyPut->value;
        $this->assertSame($expected, $actual);

        $expected = CompanyFacade::class;
        $actual   = CompanyDefinitionsEnum::CompanyFacade->value;
        $this->assertSame($expected, $actual);

        $expected = CompanyDeleteHandler::class;
        $actual   = CompanyDefinitionsEnum::CompanyDeleteHandler->value;
        $this->assertSame($expected, $actual);

        $expected = CompanyGetHandler::class;
        $actual   = CompanyDefinitionsEnum::CompanyGetHandler->value;
        $this->assertSame($expected, $actual);

        $expected = CompanyPostHandler::class;
        $actual   = CompanyDefinitionsEnum::CompanyPostHandler->value;
        $this->assertSame($expected, $actual);

        $expected = CompanyPutHandler::class;
        $actual   = CompanyDefinitionsEnum::CompanyPutHandler->value;
        $this->assertSame($expected, $actual);

        $expected = CompanyMapper::class;
        $actual   = CompanyDefinitionsEnum::CompanyMapper->value;
        $this->assertSame($expected, $actual);

        $expected = CompanyRepository::class;
        $actual   = CompanyDefinitionsEnum::CompanyRepository->value;
        $this->assertSame($expected, $actual);

        $expected = CompanySanitizer::class;
        $actual   = CompanyDefinitionsEnum::CompanySanitizer->value;
        $this->assertSame($expected, $actual);

        $expected = CompanyValidator::class;
        $actual   = CompanyDefinitionsEnum::CompanyValidator->value;
        $this->assertSame($expected, $actual);

        $expected = CompanyValidatorUpdate::class;
        $actual   = CompanyDefinitionsEnum::CompanyValidatorUpdate->value;
        $this->assertSame($expected, $actual);
    }
}
