<?php namespace App\Models;

use CodeIgniter\Model;

class AppointmentModel extends Model
{
    protected $table = "janji_temu";
    protected $primaryKey = "id_janji_temu";
    protected $allowedFields = [
        "nama_janji",
        "id_pasien",
        "tanggal_janji",
        "waktu_janji",
        "id_dokter",
        "id_paket",
        "status",
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
    public function getPasienAppointments($pasienId)
    {
        return $this->db
            ->table("janji_temu a")
            ->select("a.*, d.nama_dokter, s.nama_spesialisasi, p.nama_paket")
            ->join("dokter d", "d.id_dokter = a.id_dokter", "left")
            ->join(
                "spesialisasi s",
                "s.id_spesialisasi = d.id_spesialisasi",
                "left"
            )
            ->join("paket p", "p.id_paket = a.id_paket", "left")
            ->where("a.id_pasien", $pasienId)
            ->orderBy("a.tanggal_janji", "DESC")
            ->orderBy("a.waktu_janji", "DESC")
            ->get()
            ->getResultArray();
    }

    /**
     * Get upcoming appointments for a specific patient with a limit
     */
    public function getUpcomingAppointments($patientId, $limit = null)
    {
        $query = $this->db
            ->table("janji_temu a")
            ->select("a.*, d.nama_dokter, s.nama_spesialisasi, p.nama_paket")
            ->join("dokter d", "d.id_dokter = a.id_dokter", "left")
            ->join(
                "spesialisasi s",
                "s.id_spesialisasi = d.id_spesialisasi",
                "left"
            )
            ->join("paket p", "p.id_paket = a.id_paket", "left")
            ->where("a.id_pasien", $patientId)
            ->where("a.status", "pending")
            ->orWhere("a.status", "confirmed")
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
            ->table("janji_temu a")
            ->select(
                'a.*,
                       p.nama_paket, p.harga, p.deskripsi as DESKRIPSI_PAKET,
                       d.nama_dokter,
                       s.nama_spesialisasi,
                       diag.diagnosis, diag.REKOMENDASI, diag.TANGGAL_DIAGNOSIS,
                       diag.HASIL_LAB, diag.TANGGAL_HASIL_LAB'
            )
            ->join("paket p", "p.id_paket = a.id_paket", "left")
            ->join("dokter d", "d.id_dokter = a.id_dokter", "left")
            ->join(
                "spesialisasi s",
                "s.id_spesialisasi = d.id_spesialisasi",
                "left"
            )
            ->join("diagnosis diag", "diag.ID_JANJI = a.id_janji_temu", "left")
            ->where("a.id_janji_temu", $id);

        return $builder->get()->getRowArray();
    }

    public function saveDiagnosis($data)
    {
        $db = \Config\Database::connect();
        $builder = $db->table("diagnosis");

        return $builder->insert($data);
    }

    public function updateDiagnosis($id, $data)
    {
        $db = \Config\Database::connect();
        $builder = $db->table("diagnosis");

        return $builder->where("id_diagnosis", $id)->update($data);
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
            ->table("janji_temu")
            ->where("id_dokter", $doctorId)
            ->where("TANGGAL_JANJI", $date)
            ->where("WAKTU_JANJI", $time)
            ->where("status !=", "cancelled") // Ignore cancelled appointments
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
            ->table("janji_temu")
            ->select("TANGGAL_JANJI")
            ->where("id_pasien", $patientId)
            ->where("status", "completed")
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
            ->table("janji_temu")
            ->where("id_pasien", $patientId)
            ->where("status", "completed")
            ->countAllResults();
    }

    /**
     * Get all appointments for a specific doctor on a specific date
     */
    public function getDoctorAppointmentsByDate($doctorId, $date)
    {
        return $this->db
            ->table("janji_temu a")
            ->select(
                "a.*, p.nama_pasien as patient_name, p.telepon as patient_phone, pa.nama_paket"
            )
            ->join("pasien p", "p.id_pasien = a.id_pasien", "left")
            ->join("paket pa", "pa.id_paket = a.id_paket", "left")
            ->where("a.id_dokter", $doctorId)
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
            ->table("janji_temu a")
            ->select(
                "a.*, p.nama_pasien as patient_name, p.telepon as patient_phone, pa.nama_paket"
            )
            ->join("pasien p", "p.id_pasien = a.id_pasien", "left")
            ->join("paket pa", "pa.id_paket = a.id_paket", "left")
            ->where("a.id_dokter", $doctorId)
            ->where("a.status", "pending")
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
            ->table("janji_temu a")
            ->select(
                "a.*, p.nama_pasien as patient_name, p.telepon as patient_phone, pa.nama_paket"
            )
            ->join("pasien p", "p.id_pasien = a.id_pasien", "left")
            ->join("paket pa", "pa.id_paket = a.id_paket", "left")
            ->where("a.id_dokter", $doctorId)
            ->where("a.status", $status)
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
            ->table("janji_temu a")
            ->select(
                "a.*, p.nama_pasien as patient_name, p.telepon as patient_phone, pa.nama_paket"
            )
            ->join("pasien p", "p.id_pasien = a.id_pasien", "left")
            ->join("paket pa", "pa.id_paket = a.id_paket", "left")
            ->where("a.id_dokter", $doctorId)
            ->orderBy("a.TANGGAL_JANJI", "DESC")
            ->orderBy("a.WAKTU_JANJI", "ASC")
            ->get()
            ->getResultArray();
    }

    /**
     * Get patients waiting for diagnosis
     */
    public function getPatientsWaitingFordiagnosis($doctorId)
    {
        return $this->db
            ->table("janji_temu a")
            ->select(
                "a.*, p.nama_pasien as patient_name, p.telepon as patient_phone, pa.nama_paket"
            )
            ->join("pasien p", "p.id_pasien = a.id_pasien", "left")
            ->join("paket pa", "pa.id_paket = a.id_paket", "left")
            ->where("a.id_dokter", $doctorId)
            ->where("a.status", "confirmed")
            ->where(
                "NOT EXISTS (SELECT 1 FROM diagnosis d WHERE d.id_janji_temu = a.id_janji_temu)"
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
            ->table("janji_temu a")
            ->select(
                "a.*, p.nama_pasien as patient_name, p.telepon as patient_phone, pa.nama_paket"
            )
            ->join("pasien p", "p.id_pasien = a.id_pasien", "left")
            ->join("paket pa", "pa.id_paket = a.id_paket", "left")
            ->join(
                "Lab_Orders lo",
                "lo.id_janji_temu = a.id_janji_temu",
                "left"
            )
            ->where("a.id_dokter", $doctorId)
            ->where("a.status", "awaiting_lab_results")
            ->where("lo.status", "completed")
            ->groupBy("a.id_janji_temu")
            ->get()
            ->getResultArray();
    }
}
