<?php

namespace App\Controllers;

use App\Models\AppointmentModel;
use App\Models\pasienProfileModel;
use App\Models\PaketModel;
use App\Models\DokterModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;



class Pasien extends AuthenticatedController
{
    use ResponseTrait;

    protected $pasienId;
    protected $userId;

    public function __construct()
    {
        parent::__construct("pasien", "auth/pasien/login");

        // Get common data for all methods
        $this->pasienId = session()->get("id_pasien") ?? null;
        $this->userId = session()->get("user_id") ?? null;

        // Verify user is authenticated - this is for additional security
        if (!$this->pasienId && ENVIRONMENT === "production") {
            redirect()
                ->to("auth/pasien/login")
                ->with("error", "You must be logged in to access this area.")
                ->send();
            exit();
        }
    }

    public function dashboard()
    {
        $appointmentModel = new AppointmentModel();

        // Get upcoming appointments for dashboard
        $data["upcoming_appointments"] = !empty($this->pasienId)
            ? $appointmentModel->getUpcomingAppointments($this->pasienId, 5) // Limit to 5
            : [];

        $data["title"] = "pasien Dashboard";
        $data["username"] = session()->get("username") ?? "Guest";

        // Add script for dashboard lazy loading
        $data["scripts"] = [];

        return view("templates/pasien/header", $data) .
            view("pasien/dashboard", $data) .
            view("templates/pasien/footer");
    }

    public function beranda()
    {
        // Redirect to dashboard
        return redirect()->to("/pasien/dashboard");
    }

    public function appointment()
    {
        $dokterModel = new DokterModel();
        $paketModel = new PaketModel();
    
        // Only keep the necessary fields for doctors
        $doctors = array_map(function ($d) {
            return [
                'id_dokter' => $d['id_dokter'],
                'nama_dokter' => $d['nama_dokter'],
                'id_spesialisasi' => $d['id_spesialisasi'],
                'nama_spesialisasi' => $d['nama_spesialisasi'],
            ];
        }, $dokterModel->getAllDoctorsWithSpesialisasi());
    
        // Only keep needed fields for packages
        $packages = array_map(function ($p) {
            return [
                'id_paket' => $p['id_paket'],
                'nama_paket' => $p['nama_paket'],
                'deskripsi' => $p['deskripsi'],
                'harga' => $p['harga'],
                'id_spesialisasi' => $p['id_spesialisasi'],
                'keahlian_dibutuhkan_text' => $p['keahlian_dibutuhkan_text']
            ];
        }, $paketModel->getAllPackagesWithSpecialization());
    
        // Add CSRF protection for form
        $data["csrf_token"] = csrf_hash();
        $data["doctors"] = $doctors;
        $data["packages"] = $packages;
        $data["title"] = "Pendaftaran";
    
        // Add scripts for appointment form
        $data["scripts"] = ["assets/js/appointment.js"];
    
        return view("templates/pasien/header", $data) .
            view("pasien/pendaftaran", $data) .
            view("templates/pasien/footer");
    }

    public function saveAppointment()
    {
        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules(
            [
                "nama_janji" => "required|min_length[3]|max_length[255]|string",
                "tanggal_janji" => "required|valid_date",
                "waktu_janji" =>
                    "required|regex_match[/(0[89]|1[0-7]):[0-5][0-9]/]",
                "paket_terpilih" => "required|numeric|is_natural_no_zero",
                "id_dokter" => "required|numeric|is_natural_no_zero",
            ],
            [
                // Custom error messages
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
                    "regex_match" =>
                        "Waktu janji harus antara jam 08:00 - 17:00.",
                ],
                "paket_terpilih" => [
                    "required" => "Paket harus dipilih.",
                    "numeric" => "Paket tidak valid.",
                    "is_natural_no_zero" => "Paket tidak valid.",
                ],
                "id_dokter" => [
                    "required" => "Dokter harus dipilih.",
                    "numeric" => "Dokter tidak valid.",
                    "is_natural_no_zero" => "Dokter tidak valid.",
                ],
            ]
        );

        // If validation fails, return to the form with errors
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()
                ->back()
                ->with("error", implode("<br>", $validation->getErrors()))
                ->withInput();
        }

        // Create the appointment model instance
        $appointmentModel = new AppointmentModel();

        // Get pasien ID from session or use default for testing
        $pasienId = $this->patientId ?? 1;
        $userId = $this->userId ?? $pasienId;

        // Sanitize inputs
        $namaJanji = htmlspecialchars(
            $this->request->getPost("nama_janji"),
            ENT_QUOTES,
            "UTF-8"
        );
        $tanggalJanji = $this->request->getPost("tanggal_janji");
        $waktuJanji = $this->request->getPost("waktu_janji");
        $dokterId = (int) $this->request->getPost("id_dokter");
        $paketId = (int) $this->request->getPost("paket_terpilih");

        // Prepare data for saving
        $data = [
            "NAMA_JANJI" => $namaJanji,
            "ID_PASIEN" => $pasienId,
            "TANGGAL_JANJI" => $tanggalJanji,
            "WAKTU_JANJI" => $waktuJanji,
            "ID_DOKTER" => $dokterId,
            "ID_PAKET" => $paketId,
            "STATUS" => "pending", // Default status
            "created_by" => $userId,
            "updated_by" => $userId,
        ];

        // Validate doctor availability
        $isAvailable = $appointmentModel->isDoctorAvailable(
            $dokterId,
            $tanggalJanji,
            $waktuJanji
        );
        if (!$isAvailable) {
            return redirect()
                ->back()
                ->with(
                    "error",
                    "Dokter tidak tersedia pada waktu tersebut. Silakan pilih waktu lain."
                )
                ->withInput();
        }

        // Save the appointment with transaction for data integrity
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $appointmentModel->insert($data);
            $appointmentId = $appointmentModel->getInsertID();

            // Log the activity for audit trail
            $this->logActivity(
                "create_appointment",
                "Created new appointment with ID: " . $appointmentId
            );

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception("Database transaction failed.");
            }

            return redirect()
                ->to("/pasien/jadwal-pemeriksaan")
                ->with(
                    "success",
                    "Janji temu berhasil dibuat. Silakan tunggu konfirmasi dari dokter."
                );
        } catch (\Exception $e) {
            $db->transRollback();

            log_message(
                "error",
                "Error creating appointment: " . $e->getMessage()
            );
            return redirect()
                ->back()
                ->with(
                    "error",
                    "Gagal membuat janji: " .
                        (ENVIRONMENT === "production"
                            ? "Terjadi kesalahan sistem."
                            : $e->getMessage())
                )
                ->withInput();
        }
    }

    public function jadwalPemeriksaan()
    {
        // Get the pasien ID from session
        $pasienId = $this->patientId ?? 1; // Default to 1 for testing

        // Create model instance
        $appointmentModel = new AppointmentModel();

        // Get all appointments for this pasien
        $appointments = $appointmentModel->getPasienAppointments($pasienId);

        // Group appointments by status for easier filtering
        $data["upcoming"] = array_filter($appointments, function ($appt) {
            return $appt["STATUS"] === "pending" ||
                $appt["STATUS"] === "confirmed";
        });

        $data["past"] = array_filter($appointments, function ($appt) {
            return $appt["STATUS"] === "completed" ||
                $appt["STATUS"] === "cancelled";
        });

        $data["title"] = "Jadwal Pemeriksaan";

        // Add scripts for appointment schedule
        $data["scripts"] = ["assets/js/appointment-schedule.js"];

        return view("templates/pasien/header", $data) .
            view("pasien/jadwal_pemeriksaan", $data) .
            view("templates/pasien/footer");
    }

    public function cancelAppointment($id = null)
    {
        // Check if ID is provided
        if ($id === null) {
            return redirect()
                ->to("/pasien/jadwal-pemeriksaan")
                ->with("error", "ID janji temu diperlukan");
        }

        $appointmentModel = new AppointmentModel();
        $appointment = $appointmentModel->find($id);

        if (!$appointment) {
            return redirect()
                ->to("/pasien/jadwal-pemeriksaan")
                ->with("error", "Janji temu tidak ditemukan");
        }

        // Verify that this appointment belongs to the current pasien
        $pasienId = $this->patientId ?? 1;
        if ($appointment["ID_PASIEN"] != $pasienId) {
            return redirect()
                ->to("/pasien/jadwal-pemeriksaan")
                ->with(
                    "error",
                    "Anda tidak memiliki izin untuk membatalkan janji temu ini"
                );
        }

        // Only allow cancelling pending appointments
        if ($appointment["STATUS"] !== "pending") {
            return redirect()
                ->to("/pasien/jadwal-pemeriksaan")
                ->with(
                    "error",
                    "Hanya janji temu dengan status menunggu yang dapat dibatalkan"
                );
        }

        // Update the appointment status
        $data = [
            "STATUS" => "cancelled",
            "updated_by" => $this->userId ?? $pasienId,
        ];

        try {
            $appointmentModel->update($id, $data);

            // Log the activity
            $this->logActivity(
                "cancel_appointment",
                "Cancelled appointment with ID: " . $id
            );

            return redirect()
                ->to("/pasien/jadwal-pemeriksaan")
                ->with("success", "Janji temu berhasil dibatalkan");
        } catch (\Exception $e) {
            log_message(
                "error",
                "Error cancelling appointment: " . $e->getMessage()
            );
            return redirect()
                ->to("/pasien/jadwal-pemeriksaan")
                ->with(
                    "error",
                    "Gagal membatalkan janji temu: " .
                        (ENVIRONMENT === "production"
                            ? "Terjadi kesalahan sistem."
                            : $e->getMessage())
                );
        }
    }
    public function riwayatPemeriksaan($id = null)
    {
        if ($id === null) {
            return redirect()
                ->to("/pasien/jadwal-pemeriksaan")
                ->with("error", "Appointment ID is required");
        }

        $appointmentModel = new AppointmentModel();
        $data["appointment"] = $appointmentModel->getAppointmentDetails($id);

        if (empty($data["appointment"])) {
            return redirect()
                ->to("/pasien/jadwal-pemeriksaan")
                ->with("error", "Appointment not found");
        }

        // Check if this appointment belongs to the logged-in pasien
        $pasienId = $this->patientId ?? 1; // Default to 1 for testing
        if ($data["appointment"]["ID_PASIEN"] != $pasienId) {
            return redirect()
                ->to("/pasien/jadwal-pemeriksaan")
                ->with(
                    "error",
                    "You are not authorized to view this appointment"
                );
        }

        $data["title"] = "Detail Janji Temu";

        return view("templates/pasien/header", $data) .
            view("pasien/riwayat_pemeriksaan", $data) .
            view("templates/pasien/footer");
    }

    // API endpoint for lazy loading health stats
    public function getHealthStats()
    {
        // Verify this is an AJAX request for security
        if (!$this->request->isAJAX()) {
            return $this->failUnauthorized("Direct access not allowed");
        }

        // Get pasien ID
        $pasienId = $this->patientId ?? 1;

        // Get appointment model
        $appointmentModel = new AppointmentModel();

        // Get stats data
        $stats = [
            "lastVisit" => $appointmentModel->getLastCompletedAppointmentDate(
                $pasienId
            ),
            "totalCheckups" => $appointmentModel->getTotalCompletedAppointments(
                $pasienId
            ),
            "healthStatus" => "Baik", // This would come from your health records
        ];

        return $this->respond([
            "success" => true,
            "lastVisit" => $stats["lastVisit"]
                ? date("d M Y", strtotime($stats["lastVisit"]))
                : "Belum ada",
            "totalCheckups" => $stats["totalCheckups"],
            "healthStatus" => $stats["healthStatus"],
        ]);
    }

    // Helper method to log user activities for audit trail
    protected function logActivity(
        string $action,
        string $description,
        array $additionalData = []
    ) {
        $activityLogModel = model("ActivityLogModel");
        $activityLogModel->logActivity(
            $this->userId ?? 0,
            $action,
            $description,
            $this->request->getIPAddress(),
            $additionalData
        );
    }
}
