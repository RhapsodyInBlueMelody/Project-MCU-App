<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LabTestProcedureSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nama_procedure'      => 'Hematologi',
                'price'              => 75000,
                'deskripsi'          => 'Pemeriksaan darah lengkap.',
                'active'             => 1,
                'id_spesialisasi_lab' => "HM", // Hematologi
            ],
            [
                'nama_procedure'      => 'Urinalisa',
                'price'              => 50000,
                'deskripsi'          => 'Pemeriksaan urine lengkap.',
                'active'             => 1,
                'id_spesialisasi_lab' => "UR", // Urinalisa
            ],
            [
                'nama_procedure'      => 'Kimia Darah',
                'price'              => 100000,
                'deskripsi'          => 'Tes kimia darah (glukosa, ureum, kreatinin, dsb).',
                'active'             => 1,
                'id_spesialisasi_lab' => "KK", // Kimia Klinik (misal)
            ],
            [
                'nama_procedure'      => 'Tes Golongan Darah',
                'price'              => 35000,
                'deskripsi'          => 'Penentuan golongan darah ABO dan rhesus.',
                'active'             => 1,
                'id_spesialisasi_lab' => "IM", // Serologi/Imunologi, atau sesuai kebutuhan
            ],
            [
                'nama_procedure'      => 'Tes Serologi',
                'price'              => 120000,
                'deskripsi'          => 'Pemeriksaan antibodi dan antigen tertentu.',
                'active'             => 1,
                'id_spesialisasi_lab' => "SR", // Serologi/Imunologi, atau sesuai kebutuhan
            ],
            // Tambahkan prosedur lain sesuai kebutuhan
        ];

        $this->db->table('lab_test_procedure')->insertBatch($data);
    }
}
