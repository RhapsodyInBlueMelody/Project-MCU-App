<?php

namespace App\Models;

use CodeIgniter\Model;

class DokterJadwalModel extends Model
{
    protected $table = "dokter_jadwal";
    protected $primaryKey = "id_jadwal";
    protected $allowedFields = [
        "id_dokter",
        "hari",
        "jam_mulai",
        "jam_selesai",
        "lokasi",
    ];
    protected $useTimestamps = true;
    protected $createdField = "created_at";
    protected $updatedField = "updated_at";

    public function addSchedulesForDoctor($doctorId, $days, $jamMulai, $jamSelesai, $lokasi)
    {
        if (!$doctorId || !$days || !$jamMulai || !$jamSelesai || !$lokasi) {
            return false;
        }
        foreach ($days as $day) {
            $this->insert([
                'id_dokter'   => $doctorId,
                'hari'        => $day,
                'jam_mulai'   => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'lokasi'      => $lokasi
            ]);
        }
        return true;
    }

    public function deleteScheduleById($id_jadwal, $id_dokter)
    {
        // Extra: only delete if this schedule belongs to the logged-in doctor
        return $this->where('id_jadwal', $id_jadwal)
            ->where('id_dokter', $id_dokter)
            ->delete();
    }
}
