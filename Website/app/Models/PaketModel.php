<?php namespace App\Models;

use CodeIgniter\Model;

class PaketModel extends Model
{
    protected $table = "Paket";
    protected $primaryKey = "id_paket";
    protected $allowedFields = [
        "nama_paket",
        "deskripsi_singkat",
        "harga_paket",
        "id_spesialisasi_dibutuhkan",
        "created_by",
        "created_at",
        "updated_by",
        "updated_at",
    ];
    protected $useTimestamps = true;

    public function getAllPackagesWithSpecialization()
    {
        $result = $this->db
            ->table("Paket")
            ->select(
                "Paket.*, Spesialisasi.nama_spesialisasi AS keahlian_dibutuhkan_text"
            )
            ->join(
                "Spesialisasi",
                "Paket.id_spesialisasi_dibutuhkan = Spesialisasi.id_spesialisasi",
                "left"
            )
            ->get()
            ->getResultArray();

        // Log the query for debugging
        // log_message('debug', 'Package query: ' . $this->db->getLastQuery());

        return $result;
    }
    public function findPackageByIdWithSpecialization($packageId)
    {
        return $this->db
            ->table("Paket")
            ->select(
                "Paket.*, Spesialisasi.nama_spesialisasi AS keahlian_dibutuhkan_text"
            )
            ->join(
                "Spesialisasi",
                "Paket.id_spesialisasi_dibutuhkan = Spesialisasi.id_spesialisasi",
                "left"
            )
            ->where("id_paket", $packageId)
            ->get()
            ->getRowArray();
    }
}
