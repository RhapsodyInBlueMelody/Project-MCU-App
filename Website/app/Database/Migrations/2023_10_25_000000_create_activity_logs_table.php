<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateActivityLogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "log_id" => [
                "type" => "INT",
                "constraint" => 11,
                "unsigned" => true,
                "auto_increment" => true,
            ],
            "user_id" => [
                "type" => "INT",
                "constraint" => 11,
                "unsigned" => true,
                "null" => false,
            ],
            "action" => [
                "type" => "VARCHAR",
                "constraint" => 100,
                "null" => false,
            ],
            "description" => [
                "type" => "TEXT",
                "null" => true,
            ],
            "ip_address" => [
                "type" => "VARCHAR",
                "constraint" => 45,
                "null" => true,
            ],
            "user_agent" => [
                "type" => "VARCHAR",
                "constraint" => 255,
                "null" => true,
            ],
            "additional_data" => [
                "type" => "TEXT",
                "null" => true,
            ],
            "created_at" => [
                "type" => "DATETIME",
                "null" => false,
            ],
        ]);
        $this->forge->addKey("log_id", true);
        $this->forge->addKey("user_id");
        $this->forge->addKey("action");
        $this->forge->createTable("activity_logs");
    }

    public function down()
    {
        $this->forge->dropTable("activity_logs");
    }
}
