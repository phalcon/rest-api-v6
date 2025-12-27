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

namespace Phalcon\Api\Domain\Infrastructure\DataSource\Company\Repository;

use Phalcon\Api\Domain\Infrastructure\DataSource\Company\CompanyTypes;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\DTO\Company;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\DTO\CompanyCollection;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\DTO\CompanyCriteria;

/**
 * @phpstan-import-type TCriteria from CompanyTypes
 * @phpstan-import-type TCompanyDbRecordOptional from CompanyTypes
 */
interface CompanyRepositoryInterface
{
    /**
     * @param TCriteria $criteria
     *
     * @return int
     */
    public function deleteBy(array $criteria): int;

    /**
     * @param int $recordId
     *
     * @return int
     */
    public function deleteById(int $recordId): int;

    /**
     * @param CompanyCriteria $criteria
     *
     * @return CompanyCollection
     */
    public function find(CompanyCriteria $criteria): CompanyCollection;

    /**
     * @param int $recordId
     *
     * @return Company|null
     */
    public function findById(int $recordId): ?Company;

    /**
     * @param TCriteria $criteria
     *
     * @return Company|null
     */
    public function findOneBy(array $criteria): ?Company;

    /**
     * @param TCompanyDbRecordOptional $columns
     *
     * @return int
     */
    public function insert(array $columns): int;

    /**
     * @param int                      $recordId
     * @param TCompanyDbRecordOptional $columns
     *
     * @return int
     */
    public function update(int $recordId, array $columns): int;
}
