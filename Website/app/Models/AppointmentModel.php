<?php

namespace App\Models;

use CodeIgniter\Model;

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

    // Get appointments for a specific doctor by status
    public function getDoctorAppointmentsByStatus($doctorId, $status)
    {
        return $this->db
            ->table("janji_temu a")
            ->select("a.*, p.nama_pasien as patient_name, p.telepon as patient_phone, pa.nama_paket")
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

    // Get patients waiting for diagnosis (no diagnosis record)
    public function getPatientsWaitingFordiagnosis($doctorId)
    {
        return $this->db
            ->table("janji_temu a")
            ->select("a.*, p.nama_pasien as patient_name, p.telepon as patient_phone, pa.nama_paket")
            ->join("pasien p", "p.id_pasien = a.id_pasien", "left")
            ->join("paket pa", "pa.id_paket = a.id_paket", "left")
            ->join("diagnosis d", "d.id_janji_temu = a.id_janji_temu", "left")
            ->where("a.id_dokter", $doctorId)
            ->where("a.status", "confirmed")
            ->where("d.id_diagnosis IS NULL")
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
    public function createAppointment($data, $request)
    {
        $validation = \Config\Services::validation();
        $validation->setRules(
            [
                "nama_janji"      => "required|min_length[3]|max_length[255]|string",
                "tanggal_janji"   => "required|valid_date",
                "waktu_janji"     => "required|regex_match[/(0[89]|1[0-7]):[0-5][0-9]/]",
                "paket_terpilih"  => "required|numeric|is_natural_no_zero",
                "id_dokter"       => "required|min_length[1]|max_length[30]|alpha_numeric_punct",
            ],
            [
                "nama_janji" => [
                    "required" => "Nama janji harus diisi.",
                    "min_length" => "Nama janji minimal 3 karakter.",
                    "max_length" => "Nama janji maksimal 255 karakter.",
                ],
                "tanggal_janji" => [
                    "required" => "Tanggal janji harus diisi.",
                    "valid_date" => "Format tanggal tidak valid.",
                ],
                "waktu_janji" => [
                    "required" => "Waktu janji harus diisi.",
                    "regex_match" => "Waktu janji harus antara jam 08:00 - 17:00.",
                ],
                "paket_terpilih" => [
                    "required" => "Paket harus dipilih.",
                    "numeric" => "Paket tidak valid.",
                    "is_natural_no_zero" => "Paket tidak valid.",
                ],
                "id_dokter" => [
                    "required" => "Dokter harus dipilih.",
                    "min_length" => "ID dokter tidak boleh kosong.",
                    "max_length" => "ID dokter terlalu panjang.",
                    "alpha_numeric_punct" => "ID dokter mengandung karakter tidak valid.",
                ],
            ]
        );

        if (!$validation->withRequest($request)->run()) {
            return [
                'success' => false,
                'errors' => $validation->getErrors(),
            ];
        }

        $namaJanji = htmlspecialchars($request->getPost("nama_janji"), ENT_QUOTES, "UTF-8");
        $tanggalJanji = $request->getPost("tanggal_janji");
        $waktuJanji = $request->getPost("waktu_janji");
        $dokterId = $request->getPost("id_dokter");
        $paketId = (int) $request->getPost("paket_terpilih");
        $pasienId = $data['id_pasien'] ?? null;
        $userId = $data['user_id'] ?? $pasienId;

        if (!$pasienId) {
            return [
                'success' => false,
                'errors' => ['id_pasien' => 'ID pasien tidak ditemukan.'],
            ];
        }

        $insertData = [
            "nama_janji" => $namaJanji,
            "id_pasien" => $pasienId,
            "tanggal_janji" => $tanggalJanji,
            "waktu_janji" => $waktuJanji,
            "id_dokter" => $dokterId,
            "id_paket" => $paketId,
            "status" => "pending",
            "created_by" => $userId,
            "updated_by" => $userId,
        ];

        // Doctor availability check
        if (!$this->isDoctorAvailable($dokterId, $tanggalJanji, $waktuJanji)) {
            return [
                'success' => false,
                'errors' => ['id_dokter' => "Dokter tidak tersedia pada waktu tersebut. Silakan pilih waktu lain."],
            ];
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $db->query("CALL mcu_app.GenerateIdJanjiTemu(@newId)");
            $row = $db->query("SELECT @newId AS id_janji_temu")->getRowArray();
            $id_janji_temu = $row['id_janji_temu'];
            $insertData['id_janji_temu'] = $id_janji_temu;

            $this->insert($insertData);
            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception("Database transaction failed.");
            }

            return [
                'success' => true,
                'id_janji_temu' => $id_janji_temu,
            ];
        } catch (\Exception $e) {
            $db->transRollback();
            return [
                'success' => false,
                'errors' => ['system' => $e->getMessage()],
            ];
        }
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
}
