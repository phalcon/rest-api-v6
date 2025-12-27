<?php

declare(strict_types=1);

use Phalcon\Api\Domain\Infrastructure\Container;
use Phalcon\Api\Domain\Infrastructure\Providers\ErrorHandlerProvider;
use Phalcon\Api\Domain\Infrastructure\Providers\RouterProvider;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Mvc\Micro;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$container   = new Container();
$application = new Micro($container);
$container->set(Container::APPLICATION, $application, true);
$now = hrtime(true);
$container->set(
    Container::TIME,
    function () use ($now) {
        return $now;
    },
    true
);

/**
 * Providers
 */
$providers = [
    ErrorHandlerProvider::class,
    RouterProvider::class,
];

/** @var class-string $provider */
foreach ($providers as $provider) {
    /** @var ServiceProviderInterface $service */
    $service = new $provider();
    $container->register($service);
}


/** @var string $uri */
$uri = $_SERVER['REQUEST_URI'] ?? '';

$application->handle($uri);
