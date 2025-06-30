<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddingAddressGenderTanggalLahirToDoktorTable extends Migration
{
    public function up()
    {
        $fields = [
            'alamat_dokter' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'no_lisensi',
            ],
            'jenis_kelamin' => [
                'type' => 'ENUM',
                'constraint' => ['P', 'L'],
                'null' => false,
                'after' => 'nama_dokter',
            ],
            'tanggal_lahir' => [
                'type' => 'DATE',
                'null' => false,
                'after' => 'jenis_kelamin',
            ],
        ];

        $this->forge->addColumn('dokter', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('dokter', 'alamat_dokter');
        $this->forge->dropColumn('dokter', 'jenis_kelamin');
        $this->forge->dropColumn('dokter', 'tanggal_lahir');
    }
}
