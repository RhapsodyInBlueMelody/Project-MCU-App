<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GenerateIdJanjiTemu extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Drop existing procedure if it exists
        $db->query('DROP PROCEDURE IF EXISTS GenerateIdJanjiTemu;');

        // MariaDB-compatible procedure with proper table references
        $db->query("
            CREATE PROCEDURE GenerateIdJanjiTemu(OUT idJanjiTemu VARCHAR(20))
            BEGIN
                DECLARE today DATE;
                DECLARE prefix VARCHAR(5) DEFAULT 'MCU-';
                DECLARE seq INT;
                
                SET today = CURDATE();
                
                -- Atomic update using the correct table
                INSERT INTO appointment_id_seq (last_date, last_seq) 
                VALUES (today, 1)
                ON DUPLICATE KEY UPDATE 
                    last_seq = last_seq + 1;
                
                -- Get the updated sequence
                SELECT last_seq INTO seq FROM appointment_id_seq 
                WHERE last_date = today 
                LIMIT 1;
                
                -- Format: MCU-DDMMYYYYNNN
                SET idJanjiTemu = CONCAT(
                    prefix, 
                    DATE_FORMAT(today, '%d%m%Y'), 
                    LPAD(seq, 3, '0')
                );
            END
        ");
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $db->query('DROP PROCEDURE IF EXISTS GenerateIdJanjiTemu;');
    }
}
