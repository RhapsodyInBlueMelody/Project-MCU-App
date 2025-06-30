<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveNoTelpPasienFromPasienTable extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('pasien', 'no_telp_pasien');
    }

    public function down()
    {
        $this->forge->addColumn('pasien', [
            'no_telp_pasien' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
                'null' => true,
                'after' => 'jenis_kelamin',
            ],
        ]);
    }
}
