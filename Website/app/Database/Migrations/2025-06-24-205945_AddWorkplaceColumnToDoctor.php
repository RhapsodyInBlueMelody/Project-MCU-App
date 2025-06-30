<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWorkplaceColumnToDoctor extends Migration
{
    public function up()
    {
        $fields = [
            'lokasi_kerja' => [
                'type' => 'ENUM',
                'constraint' => ['Jakarta', 'Surabaya', 'Bandung'],
                'null' => false,
                'after' => 'no_lisensi',
            ]
        ];

        $this->forge->addColumn('dokter', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('dokter', 'lokasi_kerja');
    }
}
