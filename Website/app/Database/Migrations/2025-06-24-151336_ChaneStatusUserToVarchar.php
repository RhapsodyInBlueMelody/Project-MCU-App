<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChaneStatusUserToVarchar extends Migration
{
    public function up()
    {
        $fields = [
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'deactivated', 'pending'],
                'default' => 'pending',
                'null' => false,
            ],
        ];

        $this->forge->modifyColumn('users', $fields);
    }

    public function down()
    {
        $fields = [
            'status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'null' => false,
            ],
        ];

        $this->forge->modifyColumn('users', $fields);
    }
}
