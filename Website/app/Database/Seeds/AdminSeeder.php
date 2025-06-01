<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\UserModel;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Check if admin user already exists
        $userModel = new UserModel();
        $existingAdmin = $userModel->where("username", "admin")->first();

        if ($existingAdmin) {
            echo "Admin user already exists. Skipping creation.\n";
            $userId = $existingAdmin["user_id"];

            // Check if admin profile exists
            $existingAdminProfile = $this->db
                ->table("Admin")
                ->where("user_id", $userId)
                ->get()
                ->getRow();

            if (!$existingAdminProfile) {
                // Create admin profile if it doesn't exist
                $this->db->table("Admin")->insert([
                    "user_id" => $userId,
                    "name" => "System Administrator",
                    "email" => "admin@medicalcheckup.com",
                    "phone" => "1234567890",
                    "role_type" => "super_admin",
                    "created_by" => $userId,
                    "updated_by" => $userId,
                ]);
                echo "Admin profile created successfully.\n";
            } else {
                echo "Admin profile already exists. Skipping creation.\n";
            }
        } else {
            // Create new admin user
            $userData = [
                "username" => "admin",
                "email" => "admin@medicalcheckup.com",
                "password" => password_hash("admin123", PASSWORD_DEFAULT),
                "role" => "admin",
                "is_active" => 1,
                "created_at" => date("Y-m-d H:i:s"),
            ];

            $userId = $userModel->insert($userData);

            // Create admin profile
            $this->db->table("Admin")->insert([
                "user_id" => $userId,
                "name" => "System Administrator",
                "email" => "admin@medicalcheckup.com",
                "phone" => "1234567890",
                "role_type" => "super_admin",
                "created_by" => $userId,
                "updated_by" => $userId,
            ]);

            echo "Admin user created successfully.\n";
        }
    }
}
