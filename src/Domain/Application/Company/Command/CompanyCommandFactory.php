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

namespace Phalcon\Api\Domain\Application\Company\Command;

use Phalcon\Api\Domain\ADR\InputTypes;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\DTO\CompanyInput;
use Phalcon\Api\Domain\Infrastructure\DataSource\Interface\SanitizerInterface;

/**
 * @phpstan-import-type TCompanyInput from InputTypes
 */
final class CompanyCommandFactory implements CompanyCommandFactoryInterface
{
    /**
     * @param SanitizerInterface $sanitizer
     */
    public function __construct(
        private readonly SanitizerInterface $sanitizer
    ) {
    }

    /**
     * @param TCompanyInput $input
     *
     * @return CompanyDeleteCommand
     */
    public function delete(array $input): CompanyDeleteCommand
    {
        /**
         * Sanitize the input
         */
        $dto = CompanyInput::new($this->sanitizer, $input);

        /**
         * Return the DTO back
         */
        return new CompanyDeleteCommand($dto->id);
    }

    /**
     * @param TCompanyInput $input
     *
     * @return CompanyGetCommand
     */
    public function get(array $input): CompanyGetCommand
    {
        /**
         * Sanitize the input
         */
        $dto = CompanyInput::new($this->sanitizer, $input);

        /**
         * Return the DTO back
         */
        return new CompanyGetCommand($dto->id);
    }

    /**
     * @param TCompanyInput $input
     *
     * @return CompanyGetManyCommand
     */
    public function getMany(array $input): CompanyGetManyCommand
    {
        /**
         * Sanitize the input
         */
        $dto = CompanyInput::new($this->sanitizer, $input);

        /**
         * Return the DTO back
         */
        return new CompanyGetManyCommand(
            $dto->id,
            $dto->name,
            $dto->phone,
            $dto->email,
            $dto->website,
            $dto->addressLine1,
            $dto->addressLine2,
            $dto->city,
            $dto->stateProvince,
            $dto->zipCode,
            $dto->country,
            $dto->createdDate,
            $dto->createdUserId,
            $dto->updatedDate,
            $dto->updatedUserId,
            $dto->page,
            $dto->perPage,
        );
    }

    /**
     * @param TCompanyInput $input
     *
     * @return CompanyPostCommand
     */
    public function insert(array $input): CompanyPostCommand
    {
        /**
         * Sanitize the input
         */
        $dto = CompanyInput::new($this->sanitizer, $input);

        /**
         * Return the DTO back
         */
        return new CompanyPostCommand(
            null,
            $dto->name,
            $dto->phone,
            $dto->email,
            $dto->website,
            $dto->addressLine1,
            $dto->addressLine2,
            $dto->city,
            $dto->stateProvince,
            $dto->zipCode,
            $dto->country,
            $dto->createdDate,
            $dto->createdUserId,
            $dto->updatedDate,
            $dto->updatedUserId,
        );
    }

    /**
     * @param TCompanyInput $input
     *
     * @return CompanyPutCommand
     */
    public function update(array $input): CompanyPutCommand
    {
        /**
         * Sanitize the input
         */
        $dto = CompanyInput::new($this->sanitizer, $input);

        /**
         * Return the DTO back
         */
        return new CompanyPutCommand(
            $dto->id,
            $dto->name,
            $dto->phone,
            $dto->email,
            $dto->website,
            $dto->addressLine1,
            $dto->addressLine2,
            $dto->city,
            $dto->stateProvince,
            $dto->zipCode,
            $dto->country,
            $dto->createdDate,
            $dto->createdUserId,
            $dto->updatedDate,
            $dto->updatedUserId,
        );
    }
}
