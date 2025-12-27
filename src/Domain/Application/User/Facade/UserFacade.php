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

namespace Phalcon\Api\Domain\Application\User\Facade;

use Phalcon\Api\Domain\ADR\InputTypes;
use Phalcon\Api\Domain\ADR\Payload;
use Phalcon\Api\Domain\Application\User\Command\UserCommandFactoryInterface;
use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandBusInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\UserTypes;

/**
 * Orchestration for workflow
 *
 * - Sanitization
 * - DTO creation
 * - Validation
 * - Pre-operation checks (when necessary)
 * - Repository operation
 *
 * @phpstan-import-type TUserInput from InputTypes
 * @phpstan-import-type TUserDomainToDbRecord from UserTypes
 * @phpstan-import-type TUserDbRecordOptional from UserTypes
 */
final readonly class UserFacade
{
    /**
     * @param CommandBusInterface         $bus
     * @param UserCommandFactoryInterface $factory
     */
    public function __construct(
        private CommandBusInterface $bus,
        private UserCommandFactoryInterface $factory,
    ) {
    }

    /**
     * Delete a user.
     *
     * @param TUserInput $input
     *
     * @return Payload
     */
    public function delete(array $input): Payload
    {
        return $this->bus->dispatch($this->factory->delete($input));
    }

    /**
     * Get a user.
     *
     * @param TUserInput $input
     *
     * @return Payload
     */
    public function get(array $input): Payload
    {
        return $this->bus->dispatch($this->factory->get($input));
    }

    /**
     * Create a user.
     *
     * @param TUserInput $input
     *
     * @return Payload
     */
    public function insert(array $input): Payload
    {
        return $this->bus->dispatch($this->factory->insert($input));
    }

    /**
     * Create a user.
     *
     * @param TUserInput $input
     *
     * @return Payload
     */
    public function update(array $input): Payload
    {
        return $this->bus->dispatch($this->factory->update($input));
    }
}
