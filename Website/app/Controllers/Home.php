<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $data['title'] = ucfirst("Medical Check-Up App");

        return view('templates/home/header', $data)
            . view('home')
            . view('templates/home/footer');
    }
    // Existing methods...

    public function cariDokter()
    {
        // Example data, replace it with your database/model data if needed
        $data['dokterList'] = [
            [
                'nama' => 'dr. Andi Saputra, Sp.PD',
                'spesialisasi' => 'Spesialis Penyakit Dalam',
                'lokasi' => 'RS Cabang Jakarta',
                'jadwal' => 'Senin - Jumat, 08:00 - 14:00',
                'foto' => 'https://via.placeholder.com/80'
            ],
            [
                'nama' => 'dr. Maria Lestari, Sp.OG',
                'spesialisasi' => 'Spesialis Kandungan',
                'lokasi' => 'RS Cabang Bandung',
                'jadwal' => 'Senin, Rabu, Jumat, 09:00 - 13:00',
                'foto' => 'https://via.placeholder.com/80'
            ],
            [
                'nama' => 'dr. Budi Santoso, Sp.B',
                'spesialisasi' => 'Spesialis Bedah',
                'lokasi' => 'RS Cabang Surabaya',
                'jadwal' => 'Selasa & Kamis, 10:00 - 16:00',
                'foto' => 'https://via.placeholder.com/80'
            ],
            [
                'nama' => 'dr. Siti Aminah, Sp.A',
                'spesialisasi' => 'Spesialis Anak',
                'lokasi' => 'RS Cabang Bandung',
                'jadwal' => 'Senin - Jumat, 08:00 - 12:00',
                'foto' => 'https://via.placeholder.com/80'
            ]
        ];


        $data['title'] = ucfirst("Cari Dokter");

        return view('templates/home/header', $data)
            . view('home/cariDokter', $data) // Make sure you create a cariDokter view
            . view('templates/home/footer', $data);
    }

    public function cabangJakarta()
    {
        $data['title'] = ucfirst(" RSCabang Jakarta");
        $data['color'] = "bg-blue-600";


        return view('templates/home/header', $data)
            . view('home/cabangJakarta')
            . view('templates/home/cabang/footer', $data);
    }
    public function cabangBandung()
    {
        $data['title'] = ucfirst("RS Cabang  Bandung");
        $data['color'] = "bg-green-600 ";

        return view('templates/home/header', $data)
            . view('home/cabangBandung')
            . view('templates/home/cabang/footer', $data);
    }

    public function cabangSurabaya()
    {
        $data['title'] = ucfirst("RS Cabang Surabaya");
        $data['color'] = "bg-red-600";
        return view('templates/home/header', $data)
            . view('home/cabangSurabaya')
            . view('templates/home/cabang/footer', $data);
    }
    public function fasilitasKami()
    {
        $data['title'] = ucfirst("RS Fasilitas Kami");

        return view('templates/home/header', $data)
            . view('home/fasilitasKami')
            . view('templates/home/footer', $data);
    }
}
