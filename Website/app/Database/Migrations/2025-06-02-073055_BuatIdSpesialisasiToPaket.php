<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BuatIdSpesialisasiToPaket extends Migration
{
    public function up()
    {
        $this->forge->addColumn('paket', [
            'id_spesialisasi' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'harga',
            ],
        ]);

        // Add foreign key constraint
        $this->db->query(
            'ALTER TABLE paket
             ADD CONSTRAINT paket_id_spesialisasi_foreign
             FOREIGN KEY (id_spesialisasi)
             REFERENCES spesialisasi(id_spesialisasi)
             ON DELETE SET NULL
             ON UPDATE CASCADE'
        );
    }

    public function down()
    {
        // Drop foreign key first
        $this->db->query(
            'ALTER TABLE paket
             DROP FOREIGN KEY paket_id_spesialisasi_foreign'
        );

        // Drop the column
        $this->forge->dropColumn('paket', 'id_spesialisasi');
    }
}
