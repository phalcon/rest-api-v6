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

namespace Phalcon\Api\Tests\Unit\Container;

use Phalcon\ADR\Container\AdrProvider;
use Phalcon\Api\Container\ApiProvider;
use Phalcon\Api\Domain\Application\Auth\Facade\AuthFacade;
use Phalcon\Api\Domain\Application\Company\Facade\CompanyFacade;
use Phalcon\Api\Domain\Application\User\Facade\UserFacade;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Repository\CompanyRepositoryInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Repository\UserRepositoryInterface;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenManagerInterface;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Cache\Cache;
use Phalcon\Container\Container;
use Phalcon\Contracts\Events\Manager as EventsManagerContract;
use Phalcon\Contracts\Logger\Logger as LoggerContract;
use Phalcon\DataMapper\Pdo\Connection;

final class ApiProviderTest extends AbstractUnitTestCase
{
    public function testAutowiresTheApplicationGraph(): void
    {
        $container = $this->newContainer();

        $wanted = [
            CompanyFacade::class,
            UserFacade::class,
            AuthFacade::class,
            Connection::class,
            Cache::class,
        ];

        foreach ($wanted as $service) {
            $this->assertInstanceOf(
                $service,
                $container->get($service),
                $service . ' did not resolve'
            );
        }
    }

    public function testBindsSingleImplementationInterfaces(): void
    {
        $container = $this->newContainer();

        $bindings = [
            CompanyRepositoryInterface::class,
            UserRepositoryInterface::class,
            TokenManagerInterface::class,
        ];

        foreach ($bindings as $binding) {
            $this->assertInstanceOf(
                $binding,
                $container->get($binding),
                $binding . ' was not bound to a concrete'
            );
        }
    }

    public function testExtendsRatherThanRedefinesTheEventsManager(): void
    {
        $container = $this->newContainer();

        $manager = $container->get(EventsManagerContract::class);

        $this->assertInstanceOf(EventsManagerContract::class, $manager);
        $this->assertTrue($manager->hasListeners('user'));
        $this->assertTrue($manager->hasListeners('company'));
    }

    public function testReplacesTheNoopLogger(): void
    {
        $container = $this->newContainer();

        $logger = $container->get(LoggerContract::class);

        $this->assertInstanceOf(LoggerContract::class, $logger);
        $this->assertNotEmpty($logger->getAdapters());
    }

    private function newContainer(): Container
    {
        $container = new Container();

        (new AdrProvider())->provide($container);
        (new ApiProvider())->provide($container);

        return $container;
    }
}
