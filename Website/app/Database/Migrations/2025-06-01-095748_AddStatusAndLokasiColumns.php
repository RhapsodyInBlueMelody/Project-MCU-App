<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusAndLokasiColumns extends Migration
{
    public function up()
    {
        // Add 'status' to 'users' table
        $fieldsUsers = [
            'status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'null' => false,
                'after' => 'role',
            ],
        ];
        $this->forge->addColumn('users', $fieldsUsers);

        // Add 'lokasi' to 'pasien' table
        $fieldsPasien = [
            'lokasi' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => false,
                'after' => 'alamat', // adjust as needed
            ],
        ];
        $this->forge->addColumn('pasien', $fieldsPasien);
    }

    public function down()
    {
        // Remove 'status' from 'users' table
        $this->forge->dropColumn('users', 'status');
        // Remove 'lokasi' from 'pasien' table
        $this->forge->dropColumn('pasien', 'lokasi');
    }
}
