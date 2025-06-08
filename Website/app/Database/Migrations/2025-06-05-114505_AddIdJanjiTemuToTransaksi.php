<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdJanjiTemuToTransaksi extends Migration
{
    public function up()
    {
        $fields = [
            'id_janji_temu' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true, // or false if every transaksi must have an appointment
                'after' => 'id_paket' // position after id_paket, adjust as needed
            ]
        ];
        $this->forge->addColumn('transaksi', $fields);

        // Add FK if you want referential integrity
        $db = \Config\Database::connect();
        $db->query('ALTER TABLE transaksi ADD CONSTRAINT transaksi_id_janji_temu_foreign FOREIGN KEY (id_janji_temu) REFERENCES janji_temu(id_janji_temu) ON DELETE SET NULL ON UPDATE CASCADE;');
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $db->query('ALTER TABLE transaksi DROP FOREIGN KEY transaksi_id_janji_temu_foreign;');
        $this->forge->dropColumn('transaksi', 'id_janji_temu');
    }
}
