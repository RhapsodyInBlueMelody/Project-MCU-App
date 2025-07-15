<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddForeignKeytoPetugasLabIdSpesialisasiLab extends Migration
{
    public function up()
    {
        $this->db->query('
            ALTER TABLE `petugas_lab`
            ADD CONSTRAINT `fk_petugas_lab_id_spesialisasi_lab`
            FOREIGN KEY (`id_spesialisasi_lab`)
            REFERENCES `spesialisasi_lab`(`id_spesialisasi_lab`)
            ON UPDATE CASCADE;
            ');
    }

    public function down()
    {
        $this->db->query('
            ALTER TABLE `petugas_lab`
            DROP FOREIGN KEY `fk_petugas_lab_id_spesialisasi_lab`
            ');
    }
}
