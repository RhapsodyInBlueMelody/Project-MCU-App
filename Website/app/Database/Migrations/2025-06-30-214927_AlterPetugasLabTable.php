<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterPetugasLabTable extends Migration
{
    public function up()
    {
        $fields = [
            'user_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 32,
                'after'      => 'id_petugas_lab',
                'null'       => true,
            ],
            'id_spesialisasi_lab' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'after'      => 'nama_petugas_lab',
                'null'       => true,
            ],
            'no_lisensi' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'after'      => 'id_spesialisasi_lab',
                'null'       => true,
            ],
            'telepon_petugas_lab' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'after'      => 'no_lisensi',
                'null'       => true,
            ],
            'alamat_petugas_lab' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'after'      => 'telepon_petugas_lab',
                'null'       => true,
            ],
            'jenis_kelamin' => [
                'type'       => 'ENUM',
                'constraint' => ['L', 'P'],
                'after'      => 'alamat_petugas_lab',
                'null'       => true,
            ],
            'tanggal_lahir' => [
                'type'       => 'DATE',
                'after'      => 'jenis_kelamin',
                'null'       => true,
            ],
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'after'      => 'tanggal_lahir',
                'null'       => true,
            ],
            'is_verified' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'after'      => 'foto',
                'default'    => 0,
            ],
            'verification_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'after'      => 'is_verified',
                'default'    => 'pending',
            ],
            'verification_notes' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'after'      => 'verification_status',
                'null'       => true,
            ],
            'lokasi_kerja' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'after'      => 'verification_notes',
                'null'       => true,
            ],
        ];

        $this->forge->addColumn('petugas_lab', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('petugas_lab', [
            'user_id',
            'id_spesialisasi_lab',
            'no_lisensi',
            'telepon_petugas_lab',
            'alamat_petugas_lab',
            'jenis_kelamin',
            'tanggal_lahir',
            'foto',
            'is_verified',
            'verification_status',
            'verification_notes',
            'lokasi_kerja',
        ]);
    }
}
