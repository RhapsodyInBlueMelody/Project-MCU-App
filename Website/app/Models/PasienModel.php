<?php

namespace App\Models;

use CodeIgniter\Model;

class PasienModel extends Model
{
    protected $table = "pasien";
    protected $primaryKey = "id_pasien";
    protected $allowedFields = [
        "user_id",
        "id_pasien",
        "no_identitas",
        "nama_pasien",
        "tempat_lahir",
        "tanggal_lahir",
        "jenis_kelamin",
        "telepon",
        "alamat",
        "email",
        "lokasi",
        "created_by",
        "updated_by"
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getPatientProfile($userId)
    {
        return $this->where("user_id", $userId)->first();
    }

    /**
     * Generate a new pasien ID using stored procedure
     */
    public function generatePasienId($lokasi): string
    {
        $db = \Config\Database::connect();
        $db->query("CALL GeneratePasienId(?, @new_id_pasien)", [$lokasi]);
        $query = $db->query("SELECT @new_id_pasien AS id_pasien");
        $result = $query->getRow();

        if (!$result || !$result->id_pasien || $result->id_pasien === 'INVALID-LOC') {
            throw new \Exception('Pasien ID generation failed: invalid location or system error.');
        }

        return $result->id_pasien;
    }

    /**
     * Create new pasien profile
     */
    public function createPasienProfile($userId, $data): array
    {
        $pasienId = $this->generatePasienId($data['lokasi']);

        $pasienData = [
            "user_id" => $userId,
            "id_pasien" => $pasienId,
            "no_identitas" => $data["no_identitas"] ?? null,
            "nama_pasien" => $data["nama_pasien"],
            "jenis_kelamin" => $data["jenis_kelamin"],
            "telepon" => $data["no_telp_pasien"],
            "tempat_lahir" => $data["tempat_lahir"],
            "tanggal_lahir" => $data["tanggal_lahir"],
            "alamat" => $data["alamat"],
            "email" => $data["email"],
            "lokasi" => $data["lokasi"],
            "created_by" => $userId,
            "updated_by" => $userId,
        ];

        $this->insert($pasienData);

        return [
            'pasien_id' => $pasienId
        ];
    }
}
