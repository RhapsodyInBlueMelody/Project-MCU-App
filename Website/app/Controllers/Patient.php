<?php

namespace App\Controllers;

use App\Models\PatientModel;

class Patient extends BaseController
{
    public function register(){
        $model = new PatientModel();

        if ($this->request->getMethod() === 'post') {
            $data = [
                'patient_name' => $this->request->getPost('patient_name'),
                'patient_email' => $this->request->getPost('patient_email'),
            ];

            if ($model->save($data)) {
                return redirect()->to('/patient/dashboard')->with('success', 'Patient Registered!');
            } else {
                return redirect()->back()->withInput()->with('error', $model->errors());
            }
        }
        return view('patient/register');
    }
    
    public function dashboard()
    {
        $data['title'] = ucfirst("Pelanggan Page"); // Capitalize the first letter
        $data['username'] = ucfirst("Faizcan");

        return view('templates/patient/header', $data)
             . view('patient/dashboard', $data)
             . view('templates/patient/footer');
    }
}
