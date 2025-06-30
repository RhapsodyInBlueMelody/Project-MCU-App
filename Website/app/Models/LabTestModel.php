<?php

namespace App\Models;

use CodeIgniter\Model;

class LabTestModel extends Model
{
    protected $table = "test_lab";
    protected $primaryKey = "id_test_lab";
    protected $allowedFields = [
        "id_janji_temu",
        "tanggal_test",
        "jenis_test",
        "id_petugas_lab",
        "price",
        "hasil_test",
        "status",
        "created_by",
        "created_at",
        "updated_by",
        "updated_at"
    ];
    protected $useTimestamps = false; // You handle timestamps manually (nullable fields)

    // Example: Get all lab tests for an appointment
    public function getTestsByAppointment($id_janji_temu)
    {
        return $this->where('id_janji_temu', $id_janji_temu)->findAll();
    }

    // Example: Get all lab tests assigned to a lab staff
    public function getTestsByPetugas($id_petugas_lab)
    {
        return $this->where('id_petugas_lab', $id_petugas_lab)->findAll();
    }

    // Example: Get all completed lab tests for a patient (via appointment join)
    public function getCompletedTestsByPatient($id_pasien)
    {
        return $this->select('test_lab.*, janji_temu.id_pasien')
            ->join('janji_temu', 'janji_temu.id_janji_temu = test_lab.id_janji_temu')
            ->where('janji_temu.id_pasien', $id_pasien)
            ->where('test_lab.status', 'completed')
            ->findAll();
    }
}
