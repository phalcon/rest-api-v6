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

namespace Phalcon\Api\Domain\Infrastructure\Middleware;

use Phalcon\Api\Domain\Infrastructure\Container;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenManager;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenManagerInterface;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Api\Domain\Infrastructure\Env\EnvManager;
use Phalcon\Events\Exception as EventsException;
use Phalcon\Http\RequestInterface;
use Phalcon\Http\Response\Exception;
use Phalcon\Mvc\Micro;
use Phalcon\Support\Registry;

final class ValidateTokenStructureMiddleware extends AbstractMiddleware
{
    /**
     * @param Micro $application
     *
     * @return bool
     * @throws EventsException
     * @throws Exception
     */
    public function call(Micro $application): bool
    {
        /** @var RequestInterface $request */
        $request = $application->getSharedService(Container::REQUEST);
        /** @var EnvManager $env */
        $env = $application->getSharedService(EnvManager::class);
        /** @var TokenManagerInterface $tokenManager */
        $tokenManager = $application->getSharedService(TokenManager::class);

        $hederToken = $this->getBearerTokenFromHeader($request, $env);
        $tokenObject = $tokenManager->getObject($hederToken);

        if (null === $tokenObject) {
            $this->halt(
                $application,
                HttpCodesEnum::Unauthorized->value,
                HttpCodesEnum::Unauthorized->text(),
                [],
                [
                    [$tokenManager->getErrorMessage()],
                ]
            );

            return false;
        }

        /**
         * If we are down here the token is an object and is valid
         */
        /** @var Registry $registry */
        $registry = $application->getSharedService(Registry::class);
        $registry->set('token', $tokenObject);

        return true;
    }
}
