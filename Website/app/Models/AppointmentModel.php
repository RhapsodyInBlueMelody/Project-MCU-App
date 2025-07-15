<?php

namespace App\Models;

use CodeIgniter\Model;
use RuntimeException;

class AppointmentModel extends Model
{
    protected $table = "janji_temu";
    protected $primaryKey = "id_janji_temu";
    protected $allowedFields = [
        "id_janji_temu",
        "nama_janji",
        "id_pasien",
        "tanggal_janji",
        "waktu_janji",
        "id_dokter",
        "id_paket",
        "status",
        "rejection_reason",
        "doctor_notes",
        "created_by",
        "created_at",
        "updated_by",
        "updated_at",
    ];
    protected $useTimestamps = true;
    protected $createdField = "created_at";
    protected $updatedField = "updated_at";

    // Get all appointments for a specific patient
    public function getPasienAppointments($pasienId)
    {
        return $this->db
            ->table("janji_temu a")
            ->select("a.*, d.nama_dokter, s.nama_spesialisasi, p.nama_paket")
            ->join("dokter d", "d.id_dokter = a.id_dokter", "left")
            ->join("spesialisasi s", "s.id_spesialisasi = d.id_spesialisasi", "left")
            ->join("paket p", "p.id_paket = a.id_paket", "left")
            ->where("a.id_pasien", $pasienId)
            ->orderBy("a.tanggal_janji", "DESC")
            ->orderBy("a.waktu_janji", "DESC")
            ->get()
            ->getResultArray();
    }

    // Add these methods to your AppointmentModel class

    public function getAppointmentWithDetails($limit = null, $offset = null)
    {
        $builder = $this->select('
            janji_temu.id_janji_temu as ID_JANJI_TEMU,
            janji_temu.nama_janji as NAMA_JANJI,
            janji_temu.tanggal_janji as TANGGAL_JANJI,
            janji_temu.waktu_janji as WAKTU_JANJI,
            janji_temu.status as STATUS,
            janji_temu.rejection_reason,
            janji_temu.doctor_notes,
            pasien.nama_pasien as patient_name,
            pasien.telepon as patient_phone,
            dokter.nama_dokter as NAMA_DOKTER,
            spesialisasi.nama_spesialisasi as nama_spesialisasi,
            paket.nama_paket as nama_paket
        ')
            ->join('pasien', 'pasien.id_pasien = janji_temu.id_pasien', 'left')
            ->join('dokter', 'dokter.id_dokter = janji_temu.id_dokter', 'left')
            ->join('spesialisasi', 'spesialisasi.id_spesialisasi = dokter.id_spesialisasi', 'left')
            ->join('paket', 'paket.id_paket = janji_temu.id_paket', 'left')
            ->orderBy('janji_temu.created_at', 'DESC');

        if ($limit) {
            $builder->limit($limit, $offset);
        }

        return $builder->findAll();
    }

    public function getAppointmentWithDetailsPaginated($perPage = 10)
    {
        return $this->select('
            janji_temu.id_janji_temu as ID_JANJI_TEMU,
            janji_temu.nama_janji as NAMA_JANJI,
            janji_temu.tanggal_janji as TANGGAL_JANJI,
            janji_temu.waktu_janji as WAKTU_JANJI,
            janji_temu.status as STATUS,
            janji_temu.rejection_reason,
            janji_temu.doctor_notes,
            pasien.nama_pasien as patient_name,
            pasien.telepon as patient_phone,
            dokter.nama_dokter as NAMA_DOKTER,
            spesialisasi.nama_spesialisasi as nama_spesialisasi,
            paket.nama_paket as nama_paket
        ')
            ->join('pasien', 'pasien.id_pasien = janji_temu.id_pasien', 'left')
            ->join('dokter', 'dokter.id_dokter = janji_temu.id_dokter', 'left')
            ->join('spesialisasi', 'spesialisasi.id_spesialisasi = dokter.id_spesialisasi', 'left')
            ->join('paket', 'paket.id_paket = janji_temu.id_paket', 'left')
            ->orderBy('janji_temu.created_at', 'DESC')
            ->paginate($perPage);
    }

    public function getAppointmentStats()
    {
        return [
            'total' => $this->countAllResults(),
            'pending' => $this->where('status', 'pending')->countAllResults(),
            'confirmed' => $this->where('status', 'confirmed')->countAllResults(),
            'completed' => $this->where('status', 'completed')->countAllResults(),
            'cancelled' => $this->where('status', 'cancelled')->countAllResults()
        ];
    }

    public function getAppointmentDetailsById($appointmentId)
    {
        return $this->select('
            janji_temu.*,
            pasien.nama_pasien as patient_name,
            pasien.telepon as patient_phone,
            pasien.alamat as patient_address,
            pasien.tanggal_lahir as patient_birthdate,
            dokter.nama_dokter as doctor_name,
            spesialisasi.nama_spesialisasi as specialization_name,
            paket.nama_paket as package_name,
            paket.harga as package_price,
            paket.deskripsi as package_description
        ')
            ->join('pasien', 'pasien.id_pasien = janji_temu.id_pasien', 'left')
            ->join('dokter', 'dokter.id_dokter = janji_temu.id_dokter', 'left')
            ->join('spesialisasi', 'spesialisasi.id_spesialisasi = dokter.id_spesialisasi', 'left')
            ->join('paket', 'paket.id_paket = janji_temu.id_paket', 'left')
            ->where('janji_temu.id_janji_temu', $appointmentId)
            ->first();
    }

    public function getMonthlyAppointmentStats()
    {
        return $this->select('
            MONTH(tanggal_janji) as month,
            YEAR(tanggal_janji) as year,
            COUNT(*) as total_appointments,
            SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled
        ')
            ->where('tanggal_janji >=', date('Y-01-01'))
            ->groupBy('YEAR(tanggal_janji), MONTH(tanggal_janji)')
            ->orderBy('year, month')
            ->findAll();
    }

    public function getRecentAppointments($limit = 5)
    {
        return $this->select('
            janji_temu.id_janji_temu,
            janji_temu.nama_janji as NAMA_JANJI,
            janji_temu.tanggal_janji as TANGGAL_JANJI,
            janji_temu.waktu_janji as WAKTU_JANJI,
            janji_temu.status,
            pasien.nama_pasien as patient_name
        ')
            ->join('pasien', 'pasien.id_pasien = janji_temu.id_pasien', 'left')
            ->orderBy('janji_temu.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }



    // Get upcoming appointments for a specific patient with a limit
    public function getUpcomingAppointments($patientId, $limit = null)
    {
        $query = $this->db
            ->table("janji_temu a")
            ->select("a.*, d.nama_dokter, s.nama_spesialisasi, p.nama_paket")
            ->join("dokter d", "d.id_dokter = a.id_dokter", "left")
            ->join("spesialisasi s", "s.id_spesialisasi = d.id_spesialisasi", "left")
            ->join("paket p", "p.id_paket = a.id_paket", "left")
            ->where("a.id_pasien", $patientId)
            ->groupStart()
            ->where("a.status", "pending")
            ->orWhere("a.status", "confirmed")
            ->groupEnd()
            ->where("a.tanggal_janji >=", date("Y-m-d"))
            ->orderBy("a.tanggal_janji", "ASC")
            ->orderBy("a.waktu_janji", "ASC");

        if (!is_null($limit)) {
            $query->limit($limit);
        }

        return $query->get()->getResultArray();
    }

    // Get appointment details by ID, with lab tests for this appointment
    public function getAppointmentDetails($id)
    {
        $builder = $this->db
            ->table('janji_temu a')
            ->select(
                'a.*, 
                 p.nama_paket, p.harga, p.deskripsi as deskripsi_paket, 
                 d.nama_dokter, 
                 s.nama_spesialisasi,
                 transaksi.id_transaksi, transaksi.status_pembayaran, transaksi.doku_payment_url, transaksi.doku_expired_time,
                 diag.id_diagnosis as diagnosis_id,
                 diag.symptoms as diagnosis_symptoms, 
                 diag.diagnosis_result as diagnosis_result, 
                 diag.treatment_plan as diagnosis_treatment_plan,
                 diag.notes as diagnosis_notes,
                 diag.hasil_lab as diagnosis_hasil_lab, 
                 diag.tanggal_hasil_lab as diagnosis_tanggal_hasil_lab,
                 diag.created_at as diagnosis_created_at,
                 diag.updated_at as diagnosis_updated_at'
            )
            ->join('paket p', 'p.id_paket = a.id_paket', 'left')
            ->join('dokter d', 'd.id_dokter = a.id_dokter', 'left')
            ->join('spesialisasi s', 's.id_spesialisasi = d.id_spesialisasi', 'left')
            ->join('transaksi', 'transaksi.id_janji_temu = a.id_janji_temu', 'left')
            ->join('diagnosis diag', 'diag.id_janji_temu = a.id_janji_temu', 'left')
            ->where('a.id_janji_temu', $id);

        $appointment = $builder->get()->getRowArray();

        // Fetch all lab tests for this appointment
        if ($appointment) {
            $labTestModel = new \App\Models\LabTestModel();
            $appointment['lab_tests'] = $labTestModel->getTestsByAppointment($id);
        }

        return $appointment;
    }

    // Get all appointments for a specific doctor on a specific date
    public function getDoctorAppointmentsByDate($doctorId, $date)
    {
        return $this->db
            ->table("janji_temu a")
            ->select("a.*, p.nama_pasien as patient_name, p.telepon as patient_phone, pa.nama_paket")
            ->join("pasien p", "p.id_pasien = a.id_pasien", "left")
            ->join("paket pa", "pa.id_paket = a.id_paket", "left")
            ->where("a.id_dokter", $doctorId)
            ->where("a.tanggal_janji", $date)
            ->orderBy("a.waktu_janji", "ASC")
            ->get()
            ->getResultArray();
    }

    public function getDoctorAppointmentsByMonth($doctorId, $year, $month)
    {
        return $this->db
            ->table("janji_temu a")
            ->select("a.*, p.nama_pasien as patient_name, p.telepon as patient_phone, pa.nama_paket")
            ->join("pasien p", "p.id_pasien = a.id_pasien", "left")
            ->join("paket pa", "pa.id_paket = a.id_paket", "left")
            ->where("a.id_dokter", $doctorId)
            ->where("YEAR(a.tanggal_janji)", $year)
            ->where("MONTH(a.tanggal_janji)", $month)
            ->orderBy("a.tanggal_janji", "ASC")
            ->orderBy("a.waktu_janji", "ASC")
            ->get()
            ->getResultArray();
    }

    // Get pending appointments for a specific doctor
    public function getDoctorPendingAppointments($doctorId)
    {
        return $this->db
            ->table("janji_temu a")
            ->select("a.*, p.nama_pasien as patient_name, p.telepon as patient_phone, pa.nama_paket")
            ->join("pasien p", "p.id_pasien = a.id_pasien", "left")
            ->join("paket pa", "pa.id_paket = a.id_paket", "left")
            ->where("a.id_dokter", $doctorId)
            ->where("a.status", "pending")
            ->where("a.tanggal_janji >=", date("Y-m-d"))
            ->orderBy("a.tanggal_janji", "ASC")
            ->orderBy("a.waktu_janji", "ASC")
            ->get()
            ->getResultArray();
    }

    // Get appointments for a specific doctor by status, with info if any lab test is completed
    public function getDoctorAppointmentsByStatus($doctorId, $status)
    {
        return $this->db
            ->table("janji_temu a")
            ->select("a.*, 
                  p.nama_pasien as patient_name, 
                  p.telepon as patient_phone, 
                  pa.nama_paket,
                  EXISTS(
                    SELECT 1 FROM test_lab lo
                    WHERE lo.id_janji_temu = a.id_janji_temu AND lo.status = 'completed'
                  ) as has_completed_lab
        ")
            ->join("pasien p", "p.id_pasien = a.id_pasien", "left")
            ->join("paket pa", "pa.id_paket = a.id_paket", "left")
            ->where("a.id_dokter", $doctorId)
            ->where("a.status", $status)
            ->orderBy("a.tanggal_janji", "DESC")
            ->orderBy("a.waktu_janji", "ASC")
            ->get()
            ->getResultArray();
    }

    // Get all appointments for a specific doctor
    public function getDoctorAllAppointments($doctorId)
    {
        return $this->db
            ->table("janji_temu a")
            ->select("a.*, p.nama_pasien as patient_name, p.telepon as patient_phone, pa.nama_paket")
            ->join("pasien p", "p.id_pasien = a.id_pasien", "left")
            ->join("paket pa", "pa.id_paket = a.id_paket", "left")
            ->where("a.id_dokter", $doctorId)
            ->orderBy("a.tanggal_janji", "DESC")
            ->orderBy("a.waktu_janji", "ASC")
            ->get()
            ->getResultArray();
    }

    public function getPatientsWaitingForDiagnosis($doctorId)
    {
        // Subquery: Find appointments where at least 1 test is not completed (but not cancelled/rejected)
        $subQuery = $this->db->table('test_lab')
            ->select('id_janji_temu')
            ->whereIn('status', ['ordered', 'in progress']); // Only pending/active tests

        return $this->db
            ->table("janji_temu a")
            ->select("a.*, p.nama_pasien as patient_name, p.telepon as patient_phone, pa.nama_paket")
            ->join("pasien p", "p.id_pasien = a.id_pasien", "left")
            ->join("paket pa", "pa.id_paket = a.id_paket", "left")
            ->join("diagnosis d", "d.id_janji_temu = a.id_janji_temu", "left")
            ->where("a.id_dokter", $doctorId)
            ->whereNotIn('a.status', ['cancelled', 'rejected']) // Exclude cancelled/rejected appointments
            ->groupStart()
            ->where("d.id_diagnosis IS NULL")
            ->orWhereIn("a.id_janji_temu", $subQuery)
            ->groupEnd()
            ->get()
            ->getResultArray();
    }

    // Get patients with new lab results for the doctor
    public function getPatientsWithNewLabResults($doctorId)
    {
        return $this->db
            ->table("janji_temu a")
            ->select("a.*, p.nama_pasien as patient_name, p.telepon as patient_phone, pa.nama_paket")
            ->join("pasien p", "p.id_pasien = a.id_pasien", "left")
            ->join("paket pa", "pa.id_paket = a.id_paket", "left")
            ->join("test_lab lo", "lo.id_janji_temu = a.id_janji_temu", "left")
            ->where("a.id_dokter", $doctorId)
            ->where("a.status", "awaiting_lab_results")
            ->where("lo.status", "completed")
            ->groupBy("a.id_janji_temu")
            ->get()
            ->getResultArray();
    }

    // Create a new appointment
    public function createAppointment(array $data): string
    {
        $this->db->transStart();

        $insertResult = $this->insert($data);
        if ($insertResult === false || $this->db->transStatus() === false) {
            $this->db->transRollback();
            $dbError = $this->db->error();
            throw new RuntimeException(
                'Gagal membuat janji temu. Database error: ' .
                    ($dbError['message'] ?? 'Unknown database error.') .
                    ' (Code: ' . ($dbError['code'] ?? 'N/A') . ')'
            );
        }

        $this->db->transComplete();
        return $data['id_janji_temu'];
    }

    // Check if a doctor is available at a specific date and time
    public function isDoctorAvailable($doctorId, $date, $time)
    {
        $hour = (int) substr($time, 0, 2);
        if ($hour < 8 || $hour >= 17) {
            return false;
        }

        $conflictingAppointments = $this->db
            ->table("janji_temu")
            ->where("id_dokter", $doctorId)
            ->where("tanggal_janji", $date)
            ->where("waktu_janji", $time)
            ->where("status !=", "cancelled")
            ->countAllResults();

        return $conflictingAppointments === 0;
    }

    // Get the last completed appointment date for a patient
    public function getLastCompletedAppointmentDate($patientId)
    {
        $result = $this->db
            ->table("janji_temu")
            ->select("tanggal_janji")
            ->where("id_pasien", $patientId)
            ->where("status", "completed")
            ->orderBy("tanggal_janji", "DESC")
            ->limit(1)
            ->get()
            ->getRowArray();

        return $result ? $result["tanggal_janji"] : null;
    }

    // Get the total number of completed appointments for a patient
    public function getTotalCompletedAppointments($patientId)
    {
        return $this->db
            ->table("janji_temu")
            ->where("id_pasien", $patientId)
            ->where("status", "completed")
            ->countAllResults();
    }

    // Utility: Get all lab tests for an appointment (uses LabTestModel)
    public function getLabTests($id_janji_temu)
    {
        $labTestModel = new \App\Models\LabTestModel();
        return $labTestModel->getTestsByAppointment($id_janji_temu);
    }

    // Utility: Get all lab tests for a doctor
    public function getAllLabTestsByDoctor($doctorId)
    {
        return $this->db
            ->table('test_lab tl')
            ->select('tl.*, a.id_pasien, a.tanggal_janji, a.id_dokter')
            ->join('janji_temu a', 'a.id_janji_temu = tl.id_janji_temu')
            ->where('a.id_dokter', $doctorId)
            ->get()
            ->getResultArray();
    }

    // Utility: Get all lab tests for a patient
    public function getAllLabTestsByPatient($pasienId)
    {
        return $this->db
            ->table('test_lab tl')
            ->select('tl.*, a.id_dokter, a.tanggal_janji, a.id_pasien')
            ->join('janji_temu a', 'a.id_janji_temu = tl.id_janji_temu')
            ->where('a.id_pasien', $pasienId)
            ->get()
            ->getResultArray();
    }

    public function markAwaitingLabResults($appointmentId, $userId)
    {
        return $this->update($appointmentId, [
            "status" => "awaiting_lab_results",
            "updated_by" => $userId,
        ]);
    }

    public function markCompleted($appointmentId, $userId)
    {
        return $this->update($appointmentId, [
            "status" => "completed",
            "updated_by" => $userId,
        ]);
    }

    public function generateAppointmentId(): string
    {
        $db = \Config\Database::connect();
        $db->query("CALL GenerateIdJanjiTemu(@newId)");
        $result = $db->query("SELECT @newId AS id")->getRow();

        if (!$result || !$result->id) {
            log_message('error', 'Stored procedure failed! Returned: ' . print_r($result, true));
            throw new \RuntimeException('ID generation failed: Stored procedure returned no value');
        }

        log_message('debug', 'Generated Appointment ID: ' . $result->id);
        return $result->id;
    }
}
