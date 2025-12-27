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
use Phalcon\DataMapper\Query\Delete;
use Phalcon\DataMapper\Query\Insert;
use Phalcon\DataMapper\Query\Update;

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

    public function __construct(
        protected readonly Connection $connection,
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
        $delete = Delete::new($this->connection);

        $statement = $delete
            ->table($this->table)
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
        $insert = Insert::new($this->connection);
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
        $update = Update::new($this->connection);
        $update
            ->table($this->table)
            ->columns($columns)
            ->where($this->idField . ' = ', $recordId)
            ->perform()
        ;

        return $recordId;
    }
}
