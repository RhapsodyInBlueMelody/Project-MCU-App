<?php

namespace App\Models;

use CodeIgniter\Model;
use phpseclib3\Common\Functions\Strings;

class DokterModel extends Model
{
    protected $table = "dokter";
    protected $primaryKey = "id_dokter";
    protected $allowedFields = [
        "id_dokter",
        "user_id",
        "nama_dokter",
        "id_spesialisasi",
        "no_lisensi",
        "telepon_dokter",
        "lokasi_kerja",
        "alamat_dokter",
        "jenis_kelamin",
        "tanggal_lahir",
        "is_verified",
        "verification_status",
        "created_by",
        "updated_by"
    ];
    protected $useTimestamps = true;
    protected $createdField = "created_at";
    protected $updatedField = "updated_at";


    /**
     * Generate a new dokter ID using stored procedure
     */
    public function generateDokterId($spesialisasi): string
    {
        $db = \Config\Database::connect();
        $result = $db->query("CALL GenerateDoktorId(?, @new_id_dokter)", [$spesialisasi]);
        $query = $db->query("SELECT @new_id_dokter AS id_dokter");
        $result = $query->getRow();

        if (!$result || !$result->id_dokter) {
            throw new \Exception('Dokter ID generation failed.');
        }

        return $result->id_dokter;
    }

    public function getVerifiedDoctorProfile($userId)
    {
        $doctor = $this->where("user_id", $userId)->first();

        if (!$doctor) {
            return ["error" => "not_found"];
        }

        if ($doctor["is_verified"] != 1 || $doctor["verification_status"] !== "approved") {
            if ($doctor["verification_status"] === "rejected") {
                return ["error" => "rejected", "verification_notes" => $doctor["verification_notes"] ?? ""];
            }
            return ["error" => "not_verified"];
        }

        return $doctor;
    }

    public function checkVerification($userId)
    {
        $doctor = $this->where("user_id", $userId)->first();

        if (!$doctor) {
            return ['ok' => false, 'msg' => "Akun Dokter tidak ditemukan."];
        }
        if ($doctor["verification_status"] !== "approved" || $doctor["is_verified"] != 1) {
            $msg = "Your account is pending verification.";
            if ($doctor["verification_status"] === "rejected") {
                $msg = "Your account verification was rejected. Reason: " .
                    ($doctor["verification_notes"] ?? "No reason provided.");
            }
            return ['ok' => false, 'msg' => $msg];
        }
        return ['ok' => true, 'msg' => ''];
    }

    public function getAllDoctorsWithSpesialisasi()
    {
        return $this->db
            ->table("dokter")
            ->select("dokter.id_dokter, dokter.no_lisensi, dokter.telepon_dokter, dokter.nama_dokter, dokter.id_spesialisasi, spesialisasi.nama_spesialisasi, dokter.is_verified, dokter.verification_status")
            ->join("spesialisasi", "dokter.id_spesialisasi = spesialisasi.id_spesialisasi", "left")
            ->get()
            ->getResultArray();
    }

    public function findDoctorByIdWithSpesialisasi($doctorId)
    {
        return $this->select('dokter.*, spesialisasi.nama_spesialisasi')
            ->join('spesialisasi', 'spesialisasi.id_spesialisasi = dokter.id_spesialisasi', 'left')
            ->where('dokter.id_dokter', $doctorId)
            ->first();
    }

    // Add this method to your DokterModel
    public function updateDokterStatus($dokterId, $status, $updatedBy)
    {
        // Example of how you might map a simplified status ('active', 'inactive')
        // to your existing 'is_verified' and 'verification_status' fields.
        // You might need to adjust this logic based on your exact requirements.

        $data = [
            'updated_by' => $updatedBy,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($status === 'active') {
            $data['is_verified'] = 1; // Or true, depending on your database type
            $data['verification_status'] = 'active';
        } elseif ($status === 'inactive') {
            $data['is_verified'] = 0; // Or false
            $data['verification_status'] = 'inactive';
        }
        // You can add more conditions for 'suspended', 'on_leave', etc.

        return $this->update($dokterId, $data);
    }
}
