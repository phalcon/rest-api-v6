<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddCompaniesTable extends AbstractMigration
{
    public function down(): void
    {
        $this->table('co_companies')->drop()->save();
    }

    public function up(): void
    {
        $table = $this->table(
            'co_companies',
            [
                'id'     => 'com_id',
                'signed' => false,
            ]
        );

        $table
            ->addColumn(
                'com_name',
                'string',
                [
                    'limit'   => 128,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'com_phone',
                'string',
                [
                    'limit'   => 32,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'com_email',
                'string',
                [
                    'limit'   => 128,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'com_website',
                'string',
                [
                    'limit'   => 128,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'com_address_line_1',
                'string',
                [
                    'limit'   => 128,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'com_address_line_2',
                'string',
                [
                    'limit'   => 128,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'com_city',
                'string',
                [
                    'limit'   => 128,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'com_state_province',
                'string',
                [
                    'limit'   => 64,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'com_zip_code',
                'string',
                [
                    'limit'   => 32,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'com_country',
                'string',
                [
                    'limit'   => 8,
                    'null'    => false,
                    'default' => '',
                ]
            )
            ->addColumn(
                'com_created_date',
                'timestamp',
                [
                    'timezone' => true,
                    'default'  => 'CURRENT_TIMESTAMP',
                ]
            )
            ->addColumn(
                'com_created_usr_id',
                'biginteger',
                [
                    'null'    => false,
                    'default' => 0,
                ]
            )
            ->addColumn(
                'com_updated_date',
                'timestamp',
                [
                    'timezone' => true,
                    'default'  => null,
                ]
            )
            ->addColumn(
                'com_updated_usr_id',
                'biginteger',
                [
                    'null'    => false,
                    'default' => 0,
                ]
            )
            ->addIndex(
                [
                    'com_name',
                ],
                [
                    'name' => 'i_name',
                ]
            )
            ->addIndex(
                [
                    'com_email',
                ],
                [
                    'name' => 'i_email',
                ]
            )
            ->addIndex(
                [
                    'com_city',
                ],
                [
                    'name' => 'i_city',
                ]
            )
            ->addIndex(
                [
                    'com_state_province',
                ],
                [
                    'name' => 'i_state_province',
                ]
            )
            ->addIndex(
                [
                    'com_country',
                ],
                [
                    'name' => 'i_country',
                ]
            )
            ->save()
        ;
    }
}
