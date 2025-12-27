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

namespace Phalcon\Api\Domain\Infrastructure;

use Phalcon\Api\Domain\Infrastructure\CommandBus\ContainerHandlerLocator;
use Phalcon\Api\Domain\Infrastructure\Constants\Cache as CacheConstants;
use Phalcon\Api\Domain\Infrastructure\Enums\Container\AuthDefinitionsEnum;
use Phalcon\Api\Domain\Infrastructure\Enums\Container\CommonDefinitionsEnum;
use Phalcon\Api\Domain\Infrastructure\Enums\Container\CompanyDefinitionsEnum;
use Phalcon\Api\Domain\Infrastructure\Enums\Container\DefinitionsEnumInterface;
use Phalcon\Api\Domain\Infrastructure\Enums\Container\UserDefinitionsEnum;
use Phalcon\Api\Domain\Infrastructure\Env\EnvManager;
use Phalcon\Api\Domain\Infrastructure\Listeners\DbErrorListener;
use Phalcon\Cache\AdapterFactory;
use Phalcon\Cache\Cache;
use Phalcon\DataMapper\Pdo\Connection;
use Phalcon\Di\Di;
use Phalcon\Di\Service;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Filter\FilterFactory;
use Phalcon\Logger\Adapter\Stream;
use Phalcon\Logger\Logger;
use Phalcon\Storage\SerializerFactory;
use Psr\Log\LoggerInterface;

use function sprintf;

/**
 *
 * @phpstan-type TServiceParameter array{
 *     type: 'parameter',
 *     value: mixed
 * }
 * @phpstan-type TServiceService array{
 *     type: 'service',
 *     name: string
 * }
 * @phpstan-type TServiceArguments array<array-key, TServiceParameter|TServiceService>
 * @phpstan-type TServiceCall array{
 *     method: string,
 *     arguments: TServiceArguments
 * }
 *
 * @phpstan-type TService array{
 *     className: string,
 *     arguments?: TServiceArguments,
 *     calls?: array<array-key, TServiceCall>
 * }
 */
class Container extends Di
{
    /** @var string */
    public const APPLICATION = 'application';
    /** @var string */
    public const EVENTS_MANAGER = 'eventsManager';
    /** @var string */
    public const FILTER = 'filter';
    /** @var string */
    public const REQUEST = 'request';
    /** @var string */
    public const RESPONSE = 'response';
    /** @var string */
    public const ROUTER = 'router';
    /** @var string */
    public const TIME = 'time';

    public function __construct()
    {
        /**
         * Trying to keep this as clean and as configurable as possible.
         *
         * The enumerations that hold the definition for each service, also
         * keep the sharable state (shared service or not) and also reference
         * this file's constants for their values where necessary. Therefore,
         * we can register the services with a simple loop for each enumeration.
         *
         *
         * Base services
         */
        $services = [
            Cache::class                   => $this->getServiceCache(),
            Connection::class              => $this->getServiceConnection(),
            self::EVENTS_MANAGER           => $this->getServiceEventsManager(),
            self::FILTER                   => $this->getServiceFilter(),
            Logger::class                  => $this->getServiceLogger(),
            ContainerHandlerLocator::class => $this->getServiceHandlerLocator(),
        ];
        /**
         * Common services
         */
        $services = $this->registerFromEnum(CommonDefinitionsEnum::class, $services);
        /**
         * Auth related services
         */
        $services = $this->registerFromEnum(AuthDefinitionsEnum::class, $services);
        /**
         * Company related services
         */
        $services = $this->registerFromEnum(CompanyDefinitionsEnum::class, $services);
        /**
         * User related services
         */
        $services = $this->registerFromEnum(UserDefinitionsEnum::class, $services);

        $this->services = $services;

        parent::__construct();
    }

    /**
     * @return Service
     */
    private function getServiceCache(): Service
    {
        return new Service(
            function () {
                /** @var EnvManager $env */
                $env = $this->getShared(EnvManager::class);

                /** @var string $prefix */
                $prefix = $env->get('CACHE_PREFIX', '-rest-');
                /** @var string $host */
                $host = $env->get('CACHE_HOST', 'localhost');
                /** @var int $lifetime */
                $lifetime = $env->get('CACHE_LIFETIME', CacheConstants::CACHE_LIFETIME_DAY, 'int');
                /** @var int $index */
                $index = $env->get('CACHE_INDEX', 0, 'int');
                /** @var int $port */
                $port = $env->get('CACHE_PORT', 6379, 'int');

                $options = [
                    'host'     => $host,
                    'index'    => $index,
                    'lifetime' => $lifetime,
                    'prefix'   => $prefix,
                    'port'     => $port,
                    'uniqueId' => $prefix,
                ];

                /** @var string $adapter */
                $adapter = $env->get('CACHE_ADAPTER', 'redis');

                $serializerFactory = new SerializerFactory();
                $adapterFactory    = new AdapterFactory($serializerFactory);
                $cacheAdapter      = $adapterFactory->newInstance($adapter, $options);

                return new Cache($cacheAdapter);
            },
            true
        );
    }

    /**
     * @return Service
     */
    private function getServiceConnection(): Service
    {
        return new Service(
            function () {
                /** @var EnvManager $env */
                $env = $this->getShared(EnvManager::class);

                /** @var string $dbName */
                $dbName = $env->get('DB_NAME', 'phalcon');
                /** @var string $host */
                $host = $env->get('DB_HOST', 'rest-db');
                /** @var string $password */
                $password = $env->get('DB_PASS', 'secret');
                /** @var int $port */
                $port = $env->get('DB_PORT', 3306, 'int');
                /** @var string $username */
                $username = $env->get('DB_USER', 'root');
                /** @var string $encoding */
                $encoding = $env->get('DB_CHARSET', 'utf8');
                $queries  = ['SET NAMES utf8mb4'];
                $dsn      = sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                    $host,
                    $port,
                    $dbName,
                    $encoding
                );

                return new Connection(
                    $dsn,
                    $username,
                    $password,
                    [],
                    $queries
                );
            },
            true
        );
    }

    /**
     * @return Service
     */
    private function getServiceEventsManager(): Service
    {
        return new Service(
            function () {
                $evManager = new EventsManager();
                $evManager->enablePriorities(true);

                /** @var LoggerInterface $logger */
                $logger   = $this->getShared(Logger::class);
                $listener = new DbErrorListener($logger);

                $evManager->attach('user', $listener);
                $evManager->attach('company', $listener);

                return $evManager;
            },
            true
        );
    }

    /**
     * @return Service
     */
    private function getServiceFilter(): Service
    {
        return new Service(
            function () {
                return (new FilterFactory())->newInstance();
            },
            true
        );
    }

    /**
     * @return Service
     */
    private function getServiceHandlerLocator(): Service
    {
        return new Service(
            function () {
                return new ContainerHandlerLocator($this);
            }
        );
    }

    /**
     * @return Service
     */
    private function getServiceLogger(): Service
    {
        return new Service(
            function () {
                /** @var EnvManager $env */
                $env = $this->getShared(EnvManager::class);

                /** @var string $logName */
                $logName = $env->get('LOG_FILENAME', 'rest-api');
                /** @var string $logPath */
                $logPath = $env->get('LOG_PATH', 'storage/logs/');
                $logFile = $env->appPath($logPath) . '/' . $logName . '.log';

                return new Logger(
                    $logName,
                    [
                        'main' => new Stream($logFile),
                    ]
                );
            }
        );
    }

    /**
     * @param string                 $enum
     * @param array<string, Service> $services
     *
     * @return array<string, Service>
     */
    private function registerFromEnum(string $enum, array $services): array
    {
        /** @var DefinitionsEnumInterface[] $items */
        $items = $enum::cases();
        foreach ($items as $definition) {
            /** @var string $serviceName */
            $serviceName = $definition->value;

            $services[$serviceName] = new Service(
                $definition->definition(),
                $definition->isShared()
            );
        }

        return $services;
    }
}
