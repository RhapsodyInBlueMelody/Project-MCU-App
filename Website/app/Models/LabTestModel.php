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
        "id_lab_test_procedure",
        "id_petugas_lab",
        "hasil_test",
        "status",
        "approved_by_dokter",
        "approved_at",
        "created_by",
        "created_at",
        "updated_by",
        "updated_at"
    ];
    protected $useTimestamps = false;

    public function getTestsByAppointment($id_janji_temu)
    {
        return $this->select('test_lab.*, lab_test_procedure.nama_procedure, lab_test_procedure.price')
            ->join('lab_test_procedure', 'lab_test_procedure.id_lab_test_procedure = test_lab.id_lab_test_procedure', 'left')
            ->where('test_lab.id_janji_temu', $id_janji_temu)
            ->findAll();
    }

    public function getTestsByPetugas($id_petugas_lab)
    {
        return $this->where('id_petugas_lab', $id_petugas_lab)->findAll();
    }

    public function getCompletedTestsByPatient($id_pasien)
    {
        return $this->select('test_lab.*, janji_temu.id_pasien')
            ->join('janji_temu', 'janji_temu.id_janji_temu = test_lab.id_janji_temu')
            ->where('janji_temu.id_pasien', $id_pasien)
            ->where('test_lab.status', 'completed')
            ->findAll();
    }

    // Helper: Remove all lab tests for an appointment
    public function deleteByAppointment($id_janji_temu)
    {
        return $this->where('id_janji_temu', $id_janji_temu)->delete();
    }

    // Helper: Replace lab tests for an appointment
    public function replaceAppointmentLabTests($id_janji_temu, array $procedureIds, $userId)
    {
        // Remove previous
        $this->deleteByAppointment($id_janji_temu);
        // Add new
        $now = date("Y-m-d H:i:s");
        foreach ($procedureIds as $id_lab_test_procedure) {
            $this->insert([
                "id_janji_temu"        => $id_janji_temu,
                "tanggal_test"         => $now,
                "id_lab_test_procedure" => $id_lab_test_procedure,
                "status"               => "ordered",
                "created_by"           => $userId,
                "created_at"           => $now,
            ]);
        }
    }

    public function getLabOrdersWithDetails($id_spesialisasi_lab = null)
    {
        $builder = $this->db->table('test_lab');
        $builder->select('
        test_lab.*,
        janji_temu.nama_janji,
        janji_temu.id_pasien,
        janji_temu.id_dokter,
        pasien.nama_pasien,
        dokter.nama_dokter,
        lab_test_procedure.nama_procedure,
        lab_test_procedure.id_spesialisasi_lab
    ');
        $builder->join('janji_temu', 'janji_temu.id_janji_temu = test_lab.id_janji_temu', 'left');
        $builder->join('pasien', 'pasien.id_pasien = janji_temu.id_pasien', 'left');
        $builder->join('dokter', 'dokter.id_dokter = janji_temu.id_dokter', 'left');
        $builder->join('lab_test_procedure', 'lab_test_procedure.id_lab_test_procedure = test_lab.id_lab_test_procedure', 'left');

        if ($id_spesialisasi_lab) {
            $builder->where('lab_test_procedure.id_spesialisasi_lab', $id_spesialisasi_lab);
        }

        return $builder->get()->getResultArray();
    }

    public function countNewOrdersForSpesialisasi($id_spesialisasi_lab)
    {
        $builder = $this->db->table('test_lab');
        $builder->join('lab_test_procedure', 'lab_test_procedure.id_lab_test_procedure = test_lab.id_lab_test_procedure');
        $builder->where('test_lab.status', 'ordered');
        $builder->where('lab_test_procedure.id_spesialisasi_lab', $id_spesialisasi_lab);
        return $builder->countAllResults();
    }

    public function getNewOrdersForSpesialisasi($id_spesialisasi_lab)
    {
        $builder = $this->db->table('test_lab');
        $builder->select('
        test_lab.id_test_lab AS order_number,
        pasien.nama_pasien,
        lab_test_procedure.nama_procedure AS test_name,
        dokter.nama_dokter,
        test_lab.status
    ');
        $builder->join('lab_test_procedure', 'lab_test_procedure.id_lab_test_procedure = test_lab.id_lab_test_procedure');
        $builder->join('janji_temu', 'janji_temu.id_janji_temu = test_lab.id_janji_temu');
        $builder->join('pasien', 'pasien.id_pasien = janji_temu.id_pasien');
        $builder->join('dokter', 'dokter.id_dokter = janji_temu.id_dokter');
        $builder->where('test_lab.status', 'ordered');
        $builder->where('lab_test_procedure.id_spesialisasi_lab', $id_spesialisasi_lab);
        return $builder->get()->getResultArray();
    }

    public function countInProgressForPetugas($id_petugas_lab)
    {
        $builder = $this->db->table('test_lab');
        $builder->where('status', 'in_progress');
        $builder->where('id_petugas_lab', $id_petugas_lab);
        return $builder->countAllResults();
    }

    public function takeOrder($id_test_lab, $petugasLabId)
    {
        $builder = $this->db->table('test_lab');
        $builder->where('id_test_lab', $id_test_lab);
        $builder->where('status', 'ordered');
        $builder->set([
            'id_petugas_lab' => $petugasLabId,
            'status' => 'in_progress'
        ]);
        $builder->update();

        $affectedRows = $this->db->affectedRows();
        log_message('debug', "Update query ran, affected rows: " . $affectedRows);

        // If at least one row was updated, return true (success)
        return $affectedRows > 0;
    }

    public function getInProgressForPetugas($id_petugas_lab)
    {
        $builder = $this->db->table('test_lab');
        $builder->select('
        test_lab.id_test_lab AS order_number,
        pasien.nama_pasien,
        lab_test_procedure.nama_procedure AS test_name,
        test_lab.status
    ');
        $builder->join('lab_test_procedure', 'lab_test_procedure.id_lab_test_procedure = test_lab.id_lab_test_procedure');
        $builder->join('janji_temu', 'janji_temu.id_janji_temu = test_lab.id_janji_temu');
        $builder->join('pasien', 'pasien.id_pasien = janji_temu.id_pasien');
        $builder->where('test_lab.status', 'in_progress');
        $builder->where('test_lab.id_petugas_lab', $id_petugas_lab);
        return $builder->get()->getResultArray();
    }

    public function countDoneTodayForPetugas($id_petugas_lab)
    {
        $builder = $this->db->table('test_lab');
        $builder->where('status', 'completed');
        $builder->where('id_petugas_lab', $id_petugas_lab);
        $builder->where('DATE(tanggal_test)', date('Y-m-d'));
        return $builder->countAllResults();
    }

    /**
     * Add or update lab test requests for this appointment, preserving previous results.
     * - Adds new tests if not already present.
     * - Marks removed tests as 'cancelled' if not yet done.
     */
    public function syncAppointmentLabTests($id_janji_temu, array $procedureIds, $userId)
    {
        $existing = $this->where('id_janji_temu', $id_janji_temu)->findAll();
        $existingMap = [];
        foreach ($existing as $row) {
            $existingMap[$row['id_lab_test_procedure']] = $row;
        }

        // Add new lab tests
        $now = date("Y-m-d H:i:s");
        foreach ($procedureIds as $id_lab_test_procedure) {
            if (!isset($existingMap[$id_lab_test_procedure])) {
                $this->insert([
                    "id_janji_temu"        => $id_janji_temu,
                    "tanggal_test"         => $now,
                    "id_lab_test_procedure" => $id_lab_test_procedure,
                    "status"               => "ordered",
                    "created_by"           => $userId,
                    "created_at"           => $now,
                ]);
            }
            unset($existingMap[$id_lab_test_procedure]);
        }

        // For tests that are no longer requested: mark as cancelled if not yet started
        foreach ($existingMap as $old) {
            if ($old['status'] === "ordered") {
                $this->update($old['id_test_lab'], [
                    "status"     => "cancelled",
                    "updated_by" => $userId,
                    "updated_at" => $now,
                ]);
            }
            // If already in_progress or completed, you may want to keep or just leave as is.
        }
    }
}
