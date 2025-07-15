<?php

namespace App\Models;

use CodeIgniter\Model;

class LabTestProcedure extends Model
{
    protected $table = "lab_test_procedure";
    protected $primaryKey = "id_lab_test_procedure";
    protected $allowedFields = [
        "nama_procedure",
        "harga",
        "deskripsi",
        "active",
        "created_by",
        "updated_by",
    ];
    protected $useTimestamps = true;
    protected $createdField = "created_at";
    protected $updatedField = "updated_at";

    public function getActiveProcedures()
    {
        return $this->where('active', 1)->findAll();
    }
}
