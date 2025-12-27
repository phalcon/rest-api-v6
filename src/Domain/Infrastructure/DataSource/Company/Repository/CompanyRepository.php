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

use Phalcon\Api\Domain\Infrastructure\DataSource\AbstractRepository;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\CompanyTypes;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\DTO\Company;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\DTO\CompanyCollection;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\DTO\CompanyCriteria;
use Phalcon\Api\Domain\Infrastructure\DataSource\Company\Mapper\CompanyMapperInterface;
use Phalcon\DataMapper\Pdo\Connection;
use Phalcon\DataMapper\Query\Select;

/**
 * @phpstan-import-type TCriteria from CompanyTypes
 * @phpstan-import-type TCompanyRecord from CompanyTypes
 * @phpstan-import-type TCompanyRecords from CompanyTypes
 * @phpstan-import-type TCompanyDbRecordOptional from CompanyTypes
 */
class CompanyRepository extends AbstractRepository implements CompanyRepositoryInterface
{
    /**
     * @var string
     */
    protected string $idField = 'com_id';
    /**
     * @var string
     */
    protected string $table = 'co_companies';

    public function __construct(
        Connection $connection,
        private readonly CompanyMapperInterface $mapper,
    ) {
        parent::__construct($connection);
    }

    /**
     * @param CompanyCriteria $criteria
     *
     * @return CompanyCollection
     */
    public function find(CompanyCriteria $criteria): CompanyCollection
    {
        $select = Select::new($this->connection);

        $select->from($this->table);

        $columns = $criteria->toArray();
        if (true !== empty($columns)) {
            $select->whereEquals($columns);
        }

        /** @var TCompanyRecords $results */
        $results = $select
            ->page($criteria->page)
            ->perPage($criteria->perPage)
            ->fetchAll()
        ;

        return $this->mapper->domainRows($results);
    }

    /**
     * @param int $recordId
     *
     * @return Company|null
     */
    public function findById(int $recordId): ?Company
    {
        if ($recordId > 0) {
            return $this->findOneBy(
                [
                    $this->idField => $recordId,
                ]
            );
        }

        return null;
    }


    /**
     * @param TCriteria $criteria
     *
     * @return Company|null
     */
    public function findOneBy(array $criteria): ?Company
    {
        $select = Select::new($this->connection);

        $select->from($this->table);

        if (true !== empty($criteria)) {
            $select->whereEquals($criteria);
        }

        /** @var TCompanyRecord $result */
        $result = $select->fetchOne();

        if (empty($result)) {
            return null;
        }

        return $this->mapper->domain($result);
    }
}
