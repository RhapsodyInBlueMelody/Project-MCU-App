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

        $db->query("
            CREATE PROCEDURE GenerateIdJanjiTemu(OUT idJanjiTemu VARCHAR(20))
            BEGIN
                DECLARE today DATE;
                DECLARE prefix VARCHAR(5);
                DECLARE seq INT;
                DECLARE last_seq INT DEFAULT 1;
                DECLARE last_date DATE;

                SET today = CURDATE();
                SET prefix = 'MCU-';

                -- Get last_seq and last_date
                SELECT last_seq, last_date INTO last_seq, last_date FROM user_id_seq LIMIT 1;

                -- Update sequence
                IF last_date = today THEN
                    SET last_seq = last_seq + 1;
                    UPDATE user_id_seq SET last_seq = last_seq WHERE last_date = today;
                ELSE
                    SET last_seq = 1;
                    UPDATE user_id_seq SET last_seq = 1, last_date = today;
                END IF;

                -- Format id: MCU-DDMMYYYYNNN
                SET idJanjiTemu = CONCAT(prefix, DATE_FORMAT(today, '%d%m%Y'), LPAD(last_seq, 3, '0'));
            END;
        ");
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $db->query('DROP PROCEDURE IF EXISTS GenerateIdJanjiTemu;');
    }
}
