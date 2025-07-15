<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLabTestProcedureAndUpdateTestLab extends Migration
{
    public function up()
    {
        // 1. Create lab_test_procedure table
        $this->forge->addField([
            'id_lab_test_procedure' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_procedure' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'harga' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
                'null'       => false,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
        ]);
        $this->forge->addKey('id_lab_test_procedure', true);
        $this->forge->createTable('lab_test_procedure', true);

        // 2. Update test_lab table: add id_lab_test_procedure and make it a FK
        if (!$this->db->fieldExists('id_lab_test_procedure', 'test_lab')) {
            $this->forge->addColumn('test_lab', [
                'id_lab_test_procedure' => [
                    'type'     => 'INT',
                    'unsigned' => true,
                    'null'     => true, // temporarily allow null for migration
                    'after'    => 'id_janji_temu',
                ]
            ]);
        }

        // If the old 'jenis_test' column exists, you can keep it for legacy/data migration or drop it later
        // $this->forge->dropColumn('test_lab', 'jenis_test');

        // 3. Add foreign key constraint
        $this->db->query('
            ALTER TABLE `test_lab`
            ADD CONSTRAINT `fk_testlab_labtestprocedure`
            FOREIGN KEY (`id_lab_test_procedure`) REFERENCES `lab_test_procedure`(`id_lab_test_procedure`)
            ON UPDATE CASCADE ON DELETE SET NULL
        ');
    }

    public function down()
    {
        // Remove FK and column from test_lab
        $this->db->query('ALTER TABLE `test_lab` DROP FOREIGN KEY `fk_testlab_labtestprocedure`');
        $this->forge->dropColumn('test_lab', 'id_lab_test_procedure');

        // Drop lab_test_procedure table
        $this->forge->dropTable('lab_test_procedure', true);
    }
}
