<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropJenisTestFromTestLab extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('jenis_test', 'test_lab')) {
            $this->forge->dropColumn('test_lab', 'jenis_test');
        }
    }

    public function down()
    {
        if (!$this->db->fieldExists('jenis_test', 'test_lab')) {
            $fields = [
                'jenis_test' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => true,
                    'after' => 'id_lab_test_procedure', // or place it as needed
                ],
            ];
            $this->forge->addColumn('test_lab', $fields);
        }
    }
}
