<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSpesialisasiLab extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_spesialisasi_lab' => ['type' => 'VARCHAR', 'constraint' => 10],
            'nama_spesialisasi'   => ['type' => 'VARCHAR', 'constraint' => 100],
            'deskripsi'           => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id_spesialisasi_lab', true);
        $this->forge->createTable('spesialisasi_lab');
    }

    public function down()
    {
        $this->forge->dropTable('spesialisasi_lab');
    }
}
