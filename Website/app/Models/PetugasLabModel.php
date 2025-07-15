<?php

namespace App\Models;

use CodeIgniter\Model;
use phpseclib3\Common\Functions\Strings;

class PetugasLabModel extends Model
{
    protected $table = "petugas_lab";
    protected $primaryKey = "id_petugas_lab";
    protected $allowedFields = [
        "id_petugas_lab",
        "user_id",
        "nama_petugas_lab",
        "id_spesialisasi_lab",
        "telepon_petugas_lab",
        "alamat_petugas_lab",
        "jenis_kelamin",
        "tanggal_lahir",
        "foto",
        "no_lisensi",
        "lokasi_kerja",
        "is_verified",
        "verification_status",
        "verification_notes",
        "created_by",
        "updated_by"
    ];
    protected $useTimestamps = true;
    protected $createdField = "created_at";
    protected $updatedField = "updated_at";



    /**
     * Generate a new petguas lab ID using stored procedure
     */
    public function GeneratePetugasLabId($spesialisasi): string
    {
        $db = \Config\Database::connect();
        $db->query("CALL GeneratePetugasLabId(?, @new_id_petugsa_lab)", [$spesialisasi]);
        $query = $db->query("SELECT @new_id_petugsa_lab AS id_petugas_lab");
        $result = $query->getRow();

        if (!$result || !$result->id_petugas_lab) {
            throw new \Exception('Petguas Lab ID generation failed.');
        }

        return $result->id_petugas_lab;
    }

    public function getVerifiedLabProfile($userId)
    {
        $petugasLab = $this->where("user_id", $userId)->first();

        if (!$petugasLab) {
            return ["error" => "not_found"];
        }

        if ($petugasLab["is_verified"] != 1 || $petugasLab["verification_status"] !== "approved") {
            if ($petugasLab["verification_status"] === "rejected") {
                return ["error" => "rejected", "verification_notes" => $petugasLab["verification_notes"] ?? ""];
            }
            return ["error" => "not_verified"];
        }

        return $petugasLab;
    }
}
