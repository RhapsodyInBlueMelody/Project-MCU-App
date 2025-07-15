<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSpesialisasiLabToLabTestProcedure extends Migration
{
    public function up()
    {
        // Add the new column
        $this->forge->addColumn('lab_test_procedure', [
            'id_spesialisasi_lab' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true, // Allow null for legacy data, update later
                'after'      => 'price'
            ],
        ]);
    }

    public function down()
    {
        // Remove the column if rolled back
        $this->forge->dropColumn('lab_test_procedure', 'id_spesialisasi_lab');
    }
}
