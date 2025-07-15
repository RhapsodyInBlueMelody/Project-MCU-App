<?php

namespace App\Models;

use CodeIgniter\Model;

class SpesialisasiLabModel extends Model
{
    protected $table = "spesialisasi_lab";
    protected $primaryKey = "id_spesialisasi_lab";
    protected $allowedFields = [
        "nama_spesialisasi",
        "deskripsi",
    ];
    protected $useTimestamps = true;
}
