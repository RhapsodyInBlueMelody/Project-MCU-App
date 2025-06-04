<?php namespace App\Models;

use CodeIgniter\Model;

class PaketModel extends Model
{
    protected $table = "paket";
    protected $primaryKey = "id_paket";
    protected $allowedFields = [
        "nama_paket",
        "deskripsi",
        "harga",
        "id_spesialisasi",
        "created_by",
        "updated_by",
    ];
    protected $useTimestamps = true;
    protected $createdField = "created_at";
    protected $updatedField = "updated_at";

    public function getAllPackagesWithSpecialization()
    {
        $result = $this->db
            ->table("paket")
            ->select(
                "paket.*, spesialisasi.nama_spesialisasi AS keahlian_dibutuhkan_text"
            )
            ->join(
                "spesialisasi",
                "paket.id_spesialisasi = spesialisasi.id_spesialisasi",
                "left"
            )
            ->get()
            ->getResultArray();

        // Log the query for debugging
         //log_message('debug', 'Package query: ' . $this->db->getLastQuery());

        return $result;
    }
    public function findPackageByIdWithSpecialization($packageId)
    {
        return $this->db
            ->table("paket")
            ->select(
                "paket.*, spesialisasi.nama_spesialisasi AS keahlian_dibutuhkan_text"
            )
            ->join(
                "spesialisasi",
                "paket.id_spesialisasi = spesialisasi.id_spesialisasi",
                "left"
            )
            ->where("id_paket", $packageId)
            ->get()
            ->getRowArray();
    }
}
