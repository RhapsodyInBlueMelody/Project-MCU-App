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
        $appointmentModel = new \App\Models\AppointmentModel();
        $transaksiModel = new \App\Models\TransaksiModel();
    
        // Get pasien ID from session
        $pasienId = session()->get('id_pasien');
        $userId = session()->get('user_id');
    
        // Step 1: Create the appointment
        $result = $appointmentModel->createAppointment(
            ['id_pasien' => $pasienId, 'user_id' => $userId],
            $this->request
        );
    
        if (!$result['success']) {
            $errorMsg = is_array($result['errors'])
                ? implode("<br>", $result['errors'])
                : $result['errors'];
            return redirect()
                ->back()
                ->with("error", $errorMsg)
                ->withInput();
        }
    
        $id_janji_temu = $result['id_janji_temu'];
    
        $id_paket = $this->request->getPost('paket_terpilih');
        $packageModel = new \App\Models\PaketModel();
        $paket = $packageModel->find($id_paket);
    
        $transaksiData = [
            'id_janji_temu'     => $id_janji_temu,
            'id_pasien'         => $pasienId,
            'id_paket'          => $id_paket,
            'tanggal_transaksi' => date('Y-m-d H:i:s'),
            'total_harga'       => $paket ? $paket['harga'] : 0,
            'status_pembayaran' => 'belum lunas',
            'created_by'        => $userId,
            'created_at'        => date('Y-m-d H:i:s')
        ];
        $transaksiModel->insert($transaksiData);
        $id_transaksi = $transaksiModel->getInsertID();
    
        $this->logActivity(
            "create_appointment",
            "Created new appointment with ID: $id_janji_temu and transaction ID: $id_transaksi"
        );
    
        return redirect()
            ->to("/pasien/jadwal-pemeriksaan")
            ->with(
                "success",
                "Janji temu berhasil dibuat. Silakan lanjutkan ke pembayaran."
            );
    }

    public function jadwalPemeriksaan()
    {
        // Get the pasien ID from session
        $pasienId = session()->get('id_pasien');

        // Create model instance
        $appointmentModel = new AppointmentModel();

        // Get all appointments for this pasien
        $appointments = $appointmentModel->getPasienAppointments($pasienId);

        // Group appointments by status for easier filtering
        $data["upcoming"] = array_filter($appointments, function ($appt) {
            return $appt["status"] === "pending" ||
                $appt["status"] === "confirmed";
        });

        $data["past"] = array_filter($appointments, function ($appt) {
            return $appt["status"] === "completed" ||
                $appt["status"] === "cancelled";
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
        $pasienId = session()->get('id_pasien');
        if ($appointment["id_pasien"] != $pasienId) {
            return redirect()
                ->to("/pasien/jadwal-pemeriksaan")
                ->with(
                    "error",
                    "Anda tidak memiliki izin untuk membatalkan janji temu ini"
                );
        }

        // Only allow cancelling pending appointments
        if ($appointment["status"] !== "pending") {
            return redirect()
                ->to("/pasien/jadwal-pemeriksaan")
                ->with(
                    "error",
                    "Hanya janji temu dengan status menunggu yang dapat dibatalkan"
                );
        }

        // Update the appointment status
        $data = [
            "status" => "cancelled",
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
        $pasienId = session()->get('id_pasien');
        if ($data["appointment"]["id_pasien"] != $pasienId) {
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
