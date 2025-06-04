<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Call the GenerateUserID stored procedure to get a unique user_id
        $result = $db->query("CALL GenerateUserID(@new_id)");
        $userIdRow = $db->query("SELECT @new_id AS user_id")->getRow();
        $user_id = $userIdRow->user_id;

        // 2. Insert super admin user with the generated user_id
        $userData = [
            'user_id'     => $user_id,
            'username'    => 'superadmin',
            'email'       => 'superadmin@example.com',
            'password'    => password_hash('supersecurepassword', PASSWORD_DEFAULT),
            'role'        => 'admin',
            'status'      => 1,
            'google_id'   => null,
            'created_by'  => null,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_by'  => null,
            'updated_at'  => null,
        ];
        // Check if the user already exists by email or user_id
        $user = $db->table('users')->where('email', $userData['email'])->get()->getRow();
        if ($user) {
            $user_id = $user->user_id;
        } else {
            $db->table('users')->insert($userData);
            // $user_id is already set from the stored procedure
        }

        // 3. Insert super admin into admin table
        $adminData = [
            'nama_admin'  => 'Super Admin',
            'created_by'  => $user_id,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_by'  => null,
            'updated_at'  => null,
        ];
        // Check if the admin already exists
        $admin = $db->table('admin')->where('nama_admin', $adminData['nama_admin'])->get()->getRow();
        if (!$admin) {
            $db->table('admin')->insert($adminData);
        }
    }
}
