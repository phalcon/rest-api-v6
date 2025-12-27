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

namespace Phalcon\Api\Domain\Infrastructure\DataSource\Company\Mapper;

use Phalcon\Api\Domain\Application\Company\Command\CompanyPutCommand;
use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\CompanyTypes;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\DTO\Company;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\DTO\CompanyCollection;

/**
 * @phpstan-import-type TCompany from CompanyTypes
 * @phpstan-import-type TCompanyDbRecord from CompanyTypes
 * @phpstan-import-type TCompanyDomainToDbRecord from CompanyTypes
 */
final class CompanyMapper implements CompanyMapperInterface
{
    /**
     * Map Domain Company -> DB row (usr_*)
     *
     * @return TCompanyDomainToDbRecord
     */
    public function db(CommandInterface $company): array
    {
        /** @var CompanyPutCommand $company */
        return [
            'com_id'             => $company->id,
            'com_name'           => $company->name,
            'com_phone'          => $company->phone,
            'com_email'          => $company->email,
            'com_website'        => $company->website,
            'com_address_line_1' => $company->addressLine1,
            'com_address_line_2' => $company->addressLine2,
            'com_city'           => $company->city,
            'com_state_province' => $company->stateProvince,
            'com_zip_code'       => $company->zipCode,
            'com_country'        => $company->country,
            'com_created_date'   => $company->createdDate,
            'com_created_usr_id' => $company->createdUserId,
            'com_updated_date'   => $company->updatedDate,
            'com_updated_usr_id' => $company->updatedUserId,
        ];
    }

    /**
     * Map DB row (usr_*) -> Domain Company
     *
     * @param TCompanyDbRecord|array{} $row
     */
    public function domain(array $row): Company
    {
        return new Company(
            (int)($row['com_id'] ?? 0),
            $row['com_name'] ?? null,
            $row['com_phone'] ?? null,
            $row['com_email'] ?? null,
            $row['com_website'] ?? null,
            $row['com_address_line_1'] ?? null,
            $row['com_address_line_2'] ?? null,
            $row['com_city'] ?? null,
            $row['com_state_province'] ?? null,
            $row['com_zip_code'] ?? null,
            $row['com_country'] ?? null,
            $row['com_created_date'] ?? null,
            isset($row['com_created_usr_id']) ? (int)$row['com_created_usr_id'] : null,
            $row['com_updated_date'] ?? null,
            isset($row['com_updated_usr_id']) ? (int)$row['com_updated_usr_id'] : null,
        );
    }

    /**
     * Map DB resultset -> Domain CompanyCollection
     *
     * @param TCompanyDbRecord[]|array{} $rows
     */
    public function domainRows(array $rows): CompanyCollection
    {
        /**
         * Return the DTO collection back
         */
        $collection = new CompanyCollection();
        foreach ($rows as $row) {
            $collection->add($this->domain($row));
        }

        return $collection;
    }
}
