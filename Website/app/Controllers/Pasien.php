<?php

namespace App\Controllers;

use App\Models\AppointmentModel;
use App\Models\PaketModel;
use App\Models\TransaksiModel;
use App\Models\DokterModel;
use CodeIgniter\API\ResponseTrait;



class Pasien extends AuthenticatedController
{
    use ResponseTrait;

    protected $helpers = ['form', 'url'];

    protected $pasienId;
    protected $dokterModel;
    protected $paketModel;
    protected $transaksiModel;
    protected $appointmentModel;
    protected $userId;

    public function __construct()
    {
        parent::__construct("pasien", "auth/pasien/login");

        // Get common data for all methods
        $this->dokterModel = new DokterModel;
        $this->paketModel = new PaketModel;
        $this->appointmentModel = new AppointmentModel;
        $this->transaksiModel = new TransaksiModel;
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

        // Get upcoming appointments for dashboard
        $data["upcoming_appointments"] = !empty($this->pasienId)
            ? $this->appointmentModel->getUpcomingAppointments($this->pasienId, 5) // Limit to 5
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
        // Only keep the necessary fields for doctors
        $doctors = $this->dokterModel->getAllDoctorsWithSpesialisasi();

        // Only keep needed fields for packages
        $packages = $this->paketModel->getAllPackagesWithSpecialization();

        // Add CSRF protection for form
        $data["csrf_token"] = csrf_hash();
        $data["doctors"] = $doctors;
        $data["packages"] = $packages;
        $data["title"] = "Pendaftaran";

        // Add scripts for appointment form
        $data["scripts"] = ["assets/js/appointment.js"];

        return view("templates/pasien/header", $data) .
            view("pasien/pendaftaran_janji_temu", $data) .
            view("templates/pasien/footer");
    }

    public function saveAppointment()
    {

        // Get pasien ID from session
        $pasienId = session()->get('id_pasien');
        $userId = session()->get('user_id');

        // Define the rules array
        $rules = [
            "nama_janji"        => "required|min_length[3]|max_length[255]|string",
            "tanggal_janji"     => "required|valid_date",
            "waktu_janji"       => "required|regex_match[/(0[89]|1[0-7]):[0-5][0-9]/]",
            "paket_terpilih"    => "required|numeric|is_natural_no_zero",
            "id_dokter"         => "required|min_length[1]|max_length[30]|alpha_numeric_punct",
        ];

        // Define the custom messages array
        $messages = [
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
        ];

        // Pass the rules and messages directly to the controller's validate() method
        if (!$this->validate($rules, $messages)) {
            // $this->validator automatically holds the errors after a failed validation
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $idJanjiTemu = $this->appointmentModel->generateAppointmentId();

        if (!$idJanjiTemu) {
            return redirect()->back()->withInput()->with('error', 'Janji Tidak Dapat Dibuat');
        }


        $data = [
            'id_janji_temu' => $idJanjiTemu,
            'nama_janji'    => esc($this->request->getPost('nama_janji')),
            'id_pasien'     => session('id_pasien'), // Must exist!
            'tanggal_janji' => $this->request->getPost('tanggal_janji'), // YYYY-MM-DD
            'waktu_janji'   => $this->request->getPost('waktu_janji') . ':00', // HH:MM:SS
            'id_dokter'     => $this->request->getPost('id_dokter'),
            'id_paket'      => (int)$this->request->getPost('paket_terpilih'), // Force INT
            'status'        => 'pending'
        ];

        try {
            $this->appointmentModel->createAppointment($data);
        } catch (\Exception $e) {
            // Make sure your catch block here displays the actual database error for debugging!
            $dbError = $this->appointmentModel->db->error();
            $errorMessage = 'Gagal menyimpan: ' . $e->getMessage();
            if ($dbError['code'] !== 0) {
                $errorMessage .= ' (DB Error ' . $dbError['code'] . ': ' . $dbError['message'] . ')';
            }
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }


        $id_paket = $this->request->getPost('paket_terpilih');
        $paket = $this->paketModel->find($id_paket);

        $transaksiData = [
            'id_janji_temu'     => $idJanjiTemu,
            'id_pasien'         => $pasienId,
            'id_paket'          => $id_paket,
            'tanggal_transaksi' => date('Y-m-d H:i:s'),
            'total_harga'       => $paket ? $paket['harga'] : 0,
            'status_pembayaran' => 'pending',
            'created_by'        => $userId,
            'created_at'        => date('Y-m-d H:i:s')
        ];
        $this->transaksiModel->insert($transaksiData);
        $id_transaksi = $this->transaksiModel->getInsertID();

        $this->logActivity(
            "create_appointment",
            "Created new appointment with ID: $idJanjiTemu and transaction ID: $id_transaksi"
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

        // Get all appointments for this pasien
        $appointments = $this->appointmentModel->getPasienAppointments($pasienId);

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

        $appointment = $this->appointmentModel->find($id);

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
            $this->appointmentModel->update($id, $data);

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
        $pasienId = $this->pasienId ?? 1;

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

    public function diagnosisPrint($diagnosisId = null)
    {
        if ($diagnosisId === null) {
            return redirect()->back()->with("error", "Diagnosis ID diperlukan");
        }

        $diagnosisModel = new \App\Models\DiagnosisModel();
        $diagnosis = $diagnosisModel->getDiagnosisDetails($diagnosisId);

        if (!$diagnosis) {
            return redirect()->back()->with("error", "Diagnosis tidak ditemukan");
        }

        // Render the PDF using a view
        $html = view('document/diagnosis_pdf', ['diagnosis' => $diagnosis]);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Output PDF to browser (inline)
        $dompdf->stream('document/diagnosis.pdf', ['Attachment' => false]);
        exit; // Ensure no extra output
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
