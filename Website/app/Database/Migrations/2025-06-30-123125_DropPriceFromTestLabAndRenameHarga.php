<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropPriceFromTestLabAndRenameHarga extends Migration
{
    public function up()
    {
        // Drop 'price' from 'test_lab'
        if ($this->db->fieldExists('price', 'test_lab')) {
            $this->forge->dropColumn('test_lab', 'price');
        }

        // Rename 'harga' to 'price' in 'lab_test_procedure' (if you want to standardize)
        if ($this->db->fieldExists('harga', 'lab_test_procedure')) {
            $fields = [
                'harga' => [
                    'name' => 'price',
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00,
                    'null' => false,
                ]
            ];
            $this->forge->modifyColumn('lab_test_procedure', $fields);
        }
    }

    public function down()
    {
        // Add 'price' back to 'test_lab'
        if (!$this->db->fieldExists('price', 'test_lab')) {
            $fields = [
                'price' => [
                    'type' => 'DECIMAL',
                    'constraint' => '15,2',
                    'default' => 0.00,
                    'null' => false,
                    'after' => 'id_petugas_lab' // Or wherever you want
                ]
            ];
            $this->forge->addColumn('test_lab', $fields);
        }

        // Rename 'price' back to 'harga' in 'lab_test_procedure'
        if ($this->db->fieldExists('price', 'lab_test_procedure')) {
            $fields = [
                'price' => [
                    'name' => 'harga',
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00,
                    'null' => false,
                ]
            ];
            $this->forge->modifyColumn('lab_test_procedure', $fields);
        }
    }
}
