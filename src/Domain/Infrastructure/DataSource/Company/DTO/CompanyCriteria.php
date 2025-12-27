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

namespace Phalcon\Api\Domain\Infrastructure\DataSource\Company\DTO;

use Phalcon\Api\Domain\Application\Company\Command\CompanyGetManyCommand;
use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\CompanyTypes;

/**
 * Value object for database criteria
 *
 * @phpstan-import-type TCriteria from CompanyTypes
 * @phpstan-import-type TCompanyDbRecordOptional from CompanyTypes
 */
final readonly class CompanyCriteria
{
    /**
     * @param int|null    $id
     * @param string|null $name
     * @param string|null $phone
     * @param string|null $email
     * @param string|null $website
     * @param string|null $addressLine1
     * @param string|null $addressLine2
     * @param string|null $city
     * @param string|null $stateProvince
     * @param string|null $zipCode
     * @param string|null $country
     * @param string|null $createdDate
     * @param int|null    $createdUserId
     * @param string|null $updatedDate
     * @param int|null    $updatedUserId
     * @param int         $page
     * @param int         $perPage
     */
    private function __construct(
        public ?int $id,
        public ?string $name,
        public ?string $phone,
        public ?string $email,
        public ?string $website,
        public ?string $addressLine1,
        public ?string $addressLine2,
        public ?string $city,
        public ?string $stateProvince,
        public ?string $zipCode,
        public ?string $country,
        public ?string $createdDate,
        public ?int $createdUserId,
        public ?string $updatedDate,
        public ?int $updatedUserId,
        public int $page,
        public int $perPage,
    ) {
    }

    /**
     * @param CommandInterface $command
     *
     * @return self
     */
    public static function fromDto(CommandInterface $command): self
    {
        /** @var CompanyGetManyCommand $command */
        return new self(
            $command->id,
            $command->name,
            $command->phone,
            $command->email,
            $command->website,
            $command->addressLine1,
            $command->addressLine2,
            $command->city,
            $command->stateProvince,
            $command->zipCode,
            $command->country,
            $command->createdDate,
            $command->createdUserId,
            $command->updatedDate,
            $command->updatedUserId,
            self::checkLimit($command->page, 1),
            self::checkLimit($command->perPage, 10)
        );
    }

    /**
     * @return TCompanyDbRecordOptional
     */
    public function toArray(): array
    {
        $criteria = [
            'com_id'             => $this->id,
            'com_name'           => $this->name,
            'com_phone'          => $this->phone,
            'com_email'          => $this->email,
            'com_website'        => $this->website,
            'com_address_line_1' => $this->addressLine1,
            'com_address_line_2' => $this->addressLine2,
            'com_city'           => $this->city,
            'com_state_province' => $this->stateProvince,
            'com_zip_code'       => $this->zipCode,
            'com_country'        => $this->country,
            'com_created_date'   => $this->createdDate,
            'com_created_usr_id' => $this->createdUserId,
            'com_updated_date'   => $this->updatedDate,
            'com_updated_usr_id' => $this->updatedUserId,
        ];

        return array_filter($criteria);
    }

    /**
     * @param int $value
     * @param int $defaultValue
     *
     * @return int
     */
    private static function checkLimit(int $value, int $defaultValue): int
    {
        return $value > 0 ? $value : $defaultValue;
    }
}
