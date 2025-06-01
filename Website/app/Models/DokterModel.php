<?php namespace App\Models;

use CodeIgniter\Model;

class DokterModel extends Model
{
    protected $table = "Dokter";
    protected $primaryKey = "ID_DOKTER";
    protected $allowedFields = [
        "user_id",
        "NAMA_DOKTER",
        "id_spesialisasi",
        "NO_LISENSI",
        "NO_TELP_DOKTER",
        "is_verified",
        "verification_status",
        "verification_date",
        "verification_notes",
    ];
    protected $useTimestamps = true;
    protected $createdField = "created_at";
    protected $updatedField = "updated_at";

    public function getDoctorsBySpesialisasiId($spesialisasiId)
    {
        return $this->where("id_spesialisasi", $spesialisasiId)->findAll();
    }

    public function getAllDoctorsWithSpesialisasi()
    {
        $result = $this->db
            ->table("Dokter")
            ->select("Dokter.*, Spesialisasi.nama_spesialisasi")
            ->join(
                "Spesialisasi",
                "Dokter.id_spesialisasi = Spesialisasi.id_spesialisasi",
                "left"
            )
            ->get()
            ->getResultArray();

        return $result;
    }

    public function findDoctorByIdWithSpesialisasi($doctorId)
    {
        return $this->db
            ->table("Dokter")
            ->select("Dokter.*, Spesialisasi.nama_spesialisasi")
            ->join(
                "Spesialisasi",
                "Dokter.id_spesialisasi = Spesialisasi.id_spesialisasi",
                "left"
            )
            ->where("ID_DOKTER", $doctorId)
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
