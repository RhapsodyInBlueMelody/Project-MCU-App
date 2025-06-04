<?php namespace App\Models;

use CodeIgniter\Model;

class DokterModel extends Model
{
    protected $table = "dokter";
    protected $primaryKey = "id_dokter";
    protected $allowedFields = [
        "user_id",
        "nama_dokter",
        "id_spesialisasi",
        "no_lisensi",
        "telepon_dokter",
        "is_verified",
        "verification_status",
        "verification_date",
        "verification_notes",
    ];
    protected $useTimestamps = true;
    protected $createdField = "created_at";
    protected $updatedField = "updated_at";

    public function getVerifiedDoctorProfile($userId)
    {
        $doctor = $this->where("user_id", $userId)->first();

        if (!$doctor) {
            return ["error" => "Doctor not found"];
        }

        if ($doctor["is_verified"] != 1 || $doctor["verification_status"] != "approved") {
            if ($doctor["verification_status"] == "rejected") {
                return ["error" => "Doctor verification is still pending"];
        }
            return ["error" => "Doctor is not verified"];
        }

        return $doctor;
        
    }

    public function getDoctorsByspesialisasiId($spesialisasiId)
    {
        return $this->where("id_spesialisasi", $spesialisasiId)->findAll();
    }

    public function getAllDoctorsWithspesialisasi()
    {
        $result = $this->db
            ->table("dokter")
            ->select("dokter.*, spesialisasi.nama_spesialisasi")
            ->join(
                "spesialisasi",
                "dokter.id_spesialisasi = spesialisasi.id_spesialisasi",
                "left"
            )
            ->get()
            ->getResultArray();

        return $result;
    }

    public function findDoctorByIdWithspesialisasi($doctorId)
    {
        return $this->db
            ->table("dokter")
            ->select("dokter.*, spesialisasi.nama_spesialisasi")
            ->join(
                "spesialisasi",
                "dokter.id_spesialisasi = spesialisasi.id_spesialisasi",
                "left"
            )
            ->where("id_dokter", $doctorId)
            ->get()
            ->getRowArray();
    }

    public function findDoctorByUserId($userId)
    {
        return $this->where("user_id", $userId)->first();
    }

    public function createDoctorFromSocialLogin($userData, $doctorData)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Insert user
            $usersModel = new \App\Models\UserModel();
            $userId = $usersModel->insert($userData);

            // Insert doctor data
            $doctorData["user_id"] = $userId;
            $doctorData["created_by"] = $userId;
            $doctorData["updated_by"] = $userId;
            $doctorId = $this->insert($doctorData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                $db->transRollback();
                return false;
            }

            return [
                "user_id" => $userId,
                "doctor_id" => $doctorId,
            ];
        } catch (\Exception $e) {
            $db->transRollback();
            log_message(
                "error",
                "Error creating doctor from social login: " . $e->getMessage()
            );
            return false;
        }
    }

    public function verifyDoctor($doctorId, $verificationData)
    {
        try {
            $this->update($doctorId, $verificationData);
            return true;
        } catch (\Exception $e) {
            log_message("error", "Error verifying doctor: " . $e->getMessage());
            return false;
        }
    }
}
