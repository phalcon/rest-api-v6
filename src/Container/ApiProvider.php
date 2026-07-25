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

namespace Phalcon\Api\Container;

use Phalcon\ADR\Container\AdrProvider;
use Phalcon\ADR\ErrorResponder;
use Phalcon\Api\Action\Auth\PostAuthLogin;
use Phalcon\Api\Action\Auth\PostAuthLogout;
use Phalcon\Api\Action\Auth\PostAuthRefresh;
use Phalcon\Api\Action\Company\DeleteCompany;
use Phalcon\Api\Action\Company\GetCompany;
use Phalcon\Api\Action\Company\GetCompanyAll;
use Phalcon\Api\Action\Company\PostCompany;
use Phalcon\Api\Action\Company\PutCompany;
use Phalcon\Api\Action\User\DeleteUser;
use Phalcon\Api\Action\User\GetUser;
use Phalcon\Api\Action\User\PostUser;
use Phalcon\Api\Action\User\PutUser;
use Phalcon\Api\Domain\Application\Auth\Command\AuthCommandFactory;
use Phalcon\Api\Domain\Application\Auth\Command\AuthCommandFactoryInterface;
use Phalcon\Api\Domain\Application\Auth\Handler\AuthLoginPostHandler;
use Phalcon\Api\Domain\Application\Auth\Handler\AuthLogoutPostHandler;
use Phalcon\Api\Domain\Application\Auth\Handler\AuthRefreshPostHandler;
use Phalcon\Api\Domain\Application\Auth\Service\AuthLoginPostService;
use Phalcon\Api\Domain\Application\Auth\Service\AuthLogoutPostService;
use Phalcon\Api\Domain\Application\Auth\Service\AuthRefreshPostService;
use Phalcon\Api\Domain\Application\Company\Command\CompanyCommandFactory;
use Phalcon\Api\Domain\Application\Company\Command\CompanyCommandFactoryInterface;
use Phalcon\Api\Domain\Application\Company\Handler\CompanyPostHandler;
use Phalcon\Api\Domain\Application\Company\Handler\CompanyPutHandler;
use Phalcon\Api\Domain\Application\Company\Service\CompanyDeleteService;
use Phalcon\Api\Domain\Application\Company\Service\CompanyGetManyService;
use Phalcon\Api\Domain\Application\Company\Service\CompanyGetService;
use Phalcon\Api\Domain\Application\Company\Service\CompanyPostService;
use Phalcon\Api\Domain\Application\Company\Service\CompanyPutService;
use Phalcon\Api\Domain\Application\User\Command\UserCommandFactory;
use Phalcon\Api\Domain\Application\User\Command\UserCommandFactoryInterface;
use Phalcon\Api\Domain\Application\User\Handler\UserPostHandler;
use Phalcon\Api\Domain\Application\User\Handler\UserPutHandler;
use Phalcon\Api\Domain\Application\User\Service\UserDeleteService;
use Phalcon\Api\Domain\Application\User\Service\UserGetService;
use Phalcon\Api\Domain\Application\User\Service\UserPostService;
use Phalcon\Api\Domain\Application\User\Service\UserPutService;
use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandBus;
use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandBusInterface;
use Phalcon\Api\Domain\Infrastructure\CommandBus\ContainerHandlerLocator;
use Phalcon\Api\Domain\Infrastructure\CommandBus\HandlerLocatorInterface;
use Phalcon\Api\Domain\Infrastructure\Constants\Cache as CacheConstants;
use Phalcon\Api\Domain\Infrastructure\DataSource\Auth\Sanitizer\AuthSanitizer;
use Phalcon\Api\Domain\Infrastructure\DataSource\Auth\Validator\AuthLoginValidator;
use Phalcon\Api\Domain\Infrastructure\DataSource\Auth\Validator\AuthTokenValidator;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Mapper\CompanyMapper;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Mapper\CompanyMapperInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Repository\CompanyRepository;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Repository\CompanyRepositoryInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Sanitizer\CompanySanitizer;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Validator\CompanyValidator;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Validator\CompanyValidatorUpdate;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Mapper\UserMapper;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Mapper\UserMapperInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Repository\UserRepository;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Repository\UserRepositoryInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Sanitizer\UserSanitizer;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Validator\UserValidator;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Validator\UserValidatorUpdate;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenCache;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenCacheInterface;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenManager;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenManagerInterface;
use Phalcon\Api\Domain\Infrastructure\Env\EnvManager;
use Phalcon\Api\Domain\Infrastructure\Listeners\DbErrorListener;
use Phalcon\Api\Responder\ApiErrorResponder;
use Phalcon\Api\Responder\ApiResponder;
use Phalcon\Cache\AdapterFactory;
use Phalcon\Cache\Cache;
use Phalcon\Container\Resolver\Lazy\Get as LazyGet;
use Phalcon\Contracts\ADR\Responder\Responder as ResponderContract;
use Phalcon\Contracts\Container\Service\Collection;
use Phalcon\Contracts\Events\Manager as EventsManagerContract;
use Phalcon\Contracts\Logger\Logger as LoggerContract;
use Phalcon\DataMapper\Pdo\Connection;
use Phalcon\Events\ManagerInterface as EventsManagerInterface;
use Phalcon\Filter\FilterFactory;
use Phalcon\Filter\FilterInterface;
use Phalcon\Filter\Validation;
use Phalcon\Filter\Validation\ValidationInterface;
use Phalcon\Logger\Adapter\Stream;
use Phalcon\Logger\Logger;
use Phalcon\Logger\LoggerInterface;
use Phalcon\Storage\SerializerFactory;

use function sprintf;

/**
 * Registers the application on top of the ADR seams.
 *
 * Everything the ADR layer needs - events manager, request, response,
 * dispatcher, router, attribute filter, emitter - is inherited from
 * AdrProvider already registered. This class contributes only what the
 * application adds: services that need factory logic, the interface
 * bindings autowiring cannot infer, and the logger takeover.
 */
class ApiProvider extends AdrProvider
{
    /**
     * @param Collection $container
     *
     * @return void
     */
    public function provide(Collection $container): void
    {
        parent::provide($container);

        $this->registerEnvironment($container);
        $this->registerDatabase($container);
        $this->registerCache($container);
        $this->registerLogger($container);
        $this->registerEvents($container);
        $this->registerBindings($container);
        $this->registerCommandFactories($container);
        $this->registerHandlers($container);
        $this->registerResponders($container);
        $this->registerActions($container);
    }

    /**
     * Each action delegates to one domain service. DomainInterface is
     * implemented once, by an abstract, so autowiring cannot choose - every
     * action names its own service.
     *
     * @param Collection $container
     *
     * @return void
     */
    protected function registerActions(Collection $container): void
    {
        $pins = [
            PostAuthLogin::class   => AuthLoginPostService::class,
            PostAuthLogout::class  => AuthLogoutPostService::class,
            PostAuthRefresh::class => AuthRefreshPostService::class,
            DeleteCompany::class   => CompanyDeleteService::class,
            GetCompany::class      => CompanyGetService::class,
            GetCompanyAll::class   => CompanyGetManyService::class,
            PostCompany::class     => CompanyPostService::class,
            PutCompany::class      => CompanyPutService::class,
            DeleteUser::class      => UserDeleteService::class,
            GetUser::class         => UserGetService::class,
            PostUser::class        => UserPostService::class,
            PutUser::class         => UserPutService::class,
        ];

        foreach ($pins as $actionClass => $serviceClass) {
            $container
                ->set($actionClass, $actionClass)
                ->setArgument('service', new LazyGet($serviceClass))
            ;
        }
    }

    /**
     * Interfaces with exactly one implementation. Autowiring resolves
     * concrete types on its own; it cannot pick an implementation for an
     * interface, so each of these needs naming once.
     *
     * @param Collection $container
     *
     * @return void
     */
    protected function registerBindings(Collection $container): void
    {
        /**
         * The command bus locates handlers through the container itself, so
         * it cannot be autowired - the container has to hand itself over.
         */
        $container->bind(CommandBusInterface::class, CommandBus::class);
        $container->set(
            HandlerLocatorInterface::class,
            function (Collection $ioc) {
                return new ContainerHandlerLocator($ioc);
            }
        );

        /**
         * Every validator composes a Phalcon validation object; it takes no
         * constructor arguments, so the binding is all autowiring needs.
         */
        $container->bind(ValidationInterface::class, Validation::class);

        $container->bind(CompanyMapperInterface::class, CompanyMapper::class);
        $container->bind(CompanyRepositoryInterface::class, CompanyRepository::class);
        $container->bind(TokenCacheInterface::class, TokenCache::class);
        $container->bind(TokenManagerInterface::class, TokenManager::class);
        $container->bind(UserMapperInterface::class, UserMapper::class);
        $container->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Redis (or whichever adapter the environment names) behind the cache.
     *
     * @param Collection $container
     *
     * @return void
     */
    protected function registerCache(Collection $container): void
    {
        $container->set(
            Cache::class,
            function (Collection $ioc) {
                /** @var EnvManager $env */
                $env = $ioc->get(EnvManager::class);

                /** @var string $prefix */
                $prefix = $env->get('CACHE_PREFIX', '-rest-');
                /** @var string $host */
                $host = $env->get('CACHE_HOST', 'localhost');
                /** @var int $lifetime */
                $lifetime = $env->get(
                    'CACHE_LIFETIME',
                    CacheConstants::CACHE_LIFETIME_DAY,
                    'int'
                );
                /** @var int $index */
                $index = $env->get('CACHE_INDEX', 0, 'int');
                /** @var int $port */
                $port = $env->get('CACHE_PORT', 6379, 'int');
                /** @var string $adapter */
                $adapter = $env->get('CACHE_ADAPTER', 'redis');

                $options = [
                    'host'     => $host,
                    'index'    => $index,
                    'lifetime' => $lifetime,
                    'prefix'   => $prefix,
                    'port'     => $port,
                    'uniqueId' => $prefix,
                ];

                $adapterFactory = new AdapterFactory(new SerializerFactory());

                return new Cache($adapterFactory->newInstance($adapter, $options));
            }
        );
    }

    /**
     * SanitizerInterface is implemented once, by AbstractSanitizer, which
     * every concrete sanitizer extends. Autowiring therefore finds only an
     * abstract it cannot instantiate, so each command factory names the
     * sanitizer it wants.
     *
     * A plain class-string will not work: resolveArgs() resolves only
     * Resolvable arguments and passes anything else through as a literal.
     * LazyGet resolves through the container at build time.
     *
     * @param Collection $container
     *
     * @return void
     */
    protected function registerCommandFactories(Collection $container): void
    {
        $pins = [
            [AuthCommandFactoryInterface::class, AuthCommandFactory::class, AuthSanitizer::class],
            [CompanyCommandFactoryInterface::class, CompanyCommandFactory::class, CompanySanitizer::class],
            [UserCommandFactoryInterface::class, UserCommandFactory::class, UserSanitizer::class],
        ];

        /**
         * bind() is set() keyed by the interface name, so a definition
         * registered against the concrete is never consulted when something
         * asks for the interface. Both keys carry the pin.
         */
        foreach ($pins as [$interface, $concrete, $sanitizer]) {
            $container
                ->bind($interface, $concrete)
                ->setArgument('sanitizer', new LazyGet($sanitizer))
            ;
            $container
                ->set($concrete, $concrete)
                ->setArgument('sanitizer', new LazyGet($sanitizer))
            ;
        }
    }

    /**
     * @param Collection $container
     *
     * @return void
     */
    protected function registerDatabase(Collection $container): void
    {
        $container->set(
            Connection::class,
            function (Collection $ioc) {
                /** @var EnvManager $env */
                $env = $ioc->get(EnvManager::class);

                /** @var string $dbName */
                $dbName = $env->get('DB_NAME', 'phalcon');
                /** @var string $host */
                $host = $env->get('DB_HOST', 'rest-api-db');
                /** @var string $password */
                $password = $env->get('DB_PASS', 'secret');
                /** @var int $port */
                $port = $env->get('DB_PORT', 3306, 'int');
                /** @var string $username */
                $username = $env->get('DB_USER', 'root');
                /** @var string $encoding */
                $encoding = $env->get('DB_CHARSET', 'utf8');

                $dsn = sprintf(
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
                    ['SET NAMES utf8mb4']
                );
            }
        );
    }

    /**
     * @param Collection $container
     *
     * @return void
     */
    protected function registerEnvironment(Collection $container): void
    {
        $container->set(EnvManager::class, EnvManager::class);

        /**
         * Registered against the interface so autowiring can inject it, with
         * the short alias kept for parity with the rest of the framework.
         */
        $container->set(
            FilterInterface::class,
            function () {
                return (new FilterFactory())->newInstance();
            }
        );
        $container->setAlias(FilterInterface::class, 'filter');
    }

    /**
     * The events manager is already registered by AdrProvider; extend it
     * rather than redefining it, so the ADR layer keeps the instance it
     * expects and the application only attaches its listener.
     *
     * @param Collection $container
     *
     * @return void
     */
    protected function registerEvents(Collection $container): void
    {
        /**
         * The domain handlers type-hint Phalcon\Events\ManagerInterface,
         * which extends the ADR contract AdrProvider registers under. Alias
         * rather than register, so both names resolve to the one instance
         * and the listener attached below is present either way.
         */
        $container->setAlias(EventsManagerContract::class, EventsManagerInterface::class);

        $container->extend(
            EventsManagerContract::class,
            function (EventsManagerContract $manager, Collection $ioc) {
                $manager->enablePriorities(true);

                /**
                 * registerLogger() puts a Phalcon\Logger\Logger behind this
                 * key, which satisfies the narrower interface the listener
                 * asks for; the container itself can only promise mixed.
                 */
                /** @var LoggerInterface $logger */
                $logger = $ioc->get(LoggerContract::class);

                $listener = new DbErrorListener($logger);

                $manager->attach('user', $listener);
                $manager->attach('company', $listener);

                return $manager;
            }
        );
    }

    /**
     * ValidatorInterface is implemented once, by AbstractValidator, which
     * every concrete validator extends. Autowiring therefore finds only an
     * abstract it cannot instantiate, so each handler names the validator
     * it wants - insert rules versus update rules for the put/post pairs,
     * and login versus token rules for auth.
     *
     * Handlers are resolved by concrete class name, since the command bus
     * derives the handler from the command class rather than an interface,
     * so a single key per handler is enough here.
     *
     * @param Collection $container
     *
     * @return void
     */
    protected function registerHandlers(Collection $container): void
    {
        $pins = [
            AuthLoginPostHandler::class   => AuthLoginValidator::class,
            AuthLogoutPostHandler::class  => AuthTokenValidator::class,
            AuthRefreshPostHandler::class => AuthTokenValidator::class,
            CompanyPostHandler::class     => CompanyValidator::class,
            CompanyPutHandler::class      => CompanyValidatorUpdate::class,
            UserPostHandler::class        => UserValidator::class,
            UserPutHandler::class         => UserValidatorUpdate::class,
        ];

        foreach ($pins as $handlerClass => $validatorClass) {
            $container
                ->set($handlerClass, $handlerClass)
                ->setArgument('validator', new LazyGet($validatorClass))
            ;
        }
    }

    /**
     * AdrProvider registers a Noop logger as a null sink; swap in the
     * stream adapter the application actually writes to.
     *
     * @param Collection $container
     *
     * @return void
     */
    protected function registerLogger(Collection $container): void
    {
        $container->set(
            LoggerContract::class,
            function (Collection $ioc) {
                /** @var EnvManager $env */
                $env = $ioc->get(EnvManager::class);

                /** @var string $logName */
                $logName = $env->get('LOG_FILENAME', 'rest-api');
                /** @var string $logPath */
                $logPath = $env->get('LOG_PATH', 'storage/logs/');
                $logFile = $env->appPath($logPath) . '/' . $logName . '.log';

                return new Logger($logName, ['main' => new Stream($logFile)]);
            }
        );
    }

    /**
     * ApiResponder answers successful requests; ErrorResponder is final, so
     * it is configured with ApiErrorResponder as its chain rather than
     * subclassed. Application::handle() resolves ErrorResponder by class
     * name, which is why it is registered under that key.
     *
     * @param Collection $container
     *
     * @return void
     */
    protected function registerResponders(Collection $container): void
    {
        $container->bind(ResponderContract::class, ApiResponder::class);

        $container->set(
            ErrorResponder::class,
            function (Collection $ioc) {
                /** @var LoggerContract $logger */
                $logger = $ioc->get(LoggerContract::class);

                return new ErrorResponder(new ApiErrorResponder(), $logger);
            }
        );
    }
}
