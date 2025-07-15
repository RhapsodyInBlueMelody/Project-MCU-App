<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateLabTestTable extends Migration
{
    public function up()
    {
        // Add missing columns if they do not exist
        $fields = [];

        if (!$this->db->fieldExists('id_janji_temu', 'test_lab')) {
            $fields['id_janji_temu'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ];
        }

        if (!$this->db->fieldExists('jenis_test', 'test_lab')) {
            $fields['jenis_test'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ];
        }

        if (!$this->db->fieldExists('tanggal_test', 'test_lab')) {
            $fields['tanggal_test'] = [
                'type'       => 'DATETIME',
                'null'       => false,
                'default'    => '1970-01-01 00:00:00',
            ];
        }

        if (!$this->db->fieldExists('id_petugas_lab', 'test_lab')) {
            $fields['id_petugas_lab'] = [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ];
        }

        if (!$this->db->fieldExists('price', 'test_lab')) {
            $fields['price'] = [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
                'null'       => false,
            ];
        }

        if (!$this->db->fieldExists('hasil_test', 'test_lab')) {
            $fields['hasil_test'] = [
                'type' => 'TEXT',
                'null' => true,
            ];
        }

        if (!$this->db->fieldExists('status', 'test_lab')) {
            $fields['status'] = [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'ordered',
                'null'       => false,
            ];
        }

        if (!$this->db->fieldExists('created_by', 'test_lab')) {
            $fields['created_by'] = [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ];
        }

        if (!$this->db->fieldExists('created_at', 'test_lab')) {
            $fields['created_at'] = [
                'type'    => 'DATETIME',
                'null'    => false,
                'default' => '1970-01-01 00:00:00',
            ];
        }

        if (!$this->db->fieldExists('updated_by', 'test_lab')) {
            $fields['updated_by'] = [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ];
        }

        if (!$this->db->fieldExists('updated_at', 'test_lab')) {
            $fields['updated_at'] = [
                'type'    => 'DATETIME',
                'null'    => true,
            ];
        }

        // Example: Add a "notes" field if you want to support lab comments
        if (!$this->db->fieldExists('notes', 'test_lab')) {
            $fields['notes'] = [
                'type' => 'TEXT',
                'null' => true,
            ];
        }

        // Add all new fields at once
        if (!empty($fields)) {
            $this->forge->addColumn('test_lab', $fields);
        }

        // Modify existing columns if needed
        // Example: Make status field longer for more values
        $this->forge->modifyColumn('test_lab', [
            'status' => [
                'name'       => 'status',
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'ordered',
                'null'       => false,
            ],
        ]);

        // Add foreign key for id_janji_temu if not present
        // (If you already have it, this will be ignored)
        $this->db->query('
            ALTER TABLE `test_lab`
            ADD CONSTRAINT `fk_testlab_janjitemu`
            FOREIGN KEY (`id_janji_temu`) REFERENCES `janji_temu`(`id_janji_temu`)
            ON DELETE CASCADE ON UPDATE CASCADE
        ');

        // Optional: add FK for id_petugas_lab if desired and table exists
        /*
        $this->db->query('
            ALTER TABLE `test_lab`
            ADD CONSTRAINT `fk_testlab_petugaslab`
            FOREIGN KEY (`id_petugas_lab`) REFERENCES `petugas_lab`(`id_petugas_lab`)
            ON DELETE SET NULL ON UPDATE CASCADE
        ');
        */
    }

    public function down()
    {
        // Rollback: drop fields that were added
        $fields = [
            'notes',
        ];
        foreach ($fields as $field) {
            if ($this->db->fieldExists($field, 'test_lab')) {
                $this->forge->dropColumn('test_lab', $field);
            }
        }

        // Optionally, drop foreign keys if needed
        $this->db->query('ALTER TABLE `test_lab` DROP FOREIGN KEY `fk_testlab_janjitemu`');
        // $this->db->query('ALTER TABLE `test_lab` DROP FOREIGN KEY `fk_testlab_petugaslab`');
    }
}
