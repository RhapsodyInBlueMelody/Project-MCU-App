<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeDokterIdToVarchar extends Migration
{
    public function up()
    {
        // 1. Drop foreign keys
        $this->db->query('ALTER TABLE janji_temu DROP FOREIGN KEY janji_temu_id_dokter_foreign;');
        $this->db->query('ALTER TABLE diagnosis DROP FOREIGN KEY diagnosis_id_dokter_foreign;');

        // 2. Change column types to VARCHAR(30) and NOT NULL
        $this->forge->modifyColumn('dokter', [
            'id_dokter' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => false,
            ],
        ]);
        $this->forge->modifyColumn('diagnosis', [
            'id_dokter' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => false,
            ],
        ]);
        $this->forge->modifyColumn('janji_temu', [
            'id_dokter' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => false,
            ],
        ]);

        // 3. Re-create foreign keys with the new type
        $this->db->query(
            'ALTER TABLE janji_temu ADD CONSTRAINT janji_temu_id_dokter_foreign FOREIGN KEY (id_dokter) REFERENCES dokter(id_dokter) ON DELETE CASCADE ON UPDATE CASCADE;'
        );
        $this->db->query(
            'ALTER TABLE diagnosis ADD CONSTRAINT diagnosis_id_dokter_foreign FOREIGN KEY (id_dokter) REFERENCES dokter(id_dokter) ON DELETE CASCADE ON UPDATE CASCADE;'
        );
    }

    public function down()
    {
        // Drop new foreign keys
        $this->db->query('ALTER TABLE janji_temu DROP FOREIGN KEY janji_temu_id_dokter_foreign;');
        $this->db->query('ALTER TABLE diagnosis DROP FOREIGN KEY diagnosis_id_dokter_foreign;');

        // Revert columns to INT (adjust the type as per your original schema!)
        $this->forge->modifyColumn('dokter', [
            'id_dokter' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'unsigned' => true,
                'auto_increment' => false,
            ],
        ]);
        $this->forge->modifyColumn('diagnosis', [
            'id_dokter' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'unsigned' => true,
            ],
        ]);
        $this->forge->modifyColumn('janji_temu', [
            'id_dokter' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'unsigned' => true,
            ],
        ]);

        // Re-create original foreign keys (adjust as needed)
        $this->db->query(
            'ALTER TABLE janji_temu ADD CONSTRAINT janji_temu_id_dokter_foreign FOREIGN KEY (id_dokter) REFERENCES dokter(id_dokter) ON DELETE CASCADE ON UPDATE CASCADE;'
        );
        $this->db->query(
            'ALTER TABLE diagnosis ADD CONSTRAINT diagnosis_id_dokter_foreign FOREIGN KEY (id_dokter) REFERENCES dokter(id_dokter) ON DELETE CASCADE ON UPDATE CASCADE;'
        );
    }
}
