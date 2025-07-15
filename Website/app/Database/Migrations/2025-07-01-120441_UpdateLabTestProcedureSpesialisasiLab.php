<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateLabTestProcedureSpesialisasiLab extends Migration
{
    public function up()
    {
        // Change the column type
        $this->forge->modifyColumn('lab_test_procedure', [
            'id_spesialisasi_lab' => [
                'name' => 'id_spesialisasi_lab',
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => false,
            ],
        ]);
        $this->db->query('
            ALTER TABLE `lab_test_procedure`
            ADD CONSTRAINT `fk_lab_test_procedure_spesialisasi_lab`
            FOREIGN KEY (`id_spesialisasi_lab`)
            REFERENCES `spesialisasi_lab`(`id_spesialisasi_lab`)
            ON UPDATE CASCADE;
            ');
    }

    public function down()
    {
        $this->forge->modifyColumn('lab_test_procedure', [
            'id_spesialisasi_lab' => [
                'name' => 'id_spesialisasi_lab',
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
        ]);
        $this->db->query('
            ALTER TABLE `lab_test_procedure`
            DROP FOREIGN KEY `fk_lab_test_procedure_spesialisasi_lab_id`
            ');
    }
}
