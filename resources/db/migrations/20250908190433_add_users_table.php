<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddUsersTable extends AbstractMigration
{
    public function down(): void
    {
        $this->table('co_users')->drop()->save();
    }

    public function up(): void
    {
        $table = $this->table(
            'co_users',
            [
                'id'     => 'usr_id',
                'signed' => false,
            ]
        );

        $table
            ->addColumn(
                'usr_status_flag',
                'boolean',
                [
                    'signed'  => false,
                    'null'    => false,
                    'default' => 0,
                ]
            )
            ->addColumn(
                'usr_username',
                'string',
                [
                    'limit'   => 128,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'usr_password',
                'string',
                [
                    'limit'   => 128,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addIndex('usr_status_flag')
            ->addIndex('usr_username')
            ->save()
        ;
    }
}
