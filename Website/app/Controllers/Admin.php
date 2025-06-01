<?php

namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\DokterModel;
use App\Models\Patientmodel;
use App\Models\AppointmentModel;

class Admin extends AuthenticatedController
{
    protected $adminId;
    protected $userId;

    public function __construct()
    {
        parent::__construct("admin", "auth/admin/login");

        // Get common data for all methods
        $this->userId = session()->get("user_id") ?? null;

        // Get admin ID from user ID
        $adminModel = new AdminModel();
        if ($this->userId) {
            $adminData = $adminModel->where("user_id", $this->userId)->first();
            $this->adminId = $adminData ? $adminData["admin_id"] : null;
        }
    }

    public function dashboard()
    {
        $adminModel = new AdminModel();
        $dokterModel = new DokterModel();
        $patientModel = new PatientModel();
        $appointmentModel = new AppointmentModel();

        // Get counts for dashboard widgets
        $data["total_doctors"] = $dokterModel->countAll();
        $data["pending_verifications"] = $dokterModel
            ->where("verification_status", "pending")
            ->countAllResults();
        $data["total_patients"] = $patientModel->countAll();
        $data["total_appointments"] = $appointmentModel->countAll();

        // Recent appointments
        $data["recent_appointments"] = $appointmentModel->getRecentAppointments(
            5
        );

        $data["title"] = "Dashboard Admin";
        return view("templates/admin/header", $data) .
            view("admin/dashboard", $data) .
            view("templates/admin/footer");
    }

    public function pendingDoctorVerifications()
    {
        $adminModel = new AdminModel();
        $data[
            "pending_verifications"
        ] = $adminModel->getPendingDoctorVerifications();
        $data["title"] = "Verifikasi Dokter";

        return view("templates/admin/header", $data) .
            view("admin/pending_doctor_verifications", $data) .
            view("templates/admin/footer");
    }

    public function verifyDoctor($doctorId = null)
    {
        if (!$doctorId) {
            return redirect()
                ->to("admin/pending-doctor-verifications")
                ->with("error", "ID Dokter diperlukan");
        }

        // Get the doctor details
        $dokterModel = new DokterModel();
        $doctor = $dokterModel->findDoctorByIdWithSpesialisasi($doctorId);

        if (!$doctor) {
            return redirect()
                ->to("admin/pending-doctor-verifications")
                ->with("error", "Dokter tidak ditemukan");
        }

        // Get user details
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($doctor["user_id"]);
        $doctor["email"] = $user["email"] ?? "";
        $doctor["username"] = $user["username"] ?? "";

        $data["doctor"] = $doctor;
        $data["title"] = "Verifikasi Dokter";

        return view("templates/admin/header", $data) .
            view("admin/verify_doctor", $data) .
            view("templates/admin/footer");
    }

    public function processDoctorVerification()
    {
        $doctorId = $this->request->getPost("doctor_id");
        $status = $this->request->getPost("status");
        $notes = $this->request->getPost("notes");

        if (!in_array($status, ["approved", "rejected"])) {
            return redirect()->back()->with("error", "Status tidak valid");
        }

        $adminModel = new AdminModel();
        $result = $adminModel->verifyDoctor(
            $doctorId,
            $status,
            $notes,
            $this->userId
        );

        if ($result) {
            // Send email notification to the doctor
            $this->sendDoctorVerificationEmail($doctorId, $status, $notes);

            return redirect()
                ->to("admin/pending-doctor-verifications")
                ->with(
                    "success",
                    "Dokter telah " .
                        ($status === "approved" ? "disetujui" : "ditolak")
                );
        } else {
            return redirect()
                ->back()
                ->with("error", "Gagal memverifikasi dokter");
        }
    }

    private function sendDoctorVerificationEmail($doctorId, $status, $notes)
    {
        // Implementation of email sending (as shown in previous response)
        // This is optional, depending on if you have email functionality set up
    }

    public function doctorManagement()
    {
        $dokterModel = new DokterModel();
        $data["doctors"] = $dokterModel->getAllDoctorsWithSpesialisasi();
        $data["title"] = "Manajemen Dokter";

        return view("templates/admin/header", $data) .
            view("admin/doctor_management", $data) .
            view("templates/admin/footer");
    }

    public function patientManagement()
    {
        $patientModel = new PatientModel();
        $data["patients"] = $patientModel->getAllPatients();
        $data["title"] = "Manajemen Pasien";

        return view("templates/admin/header", $data) .
            view("admin/patient_management", $data) .
            view("templates/admin/footer");
    }

    public function appointmentManagement()
    {
        $appointmentModel = new AppointmentModel();
        $data[
            "appointments"
        ] = $appointmentModel->getAllAppointmentsWithDetails();
        $data["title"] = "Manajemen Janji Temu";

        return view("templates/admin/header", $data) .
            view("admin/appointment_management", $data) .
            view("templates/admin/footer");
    }

    public function reports()
    {
        $data["title"] = "Laporan";

        return view("templates/admin/header", $data) .
            view("admin/reports", $data) .
            view("templates/admin/footer");
    }
}
