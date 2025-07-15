<?php

namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\DokterModel;
use App\Models\PasienModel;
use App\Models\UserModel;
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
            $this->adminId = $adminData ? $adminData["id_admin"] : null;
        }
    }

    public function dashboard()
    {
        $adminModel = new AdminModel();
        $dokterModel = new DokterModel();
        $patientModel = new PasienModel();
        $appointmentModel = new AppointmentModel();

        // Get counts for dashboard widgets
        $data["total_doctors"] = $dokterModel->countAll();
        $data["pending_verifications"] = $dokterModel
            ->where("verification_status", "pending")
            ->countAllResults();
        $data["total_patients"] = $patientModel->countAll();
        $data["total_appointments"] = $appointmentModel->countAll();

        // Recent appointments
        $data["recent_appointments"] = $appointmentModel->getRecentAppointments(5);

        $data["title"] = "Dashboard Admin";
        return view("templates/admin/header", $data) .
            view("admin/dashboard", $data) .
            view("templates/admin/footer");
    }

    public function pendingDokterVerifications()
    {
        $adminModel = new AdminModel();
        $data["pending_verifications"] = $adminModel->getPendingDoctorVerifications();
        $data["title"] = "Verifikasi Dokter";

        return view("templates/admin/header", $data) .
            view("admin/pending_doctor_verifications", $data) .
            view("templates/admin/footer");
    }

    public function verifyDokter($doctorId = null)
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
        $patientModel = new PasienModel(); // Fixed: was PatientModel
        $data["patients"] = $patientModel->getAllPatients();
        $data["title"] = "Manajemen Pasien";

        return view("templates/admin/header", $data) .
            view("admin/patient_management", $data) .
            view("templates/admin/footer");
    }

    public function appointmentManagement()
    {
        $appointmentModel = new AppointmentModel();

        // Get appointments with pagination
        $data["appointments"] = $appointmentModel->getAppointmentWithDetailsPaginated(10);
        $data["pager"] = $appointmentModel->pager;

        // Get appointment statistics
        $stats = $appointmentModel->getAppointmentStats();
        $data["total"] = $stats['total'];
        $data["pending"] = $stats['pending'];
        $data["confirmed"] = $stats['confirmed'];
        $data["completed"] = $stats['completed'];
        $data["cancelled"] = $stats['cancelled'];

        $data["title"] = "Manajemen Janji Temu";

        return view("templates/admin/header", $data) .
            view("admin/appointment_management", $data) .
            view("templates/admin/footer");
    }

    public function updateAppointmentStatus($appointmentId)
    {
        $appointmentModel = new AppointmentModel();

        $status = $this->request->getPost('status');
        $reason = $this->request->getPost('reason');

        $updateData = [
            'status' => $status,
            'updated_by' => $this->userId,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Add rejection reason if cancelled
        if ($status === 'cancelled' && !empty($reason)) {
            $updateData['rejection_reason'] = $reason;
        }

        $result = $appointmentModel->update($appointmentId, $updateData);

        if ($result) {
            return redirect()->to('admin/appointment-management')
                ->with('success', 'Status janji temu berhasil diperbarui');
        } else {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui status janji temu');
        }
    }

    public function viewAppointment($appointmentId)
    {
        $appointmentModel = new AppointmentModel();

        // Get appointment details
        $appointment = $appointmentModel->getAppointmentDetailsById($appointmentId);

        if (!$appointment) {
            return redirect()->to('admin/appointment-management')
                ->with('error', 'Janji temu tidak ditemukan');
        }

        $data["appointment"] = $appointment;
        $data["title"] = "Detail Janji Temu";

        return view("templates/admin/header", $data) .
            view("admin/appointment_detail", $data) .
            view("templates/admin/footer");
    }

    public function reports()
    {
        $appointmentModel = new AppointmentModel();
        $dokterModel = new DokterModel();
        $patientModel = new PasienModel();

        // Get report data
        $data["appointment_stats"] = $appointmentModel->getAppointmentStats();
        $data["monthly_appointments"] = $appointmentModel->getMonthlyAppointmentStats();
        $data["doctor_stats"] = $dokterModel->getDoctorStats();
        $data["patient_stats"] = $patientModel->getPatientStats();

        $data["title"] = "Laporan";

        return view("templates/admin/header", $data) .
            view("admin/reports", $data) .
            view("templates/admin/footer");
    }

    public function processDokterVerification()
    {
        $doctorId = $this->request->getPost("doctor_id");
        $status = $this->request->getPost("status");
        $notes = $this->request->getPost("notes");

        if (!in_array($status, ["approved", "rejected"])) {
            return redirect()->back()->with("error", "Status tidak valid");
        }

        $dokterModel = new DokterModel(); // Initialize DokterModel
        $data = [
            'verification_status' => $status,
            'updated_by' => $this->userId,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Set is_verified based on status
        if ($status === 'approved') {
            $data['is_verified'] = 1;
        } else {
            $data['is_verified'] = 0;
        }

        // You might want to store notes somewhere if it's not already handled in AdminModel's verifyDoctor
        // If AdminModel::verifyDoctor already updates these fields, you can stick to that.
        // For this example, I'm assuming you'll directly update DokterModel here.
        // If AdminModel::verifyDoctor also handles other logic (like logging), keep it.

        $result = $dokterModel->update($doctorId, $data); // Update DokterModel directly

        if ($result) {
            // Send email notification to the doctor
            $this->sendDoctorVerificationEmail($doctorId, $status, $notes);

            return redirect()
                ->to("admin/pending-dokter-verifications")
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

        // Example (assuming you have a UserModel and Email library configured)
        $dokterModel = new DokterModel();
        $userModel = new UserModel();

        $doctor = $dokterModel->find($doctorId);
        if ($doctor) {
            $user = $userModel->find($doctor['user_id']);
            if ($user && isset($user['email'])) {
                $email = \Config\Services::email();
                $email->setTo($user['email']);
                $email->setFrom('no-reply@yourdomain.com', 'Your App Name');
                $subject = ($status === 'approved') ? 'Verifikasi Akun Dokter Anda Disetujui' : 'Verifikasi Akun Dokter Anda Ditolak';
                $message = "Halo " . $doctor['nama_dokter'] . ",<br><br>";
                $message .= "Status verifikasi akun dokter Anda telah <strong>" . ($status === 'approved' ? 'disetujui' : 'ditolak') . "</strong>.<br>";
                if ($notes) {
                    $message .= "Catatan Admin: " . $notes . "<br>";
                }
                $message .= "<br>Terima kasih.";

                $email->setSubject($subject);
                $email->setMessage($message);
                $email->send();
            }
        }
    }
}
