<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\DicomProcessor;
use App\Controllers\FaceRecognition;
use App\Controllers\Home;
use App\Controllers\AuthController;

/**
 * @var RouteCollection $routes
 */


$routes->get("/", "Home::index");
$routes->get("/home", "Home::index");

$routes->group("rumah-sakit", function ($routes) {
    $routes->get("cabang-jakarta", "Home::cabangJakarta"); // untuk halaman Cabang Jakarta
    $routes->get("cabang-bandung", "Home::cabangBandung"); // untuk halaman Cabang Bandung
    $routes->get("cabang-surabaya", "Home::cabangSurabaya"); // untuk halaman Cabang Surabaya
    $routes->get("fasilitas-kami", "Home::fasilitasKami"); // Dipindah ke sini
    $routes->get("cari-dokter", "Home::cariDokter"); // Dipindah ke sini
});

$routes->group("auth", ["namespace" => "App\Controllers"], function ($routes) {
    // Login Routes
    $routes->get("login", "AuthController::login");
    $routes->get("patient/login", "AuthController::patientLogin");
    $routes->get("doctor/login", "AuthController::doctorLogin");
    $routes->get("admin/login", "AuthController::adminLogin");
    $routes->post("authenticate", "AuthController::authenticate");

    // Registration Routes
    $routes->get("register", "AuthController::register");
    $routes->get("register/patient", "AuthController::registerPatient");
    $routes->post("register/patient", "AuthController::savePatient");
    $routes->get("register/doctor", "AuthController::registerDoctor");
    $routes->post(
        "register/doctor/save",
        "AuthController::saveDoctorRegistration"
    );

    // Logout Route
    $routes->get("logout/(:segment)", "AuthController::logout/$1");

    // Google Authentication Routes
    $routes->get("google/login/(:segment)", "AuthController::googleLogin/$1");
    $routes->get(
        "google/callback/(:segment)",
        "AuthController::googleCallback/$1"
    );

    // Social Registration
    $routes->get(
        "register/social/(:segment)",
        "AuthController::registerSocial/$1"
    );
    $routes->post(
        "complete-social-registration",
        "AuthController::completeSocialRegistration"
    );
    $routes->post(
        "complete-doctor-social-registration",
        "AuthController::completeDoctorSocialRegistration"
    );
});

// Patient Routes (protected)
$routes->group(
    "patient",
    ["namespace" => "App\Controllers", "filter" => "auth:patient"],
    function ($routes) {
        $routes->get("dashboard", "Patient::dashboard");
        $routes->get("beranda", "Patient::beranda");
        $routes->get("appointment", "Patient::appointment");
        $routes->post("appointment", "Patient::saveAppointment");
        $routes->post(
            "cancel-appointment/(:num)",
            "Patient::cancelAppointment/$1"
        );
        $routes->get(
            "cancel-appointment/(:num)",
            'Patient::cancelAppointment/$1'
        );
        $routes->get("jadwal-pemeriksaan", "Patient::jadwalPemeriksaan");
        $routes->get(
            "riwayat-pemeriksaan/(:num)",
            "Patient::riwayatPemeriksaan/$1"
        );
    }
);

// Patient API Routes
$routes->group(
    "patient/api",
    ["namespace" => "App\Controllers", "filter" => "auth:patient"],
    function ($routes) {
        $routes->get("health-stats", "Patient::getHealthStats");
    }
);

// Doctor Routes (protected)
$routes->group(
    "doctor",
    ["namespace" => "App\Controllers", "filter" => "auth:doctor"],
    function ($routes) {
        $routes->get("dashboard", "Doctor::dashboard");
        $routes->get("appointments/(:segment)", "Doctor::appointments/$1");
        $routes->get("appointments", "Doctor::appointments");
        $routes->get("appointment/(:num)", "Doctor::appointmentDetail/$1");
        $routes->post(
            "update-appointment-status",
            "Doctor::updateAppointmentStatus"
        );
        $routes->get("diagnosis/(:num)", "Doctor::diagnosis/$1");
        $routes->post("save-diagnosis", "Doctor::saveDiagnosis");
        $routes->get("lab-results/(:num)", "Doctor::labResults/$1");
        $routes->post(
            "complete-lab-results/(:num)",
            "Doctor::completeWithLabResults/$1"
        );
        $routes->get("schedule", "Doctor::mySchedule");
        $routes->get("profile", "Doctor::profile");
        $routes->post("update-profile", "Doctor::updateProfile");
    }
);

// Admin Routes (protected)
$routes->group(
    "admin",
    ["namespace" => "App\Controllers", "filter" => "auth:admin"],
    function ($routes) {
        $routes->get("dashboard", "Admin::dashboard");
        $routes->get(
            "pending-doctor-verifications",
            "Admin::pendingDoctorVerifications"
        );
        $routes->get("verify-doctor/(:num)", "Admin::verifyDoctor/$1");
        $routes->post(
            "process-doctor-verification",
            "Admin::processDoctorVerification"
        );
        $routes->get("doctor-management", "Admin::doctorManagement");
        $routes->get("patient-management", "Admin::patientManagement");
        $routes->get("appointment-management", "Admin::appointmentManagement");
        $routes->get("appointment/view/(:num)", "Admin::viewAppointment/$1");
        $routes->post(
            "appointment/update-status/(:num)",
            "Admin::updateAppointmentStatus/$1"
        );
        $routes->get("reports", "Admin::reports");
    }
);

// Utility/Testing Routes
$routes->get("test-db", function () {
    try {
        db_connect()->query("SELECT 1");
        return "Database connection successful! 🎉";
    } catch (\Exception $e) {
        return "Connection failed: " . $e->getMessage();
    }
});

// Face Recognition Routes
$routes->group("face", ["namespace" => "App\Controllers"], function ($routes) {
    $routes->get("upload", "FaceRecognition::uploadForm");
    $routes->post("upload/process", "FaceRecognition::upload");
});

// DICOM Processor Routes
$routes->group("dicom", ["namespace" => "App\Controllers"], function ($routes) {
    $routes->get("upload", "DicomProcessor::uploadForm");
    $routes->post("upload/process", "DicomProcessor::upload");
});
