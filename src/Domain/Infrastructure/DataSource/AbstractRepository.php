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

namespace Phalcon\Api\Domain\Infrastructure\DataSource;

use Phalcon\Api\Domain\Infrastructure\DataSource\Company\CompanyTypes;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\UserTypes;
use Phalcon\DataMapper\Pdo\Connection;
use Phalcon\DataMapper\Pdo\Exception\Exception as DataMapperException;
use Phalcon\DataMapper\Query\QueryFactory;

/**
 * @phpstan-import-type TCriteria from UserTypes
 * @phpstan-import-type TUserDbRecordOptional from UserTypes
 * @phpstan-import-type TCompanyDbRecordOptional from CompanyTypes
 * @phpstan-type TDbRecordOptional TCompanyDbRecordOptional|TUserDbRecordOptional
 */
abstract class AbstractRepository
{
    /**
     * @var string
     */
    protected string $idField = '';
    /**
     * @var string
     */
    protected string $table = '';

    /**
     * The query classes lost their static ::new() factory in Phalcon v6;
     * construction goes through QueryFactory. Defaulted so the existing
     * container definitions, which pass only the connection, still apply.
     */
    public function __construct(
        protected readonly Connection $connection,
        protected readonly QueryFactory $factory = new QueryFactory(),
    ) {
    }

    /**
     * @param TCriteria $criteria
     *
     * @return int
     * @throws DataMapperException
     */
    public function deleteBy(array $criteria): int
    {
        $delete = $this->factory->newDelete($this->connection);

        $statement = $delete
            ->from($this->table)
            ->whereEquals($criteria)
            ->perform()
        ;

        return $statement->rowCount();
    }

    /**
     * @param int $recordId
     *
     * @return int
     */
    public function deleteById(int $recordId): int
    {
        return $this->deleteBy(
            [
                $this->idField => $recordId,
            ]
        );
    }

    /**
     *
     * @param TDbRecordOptional $columns
     *
     * @return int
     * @throws DataMapperException
     */
    public function insert(array $columns): int
    {
        $insert = $this->factory->newInsert($this->connection);
        $insert
            ->into($this->table)
            ->columns($columns)
            ->perform()
        ;

        return (int)$insert->getLastInsertId();
    }

    /**
     * @param int               $recordId
     * @param TDbRecordOptional $columns
     *
     * @return int
     * @throws DataMapperException
     */
    public function update(int $recordId, array $columns): int
    {
        $update = $this->factory->newUpdate($this->connection);
        $update
            ->from($this->table)
            ->columns($columns)
            ->where($this->idField . ' = ', $recordId)
            ->perform()
        ;

        return $recordId;
    }
}
