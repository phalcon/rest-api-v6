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

namespace Phalcon\Api\Domain\Infrastructure\Providers;

use Phalcon\Api\Action\ActionHandler;
use Phalcon\Api\Domain\ADR\DomainInterface;
use Phalcon\Api\Domain\Infrastructure\Container;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\RoutesEnum;
use Phalcon\Api\Responder\JsonResponder;
use Phalcon\Api\Responder\ResponderInterface;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Http\RequestInterface;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\Collection;

/**
 * @phpstan-import-type TMiddleware from RoutesEnum
 */
class RouterProvider implements ServiceProviderInterface
{
    public function register(DiInterface $container): void
    {
        /** @var Micro $application */
        $application = $container->getShared(Container::APPLICATION);
        /** @var EventsManager $eventsManager */
        $eventsManager = $container->getShared(Container::EVENTS_MANAGER);

        $this->attachRoutes($application);
        $this->attachMiddleware($application, $eventsManager);

        $application->get('/health', function () {
            /** empty */
        });

        $application->setEventsManager($eventsManager);
    }

    /**
     * @param Micro         $application
     * @param EventsManager $eventsManager
     *
     * @return void
     */
    private function attachMiddleware(
        Micro $application,
        EventsManager $eventsManager
    ): void {
        /** @var TMiddleware $middleware */
        $middleware = RoutesEnum::middleware();
        foreach ($middleware as $service => $method) {
            /** @var Micro\MiddlewareInterface $instance */
            $instance = $application->getService($service);
            $eventsManager->attach('micro', $instance);
            $application->$method($instance);
        }
    }

    /**
     * Attaches routes to the application, lazy loaded
     *
     * @param Micro $application
     *
     * @return void
     */
    private function attachRoutes(Micro $application): void
    {
        /** @var RequestInterface $request */
        $request = $application->getService(Container::REQUEST);
        /** @var ResponseInterface $response */
        $response = $application->getService(Container::RESPONSE);
        /** @var ResponderInterface $responder */
        $responder = $application->getService(JsonResponder::class);

        $routes = RoutesEnum::cases();
        foreach ($routes as $route) {
            $serviceName = $route->service();
            $collection  = new Collection();
            /** @var DomainInterface $service */
            $service = $application->getService($serviceName);
            $action  = new ActionHandler(
                $request,
                $response,
                $service,
                $responder
            );

            $collection
                ->setHandler($action)
                ->setPrefix($route->prefix())
                ->{$route->method()}(
                    $route->suffix(),
                    '__invoke'
                )
            ;

            $application->mount($collection);
        }
    }
}
