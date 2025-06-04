<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeIdPasienToVarchar extends Migration
{
    public function up()
    {
        // 1. Drop FK on transaksi
        $this->db->query('ALTER TABLE transaksi DROP FOREIGN KEY transaksi_id_pasien_foreign;');

        // 2. Change id_pasien type in pasien
        $this->forge->modifyColumn('pasien', [
            'id_pasien' => [
                'name' => 'id_pasien',
                'type' => 'VARCHAR',
                'constraint' => 32,
                'null' => false,
            ],
        ]);

        // 3. Change id_pasien type in transaksi
        $this->forge->modifyColumn('transaksi', [
            'id_pasien' => [
                'name' => 'id_pasien',
                'type' => 'VARCHAR',
                'constraint' => 32,
                'null' => false,
            ],
        ]);

        // 4. Re-add FK
        $this->db->query('ALTER TABLE transaksi ADD CONSTRAINT transaksi_id_pasien_foreign FOREIGN KEY (id_pasien) REFERENCES pasien(id_pasien);');
    }

    public function down()
    {
        // Drop FK
        $this->db->query('ALTER TABLE transaksi DROP FOREIGN KEY transaksi_id_pasien_foreign;');

        // Revert column type in pasien
        $this->forge->modifyColumn('pasien', [
            'id_pasien' => [
                'name' => 'id_pasien',
                'type' => 'BIGINT',
                'constraint' => 20,
                'null' => false,
            ],
        ]);

        // Revert column type in transaksi
        $this->forge->modifyColumn('transaksi', [
            'id_pasien' => [
                'name' => 'id_pasien',
                'type' => 'BIGINT',
                'constraint' => 20,
                'null' => false,
            ],
        ]);

        // Re-add FK
        $this->db->query('ALTER TABLE transaksi ADD CONSTRAINT transaksi_id_pasien_foreign FOREIGN KEY (id_pasien) REFERENCES pasien(id_pasien);');
    }
}
