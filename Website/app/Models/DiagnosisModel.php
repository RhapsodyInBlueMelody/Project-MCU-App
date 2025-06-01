<?php namespace App\Models;

use CodeIgniter\Model;

class DiagnosisModel extends Model
{
    protected $table = "Diagnosis";
    protected $primaryKey = "id";
    protected $allowedFields = [
        "id_janji_temu",
        "ID_DOKTER",
        "ID_PASIEN",
        "symptoms",
        "diagnosis_result",
        "treatment_plan",
        "notes",
        "created_by",
        "created_at",
        "updated_by",
        "updated_at",
    ];
    protected $useTimestamps = true;
    protected $createdField = "created_at";
    protected $updatedField = "updated_at";

    /**
     * Get diagnosis with patient and doctor information
     */
    public function getDiagnosisDetails($diagnosisId)
    {
        return $this->db
            ->table("Diagnosis d")
            ->select(
                "d.*, p.NAMA_LENGKAP as patient_name, doc.NAMA_DOKTER as doctor_name"
            )
            ->join("Pasien p", "p.PASIEN_ID = d.ID_PASIEN", "left")
            ->join("Dokter doc", "doc.ID_DOKTER = d.ID_DOKTER", "left")
            ->where("d.id", $diagnosisId)
            ->get()
            ->getRowArray();
    }

    /**
     * Get diagnosis by appointment ID
     */
    public function getDiagnosisByAppointmentId($appointmentId)
    {
        return $this->where("id_janji_temu", $appointmentId)->first();
    }

    /**
     * Get all diagnoses for a patient
     */
    public function getPatientDiagnoses($patientId)
    {
        return $this->db
            ->table("Diagnosis d")
            ->select("d.*, doc.NAMA_DOKTER as doctor_name, a.TANGGAL_JANJI")
            ->join("Dokter doc", "doc.ID_DOKTER = d.ID_DOKTER", "left")
            ->join("Janji_Temu a", "a.ID_JANJI_TEMU = d.id_janji_temu", "left")
            ->where("d.ID_PASIEN", $patientId)
            ->orderBy("d.created_at", "DESC")
            ->get()
            ->getResultArray();
    }
}
