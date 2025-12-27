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

use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\CompanyTypes;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\DTO\Company;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\DTO\CompanyCollection;

/**
 * Contract for mapping between domain DTO/objects and persistence arrays.
 *
 * @phpstan-import-type TCompany from CompanyTypes
 * @phpstan-import-type TCompanyDbRecord from CompanyTypes
 * @phpstan-import-type TCompanyDomainToDbRecord from CompanyTypes
 */
interface CompanyMapperInterface
{
    /**
     * Map Domain User -> DB row (usr_*)
     *
     * @param CommandInterface $company
     *
     * @return TCompanyDomainToDbRecord
     */
    public function db(CommandInterface $company): array;

    /**
     * Map DB row (usr_*) -> Domain User
     *
     * @param TCompanyDbRecord|array{} $row
     */
    public function domain(array $row): Company;

    /**
     * Map DB resultset -> Domain CompanyCollection
     *
     * @param TCompanyDbRecord[]|array{} $rows
     */
    public function domainRows(array $rows): CompanyCollection;
}
