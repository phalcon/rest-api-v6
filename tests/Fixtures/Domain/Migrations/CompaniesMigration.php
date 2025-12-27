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

final class CompaniesMigration extends AbstractMigration
{
    protected string $table = 'co_companies';

    public function insert(
        ?int $id,
        string $name,
        string $email,
        string $phone,
        string $website,
        string $addressLine1,
        string $addressLine2,
        string $city,
        string $stateProvince,
        string $zip_code,
        string $country,
        string $createdDate,
        int $createdUserId,
        string $updatedDate,
        int $updatedUserId
    ): int {
        $sql = "INSERT INTO $this->table (
            com_id,
            com_name,
            com_email,
            com_phone,
            com_website,
            com_address_line_1,
            com_address_line_2,
            com_city,
            com_state_province,
            com_zip_code,
            com_country,
            com_created_date,
            com_created_usr_id,
            com_updated_date,
            com_updated_usr_id
        ) VALUES (
            :id,
            :name,
            :email,
            :phone,
            :website,
            :addressLine1,
            :addressLine2,
            :city,
            :stateProvince,
            :zip_code,
            :country,
            :createdDate,
            :createdUserId,
            :updatedDate,
            :updatedUserId
        )";

        $stmt   = $this->connection->prepare($sql);
        $params = [
            ':id'            => $id,
            ':name'          => $name,
            ':email'         => $email,
            ':phone'         => $phone,
            ':website'       => $website,
            ':addressLine1'  => $addressLine1,
            ':addressLine2'  => $addressLine2,
            ':city'          => $city,
            ':stateProvince' => $stateProvince,
            ':zip_code'      => $zip_code,
            ':country'       => $country,
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
