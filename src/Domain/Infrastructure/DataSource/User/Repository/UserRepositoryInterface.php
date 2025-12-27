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

namespace Phalcon\Api\Domain\Infrastructure\DataSource\User\Repository;

use Phalcon\Api\Domain\Infrastructure\DataSource\User\DTO\User;
use Phalcon\Api\Domain\Infrastructure\DataSource\User\UserTypes;

/**
 * @phpstan-import-type TCriteria from UserTypes
 * @phpstan-import-type TUserDbRecordOptional from UserTypes
 */
interface UserRepositoryInterface
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
     * @param string $email
     *
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * @param int $recordId
     *
     * @return User|null
     */
    public function findById(int $recordId): ?User;

    /**
     * @param TCriteria $criteria
     *
     * @return User|null
     */
    public function findOneBy(array $criteria): ?User;

    /**
     * @param TUserDbRecordOptional $columns
     *
     * @return int
     */
    public function insert(array $columns): int;

    /**
     * @param int                   $recordId
     * @param TUserDbRecordOptional $columns
     *
     * @return int
     */
    public function update(int $recordId, array $columns): int;
}
