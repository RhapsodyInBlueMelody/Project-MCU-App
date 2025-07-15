<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyTableJanjiTemu extends Migration
{
    public function up()
    {
        // 1. Change ENUM and add 'rejected' status
        $this->db->query("
            ALTER TABLE janji_temu 
            MODIFY COLUMN status 
                ENUM('pending', 'confirmed', 'completed', 'cancelled', 'awaiting_lab_results', 'rejected')
                NOT NULL DEFAULT 'pending'
        ");

        // 2. Add rejection_reason (nullable TEXT)
        $this->forge->addColumn('janji_temu', [
            'rejection_reason' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'status',
            ],
        ]);

        // 3. Add doctor_notes (nullable TEXT)
        $this->forge->addColumn('janji_temu', [
            'doctor_notes' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'rejection_reason',
            ],
        ]);
    }

    public function down()
    {
        // Remove doctor_notes and rejection_reason columns
        $this->forge->dropColumn('janji_temu', 'doctor_notes');
        $this->forge->dropColumn('janji_temu', 'rejection_reason');

        // Revert ENUM (remove 'rejected')
        $this->db->query("
            ALTER TABLE janji_temu 
            MODIFY COLUMN status 
                ENUM('pending', 'confirmed', 'completed', 'cancelled', 'awaiting_lab_results')
                NOT NULL DEFAULT 'pending'
        ");
    }
}
