<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePetugasLabIdTracker extends Migration
{
    public function up()
    {
        // Create the tracker table for Petugas Lab
        $this->db->query("
            CREATE TABLE IF NOT EXISTS petugas_lab_id_tracker (
                id_spesialisasi_lab VARCHAR(10) NOT NULL,
                date_ref DATE NOT NULL,
                last_seq INT NOT NULL DEFAULT 0,
                PRIMARY KEY (id_spesialisasi_lab, date_ref)
            )
        ");

        // Drop old procedure if exists
        $this->db->query("DROP PROCEDURE IF EXISTS GeneratePetugasLabId;");

        // Create the stored procedure
        $this->db->query("
            CREATE PROCEDURE GeneratePetugasLabId(
                IN p_spesialisasi_lab VARCHAR(10),
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
                FROM petugas_lab_id_tracker
                WHERE id_spesialisasi_lab = p_spesialisasi_lab AND date_ref = today
                LIMIT 1;

                IF seq IS NULL THEN
                    SET seq = 1;
                    INSERT INTO petugas_lab_id_tracker (id_spesialisasi_lab, date_ref, last_seq)
                    VALUES (p_spesialisasi_lab, today, seq);
                ELSE
                    SET seq = seq + 1;
                    UPDATE petugas_lab_id_tracker
                    SET last_seq = seq
                    WHERE id_spesialisasi_lab = p_spesialisasi_lab AND date_ref = today;
                END IF;

                SET year_part = DATE_FORMAT(today, '%y');
                SET month_part = DATE_FORMAT(today, '%m');
                SET day_part = DATE_FORMAT(today, '%d');
                SET seq_part = LPAD(seq, 4, '0');

                SET p_new_id = CONCAT(REPLACE(p_spesialisasi_lab, '.', ''), '-', year_part, month_part, day_part, '-', seq_part);
            END
        ");
    }

    public function down()
    {
        $this->db->query("DROP PROCEDURE IF EXISTS GeneratePetugasLabId;");
        $this->forge->dropTable('petugas_lab_id_tracker', true);
    }
}
