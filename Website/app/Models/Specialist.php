<?php namespace App\Models;

use CodeIgniter\Model;

class SpesialisasiModel extends Model
{
    protected $table = "spesialisasi";
    protected $primaryKey = "id_spesialisasi";
    protected $allowedFields = [
        "nama_spesialisasi",
        "created_by",
        "created_at",
        "updated_by",
        "updated_at",
    ];
    protected $useTimestamps = true;
}
