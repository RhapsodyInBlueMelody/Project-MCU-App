<?php
namespace App\Controllers;

class Doctor extends AuthenticatedController
{
    public function __construct()
    {
        parent::__construct("doctor", "login/doctor");
    }

    public function dashboard()
    {
        $data["title"] = "Doctor Dashboard";
        $data["username"] = $this->session->get("username");
        $data["doctor_name"] = $this->session->get("full_name");

        return view("templates/doctor/header", $data) .
            view("doctor/dashboard", $data) .
            view("templates/doctor/footer");
    }
}
