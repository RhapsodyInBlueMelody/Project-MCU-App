<?php

namespace App\Controllers;

class Login extends BaseController
{
    public function doctor(): string
    {
        $data['title'] = ucfirst("Doctor Login Page"); // Capitalize the first letter

        return view('login/doctor');
    }

    public function patient(): string
    {
        $data['title'] = ucfirst("Patient Login Page"); // Capitalize the first letter

        return view('login/patient');
    }
}
