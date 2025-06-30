<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLisensiDanTeleponColumnToDokter extends Migration
{
    public function up()
    {
        $this->forge->addColumn('dokter', [
            'no_lisensi' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'id_spesialisasi'
            ],
            'telepon_dokter' => [
                'type' => 'varchar',
                'constraint' => 15,
                'null' => true,
                'after' => 'no_lisensi'
            ]
        ]);
    }


    public function down()
    {
        $this->forge->dropColumn('dokter', ['no_lisensi', 'telepon_dokter']);
    }
}
