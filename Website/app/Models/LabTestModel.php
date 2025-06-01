
<?php namespace App\Models;

use CodeIgniter\Model;

class LabTestModel extends Model
{
    protected $table = "Lab_Tests";
    protected $primaryKey = "id_test";
    protected $allowedFields = [
        "test_name",
        "description",
        "normal_range",
        "test_type",
        "price",
    ];

    // Table for lab orders (separate from test definitions)
    protected $ordersTable = "Lab_Orders";

    /**
     * Get all lab tests with their types
     */
    public function getAllLabTests()
    {
        return $this->findAll();
    }

    /**
     * Get lab tests ordered for a specific appointment
     */
    public function getLabTestsByAppointmentId($appointmentId)
    {
        return $this->db
            ->table("{$this->ordersTable} lo")
            ->select(
                "lo.*, lt.test_name, lt.normal_range, lt.test_type, p.NAMA_PETUGAS_LAB as technician_name"
            )
            ->join("{$this->table} lt", "lt.id_test = lo.id_test", "left")
            ->join(
                "Petugas_Lab p",
                "p.ID_PETUGAS_LAB = lo.id_petugas_lab",
                "left"
            )
            ->where("lo.id_janji_temu", $appointmentId)
            ->get()
            ->getResultArray();
    }

    /**
     * Get pending lab tests for an appointment
     */
    public function getPendingLabTestsByAppointmentId($appointmentId)
    {
        return $this->db
            ->table("{$this->ordersTable} lo")
            ->where("lo.id_janji_temu", $appointmentId)
            ->whereIn("lo.status", ["ordered", "processing"])
            ->get()
            ->getResultArray();
    }

    /**
     * Create a new lab test order
     */
    public function createLabOrder($data)
    {
        return $this->db->table($this->ordersTable)->insert($data);
    }

    /**
     * Delete lab test orders for an appointment
     */
    public function deleteLabOrdersByAppointmentId($appointmentId)
    {
        return $this->db
            ->table($this->ordersTable)
            ->where("id_janji_temu", $appointmentId)
            ->where("status", "ordered") // Only delete orders that haven't been processed
            ->delete();
    }

    /**
     * Get lab tests assigned to a specific lab technician
     */
    public function getLabTechnicianAssignments($technicianId)
    {
        return $this->db
            ->table("{$this->ordersTable} lo")
            ->select(
                "lo.*, lt.test_name, lt.test_type, p.NAMA_LENGKAP as patient_name, a.TANGGAL_JANJI"
            )
            ->join("{$this->table} lt", "lt.id_test = lo.id_test", "left")
            ->join("Pasien p", "p.PASIEN_ID = lo.ID_PASIEN", "left")
            ->join("Janji_Temu a", "a.ID_JANJI_TEMU = lo.id_janji_temu", "left")
            ->where("lo.id_petugas_lab", $technicianId)
            ->whereIn("lo.status", ["assigned", "processing"])
            ->orderBy("a.TANGGAL_JANJI", "ASC")
            ->get()
            ->getResultArray();
    }

    /**
     * Update lab test order status
     */
    public function updateLabOrderStatus(
        $orderId,
        $status,
        $userId,
        $results = null
    ) {
        $data = [
            "status" => $status,
            "updated_by" => $userId,
            "updated_at" => date("Y-m-d H:i:s"),
        ];

        if ($results !== null) {
            $data["test_result"] = $results;
        }

        if ($status === "completed") {
            $data["completed_at"] = date("Y-m-d H:i:s");
        }

        return $this->db
            ->table($this->ordersTable)
            ->where("id", $orderId)
            ->update($data);
    }

    /**
     * Get all lab test orders for a lab technician by status
     */
    public function getLabOrdersByTechnicianAndStatus($technicianId, $status)
    {
        return $this->db
            ->table("{$this->ordersTable} lo")
            ->select(
                "lo.*, lt.test_name, lt.test_type, p.NAMA_LENGKAP as patient_name, a.TANGGAL_JANJI"
            )
            ->join("{$this->table} lt", "lt.id_test = lo.id_test", "left")
            ->join("Pasien p", "p.PASIEN_ID = lo.ID_PASIEN", "left")
            ->join("Janji_Temu a", "a.ID_JANJI_TEMU = lo.id_janji_temu", "left")
            ->where("lo.id_petugas_lab", $technicianId)
            ->where("lo.status", $status)
            ->orderBy("lo.updated_at", "DESC")
            ->get()
            ->getResultArray();
    }

    /**
     * Get unassigned lab orders (for lab admin)
     */
    public function getUnassignedLabOrders()
    {
        return $this->db
            ->table("{$this->ordersTable} lo")
            ->select(
                "lo.*, lt.test_name, lt.test_type, p.NAMA_LENGKAP as patient_name, d.NAMA_DOKTER as doctor_name, a.TANGGAL_JANJI"
            )
            ->join("{$this->table} lt", "lt.id_test = lo.id_test", "left")
            ->join("Pasien p", "p.PASIEN_ID = lo.ID_PASIEN", "left")
            ->join("Dokter d", "d.ID_DOKTER = lo.ID_DOKTER", "left")
            ->join("Janji_Temu a", "a.ID_JANJI_TEMU = lo.id_janji_temu", "left")
            ->where("lo.status", "ordered")
            ->whereNull("lo.id_petugas_lab")
            ->orderBy("a.TANGGAL_JANJI", "ASC")
            ->get()
            ->getResultArray();
    }

    /**
     * Assign lab order to a technician
     */
    public function assignLabOrder($orderId, $technicianId, $userId)
    {
        return $this->db
            ->table($this->ordersTable)
            ->where("id", $orderId)
            ->update([
                "id_petugas_lab" => $technicianId,
                "status" => "assigned",
                "updated_by" => $userId,
                "updated_at" => date("Y-m-d H:i:s"),
            ]);
    }

    /**
     * Get lab test history for a patient
     */
    public function getPatientLabTestHistory($patientId)
    {
        return $this->db
            ->table("{$this->ordersTable} lo")
            ->select(
                "lo.*, lt.test_name, lt.normal_range, lt.test_type, d.NAMA_DOKTER as doctor_name, a.TANGGAL_JANJI, p.NAMA_PETUGAS_LAB as technician_name"
            )
            ->join("{$this->table} lt", "lt.id_test = lo.id_test", "left")
            ->join("Dokter d", "d.ID_DOKTER = lo.ID_DOKTER", "left")
            ->join(
                "Petugas_Lab p",
                "p.ID_PETUGAS_LAB = lo.id_petugas_lab",
                "left"
            )
            ->join("Janji_Temu a", "a.ID_JANJI_TEMU = lo.id_janji_temu", "left")
            ->where("lo.ID_PASIEN", $patientId)
            ->where("lo.status", "completed")
            ->orderBy("lo.completed_at", "DESC")
            ->get()
            ->getResultArray();
    }

    /**
     * Get lab test statistics for reporting
     */
    public function getLabTestStatistics($startDate = null, $endDate = null)
    {
        $query = $this->db
            ->table("{$this->ordersTable} lo")
            ->select("lt.test_type, COUNT(*) as count")
            ->join("{$this->table} lt", "lt.id_test = lo.id_test", "left")
            ->where("lo.status", "completed");

        if ($startDate) {
            $query->where("lo.completed_at >=", $startDate);
        }

        if ($endDate) {
            $query->where("lo.completed_at <=", $endDate);
        }

        return $query->groupBy("lt.test_type")->get()->getResultArray();
    }

    /**
     * Get order details by ID
     */
    public function getOrderDetails($orderId)
    {
        return $this->db
            ->table("{$this->ordersTable} lo")
            ->select(
                "lo.*, lt.test_name, lt.normal_range, lt.test_type, p.NAMA_LENGKAP as patient_name, " .
                    "d.NAMA_DOKTER as doctor_name, pl.NAMA_PETUGAS_LAB as technician_name, a.TANGGAL_JANJI"
            )
            ->join("{$this->table} lt", "lt.id_test = lo.id_test", "left")
            ->join("Pasien p", "p.PASIEN_ID = lo.ID_PASIEN", "left")
            ->join("Dokter d", "d.ID_DOKTER = lo.ID_DOKTER", "left")
            ->join(
                "Petugas_Lab pl",
                "pl.ID_PETUGAS_LAB = lo.id_petugas_lab",
                "left"
            )
            ->join("Janji_Temu a", "a.ID_JANJI_TEMU = lo.id_janji_temu", "left")
            ->where("lo.id", $orderId)
            ->get()
            ->getRowArray();
    }
}
