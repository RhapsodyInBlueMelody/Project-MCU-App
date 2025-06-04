<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PaketSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $paketData = [
            [
                'nama_paket'      => 'Basic MCU',
                'deskripsi'       => 'Pemeriksaan umum dan laboratorium dasar',
                'harga'           => 350000,
                'id_spesialisasi' => null,
                'created_by'      => null,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_by'      => null,
                'updated_at'      => null,
            ],
            [
                'nama_paket'      => 'Executive MCU',
                'deskripsi'       => 'Pemeriksaan lengkap + EKG + rontgen + konsultasi spesialis',
                'harga'           => 1200000,
                'id_spesialisasi' => null,
                'created_by'      => null,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_by'      => null,
                'updated_at'      => null,
            ],
            [
                'nama_paket'      => 'Pemeriksaan Jantung',
                'deskripsi'       => 'Paket khusus jantung: EKG, treadmill test, konsultasi Sp.JP',
                'harga'           => 850000,
                'id_spesialisasi' => 'Sp.JP',
                'created_by'      => null,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_by'      => null,
                'updated_at'      => null,
            ],
            [
                'nama_paket'      => 'Pemeriksaan Mata',
                'deskripsi'       => 'Pemeriksaan mata lengkap oleh spesialis, visus, tekanan bola mata',
                'harga'           => 450000,
                'id_spesialisasi' => 'Sp.M',
                'created_by'      => null,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_by'      => null,
                'updated_at'      => null,
            ],
            [
                'nama_paket'      => 'Pemeriksaan Penyakit Dalam',
                'deskripsi'       => 'Konsultasi dan screening penyakit dalam, laboratorium, USG',
                'harga'           => 700000,
                'id_spesialisasi' => 'Sp.PD',
                'created_by'      => null,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_by'      => null,
                'updated_at'      => null,
            ],
            [
                'nama_paket'      => 'MCU Pra-Kerja',
                'deskripsi'       => 'Pemeriksaan standar untuk syarat kerja',
                'harga'           => 300000,
                'id_spesialisasi' => null,
                'created_by'      => null,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_by'      => null,
                'updated_at'      => null,
            ],
            [
                'nama_paket'      => 'MCU Anak & Remaja',
                'deskripsi'       => 'Pemeriksaan kesehatan tumbuh kembang anak/remaja',
                'harga'           => 400000,
                'id_spesialisasi' => 'Sp.KGA',
                'created_by'      => null,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_by'      => null,
                'updated_at'      => null,
            ],
        ];

        foreach ($paketData as $paket) {
            $exists = $db->table('paket')->where('nama_paket', $paket['nama_paket'])->get()->getRow();
            if (!$exists) {
                $db->table('paket')->insert($paket);
            }
        }
    }
}
