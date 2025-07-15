<?php

namespace App\Controllers;

use App\Models\PetugasLabModel;
use App\Models\SpesialisasiLabModel;
use App\Models\LabTestModel;

class LaboratoriumController extends AuthenticatedController
{
    protected $petugasLabId;
    protected $userId;

    public function __construct()
    {
        parent::__construct("petugas_lab", "auth/dokter/login");

        $this->userId = session()->get("user_id") ?? null;
        $petugasLabModel = new PetugasLabModel();
        if ($this->userId) {
            $petugasLabData = $petugasLabModel->where("user_id", $this->userId)->first();
            $this->petugasLabId = $petugasLabData ? $petugasLabData["id_petugas_lab"] : null;
        }
    }

    public function dashboard()
    {
        $petugasLabId = $this->petugasLabId;

        // Get the specialization code for this petugas
        $petugasLabModel = new \App\Models\PetugasLabModel();
        $petugas = $petugasLabModel->where('id_petugas_lab', $petugasLabId)->first();
        $id_spesialisasi_lab = $petugas['id_spesialisasi_lab'] ?? null;

        $orderModel = new \App\Models\LabTestModel();

        $newOrders = $orderModel->getNewOrdersForSpesialisasi($id_spesialisasi_lab);
        $inProgress = $orderModel->getInProgressForPetugas($petugasLabId);

        $summary = [
            'new_orders'   => count($newOrders),
            'in_progress'  => count($inProgress),
            'done_today'   => $orderModel->countDoneTodayForPetugas($petugasLabId),
        ];

        return view("templates/petugasLab/header")
            . view("petugasLab/dashboard", [
                'petugasProfile' => $petugas,
                'summary' => $summary,
                'newOrders' => $newOrders,
                'inProgress' => $inProgress,
            ])
            . view("templates/petugasLab/footer");
    }

    public function orders()
    {
        $petugasLabId = $this->petugasLabId;
        $petugasLabModel = new PetugasLabModel();
        $labTestModel = new LabTestModel();

        // Find Petugas Lab specialization
        $spesialisasiLab = null;
        $petugasLabData = $petugasLabModel->find($petugasLabId);
        if ($petugasLabData) {
            $spesialisasiLab = $petugasLabData["id_spesialisasi_lab"];
        }

        // Fetch all orders for this specialization, sorted by status and date
        $orders = $labTestModel->getLabOrdersWithDetails($spesialisasiLab);

        return view('templates/petugasLab/header')
            . view('petugasLab/orders', [
                'orders' => $orders,
                'petugasLabId' => $petugasLabId,
            ])
            . view('templates/petugasLab/footer');
    }

    public function orderWork($id_test_lab)
    {
        $labTestModel = new \App\Models\LabTestModel();
        $order = $labTestModel->find($id_test_lab);
        $petugasLabId = $this->petugasLabId; // from session/auth

        // Optional: check permission
        if (!$order || $order['id_petugas_lab'] != $petugasLabId) {
            return redirect()->back()->with('error', 'Akses tidak diizinkan.');
        }

        return view('templates/petugasLab/header')
            . view('petugasLab/order_work', ['order' => $order])
            . view('templates/petugasLab/footer');
    }

    public function downloadReport($id_test_lab)
    {
        $labTestModel = new \App\Models\LabTestModel();
        $order = $labTestModel->find($id_test_lab);

        if (!$order) {
            return redirect()->back()->with('error', 'Order tidak ditemukan.');
        }

        // Get appointment to check doctor & patient
        $appointmentModel = new \App\Models\AppointmentModel();
        $appointment = $appointmentModel->find($order['id_janji_temu']);

        // Get user roles/IDs from session (adjust as needed)
        $petugasLabId = $this->petugasLabId ?? null;
        $doctorId = $this->doctorId ?? session('doctor_id') ?? null;
        $userId = session('user_id');
        $isAdmin = session('is_admin') ?? false;
        $patientId = session('patient_id') ?? null;

        $allowed = false;

        // Petugas lab assigned to this order
        if ($order['id_petugas_lab'] && $petugasLabId && $order['id_petugas_lab'] == $petugasLabId) $allowed = true;
        // The doctor for the appointment
        if ($appointment && $doctorId && $appointment['id_dokter'] == $doctorId) $allowed = true;
        // The patient (optional)
        if ($appointment && $patientId && $appointment['id_pasien'] == $patientId) $allowed = true;
        // Admin
        if ($isAdmin) $allowed = true;

        if (!$allowed) {
            return redirect()->back()->with('error', 'Akses tidak diizinkan.');
        }

        $filepath = WRITEPATH . 'uploads/reports/' . $order['hasil_test'];

        if (!is_file($filepath)) {
            throw new \Exception('File tidak ditemukan.');
        }
        return $this->response->download($filepath, null);
    }

    public function submitWork($id_test_lab)
    {
        $labTestModel = new \App\Models\LabTestModel();
        $order = $labTestModel->find($id_test_lab);
        $diagnosisModel = new \App\Models\DiagnosisModel();
        $petugasLabModel = new \App\Models\PetugasLabModel();
        $petugasLabId = $this->petugasLabId; // from session/auth
        $petugasLab = $petugasLabModel->where('id_petugas_lab', $petugasLabId)->first();

        // Check permission
        if (!$order || $order['id_petugas_lab'] != $petugasLabId) {
            return redirect()->back()->with('error', 'Akses tidak diizinkan.');
        }

        $file = $this->request->getFile('hasil_file');
        $notes = $this->request->getPost('notes');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $filename = $file->getRandomName();
            // Make sure this directory is not web-accessible directly!
            $file->move(WRITEPATH . 'uploads/reports', $filename);

            // Save the file path (relative or absolute, your choice) into hasil_test
            $labTestModel->update($id_test_lab, [
                'hasil_test' => $filename,
                'notes' => $notes,
                'status' => 'completed',
            ]);

            return redirect()->to(site_url('petugas_lab/orders'))->with('success', 'Hasil laporan berhasil diupload.');
        }

        return redirect()->back()->with('error', 'Upload gagal.');
    }

    public function profile()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/login');
        }

        $petugasLabModel = new PetugasLabModel();
        $spesialisasiModel = new SpesialisasiLabModel();

        // Get profile
        $profile = $petugasLabModel->where('user_id', $userId)->first();

        if (!$profile) {
            // Handle if not found
            return redirect()->back()->with('error', 'Profil tidak ditemukan.');
        }

        // Get spesialisasi name
        $spesialisasi = $spesialisasiModel->find($profile['id_spesialisasi_lab']);
        $profile['spesialisasi_lab'] = $spesialisasi ? $spesialisasi['nama_spesialisasi'] : '-';

        return view('templates/petugasLab/header')
            . view('petugasLab/profile', [
                'profile' => $profile,
            ])
            . view('templates/petugasLab/footer');
    }

    public function updateProfile()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/login');
        }

        $petugasLabModel = new PetugasLabModel();

        $data = [
            'nama_petugas_lab'    => $this->request->getPost('nama'),
            'jenis_kelamin'       => $this->request->getPost('jenis_kelamin'),
            'tanggal_lahir'       => $this->request->getPost('tanggal_lahir'),
            'telepon_petugas_lab' => $this->request->getPost('telepon'),
            'alamat_petugas_lab'  => $this->request->getPost('alamat'),
            'no_lisensi'          => $this->request->getPost('nip'),
            // Add other fields as needed, but not spesialisasi!
        ];

        // Validate and update
        $petugas = $petugasLabModel->where('user_id', $userId)->first();
        if (!$petugas) {
            return redirect()->back()->with('error', 'Profil tidak ditemukan.');
        }

        $petugasLabModel->update($petugas['id_petugas_lab'], $data);

        return redirect()->to('/petugas_lab/profile')->with('success', 'Profil berhasil diperbarui.');
    }

    // OPTIONAL: Change Password
    public function changePassword()
    {
        return view('templates/petugasLab/header')
            . view('petugasLab/change_password')
            . view('templates/petugasLab/footer');
    }

    public function updatePassword()
    {
        $userId = session()->get('user_id');
        $newPassword = $this->request->getPost('new_password');
        $confirm = $this->request->getPost('confirm_password');

        if (!$newPassword || $newPassword !== $confirm) {
            return redirect()->back()->with('error', 'Konfirmasi password tidak cocok.');
        }

        // Update password in users table (not in petugas_lab!)
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);
        if (!$user) {
            return redirect()->back()->with('error', 'Akun user tidak ditemukan.');
        }
        $userModel->update($userId, ['password' => password_hash($newPassword, PASSWORD_DEFAULT)]);

        return redirect()->to('/petugas_lab/profile')->with('success', 'Password berhasil diubah.');
    }


    public function takeOrder($id_test_lab)
    {
        $petugasLabId = session()->get('id_petugas_lab');
        $labTestModel = new \App\Models\LabTestModel();

        $success = $labTestModel->takeOrder($id_test_lab, $petugasLabId);

        if ($success) {
            return redirect()->back()->with('success', 'Order berhasil diambil.');
        } else {
            return redirect()->back()->with('error', 'Order sudah diambil atau tidak valid.');
        }
    }
}
