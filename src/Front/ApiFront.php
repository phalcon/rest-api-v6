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

namespace Phalcon\Api\Front;

use Phalcon\ADR\Application;
use Phalcon\ADR\Front\AbstractHttpFront;
use Phalcon\Api\Container\ApiProvider;
use Phalcon\Api\Domain\Infrastructure\Env\EnvManager;
use Phalcon\Api\Middleware\ValidateTokenClaims;
use Phalcon\Api\Middleware\ValidateTokenPresence;
use Phalcon\Api\Middleware\ValidateTokenRevoked;
use Phalcon\Api\Middleware\ValidateTokenStructure;
use Phalcon\Api\Middleware\ValidateTokenUser;
use Phalcon\Container\Container;
use Phalcon\Contracts\ADR\Application as ApplicationInterface;

/**
 * The application's front controller.
 *
 * AbstractHttpFront::run() already sequences container, environment,
 * providers, request, handle and emit, with boot-error handling around it.
 * Only the environment and provider hooks need filling in, plus the base
 * namespace the convention router resolves action classes against.
 */
final class ApiFront extends AbstractHttpFront
{
    /**
     * The guards, in the order they run. Each depends on the one before it:
     * presence puts nothing in the registry, structure puts the token there,
     * user puts the user there, and the last two read both.
     */
    private const GUARDS = [
        ValidateTokenPresence::class,
        ValidateTokenStructure::class,
        ValidateTokenUser::class,
        ValidateTokenClaims::class,
        ValidateTokenRevoked::class,
    ];

    /**
     * Prefixes are matched against the action's class name, not the URL.
     * Company and User are secured wholesale. Auth cannot be - login must
     * stay open while logout and refresh require a token - so those two
     * name themselves.
     */
    private const SECURED = [
        '\Company',
        '\User',
        '\Auth\PostAuthLogout',
        '\Auth\PostAuthRefresh',
    ];

    /**
     * @param Container $container
     *
     * @return ApplicationInterface
     */
    protected function getApplication(Container $container): ApplicationInterface
    {
        $application = new Application($container);

        $application->setBaseNamespace('Phalcon\Api\Action');

        foreach (self::SECURED as $prefix) {
            foreach (self::GUARDS as $guard) {
                $application->secureWith($guard, $prefix);
            }
        }

        return $application;
    }

    /**
     * @param Container $container
     *
     * @return void
     */
    protected function loadEnvironment(Container $container): void
    {
        $container->get(EnvManager::class);
    }

    /**
     * @param Container $container
     *
     * @return void
     */
    protected function registerProviders(Container $container): void
    {
        (new ApiProvider())->provide($container);
    }
}
