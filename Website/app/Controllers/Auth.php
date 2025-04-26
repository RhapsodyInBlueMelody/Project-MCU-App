<?php namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PatientProfileModel;
use App\Models\DoctorProfileModel;

class Auth extends BaseController
{
    public function index()
    {
        return redirect()->to("/auth/login");
    }

    public function patientLogin()
    {
        // Check if already logged in
        if (session()->has("patient_logged_in")) {
            return redirect()->to("/patient/dashboard");
        }

        return view("auth/patient_login");
    }

    public function doctorLogin()
    {
        // Check if already logged in
        if (session()->has("doctor_logged_in")) {
            return redirect()->to("/doctor/dashboard");
        }

        return view("auth/doctor_login");
    }

    public function authenticatePatient()
    {
        $userModel = new UserModel();
        $patientProfileModel = new PatientProfileModel();

        $username = $this->request->getPost("username");
        $password = $this->request->getPost("password");

        $user = $userModel->getUserByEmailOrUsername($username, "patient");

        if ($user) {
            if (password_verify($password, $user["password"])) {
                // Get patient profile data
                $profile = $patientProfileModel->getPatientProfile($user["id"]);

                // Set session data
                session()->set([
                    "user_id" => $user["id"],
                    "username" => $user["username"],
                    "email" => $user["email"],
                    "role" => "patient",
                    "patient_logged_in" => true,
                    "full_name" => $profile
                        ? $profile["full_name"]
                        : $user["username"],
                ]);

                return redirect()->to("/patient/dashboard");
            }
        }

        return redirect()
            ->to("/login/patient")
            ->with("error", "Invalid username/email or password");
    }

    public function authenticateDoctor()
    {
        $userModel = new UserModel();
        $doctorProfileModel = new DoctorProfileModel();

        $username = $this->request->getPost("username");
        $password = $this->request->getPost("password");

        $user = $userModel->getUserByEmailOrUsername($username, "doctor");

        if ($user) {
            if (password_verify($password, $user["password"])) {
                // Get doctor profile data
                $profile = $doctorProfileModel->getDoctorProfile($user["id"]);

                // Set session data
                session()->set([
                    "user_id" => $user["id"],
                    "username" => $user["username"],
                    "email" => $user["email"],
                    "role" => "doctor",
                    "doctor_logged_in" => true,
                    "full_name" => $profile
                        ? $profile["full_name"]
                        : $user["username"],
                    "specialization" => $profile
                        ? $profile["specialization"]
                        : "",
                ]);

                return redirect()->to("/doctor/dashboard");
            }
        }

        return redirect()
            ->to("/login/doctor")
            ->with("error", "Invalid username/email or password");
    }

    public function login()
    {
        if (session()->get("isLoggedIn")) {
            return redirect()->to("/dashboard");
        }
        return view("auth/login");
    }

    public function authenticate()
    {
        $session = session();
        $userModel = new UserModel();

        $username = $this->request->getPost("username");
        $password = $this->request->getPost("password");

        $user = $userModel->getUserByEmailOrUsername($username);

        if ($user) {
            $pass = $user["password"];
            $authenticatePassword = password_verify($password, $pass);

            if ($authenticatePassword) {
                $userData = [
                    "id" => $user["id"],
                    "username" => $user["username"],
                    "email" => $user["email"],
                    "role" => $user["role"],
                    "isLoggedIn" => true,
                ];

                $session->set($userData);
                return redirect()->to("/dashboard");
            } else {
                $session->setFlashdata("msg", "Password is incorrect.");
                return redirect()->to("/auth/login");
            }
        } else {
            $session->setFlashdata("msg", "Username/Email not found.");
            return redirect()->to("/auth/login");
        }
    }

    public function patientRegister()
    {
        return view("auth/patient_register");
    }

    public function doctorRegister()
    {
        return view("auth/doctor_register");
    }

    public function register()
    {
        return view("auth/register");
    }

    public function save()
    {
        $userModel = new UserModel();

        // Validation rules here

        $password = $this->request->getPost("password");
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $data = [
            "username" => $this->request->getPost("username"),
            "email" => $this->request->getPost("email"),
            "password" => $hashedPassword,
            "role" => "patient", // Default role
        ];

        $userModel->save($data);
        session()->setFlashdata(
            "success",
            "Registration successful. Please login."
        );
        return redirect()->to("/auth/login");
    }

    public function patientLogout()
    {
        session()->remove([
            "user_id",
            "username",
            "email",
            "role",
            "patient_logged_in",
            "full_name",
        ]);
        return redirect()
            ->to("/login/patient")
            ->with("success", "You have been logged out");
    }

    public function doctorLogout()
    {
        session()->remove([
            "user_id",
            "username",
            "email",
            "role",
            "doctor_logged_in",
            "full_name",
            "specialization",
        ]);
        return redirect()
            ->to("/login/doctor")
            ->with("success", "You have been logged out");
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to("/auth/login");
    }
}
