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

use Phalcon\Api\Domain\Infrastructure\DataSource\User\Repository\UserRepository;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\Repository\UserRepositoryInterface;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenManager;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenManagerInterface;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Encryption\Security\JWT\Token\Token;
use Phalcon\Events\Exception as EventsException;
use Phalcon\Http\Response\Exception;
use Phalcon\Mvc\Micro;
use Phalcon\Support\Registry;

/**
 */
final class ValidateTokenUserMiddleware extends AbstractMiddleware
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
        /** @var UserRepositoryInterface $repository */
        $repository = $application->getSharedService(UserRepository::class);
        /** @var Registry $registry */
        $registry = $application->getSharedService(Registry::class);
        /** @var TokenManagerInterface $tokenManager */
        $tokenManager = $application->getSharedService(TokenManager::class);

        /**
         * Get the token object
         */
        /** @var Token $tokenObject */
        $tokenObject = $registry->get('token');
        $domainUser  = $tokenManager->getUser($repository, $tokenObject);

        if (null === $domainUser) {
            $this->halt(
                $application,
                HttpCodesEnum::Unauthorized->value,
                HttpCodesEnum::Unauthorized->text(),
                [],
                [HttpCodesEnum::AppTokenInvalidUser->error()]
            );

            return false;
        }

        /**
         * If we are here everything is fine and we need to keep the user
         * as a "session" user in the registry
         */
        $registry->set('user', $domainUser);

        return true;
    }
}
