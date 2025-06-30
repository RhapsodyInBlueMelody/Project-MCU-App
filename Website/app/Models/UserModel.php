<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = "users";
    protected $primaryKey = "user_id";
    protected $allowedFields = [
        "user_id",
        "username",
        "email",
        "password",
        "role",
        "status",
        "google_id",
        "updated_by",
        "created_by"
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

    public function getUserByGoogleId($googleId)
    {
        return $this->where("google_id", $googleId)->first();
    }

    public function updateGoogleId($userId, $googleId)
    {
        return $this->update($userId, ["google_id" => $googleId]);
    }

    public function registerWithGoogle(array $userData)
    {
        $existingUser = $this->where("email", $userData["email"])
            ->orWhere("google_id", $userData["google_id"])
            ->first();

        if ($existingUser) {
            return $existingUser;
        }

        $data = [
            "username" => $userData["email"],
            "email" => $userData["email"],
            "google_id" => $userData["google_id"],
            "role" => $userData["role"],
            "status" => 1,
            "password" => null,
        ];

        $userId = $this->insert($data);
        return $this->find($userId);
    }

    public function authenticateDoctor($login, $password)
    {
        $user = $this->where("role", "dokter")
            ->where("status", "active")
            ->groupStart()
            ->where("email", $login)
            ->orWhere("username", $login)
            ->groupEnd()
            ->first();


        if (!$user || !password_verify($password, $user['password'])) {
            return null;
        }

        return $user;
    }

    /**
     * Generate a new user ID using stored procedure
     */
    public function generateUserId(): string
    {
        $db = \Config\Database::connect();
        $db->query("CALL GenerateUserId(@generated_user_id)");
        $query = $db->query("SELECT @generated_user_id AS user_id");
        $result = $query->getRow();

        if (!$result || !$result->user_id) {
            throw new \Exception('User ID generation failed.');
        }

        return $result->user_id;
    }
}
