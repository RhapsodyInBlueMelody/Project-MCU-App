<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToPasienTableV2 extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pasien', [
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'telepon'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pasien', 'email');
    }
}
