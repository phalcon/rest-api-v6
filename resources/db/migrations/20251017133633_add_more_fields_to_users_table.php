<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddMoreFieldsToUsersTable extends AbstractMigration
{
    public function down(): void
    {
        $table = $this->table('co_users');
        $table
            ->removeIndexByName('i_email_x_status')
            ->removeIndexByName('i_id_x_status')
            ->removeIndexByName('i_token_id')
            ->removeIndexByName('i_email')
            ->save()
        ;

        $table
            ->removeColumn('usr_email')
            ->removeColumn('usr_name_prefix')
            ->removeColumn('usr_name_first')
            ->removeColumn('usr_name_middle')
            ->removeColumn('usr_name_last')
            ->removeColumn('usr_name_suffix')
            ->removeColumn('usr_issuer')
            ->removeColumn('usr_token_password')
            ->removeColumn('usr_token_id')
            ->removeColumn('usr_preferences')
            ->removeColumn('usr_created_date')
            ->removeColumn('usr_created_usr_id')
            ->removeColumn('usr_updated_date')
            ->removeColumn('usr_updated_usr_id')
            ->save()
        ;

        $table
            ->addColumn(
                'usr_username',
                'string',
                [
                    'limit'   => 128,
                    'null'    => false,
                    'default' => '',
                    'after'   => 'usr_status_flag',
                ]
            )
            ->addIndex('usr_status_flag')
            ->addIndex('usr_username')
            ->save()
        ;
    }

    public function up(): void
    {
        $table = $this->table('co_users');

        $table
            ->removeIndex('usr_status_flag')
            ->removeIndex('usr_username')
            ->save()
        ;

        $table->removeColumn('usr_username')->save();

        $table
            ->addColumn(
                'usr_email',
                'string',
                [
                    'limit' => 128,
                    'null'  => false,
                    'after' => 'usr_status_flag',
                ]
            )
            ->addColumn(
                'usr_name_prefix',
                'string',
                [
                    'limit'   => 16,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'usr_name_first',
                'string',
                [
                    'limit'   => 64,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'usr_name_middle',
                'string',
                [
                    'limit'   => 64,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'usr_name_last',
                'string',
                [
                    'limit'   => 128,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'usr_name_suffix',
                'string',
                [
                    'limit'   => 16,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'usr_issuer',
                'string',
                [
                    'limit' => 128,
                    'null'  => false,
                ]
            )
            ->addColumn(
                'usr_token_password',
                'string',
                [
                    'limit' => 128,
                    'null'  => false,
                ]
            )
            ->addColumn(
                'usr_token_id',
                'string',
                [
                    'limit' => 128,
                    'null'  => false,
                ]
            )
            ->addColumn(
                'usr_preferences',
                'text',
                [
                    'null' => true,
                ]
            )
            ->addColumn(
                'usr_created_date',
                'timestamp',
                [
                    'timezone' => true,
                    'default'  => 'CURRENT_TIMESTAMP',
                ]
            )
            ->addColumn(
                'usr_created_usr_id',
                'biginteger',
                [
                    'null'    => false,
                    'default' => 0,
                ]
            )
            ->addColumn(
                'usr_updated_date',
                'timestamp',
                [
                    'timezone' => true,
                    'default'  => null,
                ]
            )
            ->addColumn(
                'usr_updated_usr_id',
                'biginteger',
                [
                    'null'    => false,
                    'default' => 0,
                ]
            )
            ->addIndex(
                [
                    'usr_email',
                    'usr_status_flag',
                ],
                [
                    'name' => 'i_email_x_status',
                ]
            )
            ->addIndex(
                [
                    'usr_id',
                    'usr_status_flag',
                ],
                [
                    'name' => 'i_id_x_status',
                ]
            )
            ->addIndex(
                [
                    'usr_token_id',
                ],
                [
                    'unique' => true,
                    'name'   => 'i_token_id',
                ]
            )
            ->addIndex(
                [
                    'usr_email',
                ],
                [
                    'unique' => true,
                    'name'   => 'i_email',
                ]
            )
            ->save()
        ;
    }
}
