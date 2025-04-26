<?php
namespace App\Controllers;

use App\Models\PatientProfileModel;

class Patient extends BaseController
{
    //public function __construct()
    //{
    //    parent::__construct("patient", "login/patient", ["register"]);
    //}

    public function register()
    {
        // Registration logic (accessible without login)
        $model = new PatientProfileModel();

        if ($this->request->getMethod() === "post") {
            $data = [
                "patient_name" => $this->request->getPost("patient_name"),
                "patient_email" => $this->request->getPost("patient_email"),
            ];

            if ($model->save($data)) {
                return redirect()
                    ->to("/patient/dashboard")
                    ->with("success", "Patient Registered!");
            } else {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with("error", $model->errors());
            }
        }
        return view("patient/register");
    }

    public function dashboard()
    {
        $data["title"] = "Patient Dashboard";
        $data["username"] = "user";
        $data["patient_name"] = "dummy";
        //$data["username"] = $this->session->get("username");
        //$data["patient_name"] = $this->session->get("full_name");

        return view("templates/patient/header", $data) .
            view("patient/dashboard", $data) .
            view("templates/patient/footer");
    }

    public function beranda()
    {
        $data["title"] = "Beranda";
        return view("templates/patient/header", $data)
            . view("patient/beranda", $data) // Create a beranda_content.php for the body
            . view("templates/patient/footer");
    }

    public function pendaftaran()
    {
        $data["title"] = "Pendaftaran";
        return view("templates/patient/header", $data)
            . view("patient/pendaftaran", $data) // Create a pendaftaran_content.php
            . view("templates/patient/footer");
    }

    public function jadwalPemeriksaan()
    {
        $data["title"] = "Jadwal Pemeriksaan";
        return view("templates/patient/header", $data)
            . view("patient/jadwal_pemeriksaan", $data) // Create this file
            . view("templates/patient/footer");
    }

    public function riwayatMedicalCheckup()
    {
        $data["title"] = "Riwayat Medical Check Up";
        return view("patient/riwayat_medical_checkup", $data) // Create this file
            . view("templates/patient/footer");
    }
}
