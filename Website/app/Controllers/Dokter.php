<?php

namespace App\Controllers;

use App\Models\AppointmentModel;
use App\Models\PasienModel;
use App\Models\DokterModel;
use App\Models\DiagnosisModel;
use App\Models\LabTestModel;

class Dokter extends AuthenticatedController
{
    protected $doctorId;
    protected $userId;

    public function __construct()
    {
        parent::__construct("dokter", "auth/dokter/login");

        $this->userId = session()->get("user_id") ?? null;
        $dokterModel = new DokterModel();
        if ($this->userId) {
            $doctorData = $dokterModel->where("user_id", $this->userId)->first();
            $this->doctorId = $doctorData ? $doctorData["id_dokter"] : null;
        }
    }

    public function dashboard()
    {
        $appointmentModel = new AppointmentModel();

        $data["today_appointments"] = $this->doctorId
            ? $appointmentModel->getDoctorAppointmentsByDate($this->doctorId, date("Y-m-d"))
            : [];

        $data["pending_appointments"] = $this->doctorId
            ? $appointmentModel->getDoctorPendingAppointments($this->doctorId)
            : [];

        $data["diagnosis_queue"] = $this->doctorId
            ? $appointmentModel->getPatientsWaitingFordiagnosis($this->doctorId)
            : [];

        $data["patients_with_lab_results"] = $this->doctorId
            ? $appointmentModel->getPatientsWithNewLabResults($this->doctorId)
            : [];

        $data["title"] = "Dashboard Dokter";
        $data["username"] = session()->get("username") ?? "Guest";
        $data["doctor_name"] = session()->get("full_name") ?? "Doctor";

        if ($this->doctorId) {
            $dokterModel = new DokterModel();
            $data["doctor_info"] = $dokterModel->findDoctorByIdWithSpesialisasi($this->doctorId);
        }

        return view("templates/doctor/header", $data)
            . view("doctor/dashboard", $data)
            . view("templates/doctor/footer");
    }

    public function appointments()
    {
        $status = $this->request->getGet('status') ?? 'all';

        if (!$this->doctorId) {
            return redirect()->to("doctor/dashboard")->with("error", "Tidak dapat menemukan data dokter.");
        }

        $appointmentModel = new AppointmentModel();

        switch ($status) {
            case "today":
                $data["appointments"] = $appointmentModel->getDoctorAppointmentsByDate($this->doctorId, date("Y-m-d"));
                $data["filter_title"] = "Hari Ini";
                break;
            case "pending":
                $data["appointments"] = $appointmentModel->getDoctorPendingAppointments($this->doctorId);
                $data["filter_title"] = "Menunggu Konfirmasi";
                break;
            case "confirmed":
                $data["appointments"] = $appointmentModel->getDoctorAppointmentsByStatus($this->doctorId, ["confirmed"]);
                $data["filter_title"] = "Terkonfirmasi";
                break;
            case "awaiting_lab_results":
                $data["appointments"] = $appointmentModel->getDoctorAppointmentsByStatus($this->doctorId, "awaiting_lab_results");
                $data["filter_title"] = "Menunggu Hasil Test Laboratorium";
                break;
            case "completed":
                $data["appointments"] = $appointmentModel->getDoctorAppointmentsByStatus($this->doctorId, "completed");
                $data["filter_title"] = "Selesai";
                break;
            case "cancelled":
                $data["appointments"] = $appointmentModel->getDoctorAppointmentsByStatus($this->doctorId, "cancelled");
                $data["filter_title"] = "Dibatalkan";
                break;
            default:
                $data["appointments"] = $appointmentModel->getDoctorAllAppointments($this->doctorId);
                $data["filter_title"] = "Semua";
                break;
        }

        $data["active_status"] = $status;
        $data["title"] = "Janji Temu Pasien";

        return view("templates/doctor/header", $data)
            . view("doctor/appointments", $data)
            . view("templates/doctor/footer");
    }

    public function accept($id)
    {
        $model = new AppointmentModel();
        $model->update($id, [
            'status' => 'confirmed',
            'rejection_reason' => null
        ]);
        return redirect()->back()->with('success', 'Janji temu diterima');
    }

    public function reject()
    {
        $id = $this->request->getPost('id_janji_temu');
        $reason = $this->request->getPost('rejection_reason');
        if (!$reason) {
            return redirect()->back()->with('error', 'Alasan penolakan wajib diisi');
        }
        $model = new AppointmentModel();
        $model->update($id, [
            'status' => 'rejected',
            'rejection_reason' => $reason
        ]);
        return redirect()->back()->with('success', 'Janji temu ditolak');
    }

    public function appointmentDetail($id = null)
    {
        if (!$id) {
            return redirect()->to("dokter/appointments")->with("error", "ID janji temu diperlukan");
        }

        $appointmentModel = new AppointmentModel();
        $appointment = $appointmentModel->getAppointmentDetails($id);

        if (!$appointment) {
            return redirect()->to("dokter/appointments")->with("error", "Janji temu tidak ditemukan");
        }

        if ($appointment["id_dokter"] != $this->doctorId) {
            return redirect()->to("dokter/appointments")->with("error", "Anda tidak memiliki akses untuk janji temu ini");
        }

        $data["appointment"] = $appointment;
        $patientModel = new PasienModel();
        $data["patient"] = $patientModel->find($appointment["id_pasien"]);
        $diagnosisModel = new DiagnosisModel();
        $data["diagnosis"] = $diagnosisModel->where("id_janji_temu", $id)->first();
        $labTestModel = new LabTestModel();
        $data["lab_tests"] = $labTestModel->getTestsByAppointment($id);

        $data["title"] = "Detail Janji Temu";

        return view("templates/doctor/header", $data)
            . view("doctor/appointment_detail", $data)
            . view("templates/doctor/footer");
    }

    public function updateAppointmentStatus()
    {
        if ($this->request->getMethod() !== "post") {
            return redirect()->to("doctor/appointments")->with("error", "Metode yang tidak valid");
        }

        $rules = [
            "appointment_id" => "required|numeric",
            "status" => "required|in_list[confirmed,cancelled,completed]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->to("doctor/appointments")->with("error", "Data yang dikirimkan tidak valid");
        }

        $appointmentId = $this->request->getPost("appointment_id");
        $status = $this->request->getPost("status");
        $reason = $this->request->getPost("reason") ?? "";

        $appointmentModel = new AppointmentModel();
        $appointment = $appointmentModel->find($appointmentId);

        if (!$appointment) {
            return redirect()->to("doctor/appointments")->with("error", "Janji temu tidak ditemukan");
        }

        if ($appointment["id_dokter"] != $this->doctorId) {
            return redirect()->to("doctor/appointments")->with("error", "Anda tidak memiliki akses untuk janji temu ini");
        }

        $updateData = [
            "status" => $status,
            "rejection_reason" => $status == "cancelled" ? $reason : null,
            "updated_by" => $this->userId,
        ];

        try {
            $appointmentModel->update($appointmentId, $updateData);

            $message = "";
            if ($status == "confirmed") {
                $message = "Janji temu berhasil dikonfirmasi";
            } elseif ($status == "cancelled") {
                $message = "Janji temu berhasil dibatalkan";
            } elseif ($status == "completed") {
                $message = "Janji temu berhasil diselesaikan";
            }

            return redirect()->to("doctor/appointment/" . $appointmentId)->with("success", $message);
        } catch (\Exception $e) {
            return redirect()->to("doctor/appointment/" . $appointmentId)->with("error", "Gagal memperbarui status: " . $e->getMessage());
        }
    }

    public function diagnosis($appointmentId = null)
    {
        if (!$appointmentId) {
            return redirect()->to("dokter/appointments")->with("error", "ID janji temu diperlukan");
        }

        $appointmentModel = new AppointmentModel();
        $appointment = $appointmentModel->getAppointmentDetails($appointmentId);

        if (!$appointment) {
            return redirect()->to("dokter/appointments")->with("error", "Janji temu tidak ditemukan");
        }

        if ($appointment["id_dokter"] != $this->doctorId) {
            return redirect()->to("dokter/appointments")->with("error", "Anda tidak memiliki akses untuk janji temu ini");
        }

        if ($appointment["status"] == "completed") {
            return redirect()->to("dokter/appointment/" . $appointmentId)->with("error", "Hanya janji temu yang terkonfirmasi yang dapat didiagnosis");
        }

        $data["appointment"] = $appointment;

        $patientModel = new PasienModel();
        $data["patient"] = $patientModel->find($appointment["id_pasien"]);

        $diagnosisModel = new DiagnosisModel();
        $data["diagnosis"] = $diagnosisModel->where("id_janji_temu", $appointmentId)->first();

        // Get all existing lab tests for the appointment (for approval/status table)
        $labTestModel = new LabTestModel();
        $orderedLabTests = $labTestModel->getTestsByAppointment($appointmentId);   // All test_lab for this appointment
        $data["orderedLabTests"] = $orderedLabTests;

        // Get all active lab test procedures for ordering new ones
        $labTestProcedureModel = new \App\Models\LabTestProcedure();
        $data["lab_tests"] = $labTestProcedureModel->getActiveProcedures();

        // Determine which lab test procedures are already ordered for this appointment
        $checkedLabTestProcedures = [];
        if (!empty($orderedLabTests)) {
            foreach ($orderedLabTests as $test) {
                $checkedLabTestProcedures[] = $test['id_lab_test_procedure'];
            }
        }
        $data['checked_lab_tests'] = $checkedLabTestProcedures;
        $data['require_lab'] = !empty($checkedLabTestProcedures) ? 1 : 0;

        $data["title"] = "Diagnosis Pasien";

        return view("templates/doctor/header", $data)
            . view("doctor/diagnosis_form", $data)
            . view("templates/doctor/footer");
    }

    public function saveDiagnosis()
    {
        $appointmentId = $this->request->getPost("id_janji_temu");

        $rules = [
            "id_janji_temu"    => "required",
            "id_dokter"        => "required",
            "id_pasien"        => "required",
            "symptoms"         => "required",
            "diagnosis_result" => "required",
            "treatment_plan"   => "required",
            "require_lab"      => "required|in_list[0,1]",
        ];

        if (!$this->validate($rules)) {
            log_message('error', '[saveDiagnosis] Validation failed: ' . print_r($this->validator->getErrors(), true));
            return redirect()->back()->withInput()->with("error", "Data yang dikirimkan tidak lengkap");
        }

        $diagnosisData = [
            "id_janji_temu"     => $this->request->getPost("id_janji_temu"),
            "id_dokter"         => $this->request->getPost("id_dokter"),
            "id_pasien"         => $this->request->getPost("id_pasien"),
            "symptoms"          => $this->request->getPost("symptoms"),
            "diagnosis_result"  => $this->request->getPost("diagnosis_result"),
            "treatment_plan"    => $this->request->getPost("treatment_plan"),
            "notes"             => $this->request->getPost("notes") ?? "",
            "tanggal_hasil_lab" => $this->request->getPost("tanggal_hasil_lab") ?? null,
            "created_by"        => $this->userId,
            "updated_by"        => $this->userId,
        ];

        $requireLab = (int)$this->request->getPost("require_lab");
        $orderedLabTests = $requireLab ? ($this->request->getPost("lab_tests") ?? []) : [];
        $approveLabTestIds = $this->request->getPost('approve_lab_tests') ?? [];

        $diagnosisModel = new DiagnosisModel();
        $labTestModel = new \App\Models\LabTestModel();
        $appointmentModel = new \App\Models\AppointmentModel();

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Save or update diagnosis
            $existing = $diagnosisModel->where("id_janji_temu", $diagnosisData["id_janji_temu"])->first();
            if ($existing) {
                $result = $diagnosisModel->update($existing["id_diagnosis"], $diagnosisData);
                if ($result === false) {
                    log_message('error', '[saveDiagnosis] Update diagnosis errors: ' . print_r($diagnosisModel->errors(), true));
                }
            } else {
                $result = $diagnosisModel->insert($diagnosisData);
                if ($result === false) {
                    log_message('error', '[saveDiagnosis] Insert diagnosis errors: ' . print_r($diagnosisModel->errors(), true));
                }
            }

            // Handle lab test logic
            if ($requireLab && !empty($orderedLabTests)) {
                $labTestModel->syncAppointmentLabTests($appointmentId, $orderedLabTests, $this->userId);
                $result = $appointmentModel->markAwaitingLabResults($appointmentId, $this->userId);
                if ($result === false) {
                    log_message('error', '[saveDiagnosis] markAwaitingLabResults errors: ' . print_r($appointmentModel->errors(), true));
                }
            } else {
                $labTestModel->deleteByAppointment($appointmentId);
                $result = $appointmentModel->markCompleted($appointmentId, $this->userId);
                if ($result === false) {
                    log_message('error', '[saveDiagnosis] markCompleted errors: ' . print_r($appointmentModel->errors(), true));
                }
            }

            // Approval section
            $now = date('Y-m-d H:i:s');
            foreach ($approveLabTestIds as $id_test_lab) {
                $updateData = [
                    'approved_by_dokter' => $diagnosisData["id_dokter"],
                    'approved_at' => $now
                ];
                $result = $labTestModel->update($id_test_lab, $updateData);
                if ($result === false) {
                    log_message('error', '[saveDiagnosis] Approve lab test error: ' . print_r($labTestModel->errors(), true));
                }
            }

            // Mark appointment as completed if all lab tests are completed & approved
            $allApproved = $labTestModel->where('id_janji_temu', $appointmentId)
                ->where('status', 'completed')
                ->where('approved_by_dokter IS NULL', null, false)
                ->countAllResults() === 0;

            if ($allApproved && $requireLab) {
                $result = $appointmentModel->update($appointmentId, [
                    'status' => 'completed'
                ]);
                if ($result === false) {
                    log_message('error', '[saveDiagnosis] appointmentModel->update errors: ' . print_r($appointmentModel->errors(), true));
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                log_message('error', '[saveDiagnosis] Transaction failed.');
                return redirect()->back()->withInput()->with("error", "Gagal menyimpan diagnosis (DB error)");
            }

            return redirect()->to("dokter/appointment/" . $appointmentId)->with("success", "Diagnosis & approval berhasil disimpan");
        } catch (\Throwable $e) {
            log_message('error', '[saveDiagnosis] Exception: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->withInput()->with("error", "DB Exception: " . $e->getMessage());
        }
    }

    public function labResults($appointmentId = null)
    {
        if (!$appointmentId) {
            return redirect()->to("doctor/appointments")->with("error", "ID janji temu diperlukan");
        }

        $appointmentModel = new AppointmentModel();
        $appointment = $appointmentModel->getAppointmentDetails($appointmentId);

        if (!$appointment) {
            return redirect()->to("doctor/appointments")->with("error", "Janji temu tidak ditemukan");
        }

        if ($appointment["id_dokter"] != $this->doctorId) {
            return redirect()->to("doctor/appointments")->with("error", "Anda tidak memiliki akses untuk janji temu ini");
        }

        $data["appointment"] = $appointment;
        $labTestModel = new LabTestModel();
        $data["lab_tests"] = $labTestModel->getTestsByAppointment($appointmentId);

        $data["title"] = "Hasil Laboratorium";

        return view("templates/doctor/header", $data)
            . view("doctor/lab_results", $data)
            . view("templates/doctor/footer");
    }

    public function completeWithLabResults($appointmentId = null)
    {
        if (!$appointmentId) {
            return redirect()->to("doctor/appointments")->with("error", "ID janji temu diperlukan");
        }

        $appointmentModel = new AppointmentModel();
        $appointment = $appointmentModel->find($appointmentId);

        if (!$appointment || $appointment["id_dokter"] != $this->doctorId) {
            return redirect()->to("doctor/appointments")->with("error", "Janji temu tidak valid");
        }

        $labTestModel = new LabTestModel();
        $pendingTests = $labTestModel->where('id_janji_temu', $appointmentId)
            ->whereIn('status', ['ordered', 'in_progress'])
            ->findAll();

        if (!empty($pendingTests)) {
            return redirect()->to("doctor/lab-results/" . $appointmentId)
                ->with("error", "Tidak dapat menyelesaikan janji temu, masih ada hasil lab yang tertunda");
        }

        try {
            $appointmentModel->update($appointmentId, [
                "status" => "completed",
                "updated_by" => $this->userId,
            ]);

            return redirect()->to("doctor/appointment/" . $appointmentId)
                ->with("success", "Janji temu berhasil diselesaikan dengan hasil lab");
        } catch (\Exception $e) {
            return redirect()->to("doctor/lab-results/" . $appointmentId)
                ->with("error", "Gagal menyelesaikan janji temu: " . $e->getMessage());
        }
    }

    public function mySchedule()
    {
        $doctorId = session()->get('doctor_id'); // or however you get it
        $data = [
            "title" => "Jadwal Dokter",
            "doctorId" => $doctorId
        ];
        return view("templates/doctor/header", $data)
            . view("doctor/schedule", $data)
            . view("templates/doctor/footer");
    }

    public function updateProfile()
    {
        $doctorId = $this->doctorId;
        $dokterModel = new \App\Models\DokterModel();

        $data = $this->request->getPost([
            'nama_dokter',
            'jenis_kelamin',
            'tanggal_lahir',
            'no_lisensi',
            'telepon_dokter',
            'lokasi_kerja',
            'alamat_dokter'
        ]);

        if ($dokterModel->update($doctorId, $data)) {
            return redirect()->to('dokter/profile')->with('success', 'Profil berhasil diperbarui.');
        }
        return redirect()->to('dokter/profile')->with('error', 'Gagal memperbarui profil.');
    }

    public function profile()
    {
        if (!$this->doctorId) {
            return redirect()->to("dokter/dashboard")
                ->with("error", "Tidak dapat menemukan data dokter.");
        }

        $dokterModel = new DokterModel();
        $data["doctor"] = $dokterModel->findDoctorByIdWithSpesialisasi($this->doctorId);

        $jadwalModel = new \App\Models\DokterJadwalModel();
        $data["schedules"] = $jadwalModel->where('id_dokter', $this->doctorId)->findAll();

        $data["title"] = "Profil Dokter";

        return view("templates/doctor/header", $data)
            . view("doctor/profile", $data)
            . view("templates/doctor/footer");
    }

    public function addSchedule()
    {
        $data['title'] = 'Tambah Jadwal Praktek';
        return view("templates/doctor/header", $data)
            . view("doctor/add_schedule", $data)
            . view("templates/doctor/footer");
    }

    public function deleteSchedule($id_jadwal = null)
    {
        if (!$id_jadwal) {
            return redirect()->back()->with('error', 'ID jadwal tidak ditemukan.');
        }

        $jadwalModel = new \App\Models\DokterJadwalModel();
        $deleted = $jadwalModel->deleteScheduleById($id_jadwal, $this->doctorId);

        if ($deleted) {
            return redirect()->to('dokter/profile')->with('success', 'Jadwal berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'Jadwal gagal dihapus atau tidak ditemukan.');
        }
    }

    public function saveSchedule()
    {
        $doctorId = $this->doctorId;
        $days = $this->request->getPost('hari'); // array
        $jamMulai = $this->request->getPost('jam_mulai');
        $jamSelesai = $this->request->getPost('jam_selesai');
        $lokasi = $this->request->getPost('lokasi');

        if (!(new \App\Models\DokterJadwalModel())->addSchedulesForDoctor($doctorId, $days, $jamMulai, $jamSelesai, $lokasi)) {
            return redirect()->back()->with('error', 'Semua field wajib diisi.');
        }
        return redirect()->to('dokter/profile')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function saveDoctorRegistration()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            "nama_dokter" => "required|min_length[3]|max_length[255]",
            "email" => "required|valid_email|is_unique[Users.email]",
            "username" => "required|alpha_numeric_space|min_length[3]|is_unique[Users.username]",
            "password" => "required|min_length[8]|matches[password_confirm]",
            "password_confirm" => "required",
            "no_telp_dokter" => "required|numeric|min_length[10]",
            "id_spesialisasi" => "required|numeric",
            "no_lisensi" => "required|is_unique[Dokter.NO_LISENSI]",
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with("error", implode("<br>", $validation->getErrors()));
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $userModel = new \App\Models\UserModel();
            $userData = [
                "username" => $this->request->getPost("username"),
                "email" => $this->request->getPost("email"),
                "password" => password_hash($this->request->getPost("password"), PASSWORD_DEFAULT),
                "role" => "dokter",
                "is_active" => 1,
                "created_at" => date("Y-m-d H:i:s"),
            ];

            $userId = $userModel->insert($userData);

            $dokterModel = new \App\Models\DokterModel();
            $doctorData = [
                "user_id" => $userId,
                "NAMA_DOKTER" => $this->request->getPost("nama_dokter"),
                "id_spesialisasi" => $this->request->getPost("id_spesialisasi"),
                "NO_TELP_DOKTER" => $this->request->getPost("no_telp_dokter"),
                "NO_LISENSI" => $this->request->getPost("no_lisensi"),
                "is_verified" => 0,
                "verification_status" => "pending",
                "created_by" => $userId,
                "updated_by" => $userId,
            ];

            $dokterModel->insert($doctorData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception("Database transaction failed");
            }

            return redirect()->to("auth/doctor/login")
                ->with("success", "Registrasi berhasil! Akun Anda sedang dalam proses verifikasi. Kami akan menghubungi Anda melalui email jika akun sudah terverifikasi.");
        } catch (\Exception $e) {
            $db->transRollback();
            log_message("error", "Registration failed: " . $e->getMessage());
            return redirect()->back()->withInput()
                ->with("error", "Registrasi gagal. Silakan coba lagi nanti atau hubungi admin.");
        }
    }

    public function registerSocialDoctor()
    {
        $session = session();
        $googleData = $session->get("temp_google_data");

        if (!$googleData) {
            return redirect()->to("auth/doctor/login")
                ->with("error", "Invalid request. Please start the Google registration process again.");
        }

        $spesialisasiModel = new \App\Models\SpesialisasiModel();
        $data["specializations"] = $spesialisasiModel->findAll();
        $data["email"] = $googleData["email"];
        $data["name"] = $googleData["name"];
        $data["oauth_id"] = $googleData["oauth_id"];
        $data["oauth_provider"] = $googleData["oauth_provider"];

        return view("auth/complete_doctor_registration", $data);
    }

    public function approve_lab_results($id_janji_temu)
    {
        $approvedTestIds = $this->request->getPost('approve_lab_tests') ?? [];
        $labTestModel = new \App\Models\LabTestModel();

        $now = date('Y-m-d H:i:s');
        foreach ($approvedTestIds as $id_test_lab) {
            $labTestModel->update($id_test_lab, [
                'approved_by_dokter' => $this->userId,
                'approved_at' => $now
            ]);
        }

        // Mark appointment as completed if all lab tests are completed & approved
        $allApproved = $labTestModel->where('id_janji_temu', $id_janji_temu)
            ->where('status', 'completed')
            ->where('approved_by_dokter IS NULL', null, false)
            ->countAllResults() === 0;

        if ($allApproved) {
            $appointmentModel = new \App\Models\AppointmentModel();
            $appointmentModel->update($id_janji_temu, [
                'status' => 'completed'
            ]);
        }

        return redirect()->back()->with('success', 'Lab results approved!');
    }

    public function downloadLabReport($id_test_lab)
    {
        $labTestModel = new \App\Models\LabTestModel();
        $order = $labTestModel->find($id_test_lab);

        if (!$order) {
            return redirect()->back()->with('error', 'Lab test tidak ditemukan.');
        }

        // Get appointment to check doctor
        $appointmentModel = new \App\Models\AppointmentModel();
        $appointment = $appointmentModel->find($order['id_janji_temu']);

        // Check doctor permission
        if (!$appointment || $appointment['id_dokter'] != $this->doctorId) {
            return redirect()->back()->with('error', 'Akses tidak diizinkan.');
        }

        $filepath = WRITEPATH . 'uploads/reports/' . $order['hasil_test'];

        if (!is_file($filepath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        // You may want to log this download event for auditing
        // $this->logActivity('download_lab_report', 'Doctor downloaded lab report', ['id_test_lab' => $id_test_lab]);

        return $this->response->download($filepath, null);
    }
}
