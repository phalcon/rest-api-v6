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

use Phalcon\DataMapper\Pdo\Connection;

abstract class AbstractMigration
{
    protected string $table = '';

    public function __construct(
        protected readonly Connection $connection
    ) {
        $this->clear();
    }

    /**
     * @return int
     */
    public function clear(): int
    {
        return $this->connection->exec('DELETE FROM ' . $this->table);
    }
}
