<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GenerateUserId extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Create table to store last sequence info
        $db->query("
            CREATE TABLE IF NOT EXISTS user_id_seq (
                last_date DATE NOT NULL,
                last_seq INT NOT NULL DEFAULT 0
            );
        ");

        // Ensure at least one row exists
        $count = $db->query("SELECT COUNT(*) AS total FROM user_id_seq")->getRow()->total;
        if ($count == 0) {
            $db->query("INSERT INTO user_id_seq (last_date, last_seq) VALUES (CURDATE(), 0);");
        }

        // 2. Create stored procedure
        $db->query("DROP PROCEDURE IF EXISTS GenerateUserId;");
        $db->query("
            CREATE PROCEDURE GenerateUserId(OUT new_user_id VARCHAR(20))
            BEGIN
                DECLARE today DATE;
                DECLARE prefix VARCHAR(6);
                DECLARE seq INT;

                SET today = CURDATE();
                SET prefix = DATE_FORMAT(today, '%y%m%d');

                -- Check and update sequence
                IF (SELECT last_date FROM user_id_seq LIMIT 1) = today THEN
                    UPDATE user_id_seq SET last_seq = last_seq + 1;
                ELSE
                    UPDATE user_id_seq SET last_seq = 1, last_date = today;
                END IF;

                -- Get the new sequence
                SELECT last_seq INTO seq FROM user_id_seq LIMIT 1;

                -- Combine date + padded sequence
                SET new_user_id = CONCAT(prefix, LPAD(seq, 3, '0'));
            END;
        ");
    }

    public function down()
    {
        $db = \Config\Database::connect();

        // Drop stored procedure and sequence table
        $db->query("DROP PROCEDURE IF EXISTS GenerateUserId;");
        $db->query("DROP TABLE IF EXISTS user_id_seq;");
    }
}
