<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJanjiTemuTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_janji_temu' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_janji' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'id_pasien' => [
                'type'       => 'VARCHAR',
                'constraint' => 32,
                'null'       => false,
            ],
            'tanggal_janji' => [
                'type' => 'DATE',
            ],
            'waktu_janji' => [
                'type' => 'TIME',
            ],
            'id_dokter' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
            ],
            'id_paket' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'confirmed', 'completed', 'cancelled', 'awaiting_lab_results'],
                'default'    => 'pending',
            ],
            'created_by' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_by' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_janji_temu', true);
        $this->forge->addForeignKey('id_pasien', 'pasien', 'id_pasien', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_dokter', 'dokter', 'id_dokter', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_paket', 'paket', 'id_paket', 'SET NULL', 'CASCADE');
        $this->forge->createTable('janji_temu');
    }

    public function down()
    {
        $this->forge->dropTable('janji_temu', true);
    }
}
