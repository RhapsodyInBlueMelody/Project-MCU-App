<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDokterJadwal extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_jadwal'   => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'id_dokter'   => ['type' => 'VARCHAR', 'constraint' => 30], // Match your id_dokter type!
            'hari'        => ['type' => 'ENUM', 'constraint' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']],
            'jam_mulai'   => ['type' => 'TIME'],
            'jam_selesai' => ['type' => 'TIME'],
            'lokasi'      => ['type' => 'ENUM', 'constraint' => ['Jakarta', 'Bandung', 'Surabaya'], 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_jadwal', true);
        $this->forge->addForeignKey('id_dokter', 'dokter', 'id_dokter', 'CASCADE', 'CASCADE');
        $this->forge->createTable('dokter_jadwal');
    }

    public function down()
    {
        $this->forge->dropTable('dokter_jadwal');
    }
}
