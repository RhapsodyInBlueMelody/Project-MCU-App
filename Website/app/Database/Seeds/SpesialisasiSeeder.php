<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SpesialisasiSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id_spesialisasi' => 'Sp.B', 'nama_spesialisasi' => 'Dokter Spesialis Bedah'],
            ['id_spesialisasi' => 'Sp.JP', 'nama_spesialisasi' => 'Dokter Spesialis Jantung dan Pembuluh Darah'],
            ['id_spesialisasi' => 'Sp.PD', 'nama_spesialisasi' => 'Dokter Spesialis Penyakit Dalam'],
            ['id_spesialisasi' => 'Sp.M', 'nama_spesialisasi' => 'Dokter Spesialis Mata'],
            ['id_spesialisasi' => 'Sp.P', 'nama_spesialisasi' => 'Dokter Spesialis Paru'],
            ['id_spesialisasi' => 'Sp.OG', 'nama_spesialisasi' => 'Dokter Spesialis Kandungan dan Ginekologi'],
            ['id_spesialisasi' => 'Sp.KK', 'nama_spesialisasi' => 'Dokter Spesialis Kulit dan Kelamin'],
            ['id_spesialisasi' => 'Sp.KJ', 'nama_spesialisasi' => 'Dokter Spesialis Kesehatan Jiwa'],
            ['id_spesialisasi' => 'Sp.BM', 'nama_spesialisasi' => 'Dokter Spesialis Bedah Mulut'],
            ['id_spesialisasi' => 'Sp.S', 'nama_spesialisasi' => 'Dokter Spesialis Saraf atau Neurolog'],
            ['id_spesialisasi' => 'Sp.An', 'nama_spesialisasi' => 'Dokter Spesialis Anestesi'],
            ['id_spesialisasi' => 'Sp.And', 'nama_spesialisasi' => 'Dokter Spesialis Andrologi'],
            ['id_spesialisasi' => 'Sp.BTKV', 'nama_spesialisasi' => 'Dokter Spesialis Bedah Toraks Kardiovaskuler'],
            ['id_spesialisasi' => 'Sp.BP', 'nama_spesialisasi' => 'Dokter Spesialis Bedah Plastik'],
            ['id_spesialisasi' => 'Sp.EM', 'nama_spesialisasi' => 'Dokter Spesialis Kedaruratan Medik'],
            ['id_spesialisasi' => 'Sp.F', 'nama_spesialisasi' => 'Dokter Spesialis Kedokteran Forensik'],
            ['id_spesialisasi' => 'Sp.FK', 'nama_spesialisasi' => 'Dokter Spesialis Farmakologi Klinik'],
            ['id_spesialisasi' => 'Sp.KG', 'nama_spesialisasi' => 'Dokter Spesialis Konservasi Gigi'],
            ['id_spesialisasi' => 'Sp.KGA', 'nama_spesialisasi' => 'Dokter Spesialis Kedokteran Gigi Anak'],
            ['id_spesialisasi' => 'Sp.KN', 'nama_spesialisasi' => 'Dokter Spesialis Kedokteran Nuklir'],
            ['id_spesialisasi' => 'Sp.KO', 'nama_spesialisasi' => 'Dokter Spesialis Kedokteran Olahraga'],
            ['id_spesialisasi' => 'Sp.MK', 'nama_spesialisasi' => 'Dokter Spesialis Mikrobiologi Klinik'],
        ];

        $db = \Config\Database::connect();
        foreach ($data as $row) {
            $exists = $db->table('spesialisasi')->where('id_spesialisasi', $row['id_spesialisasi'])->get()->getRow();
            if (!$exists) {
                $db->table('spesialisasi')->insert($row);
            }
        }
    }
}
