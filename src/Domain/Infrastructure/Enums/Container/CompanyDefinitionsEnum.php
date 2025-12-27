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
use Phalcon\DataMapper\Pdo\Connection;
use Phalcon\Filter\Validation;
use Phalcon\Support\Registry;

/**
 * @phpstan-import-type TService from Container
 */
enum CompanyDefinitionsEnum: string implements DefinitionsEnumInterface
{
    case CompanyCommandFactory  = CompanyCommandFactory::class;
    case CompanyDelete          = CompanyDeleteService::class;
    case CompanyGet             = CompanyGetService::class;
    case CompanyGetMany         = CompanyGetManyService::class;
    case CompanyPost            = CompanyPostService::class;
    case CompanyPut             = CompanyPutService::class;
    case CompanyFacade          = CompanyFacade::class;
    case CompanyDeleteHandler   = CompanyDeleteHandler::class;
    case CompanyGetHandler      = CompanyGetHandler::class;
    case CompanyGetManyHandler  = CompanyGetManyHandler::class;
    case CompanyPostHandler     = CompanyPostHandler::class;
    case CompanyPutHandler      = CompanyPutHandler::class;
    case CompanyMapper          = CompanyMapper::class;
    case CompanyRepository      = CompanyRepository::class;
    case CompanySanitizer       = CompanySanitizer::class;
    case CompanyValidator       = CompanyValidator::class;
    case CompanyValidatorUpdate = CompanyValidatorUpdate::class;

    /**
     * @return TService
     */
    public function definition(): array
    {
        return match ($this) {
            self::CompanyCommandFactory  => [
                'className' => $this->value,
                'arguments' => [
                    [
                        'type' => 'service',
                        'name' => CompanySanitizer::class,
                    ],
                ],
            ],
            self::CompanyDelete,
            self::CompanyGet,
            self::CompanyGetMany,
            self::CompanyPost,
            self::CompanyPut             => [
                'className' => $this->value,
                'arguments' => [
                    [
                        'type' => 'service',
                        'name' => CompanyFacade::class,
                    ],
                ],
            ],
            self::CompanyFacade          => [
                'className' => $this->value,
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
            ],
            self::CompanyDeleteHandler,
            self::CompanyGetHandler,
            self::CompanyGetManyHandler  => [
                'className' => $this->value,
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
            ],
            self::CompanyPostHandler     => $this->getServicePutPost(
                $this->value,
                CompanyValidator::class
            ),
            self::CompanyPutHandler      => $this->getServicePutPost(
                $this->value,
                CompanyValidatorUpdate::class
            ),
            self::CompanyMapper          => [
                'className' => $this->value,
            ],
            self::CompanyRepository      => [
                'className' => $this->value,
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
            ],
            self::CompanySanitizer       => [
                'className' => $this->value,
                'arguments' => [
                    [
                        'type' => 'service',
                        'name' => Container::FILTER,
                    ],
                ],
            ],
            self::CompanyValidator,
            self::CompanyValidatorUpdate => [
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
            self::CompanyMapper,
            self::CompanyRepository,
            self::CompanySanitizer => true,
            default                => false,
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
    }
}
