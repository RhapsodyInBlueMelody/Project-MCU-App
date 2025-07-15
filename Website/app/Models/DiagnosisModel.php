<?php

namespace App\Models;

use CodeIgniter\Model;

class DiagnosisModel extends Model
{
    protected $table = "diagnosis";
    protected $primaryKey = "id_diagnosis";
    protected $allowedFields = [
        "id_diagnosis",
        "id_janji_temu",
        "id_dokter",
        "id_pasien",
        "id_petugas_lab",
        "nama_petugas_lab", //TO BE REMOVE!!!
        "symptoms",
        "diagnosis_result",
        "treatment_plan",
        "notes",
        "tanggal_hasil_lab",
        "hasil_lab",
        "created_by",
        "updated_by",
    ];
    protected $useTimestamps = true;
    protected $createdField = "created_at";
    protected $updatedField = "updated_at";

    /**
     * Get diagnosis with patient and doctor information
     * @param int $diagnosisId
     * @return array|null
     */
    public function getDiagnosisDetails($diagnosisId)
    {
        return $this->db
            ->table("diagnosis d")
            ->select(
                "d.*, p.nama_pasien as patient_name, doc.nama_dokter as doctor_name"
            )
            ->join("pasien p", "p.id_pasien = d.id_pasien", "left")
            ->join("dokter doc", "doc.id_dokter = d.id_dokter", "left")
            ->where("d.id_diagnosis", $diagnosisId)
            ->get()
            ->getRowArray();
    }

    /**
     * Get diagnosis by appointment ID
     * @param mixed $appointmentId
     * @return array|null
     */
    public function getDiagnosisByAppointmentId($appointmentId)
    {
        return $this->where("id_janji_temu", $appointmentId)->first();
    }

    /**
     * Get all diagnoses for a patient
     * @param mixed $patientId
     * @return array
     */
    public function getPatientDiagnoses($patientId)
    {
        return $this->db
            ->table("diagnosis d")
            ->select("d.*, doc.nama_dokter as doctor_name, a.tanggal_janji")
            ->join("dokter doc", "doc.id_dokter = d.id_dokter", "left")
            ->join("janji_temu a", "a.id_janji_temu = d.id_janji_temu", "left")
            ->where("d.id_pasien", $patientId)
            ->orderBy("d.created_at", "DESC")
            ->get()
            ->getResultArray();
    }

    public function diagnosisPrint($diagnosisId = null)
    {
        if ($diagnosisId === null) {
            return redirect()->back()->with("error", "Diagnosis ID diperlukan");
        }

        $diagnosisModel = new \App\Models\DiagnosisModel();
        $diagnosis = $diagnosisModel->getDiagnosisDetails($diagnosisId);

        if (!$diagnosis) {
            return redirect()->back()->with("error", "Diagnosis tidak ditemukan");
        }

        // Render the PDF using a view
        $html = view('diagnosis_pdf', ['diagnosis' => $diagnosis]);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Output PDF to browser (inline)
        $dompdf->stream('diagnosis.pdf', ['Attachment' => false]);
        exit; // Ensure no extra output
    }

    public function saveDiagnosisWithLabTests($diagnosisData, $labTests, $userId)
    {
        $db = \Config\Database::connect();
        $db->transStart();
        $appointmentModel = new \App\Models\AppointmentModel();

        if (!empty($labTests)) {
            $appointmentModel->markAwaitingLabResults($diagnosisData["id_janji_temu"], $userId);
        } else {
            $appointmentModel->markCompleted($diagnosisData["id_janji_temu"], $userId);
        }


        // Save or update diagnosis
        $existing = $this->where("id_janji_temu", $diagnosisData["id_janji_temu"])->first();
        if ($existing) {
            $this->update($existing["id"], $diagnosisData);
        } else {
            $this->insert($diagnosisData);
        }

        // Handle lab tests
        $labTestModel = new \App\Models\LabTestModel();
        $db->table('test_lab')->where('id_janji_temu', $diagnosisData["id_janji_temu"])->delete();

        if (!empty($labTests)) {
            foreach ($labTests as $jenis_test) {
                $labTestModel->insert([
                    "id_janji_temu" => $diagnosisData["id_janji_temu"],
                    "tanggal_test" => date("Y-m-d H:i:s"),
                    "jenis_test" => $jenis_test,
                    "status" => "ordered",
                    "created_by" => $userId,
                    "created_at" => date("Y-m-d H:i:s"),
                ]);
            }
            // Update appointment status to await lab results
            $appointmentModel = new \App\Models\AppointmentModel();
            $appointmentModel->update($diagnosisData["id_janji_temu"], [
                "status" => "awaiting_lab_results",
                "updated_by" => $userId,
            ]);
        } else {
            // Update appointment status to completed
            $appointmentModel = new \App\Models\AppointmentModel();
            $appointmentModel->update($diagnosisData["id_janji_temu"], [
                "status" => "completed",
                "updated_by" => $userId,
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new \Exception("Database transaction failed");
        }

        return true;
    }
}
