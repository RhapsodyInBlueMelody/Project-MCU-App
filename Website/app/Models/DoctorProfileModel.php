<?php namespace App\Models;

use CodeIgniter\Model;

class DoctorProfileModel extends Model
{
    protected $table = "doctor_profiles";
    protected $primaryKey = "id";
    protected $allowedFields = [
        "user_id",
        "full_name",
        "specialization",
        "license_number",
        "phone_number",
        "bio",
    ];

    protected $useTimestamps = false;

    public function getDoctorProfile($userId)
    {
        return $this->where("user_id", $userId)->first();
    }

    // You might want to add more useful methods
    public function createProfile($data)
    {
        return $this->insert($data);
    }

    public function updateProfile($userId, $data)
    {
        return $this->where("user_id", $userId)->update(null, $data);
    }

    public function getDoctorWithUserData($userId)
    {
        $db = \Config\Database::connect();

        $builder = $db->table("doctor_profiles as dp");
        $builder->select("dp.*, u.email, u.username");
        $builder->join("users as u", "dp.user_id = u.id", "left");
        $builder->where("dp.user_id", $userId);

        return $builder->get()->getRowArray();
    }

    public function getAllDoctors()
    {
        $db = \Config\Database::connect();

        $builder = $db->table("doctor_profiles as dp");
        $builder->select("dp.*, u.email, u.username");
        $builder->join("users as u", "dp.user_id = u.id", "left");
        $builder->where("u.status", 1);

        return $builder->get()->getResultArray();
    }

    public function getDoctorsBySpecialization($specialization)
    {
        $db = \Config\Database::connect();

        $builder = $db->table("doctor_profiles as dp");
        $builder->select("dp.*, u.email, u.username");
        $builder->join("users as u", "dp.user_id = u.id", "left");
        $builder->where("u.status", 1);
        $builder->where("dp.specialization", $specialization);

        return $builder->get()->getResultArray();
    }
}
