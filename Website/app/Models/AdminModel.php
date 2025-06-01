<?php namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table = "Admin";
    protected $primaryKey = "admin_id";
    protected $allowedFields = [
        "user_id",
        "name",
        "email",
        "phone",
        "role_type",
        "created_at",
        "updated_at",
        "created_by",
        "updated_by",
    ];
    protected $useTimestamps = true;
    protected $createdField = "created_at";
    protected $updatedField = "updated_at";

    public function getPendingDoctorVerifications()
    {
        return $this->db
            ->table("Dokter d")
            ->select("d.*, s.nama_spesialisasi, u.email, u.username")
            ->join(
                "Spesialisasi s",
                "d.id_spesialisasi = s.id_spesialisasi",
                "left"
            )
            ->join("Users u", "d.user_id = u.user_id", "left")
            ->where("d.verification_status", "pending")
            ->orderBy("d.created_at", "ASC")
            ->get()
            ->getResultArray();
    }

    public function verifyDoctor($doctorId, $status, $notes, $adminId)
    {
        $data = [
            "is_verified" => $status === "approved" ? 1 : 0,
            "verification_status" => $status,
            "verification_date" => date("Y-m-d H:i:s"),
            "verification_notes" => $notes,
            "updated_by" => $adminId,
        ];

        $dokterModel = new \App\Models\DokterModel();
        return $dokterModel->update($doctorId, $data);
    }

    public function getDashboardStats()
    {
        $stats = [
            "total_doctors" => $this->db->table("Dokter")->countAllResults(),
            "verified_doctors" => $this->db
                ->table("Dokter")
                ->where("is_verified", 1)
                ->countAllResults(),
            "pending_doctors" => $this->db
                ->table("Dokter")
                ->where("verification_status", "pending")
                ->countAllResults(),
            "total_patients" => $this->db->table("Pasien")->countAllResults(),
            "total_appointments" => $this->db
                ->table("Janji_Temu")
                ->countAllResults(),
            "completed_appointments" => $this->db
                ->table("Janji_Temu")
                ->where("STATUS", "completed")
                ->countAllResults(),
            "pending_appointments" => $this->db
                ->table("Janji_Temu")
                ->where("STATUS", "pending")
                ->countAllResults(),
        ];

        return $stats;
    }
}
