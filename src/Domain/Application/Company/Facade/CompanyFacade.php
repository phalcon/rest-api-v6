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

namespace Phalcon\Api\Domain\Application\Company\Facade;

use Phalcon\Api\Domain\ADR\InputTypes;
use Phalcon\Api\Domain\ADR\Payload;
use Phalcon\Api\Domain\Application\Company\Command\CompanyCommandFactoryInterface;
use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandBusInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\CompanyTypes;

/**
 * Orchestration for workflow
 *
 * - Sanitization
 * - DTO creation
 * - Validation
 * - Pre-operation checks (when necessary)
 * - Repository operation
 *
 * @phpstan-import-type TCompanyInput from InputTypes
 * @phpstan-import-type TCompanyDomainToDbRecord from CompanyTypes
 * @phpstan-import-type TCompanyDbRecordOptional from CompanyTypes
 */
final readonly class CompanyFacade
{
    /**
     * @param CommandBusInterface            $bus
     * @param CompanyCommandFactoryInterface $factory
     */
    public function __construct(
        private CommandBusInterface $bus,
        private CompanyCommandFactoryInterface $factory,
    ) {
    }

    /**
     * Delete a company.
     *
     * @param TCompanyInput $input
     *
     * @return Payload
     */
    public function delete(array $input): Payload
    {
        return $this->bus->dispatch($this->factory->delete($input));
    }

    /**
     * Get a company.
     *
     * @param TCompanyInput $input
     *
     * @return Payload
     */
    public function get(array $input): Payload
    {
        return $this->bus->dispatch($this->factory->get($input));
    }

    /**
     * Get many records
     *
     * @param TCompanyInput $input
     *
     * @return Payload
     */
    public function getMany(array $input): Payload
    {
        return $this->bus->dispatch($this->factory->getMany($input));
    }

    /**
     * Create a company.
     *
     * @param TCompanyInput $input
     *
     * @return Payload
     */
    public function insert(array $input): Payload
    {
        return $this->bus->dispatch($this->factory->insert($input));
    }

    /**
     * Create a company.
     *
     * @param TCompanyInput $input
     *
     * @return Payload
     */
    public function update(array $input): Payload
    {
        return $this->bus->dispatch($this->factory->update($input));
    }
}
