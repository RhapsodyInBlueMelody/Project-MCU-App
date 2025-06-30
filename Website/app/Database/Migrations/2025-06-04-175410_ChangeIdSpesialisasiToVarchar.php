<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeIdSpesialisasiToVarchar extends Migration
{
    public function up()
    {
        // 1. Drop foreign keys from 'dokter' and 'paket' tables
        $this->db->query('ALTER TABLE dokter DROP FOREIGN KEY dokter_id_spesialisasi_foreign');
        $this->db->query('ALTER TABLE paket DROP FOREIGN KEY paket_id_spesialisasi_foreign');

        // 2. Change primary key in 'spesialisasi' to VARCHAR
        $this->db->query('ALTER TABLE spesialisasi MODIFY id_spesialisasi VARCHAR(10) NOT NULL');
        $this->db->query('ALTER TABLE spesialisasi DROP PRIMARY KEY, ADD PRIMARY KEY (id_spesialisasi)');

        // 3. Change related foreign key columns in 'dokter' and 'paket' to VARCHAR
        $this->db->query('ALTER TABLE dokter MODIFY id_spesialisasi VARCHAR(10)');
        $this->db->query('ALTER TABLE paket MODIFY id_spesialisasi VARCHAR(10)');

        // 4. Re-add foreign keys
        $this->db->query('ALTER TABLE dokter ADD CONSTRAINT dokter_id_spesialisasi_fk FOREIGN KEY (id_spesialisasi) REFERENCES spesialisasi(id_spesialisasi) ON DELETE RESTRICT ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE paket ADD CONSTRAINT paket_id_spesialisasi_fk FOREIGN KEY (id_spesialisasi) REFERENCES spesialisasi(id_spesialisasi) ON DELETE RESTRICT ON UPDATE CASCADE');
    }

    public function down()
    {
        // 1. Drop the re-added foreign keys
        $this->db->query('ALTER TABLE dokter DROP FOREIGN KEY dokter_id_spesialisasi_fk');
        $this->db->query('ALTER TABLE paket DROP FOREIGN KEY paket_id_spesialisasi_fk');

        // 2. Change primary key in 'spesialisasi' back to BIGINT UNSIGNED AUTO_INCREMENT
        $this->db->query('ALTER TABLE spesialisasi MODIFY id_spesialisasi BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->db->query('ALTER TABLE spesialisasi DROP PRIMARY KEY, ADD PRIMARY KEY (id_spesialisasi)');

        // 3. Revert 'dokter' and 'paket' columns to BIGINT UNSIGNED
        $this->db->query('ALTER TABLE dokter MODIFY id_spesialisasi BIGINT UNSIGNED');
        $this->db->query('ALTER TABLE paket MODIFY id_spesialisasi BIGINT UNSIGNED');

        // 4. Re-add original foreign keys (same names or assumed default names)
        $this->db->query('ALTER TABLE dokter ADD CONSTRAINT dokter_id_spesialisasi_foreign FOREIGN KEY (id_spesialisasi) REFERENCES spesialisasi(id_spesialisasi) ON DELETE RESTRICT ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE paket ADD CONSTRAINT paket_id_spesialisasi_foreign FOREIGN KEY (id_spesialisasi) REFERENCES spesialisasi(id_spesialisasi) ON DELETE RESTRICT ON UPDATE CASCADE');
    }
}
