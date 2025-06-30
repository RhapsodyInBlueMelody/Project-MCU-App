<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToPasienTable extends Migration
{
    public function up()
    {
        // Add user_id after id_pasien
        $this->forge->addColumn('pasien', [
            'user_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
                'after' => 'id_pasien',
            ]
        ]);

        // Add no_identitas after user_id
        $this->forge->addColumn('pasien', [
            'no_identitas' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'user_id',
            ]
        ]);

        // Add tempat_lahir after nama_pasien
        $this->forge->addColumn('pasien', [
            'tempat_lahir' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'nama_pasien',
            ]
        ]);

        // Add tanggal_lahir after tempat_lahir
        $this->forge->addColumn('pasien', [
            'tanggal_lahir' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'tempat_lahir',
            ]
        ]);

        // Add jenis_kelamin after tanggal_lahir
        $this->forge->addColumn('pasien', [
            'jenis_kelamin' => [
                'type' => 'CHAR',
                'constraint' => 1,
                'null' => true,
                'after' => 'tanggal_lahir',
            ]
        ]);

        // Add no_telp_pasien after jenis_kelamin
        $this->forge->addColumn('pasien', [
            'no_telp_pasien' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
                'null' => true,
                'after' => 'jenis_kelamin',
            ]
        ]);
    }

    public function down()
    {
        // Drop in reverse order
        $this->forge->dropColumn('pasien', 'no_telp_pasien');
        $this->forge->dropColumn('pasien', 'jenis_kelamin');
        $this->forge->dropColumn('pasien', 'tanggal_lahir');
        $this->forge->dropColumn('pasien', 'tempat_lahir');
        $this->forge->dropColumn('pasien', 'no_identitas');
        $this->forge->dropColumn('pasien', 'user_id');
    }
}
