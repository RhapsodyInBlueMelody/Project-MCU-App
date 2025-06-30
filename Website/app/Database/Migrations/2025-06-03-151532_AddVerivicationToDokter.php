<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVerificationToDokter extends Migration
{
    public function up()
    {
        $this->forge->addColumn('dokter', [
            'is_verified' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'id_spesialisasi',
            ],
            'verification_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default'    => 'pending',
                'after'      => 'is_verified',
            ],
            'verification_notes' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'verification_status',
            ],
            'verified_by' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'verification_notes',
            ],
            'verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'verified_by',
            ],
        ]);
        // Optionally, add foreign key to admin table for verified_by
        $this->db->query(
            'ALTER TABLE dokter
             ADD CONSTRAINT dokter_verified_by_admin_fk
             FOREIGN KEY (verified_by)
             REFERENCES admin(id_admin)
             ON DELETE SET NULL ON UPDATE CASCADE'
        );
    }

    public function down()
    {
        $this->db->query(
            'ALTER TABLE dokter DROP FOREIGN KEY dokter_verified_by_admin_fk'
        );
        $this->forge->dropColumn('dokter', [
            'is_verified',
            'verification_status',
            'verification_notes',
            'verified_by',
            'verified_at',
        ]);
    }
}
