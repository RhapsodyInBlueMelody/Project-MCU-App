<?php namespace App\Models;

use CodeIgniter\Model;

class PatientProfileModel extends Model
{
    protected $table = "patient_profiles";
    protected $primaryKey = "id";
    protected $allowedFields = [
        "user_id",
        "full_name",
        "date_of_birth",
        "gender",
        "phone_number",
        "address",
        "medical_history",
    ];

    protected $useTimestamps = false;

    public function getPatientProfile($userId)
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

    public function getPatientWithUserData($userId)
    {
        $db = \Config\Database::connect();

        $builder = $db->table("patient_profiles as pp");
        $builder->select("pp.*, u.email, u.username");
        $builder->join("users as u", "pp.user_id = u.id", "left");
        $builder->where("pp.user_id", $userId);

        return $builder->get()->getRowArray();
    }
}
