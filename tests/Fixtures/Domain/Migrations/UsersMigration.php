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

namespace Phalcon\Api\Tests\Fixtures\Domain\Migrations;

use PHPUnit\Framework\Assert;

final class UsersMigration extends AbstractMigration
{
    protected string $table = 'co_users';

    public function insert(
        ?int $id,
        int $status,
        string $email,
        string $password,
        string $namePrefix,
        string $nameFirst,
        string $nameMiddle,
        string $nameLast,
        string $nameSuffix,
        string $issuer,
        string $tokenPassword,
        string $tokenId,
        string $preferences,
        string $createdDate,
        int $createdUserId,
        string $updatedDate,
        int $updatedUserId
    ): int {
        $sql = "INSERT INTO $this->table (
            usr_id,
            usr_status_flag,
            usr_email,
            usr_password,
            usr_name_prefix,
            usr_name_first,
            usr_name_middle,
            usr_name_last,
            usr_name_suffix,
            usr_issuer,
            usr_token_password,
            usr_token_id,
            usr_preferences,
            usr_created_date,
            usr_created_usr_id,
            usr_updated_date,
            usr_updated_usr_id
        ) VALUES (
            :id,
            :status,
            :email,
            :password,
            :namePrefix,
            :nameFirst,
            :nameMiddle,
            :nameLast,
            :nameSuffix,
            :issuer,
            :tokenPassword,
            :tokenId,
            :preferences,
            :createdDate,
            :createdUserId,
            :updatedDate,
            :updatedUserId
        )";

        $stmt   = $this->connection->prepare($sql);
        $params = [
            ':id'            => $id,
            ':status'        => $status,
            ':email'         => $email,
            ':password'      => $password,
            ':namePrefix'    => $namePrefix,
            ':nameFirst'     => $nameFirst,
            ':nameMiddle'    => $nameMiddle,
            ':nameLast'      => $nameLast,
            ':nameSuffix'    => $nameSuffix,
            ':issuer'        => $issuer,
            ':tokenPassword' => $tokenPassword,
            ':tokenId'       => $tokenId,
            ':preferences'   => $preferences,
            ':createdDate'   => $createdDate,
            ':createdUserId' => $createdUserId,
            ':updatedDate'   => $updatedDate,
            ':updatedUserId' => $updatedUserId,
        ];
        $result = $stmt->execute($params);
        if (!$result) {
            Assert::fail(
                "Failed to insert id [#$id] into table [$this->table]"
            );
        }

        return (int)$this->connection->lastInsertId();
    }
}
