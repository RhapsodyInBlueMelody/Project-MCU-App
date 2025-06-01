<?php namespace App\Models;

use CodeIgniter\Model;

class AppointmentModel extends Model
{
    protected $table = "Janji_Temu";
    protected $primaryKey = "ID_JANJI_TEMU";
    protected $allowedFields = [
        "NAMA_JANJI",
        "ID_PASIEN",
        "TANGGAL_JANJI",
        "WAKTU_JANJI",
        "ID_DOKTER",
        "ID_PAKET",
        "STATUS",
        "created_by",
        "created_at",
        "updated_by",
        "updated_at",
    ];
    protected $useTimestamps = true;
    protected $createdField = "created_at";
    protected $updatedField = "updated_at";

    /**
     * Get all appointments for a specific patient
     */
    public function getPatientAppointments($patientId)
    {
        return $this->db
            ->table("Janji_Temu a")
            ->select("a.*, d.NAMA_DOKTER, s.nama_spesialisasi, p.nama_paket")
            ->join("Dokter d", "d.ID_DOKTER = a.ID_DOKTER", "left")
            ->join(
                "Spesialisasi s",
                "s.id_spesialisasi = d.id_spesialisasi",
                "left"
            )
            ->join("Paket p", "p.id_paket = a.ID_PAKET", "left")
            ->where("a.ID_PASIEN", $patientId)
            ->orderBy("a.TANGGAL_JANJI", "DESC")
            ->orderBy("a.WAKTU_JANJI", "DESC")
            ->get()
            ->getResultArray();
    }

    /**
     * Get upcoming appointments for a specific patient with a limit
     */
    public function getUpcomingAppointments($patientId, $limit = null)
    {
        $query = $this->db
            ->table("Janji_Temu a")
            ->select("a.*, d.NAMA_DOKTER, s.nama_spesialisasi, p.nama_paket")
            ->join("Dokter d", "d.ID_DOKTER = a.ID_DOKTER", "left")
            ->join(
                "Spesialisasi s",
                "s.id_spesialisasi = d.id_spesialisasi",
                "left"
            )
            ->join("Paket p", "p.id_paket = a.ID_PAKET", "left")
            ->where("a.ID_PASIEN", $patientId)
            ->where("a.STATUS", "pending")
            ->orWhere("a.STATUS", "confirmed")
            ->where("a.TANGGAL_JANJI >=", date("Y-m-d"))
            ->orderBy("a.TANGGAL_JANJI", "ASC")
            ->orderBy("a.WAKTU_JANJI", "ASC");

        if (!is_null($limit)) {
            $query->limit($limit);
        }

        return $query->get()->getResultArray();
    }

    /**
     * Get appointment details by ID
     */
    public function getAppointmentDetails($id)
    {
        $builder = $this->db
            ->table("Janji_Temu a")
            ->select(
                'a.*,
                       p.NAMA_PAKET, p.Harga_Paket, p.deskripsi_singkat as DESKRIPSI_PAKET,
                       d.NAMA_DOKTER,
                       s.NAMA_SPESIALISASI,
                       diag.DIAGNOSIS, diag.REKOMENDASI, diag.TANGGAL_DIAGNOSIS,
                       diag.HASIL_LAB, diag.TANGGAL_HASIL_LAB'
            )
            ->join("Paket p", "p.id_paket = a.ID_PAKET", "left")
            ->join("Dokter d", "d.ID_DOKTER = a.ID_DOKTER", "left")
            ->join(
                "Spesialisasi s",
                "s.id_spesialisasi = d.id_spesialisasi",
                "left"
            )
            ->join("DIAGNOSIS diag", "diag.ID_JANJI = a.ID_JANJI_TEMU", "left")
            ->where("a.ID_JANJI_TEMU", $id);

        return $builder->get()->getRowArray();
    }

    public function saveDiagnosis($data)
    {
        $db = \Config\Database::connect();
        $builder = $db->table("DIAGNOSIS");

        return $builder->insert($data);
    }

    public function updateDiagnosis($id, $data)
    {
        $db = \Config\Database::connect();
        $builder = $db->table("DIAGNOSIS");

        return $builder->where("ID_DIAGNOSIS", $id)->update($data);
    }
    /**
     * Check if a doctor is available at a specific date and time
     *
     * @param int $doctorId The doctor ID
     * @param string $date The appointment date (YYYY-MM-DD)
     * @param string $time The appointment time (HH:MM:SS)
     * @return bool True if doctor is available, false otherwise
     */
    public function isDoctorAvailable($doctorId, $date, $time)
    {
        // Check doctor's working hours (based on your business rules)
        $hour = (int) substr($time, 0, 2);
        if ($hour < 8 || $hour >= 17) {
            return false; // Outside working hours
        }

        // Check existing appointments
        $conflictingAppointments = $this->db
            ->table("Janji_Temu")
            ->where("ID_DOKTER", $doctorId)
            ->where("TANGGAL_JANJI", $date)
            ->where("WAKTU_JANJI", $time)
            ->where("STATUS !=", "cancelled") // Ignore cancelled appointments
            ->countAllResults();

        return $conflictingAppointments === 0;
    }

    /**
     * Get the last completed appointment date for a patient
     *
     * @param int $patientId
     * @return string|null Date of last completed appointment or null
     */
    public function getLastCompletedAppointmentDate($patientId)
    {
        $result = $this->db
            ->table("Janji_Temu")
            ->select("TANGGAL_JANJI")
            ->where("ID_PASIEN", $patientId)
            ->where("STATUS", "completed")
            ->orderBy("TANGGAL_JANJI", "DESC")
            ->limit(1)
            ->get()
            ->getRowArray();

        return $result ? $result["TANGGAL_JANJI"] : null;
    }

    /**
     * Get the total number of completed appointments for a patient
     *
     * @param int $patientId
     * @return int Count of completed appointments
     */
    public function getTotalCompletedAppointments($patientId)
    {
        return $this->db
            ->table("Janji_Temu")
            ->where("ID_PASIEN", $patientId)
            ->where("STATUS", "completed")
            ->countAllResults();
    }

    /**
     * Get all appointments for a specific doctor on a specific date
     */
    public function getDoctorAppointmentsByDate($doctorId, $date)
    {
        return $this->db
            ->table("Janji_Temu a")
            ->select(
                "a.*, p.NAMA_LENGKAP as patient_name, p.NO_TELP_PASIEN as patient_phone, pa.nama_paket"
            )
            ->join("Pasien p", "p.PASIEN_ID = a.ID_PASIEN", "left")
            ->join("Paket pa", "pa.id_paket = a.ID_PAKET", "left")
            ->where("a.ID_DOKTER", $doctorId)
            ->where("a.TANGGAL_JANJI", $date)
            ->orderBy("a.WAKTU_JANJI", "ASC")
            ->get()
            ->getResultArray();
    }

    /**
     * Get pending appointments for a specific doctor
     */
    public function getDoctorPendingAppointments($doctorId)
    {
        return $this->db
            ->table("Janji_Temu a")
            ->select(
                "a.*, p.NAMA_LENGKAP as patient_name, p.NO_TELP_PASIEN as patient_phone, pa.nama_paket"
            )
            ->join("Pasien p", "p.PASIEN_ID = a.ID_PASIEN", "left")
            ->join("Paket pa", "pa.id_paket = a.ID_PAKET", "left")
            ->where("a.ID_DOKTER", $doctorId)
            ->where("a.STATUS", "pending")
            ->where("a.TANGGAL_JANJI >=", date("Y-m-d"))
            ->orderBy("a.TANGGAL_JANJI", "ASC")
            ->orderBy("a.WAKTU_JANJI", "ASC")
            ->get()
            ->getResultArray();
    }

    /**
     * Get appointments for a specific doctor by status
     */
    public function getDoctorAppointmentsByStatus($doctorId, $status)
    {
        return $this->db
            ->table("Janji_Temu a")
            ->select(
                "a.*, p.NAMA_LENGKAP as patient_name, p.NO_TELP_PASIEN as patient_phone, pa.nama_paket"
            )
            ->join("Pasien p", "p.PASIEN_ID = a.ID_PASIEN", "left")
            ->join("Paket pa", "pa.id_paket = a.ID_PAKET", "left")
            ->where("a.ID_DOKTER", $doctorId)
            ->where("a.STATUS", $status)
            ->orderBy("a.TANGGAL_JANJI", "DESC")
            ->orderBy("a.WAKTU_JANJI", "ASC")
            ->get()
            ->getResultArray();
    }

    /**
     * Get all appointments for a specific doctor
     */
    public function getDoctorAllAppointments($doctorId)
    {
        return $this->db
            ->table("Janji_Temu a")
            ->select(
                "a.*, p.NAMA_LENGKAP as patient_name, p.NO_TELP_PASIEN as patient_phone, pa.nama_paket"
            )
            ->join("Pasien p", "p.PASIEN_ID = a.ID_PASIEN", "left")
            ->join("Paket pa", "pa.id_paket = a.ID_PAKET", "left")
            ->where("a.ID_DOKTER", $doctorId)
            ->orderBy("a.TANGGAL_JANJI", "DESC")
            ->orderBy("a.WAKTU_JANJI", "ASC")
            ->get()
            ->getResultArray();
    }

    /**
     * Get patients waiting for diagnosis
     */
    public function getPatientsWaitingForDiagnosis($doctorId)
    {
        return $this->db
            ->table("Janji_Temu a")
            ->select(
                "a.*, p.NAMA_LENGKAP as patient_name, p.NO_TELP_PASIEN as patient_phone, pa.nama_paket"
            )
            ->join("Pasien p", "p.PASIEN_ID = a.ID_PASIEN", "left")
            ->join("Paket pa", "pa.id_paket = a.ID_PAKET", "left")
            ->where("a.ID_DOKTER", $doctorId)
            ->where("a.STATUS", "confirmed")
            ->where(
                "NOT EXISTS (SELECT 1 FROM Diagnosis d WHERE d.id_janji_temu = a.ID_JANJI_TEMU)"
            )
            ->get()
            ->getResultArray();
    }

    /**
     * Get patients with new lab results
     */
    public function getPatientsWithNewLabResults($doctorId)
    {
        return $this->db
            ->table("Janji_Temu a")
            ->select(
                "a.*, p.NAMA_LENGKAP as patient_name, p.NO_TELP_PASIEN as patient_phone, pa.nama_paket"
            )
            ->join("Pasien p", "p.PASIEN_ID = a.ID_PASIEN", "left")
            ->join("Paket pa", "pa.id_paket = a.ID_PAKET", "left")
            ->join(
                "Lab_Orders lo",
                "lo.id_janji_temu = a.ID_JANJI_TEMU",
                "left"
            )
            ->where("a.ID_DOKTER", $doctorId)
            ->where("a.STATUS", "awaiting_lab_results")
            ->where("lo.status", "completed")
            ->groupBy("a.ID_JANJI_TEMU")
            ->get()
            ->getResultArray();
    }
}
