<?php namespace App\Models;

use CodeIgniter\Model;

class diagnosisModel extends Model
{
    protected $table = "diagnosis";
    protected $primaryKey = "id";
    protected $allowedFields = [
        "id_janji_temu",
        "id_dokter",
        "id_pasien",
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
            ->table("diagnosis d")
            ->select(
                "d.*, p.nama_pasien as patient_name, doc.nama_dokter as doctor_name"
            )
            ->join("pasien p", "p.id_pasien = d.id_pasien", "left")
            ->join("dokter doc", "doc.id_dokter = d.id_dokter", "left")
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
            ->table("diagnosis d")
            ->select("d.*, doc.nama_dokter as doctor_name, a.tanggal_janji")
            ->join("dokter doc", "doc.id_dokter = d.id_dokter", "left")
            ->join("janji_temu a", "a.id_janji_temu = d.id_janji_temu", "left")
            ->where("d.id_pasien", $patientId)
            ->orderBy("d.created_at", "DESC")
            ->get()
            ->getResultArray();
    }
}
