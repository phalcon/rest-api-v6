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

namespace Phalcon\Api\Domain\Application\Auth\Handler;

use Phalcon\Api\Domain\ADR\InputTypes;
use Phalcon\Api\Domain\ADR\Payload;
use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\DTO\User;

/**
 * @phpstan-import-type TAuthRefreshInput from InputTypes
 */
final class AuthRefreshPostHandler extends AbstractAuthLogoutRefreshHandler
{
    /**
     * Refresh: validate refresh token, issue new tokens via TokenManager.
     *
     * @param CommandInterface $command
     *
     * @return Payload
     */
    public function __invoke(CommandInterface $command): Payload
    {
        /**
         * Validate
         */
        $validation = $this->validator->validate($command);
        if (!$validation->isValid()) {
            return Payload::unauthorized($validation->getErrors());
        }

        /**
         * If we are here validation has passed and the Result object
         * has the user in the meta store
         */
        /** @var User $domainUser */
        $domainUser = $validation->getMeta('user');

        $tokens = $this->tokenManager->refresh($domainUser);

        return Payload::success($this->transformer->refresh($tokens));
    }
}
