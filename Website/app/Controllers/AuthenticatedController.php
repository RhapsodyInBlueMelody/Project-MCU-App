<?php
namespace App\Controllers;

class AuthenticatedController extends BaseController
{
    protected $session;
    protected $userType; // 'patient' or 'doctor'
    protected $redirectPath;

    public function __construct($userType = null, $redirectPath = null)
    {
        $this->session = \Config\Services::session();
        $this->userType = $userType;
        $this->redirectPath = $redirectPath;

        $this->checkAuthentication();
    }

    protected function checkAuthentication()
    {
        if (
            $this->userType === "patient" &&
            !$this->session->has("patient_logged_in")
        ) {
            header(
                "Location: " . base_url($this->redirectPath ?? "login/patient")
            );
            exit();
        } elseif (
            $this->userType === "doctor" &&
            !$this->session->has("doctor_logged_in")
        ) {
            header(
                "Location: " . base_url($this->redirectPath ?? "login/doctor")
            );
            exit();
        }
    }
}
