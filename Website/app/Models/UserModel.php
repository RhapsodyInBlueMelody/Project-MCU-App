<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = "users";
    protected $primaryKey = "user_id"; // Assuming your primary key is 'id', adjust if it's 'user_id'
    protected $allowedFields = [
        "username",
        "email",
        "password",
        "role",
        "status",
        "google_id", // Added google_id
    ];
    protected $useTimestamps = true;
    protected $createdField = "created_at";
    protected $updatedField = "updated_at";


    public function getUserByEmailOrUsername($login, $role)
    {
        return $this->where("role", $role)
            ->where("status", 1)
            ->groupStart()
                ->where("email", $login)
                ->orWhere("username", $login)
            ->groupEnd()
            ->first();
    }

    public function getUserByGoogleId(string $googleId)
    {
        return $this->where("google_id", $googleId)->first();
    }

    public function registerWithGoogle(array $userData)
    {
        $existingUser = $this->where("email", $userData["email"])
            ->orWhere("google_id", $userData["google_id"])
            ->first();

        if ($existingUser) {
            // If a user with the same email or Google ID exists, you might want to:
            // 1. Link the Google ID to the existing account (if google_id is null for that user).
            // 2. Return the existing user.
            // 3. Handle the conflict as per your application's logic.
            return $existingUser; // For now, let's just return the existing user
        }

        $data = [
            "username" => $userData["email"], // You might want to generate a unique username
            "email" => $userData["email"],
            "google_id" => $userData["google_id"],
            "role" => $userData["role"], // Determine the role based on your logic
            "status" => 1,
            // Password can be null or a randomly generated string for Google users
            "password" => null,
        ];

        $userId = $this->insert($data);
        return $this->find($userId);
    }

    public function authenticateDoctor($login, $password)
    {
        return $this->where("role", "dokter")
            ->where("status", 1)
            ->groupStart()
                ->where("email", $login)
                ->orWhere("username", $login)
            ->groupEnd()
            ->first();

        if(!$user || !password_verify($password, $user['password'])) {
            return null;
        }

        return $user;
        
    }
}
