<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GenerateDoktorIdSP extends Migration
{
    public function up()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS dokter_id_tracker (
                id_spesialisasi VARCHAR(10) NOT NULL,
                date_ref DATE NOT NULL,
                last_seq INT NOT NULL DEFAULT 0,
                PRIMARY KEY (id_spesialisasi, date_ref)
            )
        ");
    
        $this->db->query("
            DROP PROCEDURE IF EXISTS GenerateDoktorId;
        ");
    
        $this->db->query("
            CREATE PROCEDURE GenerateDoktorId(
                IN p_spesialisasi VARCHAR(10),
                OUT p_new_id VARCHAR(50)
            )
            BEGIN
                DECLARE today DATE;
                DECLARE seq INT;
                DECLARE year_part VARCHAR(2);
                DECLARE month_part VARCHAR(2);
                DECLARE day_part VARCHAR(2);
                DECLARE seq_part VARCHAR(4);
    
                SET today = CURDATE();
    
                SELECT last_seq INTO seq
                FROM dokter_id_tracker
                WHERE id_spesialisasi = p_spesialisasi AND date_ref = today
                LIMIT 1;
    
                IF seq IS NULL THEN
                    SET seq = 1;
                    INSERT INTO dokter_id_tracker (id_spesialisasi, date_ref, last_seq)
                    VALUES (p_spesialisasi, today, seq);
                ELSE
                    SET seq = seq + 1;
                    UPDATE dokter_id_tracker
                    SET last_seq = seq
                    WHERE id_spesialisasi = p_spesialisasi AND date_ref = today;
                END IF;
    
                SET year_part = DATE_FORMAT(today, '%y');
                SET month_part = DATE_FORMAT(today, '%m');
                SET day_part = DATE_FORMAT(today, '%d');
                SET seq_part = LPAD(seq, 4, '0');
    
                SET p_new_id = CONCAT(REPLACE(p_spesialisasi, '.', ''), '-', year_part, month_part, day_part, '-', seq_part);
            END
        ");
    }
    
    public function down()
    {
        $this->db->query("DROP PROCEDURE IF EXISTS GenerateDoktorId;");
        $this->db->query("DROP TABLE IF EXISTS dokter_id_tracker;");
    }
}
