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

namespace Phalcon\Api\Tests\Unit\Domain\Infrastructure\Providers;

use Phalcon\Api\Domain\Infrastructure\Container;
use Phalcon\Api\Domain\Infrastructure\Providers\RouterProvider;
use Phalcon\Api\Tests\AbstractUnitTestCase;
use Phalcon\Mvc\Micro;

final class RouterProviderTest extends AbstractUnitTestCase
{
    public function testCheckRegistration(): void
    {
        $application = new Micro($this->container);
        $this->container->setShared(Container::APPLICATION, $application);

        $provider = new RouterProvider();
        $provider->register($this->container);

        $router = $application->getRouter();
        $routes = $router->getRoutes();

        $data = [
            [
                'method'  => 'POST',
                'pattern' => '/auth/login',
            ],
            [
                'method'  => 'POST',
                'pattern' => '/auth/logout',
            ],
            [
                'method'  => 'POST',
                'pattern' => '/auth/refresh',
            ],
            [
                'method'  => 'DELETE',
                'pattern' => '/company',
            ],
            [
                'method'  => 'GET',
                'pattern' => '/company',
            ],
            [
                'method'  => 'GET',
                'pattern' => '/company/all',
            ],
            [
                'method'  => 'POST',
                'pattern' => '/company',
            ],
            [
                'method'  => 'PUT',
                'pattern' => '/company',
            ],
            [
                'method'  => 'DELETE',
                'pattern' => '/user',
            ],
            [
                'method'  => 'GET',
                'pattern' => '/user',
            ],
            [
                'method'  => 'POST',
                'pattern' => '/user',
            ],
            [
                'method'  => 'PUT',
                'pattern' => '/user',
            ],
            [
                'method'  => 'GET',
                'pattern' => '/health',
            ],
        ];

        $expected = count($data);
        $actual   = count($routes);
        $this->assertSame($expected, $actual);

        foreach ($data as $index => $route) {
            $expected = $route['method'];
            $actual   = $routes[$index]->getHttpMethods();
            $this->assertSame($expected, $actual);

            $expected = $route['pattern'];
            $actual   = $routes[$index]->getPattern();
            $this->assertSame($expected, $actual);
        }
    }
}
