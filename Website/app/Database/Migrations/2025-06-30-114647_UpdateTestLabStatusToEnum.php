<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateTestLabStatusEnum extends Migration
{
    public function up()
    {
        // MySQL/MariaDB only: Change status to ENUM type
        $this->db->query("
            ALTER TABLE `test_lab`
            MODIFY `status` ENUM('ordered', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'ordered'
        ");
    }

    public function down()
    {
        // Roll back to VARCHAR(30)
        $this->db->query("
            ALTER TABLE `test_lab`
            MODIFY `status` VARCHAR(30) NOT NULL DEFAULT 'ordered'
        ");
    }
}
