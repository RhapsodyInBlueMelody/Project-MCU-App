<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GeneratePasienIdSP extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        $db->query('DROP PROCEDURE IF EXISTS GeneratePasienId;');
        
        $db->query("
            CREATE PROCEDURE GeneratePasienId(
                IN in_lokasi VARCHAR(10),
                OUT out_id_pasien VARCHAR(30)
            )
            BEGIN
                DECLARE prefix VARCHAR(20);
                DECLARE sequence INT DEFAULT 1;
                DECLARE date_part VARCHAR(6);
                DECLARE seq_part VARCHAR(4);
                DECLARE today DATE;
        
                -- Optional: Validate the lokasi input
                IF in_lokasi NOT IN ('JKT', 'BDG', 'SBY') THEN
                    SET out_id_pasien = 'INVALID-LOC';
                END IF;
        
                SET today = CURRENT_DATE();
                SET date_part = DATE_FORMAT(today, '%y%m%d');  
                SET prefix = CONCAT(in_lokasi, '-', date_part);  
        
                SELECT COUNT(*) + 1 INTO sequence
                FROM pasien
                WHERE lokasi = in_lokasi
                  AND DATE(created_at) = today;
        
                SET seq_part = LPAD(sequence, 3, '0');
                SET out_id_pasien = CONCAT(prefix, '-', seq_part);
            END
        ");
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $db->query('DROP PROCEDURE IF EXISTS GeneratePasienId;');
    }
}
