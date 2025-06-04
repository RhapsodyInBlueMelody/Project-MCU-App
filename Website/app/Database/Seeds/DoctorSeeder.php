<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Example doctor data
        $doctors = [
            [
                'id_spesialisasi'   => 'Sp.M',
                'nama_dokter'       => 'Dr. Megane Tirta',
                'username'          => 'drmatabagus',
                'email'             => 'drmatabagus@example.com',
                'password'          => password_hash('testmata', PASSWORD_DEFAULT),
                'no_lisensi'        => 'DOK-2024-0001',
                'telepon_dokter'    => '081234567890',
            ],
            [
                'id_spesialisasi'   => 'Sp.PD',
                'nama_dokter'       => 'Dr. Gatot Hendro',
                'username'          => 'drpenyakitdalam',
                'email'             => 'drpenyakitdalam@example.com',
                'password'          => password_hash('testdalam', PASSWORD_DEFAULT),
                'no_lisensi'        => 'DOK-2024-0002',
                'telepon_dokter'    => '081234567891',
            ],
            [
                'id_spesialisasi'   => 'Sp.KK',
                'nama_dokter'       => 'Dr. Kartika Kulit',
                'username'          => 'drkulitcantik',
                'email'             => 'drkulit@example.com',
                'password'          => password_hash('testkulit', PASSWORD_DEFAULT),
                'no_lisensi'        => 'DOK-2024-0003',
                'telepon_dokter'    => '081234567892',
            ],
        ];

        foreach ($doctors as $entry) {
            // 1. Upsert specialization
            $spec = $db->table('spesialisasi')->where('id_spesialisasi', $entry['id_spesialisasi'])->get()->getRow();
            if (!$spec) {
                $db->table('spesialisasi')->insert([
                    'id_spesialisasi'   => $entry['id_spesialisasi'],
                    'nama_spesialisasi' => $entry['id_spesialisasi'],
                    'created_by'        => null,
                    'created_at'        => date('Y-m-d H:i:s'),
                ]);
            }

            // 2. Generate user_id via stored procedure
            $db->query("CALL GenerateUserID(@new_id)");
            $user_id = $db->query("SELECT @new_id AS user_id")->getRow()->user_id;

            // 3. Upsert user
            $userRow = $db->table('users')->where('email', $entry['email'])->get()->getRow();
            if ($userRow) {
                $user_id = $userRow->user_id;
            } else {
                $db->table('users')->insert([
                    'user_id'    => $user_id,
                    'username'   => $entry['username'],
                    'email'      => $entry['email'],
                    'password'   => $entry['password'],
                    'role'       => 'dokter',
                    'status'     => 1,
                    'created_by' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            // 4. Prepare specialization code (uppercase, no dots)
            $spes_code = strtoupper(str_replace('.', '', $entry['id_spesialisasi']));

            // 5. Generate id_dokter using stored procedure
            $db->query("CALL GenerateDoktorId(?, @new_id)", [$spes_code]);
            $id_dokter = $db->query("SELECT @new_id AS id_dokter")->getRow()->id_dokter;

            // 6. Set created_at for dokter
            $created_at = date('Y-m-d H:i:s');

            // 7. Upsert dokter
            $dokterRow = $db->table('dokter')->where('user_id', $user_id)->get()->getRow();
            if (!$dokterRow) {
                $db->table('dokter')->insert([
                    'id_dokter'          => $id_dokter,
                    'user_id'            => $user_id,
                    'nama_dokter'        => $entry['nama_dokter'],
                    'id_spesialisasi'    => $entry['id_spesialisasi'],
                    'no_lisensi'         => $entry['no_lisensi'],
                    'telepon_dokter'     => $entry['telepon_dokter'],
                    'is_verified'        => 1,
                    'verification_status'=> 'approved',
                    'verification_notes' => 'Seeder auto-approve',
                    'created_by'         => null,
                    'created_at'         => $created_at,
                ]);
            }
        }
    }
}
