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

        // Get common data for all methods
        $this->userId = session()->get("user_id") ?? null;

        // Get doctor ID from user ID
        $dokterModel = new DokterModel();
        if ($this->userId) {
            $doctorData = $dokterModel
                ->where("user_id", $this->userId)
                ->first();
            $this->doctorId = $doctorData ? $doctorData["id_dokter"] : null;
        }
    }



    public function dashboard()
    {
        $appointmentModel = new AppointmentModel();

        // Get today's appointments
        $data["today_appointments"] = $this->doctorId
            ? $appointmentModel->getDoctorAppointmentsByDate(
                $this->doctorId,
                date("Y-m-d")
            )
            : [];

        // Get pending appointments (waiting for confirmation)
        $data["pending_appointments"] = $this->doctorId
            ? $appointmentModel->getDoctorPendingAppointments($this->doctorId)
            : [];

        // Get patients waiting for diagnosis
        $data["diagnosis_queue"] = $this->doctorId
            ? $appointmentModel->getPatientsWaitingForDiagnosis($this->doctorId)
            : [];

        // Get patients with recent lab results
        $data["patients_with_lab_results"] = $this->doctorId
            ? $appointmentModel->getPatientsWithNewLabResults($this->doctorId)
            : [];

        $data["title"] = "Dashboard Dokter";
        $data["username"] = session()->get("username") ?? "Guest";
        $data["doctor_name"] = session()->get("full_name") ?? "Doctor";

        // Add doctor's basic info
        if ($this->doctorId) {
            $dokterModel = new DokterModel();
            $data["doctor_info"] = $dokterModel->findDoctorByIdWithSpesialisasi(
                $this->doctorId
            );
        }

        // Load appropriate view
        return view("templates/doctor/header", $data) .
            view("doctor/dashboard", $data) .
            view("templates/doctor/footer");
    }

    public function appointments($status = "all")
    {
        if (!$this->doctorId) {
            return redirect()
                ->to("doctor/dashboard")
                ->with("error", "Tidak dapat menemukan data dokter.");
        }

        $appointmentModel = new AppointmentModel();

        // Get appointments based on status
        switch ($status) {
            case "today":
                $data[
                    "appointments"
                ] = $appointmentModel->getDoctorAppointmentsByDate(
                    $this->doctorId,
                    date("Y-m-d")
                );
                $data["filter_title"] = "Hari Ini";
                break;
            case "pending":
                $data[
                    "appointments"
                ] = $appointmentModel->getDoctorPendingAppointments(
                    $this->doctorId
                );
                $data["filter_title"] = "Menunggu Konfirmasi";
                break;
            case "confirmed":
                $data[
                    "appointments"
                ] = $appointmentModel->getDoctorAppointmentsByStatus(
                    $this->doctorId,
                    "confirmed"
                );
                $data["filter_title"] = "Terkonfirmasi";
                break;
            case "completed":
                $data[
                    "appointments"
                ] = $appointmentModel->getDoctorAppointmentsByStatus(
                    $this->doctorId,
                    "completed"
                );
                $data["filter_title"] = "Selesai";
                break;
            case "cancelled":
                $data[
                    "appointments"
                ] = $appointmentModel->getDoctorAppointmentsByStatus(
                    $this->doctorId,
                    "cancelled"
                );
                $data["filter_title"] = "Dibatalkan";
                break;
            default:
                $data[
                    "appointments"
                ] = $appointmentModel->getDoctorAllAppointments(
                    $this->doctorId
                );
                $data["filter_title"] = "Semua";
                break;
        }

        $data["active_status"] = $status;
        $data["title"] = "Janji Temu Pasien";

        return view("templates/doctor/header", $data) .
            view("doctor/appointments", $data) .
            view("templates/doctor/footer");
    }

    public function appointmentDetail($id = null)
    {
        if (!$id) {
            return redirect()
                ->to("doctor/appointments")
                ->with("error", "ID janji temu diperlukan");
        }

        $appointmentModel = new AppointmentModel();
        $appointment = $appointmentModel->getAppointmentDetails($id);

        if (!$appointment) {
            return redirect()
                ->to("doctor/appointments")
                ->with("error", "Janji temu tidak ditemukan");
        }

        // Verify this appointment belongs to the doctor
        if ($appointment["ID_DOKTER"] != $this->doctorId) {
            return redirect()
                ->to("doctor/appointments")
                ->with(
                    "error",
                    "Anda tidak memiliki akses untuk janji temu ini"
                );
        }

        $data["appointment"] = $appointment;

        // Get patient detail
        $patientModel = new PatientModel();
        $data["patient"] = $patientModel->find($appointment["ID_PASIEN"]);

        // Get diagnosis if exists
        $diagnosisModel = new DiagnosisModel();
        $data["diagnosis"] = $diagnosisModel
            ->where("id_janji_temu", $id)
            ->first();

        // Get lab tests if ordered
        $labTestModel = new LabTestModel();
        $data["lab_tests"] = $labTestModel->getLabTestsByAppointmentId($id);

        $data["title"] = "Detail Janji Temu";

        return view("templates/doctor/header", $data) .
            view("doctor/appointment_detail", $data) .
            view("templates/doctor/footer");
    }

    public function updateAppointmentStatus()
    {
        // Ensure it's a POST request
        if ($this->request->getMethod() !== "post") {
            return redirect()
                ->to("doctor/appointments")
                ->with("error", "Metode yang tidak valid");
        }

        // Validate input
        $rules = [
            "appointment_id" => "required|numeric",
            "status" => "required|in_list[confirmed,cancelled,completed]",
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->to("doctor/appointments")
                ->with("error", "Data yang dikirimkan tidak valid");
        }

        $appointmentId = $this->request->getPost("appointment_id");
        $status = $this->request->getPost("status");
        $reason = $this->request->getPost("reason") ?? "";

        $appointmentModel = new AppointmentModel();
        $appointment = $appointmentModel->find($appointmentId);

        if (!$appointment) {
            return redirect()
                ->to("doctor/appointments")
                ->with("error", "Janji temu tidak ditemukan");
        }

        // Verify this appointment belongs to the doctor
        if ($appointment["ID_DOKTER"] != $this->doctorId) {
            return redirect()
                ->to("doctor/appointments")
                ->with(
                    "error",
                    "Anda tidak memiliki akses untuk janji temu ini"
                );
        }

        // Update appointment status
        $updateData = [
            "STATUS" => $status,
            "rejection_reason" => $status == "cancelled" ? $reason : null,
            "updated_by" => $this->userId,
        ];

        try {
            $appointmentModel->update($appointmentId, $updateData);

            // Set appropriate success message
            $message = "";
            if ($status == "confirmed") {
                $message = "Janji temu berhasil dikonfirmasi";
            } elseif ($status == "cancelled") {
                $message = "Janji temu berhasil dibatalkan";
            } elseif ($status == "completed") {
                $message = "Janji temu berhasil diselesaikan";
            }

            return redirect()
                ->to("doctor/appointment/" . $appointmentId)
                ->with("success", $message);
        } catch (\Exception $e) {
            return redirect()
                ->to("doctor/appointment/" . $appointmentId)
                ->with(
                    "error",
                    "Gagal memperbarui status: " . $e->getMessage()
                );
        }
    }

    public function diagnosis($appointmentId = null)
    {
        if (!$appointmentId) {
            return redirect()
                ->to("doctor/appointments")
                ->with("error", "ID janji temu diperlukan");
        }

        $appointmentModel = new AppointmentModel();
        $appointment = $appointmentModel->getAppointmentDetails($appointmentId);

        if (!$appointment) {
            return redirect()
                ->to("doctor/appointments")
                ->with("error", "Janji temu tidak ditemukan");
        }

        // Verify this appointment belongs to the doctor
        if ($appointment["ID_DOKTER"] != $this->doctorId) {
            return redirect()
                ->to("doctor/appointments")
                ->with(
                    "error",
                    "Anda tidak memiliki akses untuk janji temu ini"
                );
        }

        // Check if appointment is confirmed
        if ($appointment["STATUS"] !== "confirmed") {
            return redirect()
                ->to("doctor/appointment/" . $appointmentId)
                ->with(
                    "error",
                    "Hanya janji temu yang terkonfirmasi yang dapat didiagnosis"
                );
        }

        $data["appointment"] = $appointment;

        // Get patient detail
        $patientModel = new PatientModel();
        $data["patient"] = $patientModel->find($appointment["ID_PASIEN"]);

        // Get diagnosis if exists
        $diagnosisModel = new DiagnosisModel();
        $data["diagnosis"] = $diagnosisModel
            ->where("id_janji_temu", $appointmentId)
            ->first();

        // Get available lab tests
        $labTestModel = new LabTestModel();
        $data["available_lab_tests"] = $labTestModel->findAll();

        $data["title"] = "Diagnosis Pasien";

        return view("templates/doctor/header", $data) .
            view("doctor/diagnosis_form", $data) .
            view("templates/doctor/footer");
    }

    public function saveDiagnosis()
    {
        // Ensure it's a POST request
        if ($this->request->getMethod() !== "post") {
            return redirect()
                ->to("doctor/appointments")
                ->with("error", "Metode yang tidak valid");
        }

        // Validate input
        $rules = [
            "appointment_id" => "required|numeric",
            "symptoms" => "required",
            "diagnosis" => "required",
            "treatment" => "required",
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with("error", "Data yang dikirimkan tidak lengkap");
        }

        $appointmentId = $this->request->getPost("appointment_id");

        // Verify the appointment exists and belongs to this doctor
        $appointmentModel = new AppointmentModel();
        $appointment = $appointmentModel->find($appointmentId);

        if (!$appointment || $appointment["ID_DOKTER"] != $this->doctorId) {
            return redirect()
                ->to("doctor/appointments")
                ->with("error", "Janji temu tidak valid");
        }

        // Prepare diagnosis data
        $diagnosisData = [
            "id_janji_temu" => $appointmentId,
            "ID_DOKTER" => $this->doctorId,
            "ID_PASIEN" => $appointment["ID_PASIEN"],
            "symptoms" => $this->request->getPost("symptoms"),
            "diagnosis_result" => $this->request->getPost("diagnosis"),
            "treatment_plan" => $this->request->getPost("treatment"),
            "notes" => $this->request->getPost("notes") ?? "",
            "created_by" => $this->userId,
            "updated_by" => $this->userId,
        ];

        // Check if lab tests are ordered
        $orderedLabTests = $this->request->getPost("lab_tests") ?? [];

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Save diagnosis
            $diagnosisModel = new DiagnosisModel();

            // Check if diagnosis already exists
            $existingDiagnosis = $diagnosisModel
                ->where("id_janji_temu", $appointmentId)
                ->first();

            if ($existingDiagnosis) {
                // Update existing
                $diagnosisModel->update(
                    $existingDiagnosis["id"],
                    $diagnosisData
                );
                $diagnosisId = $existingDiagnosis["id"];
            } else {
                // Create new
                $diagnosisModel->insert($diagnosisData);
                $diagnosisId = $diagnosisModel->getInsertID();
            }

            // Order lab tests if selected
            if (!empty($orderedLabTests)) {
                $labTestModel = new LabTestModel();

                // Clear existing lab test orders for this appointment
                $labTestModel->deleteLabOrdersByAppointmentId($appointmentId);

                // Create new lab test orders
                foreach ($orderedLabTests as $testId) {
                    $labTestData = [
                        "id_janji_temu" => $appointmentId,
                        "id_test" => $testId,
                        "ID_DOKTER" => $this->doctorId,
                        "ID_PASIEN" => $appointment["ID_PASIEN"],
                        "status" => "ordered",
                        "created_by" => $this->userId,
                    ];
                    $labTestModel->createLabOrder($labTestData);
                }

                // Update appointment status if lab tests are ordered
                $appointmentModel->update($appointmentId, [
                    "STATUS" => "awaiting_lab_results",
                    "updated_by" => $this->userId,
                ]);
            } else {
                // Complete the appointment if no lab tests are ordered
                $appointmentModel->update($appointmentId, [
                    "STATUS" => "completed",
                    "updated_by" => $this->userId,
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception("Database transaction failed");
            }

            return redirect()
                ->to("doctor/appointment/" . $appointmentId)
                ->with("success", "Diagnosis berhasil disimpan");
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    "error",
                    "Gagal menyimpan diagnosis: " . $e->getMessage()
                );
        }
    }

    public function labResults($appointmentId = null)
    {
        if (!$appointmentId) {
            return redirect()
                ->to("doctor/appointments")
                ->with("error", "ID janji temu diperlukan");
        }

        $appointmentModel = new AppointmentModel();
        $appointment = $appointmentModel->getAppointmentDetails($appointmentId);

        if (!$appointment) {
            return redirect()
                ->to("doctor/appointments")
                ->with("error", "Janji temu tidak ditemukan");
        }

        // Verify this appointment belongs to the doctor
        if ($appointment["ID_DOKTER"] != $this->doctorId) {
            return redirect()
                ->to("doctor/appointments")
                ->with(
                    "error",
                    "Anda tidak memiliki akses untuk janji temu ini"
                );
        }

        $data["appointment"] = $appointment;

        // Get lab tests
        $labTestModel = new LabTestModel();
        $data["lab_tests"] = $labTestModel->getLabTestsByAppointmentId(
            $appointmentId
        );

        $data["title"] = "Hasil Laboratorium";

        return view("templates/doctor/header", $data) .
            view("doctor/lab_results", $data) .
            view("templates/doctor/footer");
    }

    public function completeWithLabResults($appointmentId = null)
    {
        if (!$appointmentId) {
            return redirect()
                ->to("doctor/appointments")
                ->with("error", "ID janji temu diperlukan");
        }

        // Verify the appointment exists and belongs to this doctor
        $appointmentModel = new AppointmentModel();
        $appointment = $appointmentModel->find($appointmentId);

        if (!$appointment || $appointment["ID_DOKTER"] != $this->doctorId) {
            return redirect()
                ->to("doctor/appointments")
                ->with("error", "Janji temu tidak valid");
        }

        // Make sure all lab tests are completed
        $labTestModel = new LabTestModel();
        $pendingTests = $labTestModel->getPendingLabTestsByAppointmentId(
            $appointmentId
        );

        if (!empty($pendingTests)) {
            return redirect()
                ->to("doctor/lab-results/" . $appointmentId)
                ->with(
                    "error",
                    "Tidak dapat menyelesaikan janji temu, masih ada hasil lab yang tertunda"
                );
        }

        // Complete the appointment
        try {
            $appointmentModel->update($appointmentId, [
                "STATUS" => "completed",
                "updated_by" => $this->userId,
            ]);

            return redirect()
                ->to("doctor/appointment/" . $appointmentId)
                ->with(
                    "success",
                    "Janji temu berhasil diselesaikan dengan hasil lab"
                );
        } catch (\Exception $e) {
            return redirect()
                ->to("doctor/lab-results/" . $appointmentId)
                ->with(
                    "error",
                    "Gagal menyelesaikan janji temu: " . $e->getMessage()
                );
        }
    }

    public function mySchedule()
    {
        // Get doctor's schedule information
        // This is a placeholder - you'd need to implement a schedule model

        $data["title"] = "Jadwal Dokter";

        return view("templates/doctor/header", $data) .
            view("doctor/schedule", $data) .
            view("templates/doctor/footer");
    }

    public function profile()
    {
        if (!$this->doctorId) {
            return redirect()
                ->to("doctor/dashboard")
                ->with("error", "Tidak dapat menemukan data dokter.");
        }

        $dokterModel = new DokterModel();
        $data["doctor"] = $dokterModel->findDoctorByIdWithSpesialisasi(
            $this->doctorId
        );

        $data["title"] = "Profil Dokter";

        return view("templates/doctor/header", $data) .
            view("doctor/profile", $data) .
            view("templates/doctor/footer");
    }

    public function updateProfile()
    {
        // Handle doctor profile updates
        // Placeholder - you would implement profile updating logic here

        return redirect()
            ->to("doctor/profile")
            ->with("success", "Profil berhasil diperbarui");
    }

    public function saveDoctorRegistration()
    {
        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            "nama_dokter" => "required|min_length[3]|max_length[255]",
            "email" => "required|valid_email|is_unique[Users.email]",
            "username" =>
                "required|alpha_numeric_space|min_length[3]|is_unique[Users.username]",
            "password" => "required|min_length[8]|matches[password_confirm]",
            "password_confirm" => "required",
            "no_telp_dokter" => "required|numeric|min_length[10]",
            "id_spesialisasi" => "required|numeric",
            "no_lisensi" => "required|is_unique[Dokter.NO_LISENSI]",
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()
                ->back()
                ->withInput()
                ->with("error", implode("<br>", $validation->getErrors()));
        }

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create user account
            $userModel = new \App\Models\UserModel();
            $userData = [
                "username" => $this->request->getPost("username"),
                "email" => $this->request->getPost("email"),
                "password" => password_hash(
                    $this->request->getPost("password"),
                    PASSWORD_DEFAULT
                ),
                "role" => "dokter",
                "is_active" => 1, // Set to 0 if you want to implement email verification
                "created_at" => date("Y-m-d H:i:s"),
            ];

            $userId = $userModel->insert($userData);

            // Create doctor profile
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

            // Send verification email if needed
            // $this->sendVerificationEmail($userData['email']);

            return redirect()
                ->to("auth/doctor/login")
                ->with(
                    "success",
                    "Registrasi berhasil! Akun Anda sedang dalam proses verifikasi. Kami akan menghubungi Anda melalui email jika akun sudah terverifikasi."
                );
        } catch (\Exception $e) {
            $db->transRollback();
            log_message("error", "Registration failed: " . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    "error",
                    "Registrasi gagal. Silakan coba lagi nanti atau hubungi admin."
                );
        }
    }


    public function registerSocialDoctor()
    {
        // Check if we have the Google data
        $session = session();
        $googleData = $session->get("temp_google_data");

        if (!$googleData) {
            return redirect()
                ->to("auth/doctor/login")
                ->with(
                    "error",
                    "Invalid request. Please start the Google registration process again."
                );
        }

        // Get specializations for the form
        $spesialisasiModel = new \App\Models\SpesialisasiModel();
        $data["specializations"] = $spesialisasiModel->findAll();
        $data["email"] = $googleData["email"];
        $data["name"] = $googleData["name"];
        $data["oauth_id"] = $googleData["oauth_id"];
        $data["oauth_provider"] = $googleData["oauth_provider"];

        return view("auth/complete_doctor_registration", $data);
    }

    public function completeDoctorSocialRegistration()
    {
        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            "username" =>
                "required|alpha_numeric_space|min_length[3]|is_unique[Users.username]",
            "no_telp_dokter" => "required|numeric|min_length[10]",
            "id_spesialisasi" => "required|numeric",
            "no_lisensi" => "required|is_unique[Dokter.NO_LISENSI]",
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()
                ->back()
                ->withInput()
                ->with("error", implode("<br>", $validation->getErrors()));
        }

        // Get data from form
        $email = $this->request->getPost("email");
        $name = $this->request->getPost("name");
        $oauthId = $this->request->getPost("oauth_id");
        $oauthProvider = $this->request->getPost("oauth_provider");

        // Create user data
        $userData = [
            "username" => $this->request->getPost("username"),
            "email" => $email,
            "password" => password_hash(
                random_string("alnum", 10),
                PASSWORD_DEFAULT
            ),
            "role" => "doctor",
            "is_active" => 1,
            "google_id" => $oauthId,
            "created_at" => date("Y-m-d H:i:s"),
        ];

        // Create doctor data
        $doctorData = [
            "NAMA_DOKTER" => $name,
            "id_spesialisasi" => $this->request->getPost("id_spesialisasi"),
            "NO_TELP_DOKTER" => $this->request->getPost("no_telp_dokter"),
            "NO_LISENSI" => $this->request->getPost("no_lisensi"),
            "is_verified" => 0,
            "verification_status" => "pending",
        ];

        // Save to database
        $dokterModel = new \App\Models\DokterModel();
        $result = $dokterModel->createDoctorFromSocialLogin(
            $userData,
            $doctorData
        );

        if (!$result) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    "error",
                    "Registrasi gagal. Silakan coba lagi nanti atau hubungi admin."
                );
        }

        // Clear the temporary session data
        session()->remove("temp_google_data");

        return redirect()
            ->to("auth/doctor/login")
            ->with(
                "success",
                "Registrasi berhasil! Akun Anda sedang dalam proses verifikasi. Kami akan menghubungi Anda melalui email jika akun sudah terverifikasi."
            );
    }

    private function generateUsername($name)
    {
        // Generate a username from name
        $username = strtolower(preg_replace("/[^a-zA-Z0-9]/", "", $name));

        // Check if username exists
        $userModel = new \App\Models\UserModel();
        $existing = $userModel->where("username", $username)->first();

        if (!$existing) {
            return $username;
        }

        // Add a random number until we find a unique username
        $i = 1;
        while ($userModel->where("username", $username . $i)->first()) {
            $i++;
        }

        return $username . $i;
    }

    private function setUserSession($userData)
    {
        $session = session();
        $sessionData = [
            "user_id" => $userData["user_id"],
            "username" => $userData["username"],
            "email" => $userData["email"],
            "role" => $userData["role"],
            "isLoggedIn" => true,
        ];

        // For doctors, get additional information
        if ($userData["role"] == "doctor") {
            $dokterModel = new \App\Models\DokterModel();
            $doctorData = $dokterModel
                ->where("user_id", $userData["user_id"])
                ->first();

            if ($doctorData) {
                $sessionData["doctor_id"] = $doctorData["ID_DOKTER"];
                $sessionData["full_name"] = $doctorData["NAMA_DOKTER"];
            }
        }

        // For patients, get additional information
        if ($userData["role"] == "patient") {
            $patientModel = new \App\Models\PatientModel();
            $patientData = $patientModel
                ->where("PASIEN_ID", $userData["user_id"])
                ->first();

            if ($patientData) {
                $sessionData["patient_id"] = $patientData["PASIEN_ID"];
                $sessionData["full_name"] = $patientData["NAMA_LENGKAP"];
            }
        }

        $session->set($sessionData);
    }
}
