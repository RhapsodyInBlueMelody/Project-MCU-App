<?php

namespace App\Controllers;

class Doctor extends BaseController
{
    public function index(): string
    {
        $data['title'] = ucfirst("Doctor Page"); // Capitalize the first letter

        return view('templates/doctor/header', $data)
        . view('pages/home')
        . view('templates/doctor/footer');
    }
}
