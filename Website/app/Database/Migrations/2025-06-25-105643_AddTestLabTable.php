<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTestLabTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_test_lab'     => [
                'type'           => 'INT',
                'auto_increment' => true,
                'unsigned'       => true,
            ],
            'id_janji_temu'   => [
                'type'           => 'VARCHAR',
                'constraint'     => 32,
                'null'           => false,
            ],
            'tanggal_test'    => [
                'type'           => 'DATETIME',
                'null'           => false,
            ],
            'jenis_test'      => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'null'           => false,
            ],
            'id_petugas_lab'  => [
                'type'           => 'VARCHAR',
                'constraint'     => 30,
                'null'           => true,
            ],
            'price'           => [
                'type'           => 'DECIMAL',
                'constraint'     => '15,2',
                'default'        => 0,
            ],
            'hasil_test'      => [
                'type'           => 'TEXT',
                'null'           => true,
            ],
            'status'          => [
                'type'           => 'ENUM',
                'constraint'     => ['ordered', 'in_progress', 'completed', 'cancelled'],
                'default'        => 'ordered',
            ],
            'created_by'      => [
                'type'           => 'VARCHAR',
                'constraint'     => 32,
                'null'           => true,
            ],
            'created_at'      => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
            'updated_by'      => [
                'type'           => 'VARCHAR',
                'constraint'     => 32,
                'null'           => true,
            ],
            'updated_at'      => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
        ]);

        $this->forge->addKey('id_test_lab', true);
        $this->forge->addForeignKey('id_janji_temu', 'janji_temu', 'id_janji_temu', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_petugas_lab', 'petugas_lab', 'id_petugas_lab', 'SET NULL', 'CASCADE');
        $this->forge->createTable('test_lab', true);
    }

    public function down()
    {
        $this->forge->dropTable('test_lab', true);
    }
}
