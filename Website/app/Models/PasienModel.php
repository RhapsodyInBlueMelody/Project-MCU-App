<?php namespace App\Models;

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
        "lokasi"
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Retrieves a patient profile based on the user ID.
     *
     * @param int $userId The ID of the user associated with the profile.
     * @return array|object|null The patient profile data, or null if not found.
     */
    public function getPatientProfile(int $userId)
    {
        return $this->where("user_id", $userId)->first();
    }

    // You can add other methods to your PatientModel here, such as
    // methods to update or delete patient profiles.
}
