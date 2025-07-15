<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SpesialisasiLabSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_spesialisasi_lab' => 'PK',
                'nama_spesialisasi'   => 'Patologi Klinik',
                'deskripsi'           => 'Analisis laboratorium klinik untuk diagnosis dan pemantauan penyakit.',
            ],
            [
                'id_spesialisasi_lab' => 'MB',
                'nama_spesialisasi'   => 'Mikrobiologi Klinik',
                'deskripsi'           => 'Analisis mikroorganisme dalam sampel klinis.',
            ],
            [
                'id_spesialisasi_lab' => 'PA',
                'nama_spesialisasi'   => 'Patologi Anatomi',
                'deskripsi'           => 'Analisis jaringan dan sel untuk diagnosis penyakit.',
            ],
            [
                'id_spesialisasi_lab' => 'HM',
                'nama_spesialisasi'   => 'Hematologi',
                'deskripsi'           => 'Analisis darah dan kelainan hematologi.',
            ],
            [
                'id_spesialisasi_lab' => 'IM',
                'nama_spesialisasi'   => 'Imunologi',
                'deskripsi'           => 'Analisis sistem imun dan antibodi.',
            ],
            [
                'id_spesialisasi_lab' => 'KK',
                'nama_spesialisasi'   => 'Kimia Klinik',
                'deskripsi'           => 'Analisis kimia dalam cairan tubuh.',
            ],
            [
                'id_spesialisasi_lab' => 'PR',
                'nama_spesialisasi'   => 'Parasitologi',
                'deskripsi'           => 'Analisis parasit dalam sampel klinis.',
            ],
            [
                'id_spesialisasi_lab' => 'SR',
                'nama_spesialisasi'   => 'Serologi',
                'deskripsi'           => 'Analisis serum untuk mendeteksi antibodi dan antigen.',
            ],
            [
                'id_spesialisasi_lab' => 'BD',
                'nama_spesialisasi'   => 'Bank Darah',
                'deskripsi'           => 'Pelayanan dan pengelolaan darah donor.',
            ],
            [
                'id_spesialisasi_lab' => 'UR',
                'nama_spesialisasi'   => 'Urinalisa',
                'deskripsi'           => 'Analisis urin untuk diagnosis penyakit.',
            ],
            [
                'id_spesialisasi_lab' => 'TX',
                'nama_spesialisasi'   => 'Toksikologi',
                'deskripsi'           => 'Analisis zat toksik dalam tubuh.',
            ]
        ];

        // Insert batch
        $this->db->table('spesialisasi_lab')->insertBatch($data);
    }
}
