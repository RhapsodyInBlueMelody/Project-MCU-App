<?php

namespace App\Controllers;

class Login extends BaseController
{
    public function patient()
    {
        return redirect()->to("/auth/patient/login");
    }

    public function doctor()
    {
        return redirect()->to("/auth/doctor/login");
    }

    public function attemptPatientLogin()
    {
        return redirect()
            ->to("/auth/attempt/login")
            ->withInput(["user_type" => "patient"]);
    }

    public function attemptDoctorLogin()
    {
        return redirect()
            ->to("/auth/attempt/login")
            ->withInput(["user_type" => "doctor"]);
    }

    public function registerPatient()
    {
        return redirect()->to("/auth/register/patient");
    }
}
