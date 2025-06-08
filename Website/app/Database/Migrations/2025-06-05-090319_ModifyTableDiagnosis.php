<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyDiagnosisTable extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Drop existing foreign key constraints
        $db->query('ALTER TABLE diagnosis DROP FOREIGN KEY diagnosis_id_janji_temu_foreign;');
        $db->query('ALTER TABLE diagnosis DROP FOREIGN KEY diagnosis_id_dokter_foreign;');
        $db->query('ALTER TABLE diagnosis DROP FOREIGN KEY diagnosis_id_pasien_foreign;');
        // Don't drop FK on nama_petugas_lab because (should not have one)

        // 2. Rename `id` to `id_diagnosis`
        $db->query('ALTER TABLE diagnosis CHANGE id id_diagnosis VARCHAR(30) NOT NULL ;');
        $db->query('ALTER TABLE petugas_lab CHANGE id_petugas_lab VARCHAR(30) NOT NULL ;');

        // 3. Modify id_janji_temu type if needed
        $db->query('ALTER TABLE diagnosis MODIFY COLUMN id_janji_temu VARCHAR(30) NOT NULL;');
        $db->query('ALTER TABLE janji_temu MODIFY COLUMN id_janji_temu VARCHAR(30) NOT NULL;');

        // 4. Add columns if not exist (using correct case)
        $fields = $db->getFieldNames('diagnosis');
        if (!in_array('hasil_lab', $fields)) {
            $this->forge->addColumn('diagnosis', [
                'hasil_lab' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'notes'
                ]
            ]);
        }
        if (!in_array('tanggal_hasil_lab', $fields)) {
            $this->forge->addColumn('diagnosis', [
                'tanggal_hasil_lab' => [
                    'type' => 'DATE',
                    'null' => true,
                    'after' => 'hasil_lab'
                ]
            ]);
        }
        if (!in_array('id_petugas_lab', $fields)) {
            $this->forge->addColumn('diagnosis', [
                'id_petugas_lab' => [
                    'type' => 'VARCHAR',
                    'constraint' => '30',
                    'null' => true,
                    'after' => 'id_pasien'
                ]
            ]);
        }
        if (!in_array('nama_petugas_lab', $fields)) {
            $this->forge->addColumn('diagnosis', [
                'nama_petugas_lab' => [
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                    'after' => 'id_petugas_lab'
                ]
            ]);
        }
        $this->forge->modifyColumn('petugas_lab', [
            'id_petugas_lab' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => false],
        ]);

        // 5. Modify created_at and updated_at to allow nulls
        $db->query('ALTER TABLE diagnosis MODIFY COLUMN created_at DATETIME NULL DEFAULT NULL;');
        $db->query('ALTER TABLE diagnosis MODIFY COLUMN updated_at DATETIME NULL DEFAULT NULL;');

        // 6. Re-add foreign key constraints (do NOT add one for nama_petugas_lab)
        $db->query('ALTER TABLE diagnosis ADD CONSTRAINT diagnosis_id_janji_temu_foreign FOREIGN KEY (id_janji_temu) REFERENCES janji_temu(id_janji_temu) ON DELETE CASCADE ON UPDATE CASCADE;');
        $db->query('ALTER TABLE diagnosis ADD CONSTRAINT diagnosis_id_dokter_foreign FOREIGN KEY (id_dokter) REFERENCES dokter(id_dokter) ON DELETE CASCADE ON UPDATE CASCADE;');
        $db->query('ALTER TABLE diagnosis ADD CONSTRAINT diagnosis_id_pasien_foreign FOREIGN KEY (id_pasien) REFERENCES pasien(id_pasien) ON DELETE CASCADE ON UPDATE CASCADE;');
        $db->query('ALTER TABLE diagnosis ADD CONSTRAINT diagnosis_id_petugas_lab_foreign FOREIGN KEY (id_petugas_lab) REFERENCES petugas_lab(id_petugas_lab) ON DELETE CASCADE ON UPDATE CASCADE;');
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $db->query('ALTER TABLE diagnosis DROP FOREIGN KEY diagnosis_id_janji_temu_foreign;');
        $db->query('ALTER TABLE diagnosis DROP FOREIGN KEY diagnosis_id_dokter_foreign;');
        $db->query('ALTER TABLE diagnosis DROP FOREIGN KEY diagnosis_id_pasien_foreign;');
        $db->query('ALTER TABLE diagnosis DROP FOREIGN KEY diagnosis_id_petugas_lab_foreign;');

        $fields = $db->getFieldNames('diagnosis');
        if (in_array('hasil_lab', $fields)) {
            $this->forge->dropColumn('diagnosis', 'hasil_lab');
        }
        if (in_array('tanggal_hasil_lab', $fields)) {
            $this->forge->dropColumn('diagnosis', 'tanggal_hasil_lab');
        }
        if (in_array('nama_petugas_lab', $fields)) {
            $this->forge->dropColumn('diagnosis', 'nama_petugas_lab');
        }
        if (in_array('id_petugas_lab', $fields)) {
            $this->forge->dropColumn('diagnosis', 'id_petugas_lab');
        }
        // Rename id_diagnosis back to id if you want to revert
        $db->query('ALTER TABLE diagnosis CHANGE id_diagnosis id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;');
        // You may want to revert created_at, updated_at to NOT NULL, up to you
    }
}
