<?php namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = "users";
    protected $primaryKey = "id";
    protected $allowedFields = [
        "username",
        "email",
        "password",
        "role",
        "status",
    ];
    protected $useTimestamps = true;
    protected $createdField = "created_at";
    protected $updatedField = "updated_at";

    public function getUserByEmailOrUsername($login, $role)
    {
        return $this->where("email", $login)
            ->orWhere("username", $login)
            ->where("role", $role)
            ->where("status", 1)
            ->first();
    }

    // You might want to fix a potential bug in the query above
    // The current query might match records where username OR (email AND role AND status)
    // A safer version would be:
    public function getUserByEmailOrUsernameFixed($login, $role)
    {
        return $this->where("role", $role)
            ->where("status", 1)
            ->group_start()
            ->where("email", $login)
            ->orWhere("username", $login)
            ->group_end()
            ->first();
    }
}
