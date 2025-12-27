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

use Phalcon\Api\Domain\Infrastructure\DataSource\User\DTO\User;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenManager;
use Phalcon\Api\Domain\Infrastructure\Encryption\TokenManagerInterface;
use Phalcon\Api\Domain\Infrastructure\Enums\Http\HttpCodesEnum;
use Phalcon\Encryption\Security\JWT\Token\Token;
use Phalcon\Events\Exception as EventsException;
use Phalcon\Http\Response\Exception;
use Phalcon\Mvc\Micro;
use Phalcon\Support\Registry;

/**
 * Validates the token claims
 */
final class ValidateTokenClaimsMiddleware extends AbstractMiddleware
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
        /** @var Registry $registry */
        $registry = $application->getSharedService(Registry::class);
        /** @var TokenManagerInterface $tokenManager */
        $tokenManager = $application->getSharedService(TokenManager::class);

        /**
         * Get the token object
         */
        /** @var Token $tokenObject */
        $tokenObject = $registry->get('token');
        /**
         * Our used is in the transport, so we can get it without a
         * database call
         */
        /** @var User $sessionUser */
        $sessionUser = $registry->get('user');

        /**
         * This is where we validate everything. Even though the user
         * we got from the database has been checked against the 'uid'
         * claims of the token, we will still check the claims against the
         * user stored in the session
         */
        /** @var array<int, string> $errors */
        $errors = $tokenManager->validate($tokenObject, $sessionUser);

        if (true !== empty($errors)) {
            $this->halt(
                $application,
                HttpCodesEnum::Unauthorized->value,
                HttpCodesEnum::Unauthorized->text(),
                [],
                [$errors]
            );

            return false;
        }

        return true;
    }
}
