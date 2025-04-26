<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PatientProfileModel;
use App\Models\DoctorProfileModel;

class Login extends BaseController
{
    public function patient()
    {
        $data["title"] = "Patient Login Page";
        // If already logged in as patient, redirect to dashboard
        if (session()->has("patient_logged_in")) {
            return redirect()->to("/patient/dashboard");
        }

        return view("auth/login/patient", $data);
    }

    public function doctor()
    {
        $data["title"] = "Doctor Login Page";
        // If already logged in as doctor, redirect to dashboard
        if (session()->has("doctor_logged_in")) {
            return redirect()->to("/doctor/dashboard");
        }

        return view("auth/login/doctor", $data);
    }

    public function attemptPatientLogin()
    {
        $userModel = new \App\Models\UserModel();
        $profileModel = new \App\Models\PatientProfileModel();

        $email = $this->request->getPost("email");
        $password = $this->request->getPost("password");

        // Use the fixed method to avoid query logic issues
        $user = $userModel->getUserByEmailOrUsernameFixed($email, "patient");

        if ($user && password_verify($password, $user["password"])) {
            $profile = $profileModel->getPatientProfile($user["id"]);

            // Set session data
            session()->set([
                "user_id" => $user["id"],
                "username" => $user["username"],
                "full_name" => $profile
                    ? $profile["full_name"]
                    : $user["username"],
                "email" => $user["email"],
                "patient_logged_in" => true,
            ]);

            return redirect()->to("/patient/dashboard");
        }

        return redirect()
            ->to("/login/patient")
            ->with("error", "Invalid login credentials");
    }

    public function attemptDoctorLogin()
    {
        $userModel = new UserModel();
        $profileModel = new DoctorProfileModel();

        $email = $this->request->getPost("email");
        $password = $this->request->getPost("password");

        $user = $userModel->getUserByEmailOrUsername($email, "doctor");

        if ($user && password_verify($password, $user["password"])) {
            $profile = $profileModel->getDoctorProfile($user["id"]);

            // Set session data
            session()->set([
                "user_id" => $user["id"],
                "username" => $user["username"],
                "full_name" => $profile
                    ? $profile["full_name"]
                    : $user["username"],
                "email" => $user["email"],
                "specialization" => $profile ? $profile["specialization"] : "",
                "doctor_logged_in" => true,
            ]);

            return redirect()->to("/doctor/dashboard");
        }

        return redirect()
            ->to("/login/doctor")
            ->with("error", "Invalid login credentials");
    }

    public function registerPatient()
    {
        helper(["form"]);

        if ($this->request->getMethod() === "post") {
            // Validation rules
            $rules = [
                "username" =>
                    "required|min_length[4]|is_unique[users.username]",
                "email" => "required|valid_email|is_unique[users.email]",
                "password" => "required|min_length[8]",
                "password_confirm" => "required|matches[password]",
                "full_name" => "required",
                "phone_number" => "required",
            ];

            if ($this->validate($rules)) {
                $userModel = new \App\Models\UserModel();
                $profileModel = new \App\Models\PatientProfileModel();

                // Hash password
                $password = password_hash(
                    $this->request->getPost("password"),
                    PASSWORD_DEFAULT
                );

                // User data
                $userData = [
                    "username" => $this->request->getPost("username"),
                    "email" => $this->request->getPost("email"),
                    "password" => $password,
                    "role" => "patient",
                    "status" => 1,
                ];

                // Insert user and get ID
                $userId = $userModel->insert($userData);

                // Profile data
                $profileData = [
                    "user_id" => $userId,
                    "full_name" => $this->request->getPost("full_name"),
                    "phone_number" => $this->request->getPost("phone_number"),
                    "date_of_birth" =>
                        $this->request->getPost("date_of_birth") ?? null,
                    "gender" => $this->request->getPost("gender") ?? null,
                    "address" => $this->request->getPost("address") ?? null,
                ];

                // Insert profile
                $profileModel->insert($profileData);

                // Success message and redirect
                return redirect()
                    ->to("/login/patient")
                    ->with("success", "Registration successful! Please login.");
            } else {
                // Return validation errors
                return view("auth/register_patient", [
                    "validation" => $this->validator,
                ]);
            }
        }

        // Display registration form
        return view("auth/register_patient");
    }
}
