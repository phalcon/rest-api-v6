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

namespace Phalcon\Api\Domain\Application\Auth\Facade;

use Phalcon\Api\Domain\ADR\InputTypes;
use Phalcon\Api\Domain\Application\Auth\Command\AuthCommandFactoryInterface;
use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandBusInterface;
use Phalcon\Contracts\ADR\Payload\Payload as PayloadContract;

/**
 * @phpstan-import-type TAuthLoginInput from InputTypes
 * @phpstan-import-type TAuthLogoutInput from InputTypes
 * @phpstan-import-type TAuthRefreshInput from InputTypes
 */
final readonly class AuthFacade
{
    /**
     * @param CommandBusInterface         $bus
     * @param AuthCommandFactoryInterface $factory
     */
    public function __construct(
        private CommandBusInterface $bus,
        private AuthCommandFactoryInterface $factory
    ) {
    }

    /**
     * Authenticates users (login)
     *
     * @param TAuthLoginInput $input
     *
     * @return PayloadContract
     */
    public function authenticate(array $input): PayloadContract
    {
        return $this->bus->dispatch($this->factory->authenticate($input));
    }

    /**
     * Logout: revoke refresh token after parsing/validation.
     *
     * @param TAuthLogoutInput $input
     *
     * @return PayloadContract
     */
    public function logout(array $input): PayloadContract
    {
        return $this->bus->dispatch($this->factory->logout($input));
    }

    /**
     * Refresh: validate refresh token, issue new tokens via TokenManager.
     *
     * @param TAuthLogoutInput $input
     *
     * @return PayloadContract
     */
    public function refresh(array $input): PayloadContract
    {
        return $this->bus->dispatch($this->factory->refresh($input));
    }
}
