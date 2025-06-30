<?php

namespace App\Controllers;

use App\Models\AppointmentModel;
use App\Models\PatientModel;
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
                $data["appointments"] = $appointmentModel->getDoctorAppointmentsByStatus($this->doctorId, "confirmed");
                $data["filter_title"] = "Terkonfirmasi";
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

    public function appointmentDetail($id = null)
    {
        if (!$id) {
            return redirect()->to("doctor/appointments")->with("error", "ID janji temu diperlukan");
        }

        $appointmentModel = new AppointmentModel();
        $appointment = $appointmentModel->getAppointmentDetails($id);

        if (!$appointment) {
            return redirect()->to("doctor/appointments")->with("error", "Janji temu tidak ditemukan");
        }

        if ($appointment["id_dokter"] != $this->doctorId) {
            return redirect()->to("doctor/appointments")->with("error", "Anda tidak memiliki akses untuk janji temu ini");
        }

        $data["appointment"] = $appointment;
        $patientModel = new PatientModel();
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

        if ($appointment["status"] !== "confirmed") {
            return redirect()->to("doctor/appointment/" . $appointmentId)->with("error", "Hanya janji temu yang terkonfirmasi yang dapat didiagnosis");
        }

        $data["appointment"] = $appointment;
        $patientModel = new PatientModel();
        $data["patient"] = $patientModel->find($appointment["id_pasien"]);
        $diagnosisModel = new DiagnosisModel();
        $data["diagnosis"] = $diagnosisModel->where("id_janji_temu", $appointmentId)->first();

        $labTestModel = new LabTestModel();
        $data["ordered_lab_tests"] = $labTestModel->getTestsByAppointment($appointmentId);

        $data["title"] = "Diagnosis Pasien";

        return view("templates/doctor/header", $data)
            . view("doctor/diagnosis_form", $data)
            . view("templates/doctor/footer");
    }

    public function saveDiagnosis()
    {
        if ($this->request->getMethod() !== "post") {
            return redirect()->to("doctor/appointments")->with("error", "Metode yang tidak valid");
        }

        $rules = [
            "appointment_id" => "required|numeric",
            "symptoms" => "required",
            "diagnosis" => "required",
            "treatment" => "required",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with("error", "Data yang dikirimkan tidak lengkap");
        }

        $appointmentId = $this->request->getPost("appointment_id");

        $appointmentModel = new AppointmentModel();
        $appointment = $appointmentModel->find($appointmentId);

        if (!$appointment || $appointment["id_dokter"] != $this->doctorId) {
            return redirect()->to("doctor/appointments")->with("error", "Janji temu tidak valid");
        }

        $diagnosisData = [
            "id_janji_temu" => $appointmentId,
            "id_dokter" => $this->doctorId,
            "id_pasien" => $appointment["id_pasien"],
            "symptoms" => $this->request->getPost("symptoms"),
            "diagnosis_result" => $this->request->getPost("diagnosis"),
            "treatment_plan" => $this->request->getPost("treatment"),
            "notes" => $this->request->getPost("notes") ?? "",
            "created_by" => $this->userId,
            "updated_by" => $this->userId,
        ];

        $orderedLabTests = $this->request->getPost("lab_tests") ?? [];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $diagnosisModel = new DiagnosisModel();
            $existingDiagnosis = $diagnosisModel->where("id_janji_temu", $appointmentId)->first();

            if ($existingDiagnosis) {
                $diagnosisModel->update($existingDiagnosis["id"], $diagnosisData);
            } else {
                $diagnosisModel->insert($diagnosisData);
            }

            $labTestModel = new LabTestModel();

            // Remove all old lab tests for this appointment
            $db->table('test_lab')->where('id_janji_temu', $appointmentId)->delete();

            // Insert new lab test orders
            if (!empty($orderedLabTests)) {
                foreach ($orderedLabTests as $jenis_test) {
                    $labTestModel->insert([
                        "id_janji_temu" => $appointmentId,
                        "tanggal_test" => date("Y-m-d H:i:s"),
                        "jenis_test" => $jenis_test,
                        "status" => "ordered",
                        "created_by" => $this->userId,
                        "created_at" => date("Y-m-d H:i:s"),
                    ]);
                }
                $appointmentModel->update($appointmentId, [
                    "status" => "awaiting_lab_results",
                    "updated_by" => $this->userId,
                ]);
            } else {
                $appointmentModel->update($appointmentId, [
                    "status" => "completed",
                    "updated_by" => $this->userId,
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception("Database transaction failed");
            }

            return redirect()->to("doctor/appointment/" . $appointmentId)->with("success", "Diagnosis berhasil disimpan");
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with("error", "Gagal menyimpan diagnosis: " . $e->getMessage());
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

    public function profile()
    {
        if (!$this->doctorId) {
            return redirect()->to("doctor/dashboard")
                ->with("error", "Tidak dapat menemukan data dokter.");
        }

        $dokterModel = new DokterModel();
        $data["doctor"] = $dokterModel->findDoctorByIdWithSpesialisasi($this->doctorId);

        $data["title"] = "Profil Dokter";

        return view("templates/doctor/header", $data)
            . view("doctor/profile", $data)
            . view("templates/doctor/footer");
    }

    public function updateProfile()
    {
        // Placeholder for profile update logic
        return redirect()->to("doctor/profile")->with("success", "Profil berhasil diperbarui");
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

}
