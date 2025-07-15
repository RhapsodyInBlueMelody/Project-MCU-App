<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddApprovedColumnToTestLab extends Migration
{
    public function up()
    {
        $this->forge->addColumn('test_lab', [
            'approved_by_dokter' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => 'status'
            ],
            'approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'approved_by_dokter',
            ]
        ]);


        $this->db->query('
            ALTER TABLE `test_lab`
            ADD CONSTRAINT `fk_test_lab_approved_by_dokter`
            FOREIGN KEY (`approved_by_dokter`)
            REFERENCES `janji_temu`(`id_dokter`)
            ON UPDATE CASCADE;
            ');
    }

    public function down()
    {
        $this->db->query('
            ALTER TABLE `test_lab`
            DROP FOREIGN KEY `fk_test_lab_approved_by_dokter`
            ');

        $this->forge->dropColumn('test_lab', ['approved_by_dokter', 'approved_at']);
    }
}
