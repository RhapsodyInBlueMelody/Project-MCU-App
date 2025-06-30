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
    $routes->get("pasien/login", "AuthController::pasienLogin");
    $routes->get("dokter/login", "AuthController::doctorLogin");
    $routes->get("admin/login", "AuthController::adminLogin");
    $routes->post("authenticate", "AuthController::authenticate");

    // Registration Routes
    $routes->get("register", "AuthController::register");
    $routes->get("register/pasien", "AuthController::registerPatient");
    $routes->post("register/pasien", "AuthController::savePatient");
    $routes->get("register/dokter", "AuthController::registerDoctor");
    $routes->post(
        "register/dokter/save",
        "AuthController::savedokterRegistration"
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
        "complete-dokter-social-registration",
        "AuthController::completedokterSocialRegistration"
    );
});

// pasien Routes (protected)
$routes->group(
    "pasien",
    ["namespace" => "App\Controllers", "filter" => "auth:pasien"],
    function ($routes) {
        $routes->get("dashboard", "Pasien::dashboard");
        $routes->get("beranda", "Pasien::beranda");
        $routes->get("appointment", "Pasien::appointment");
        $routes->post("appointment", "Pasien::saveAppointment");
        $routes->post(
            "cancel-appointment/(:num)",
            "Pasien::cancelAppointment/$1"
        );
        $routes->get(
            "cancel-appointment/(:num)",
            'Pasien::cancelAppointment/$1'
        );
        $routes->get("jadwal-pemeriksaan", "Pasien::jadwalPemeriksaan");
        $routes->get(
            "riwayat-pemeriksaan/(:segment)",
            "Pasien::riwayatPemeriksaan/$1"
        );
    }
);

// Payment Routes (protected for pasien)
$routes->group(
    "payment",
    ["namespace" => "App\Controllers", "filter" => "auth:pasien"],
    function ($routes) {
        // Checkout/payment creation for a transaction (invoke payment gateway)
        $routes->get("checkout/(:num)", "Payment::checkout/$1");
        $routes->post("checkout/(:num)", "Payment::checkout/$1");
        // Check payment status via AJAX or link
        $routes->get("status/(:num)", "Payment::checkStatus/$1");
    }
);

// DOKU Payment Webhooks/Callbacks (unprotected, DOKU server must reach them)
$routes->post('payment/callback', 'Payment::callback');
$routes->post('payment/cancel', 'Payment::cancel');   // If you want to handle this
$routes->get('payment/result', 'Payment::result');
$routes->post('payment/result', 'Payment::result');   // Optional, for display only

// pasien API Routes
$routes->group(
    "pasien/api",
    ["namespace" => "App\Controllers", "filter" => "auth:pasien"],
    function ($routes) {
        $routes->get("health-stats", "Pasien::getHealthStats");
    }
);

// dokter Routes (protected)
$routes->group(
    "dokter",
    ["namespace" => "App\Controllers", "filter" => "auth:dokter"],
    function ($routes) {
        $routes->get("dashboard", "Dokter::dashboard");
        $routes->get("appointments", "Dokter::appointments");
        $routes->get("appointment/(:num)", "Dokter::appointmentDetail/$1");
        $routes->post(
            "update-appointment-status",
            "Dokter::updateAppointmentStatus"
        );
        $routes->get("diagnosis/(:num)", "Dokter::diagnosis/$1");
        $routes->post("save-diagnosis", "Dokter::saveDiagnosis");
        $routes->get("lab-results/(:num)", "Dokter::labResults/$1");
        $routes->post(
            "complete-lab-results/(:num)",
            "Dokter::completeWithLabResults/$1"
        );
        $routes->get("schedule", "Dokter::mySchedule");
        $routes->get("profile", "Dokter::profile");
        $routes->post("update-profile", "Dokter::updateProfile");
        $routes->get('schedule/api/(:segment)', 'JadwalDokterController::getScheduleApi/$1');
    }
);

// Admin Routes (protected)
$routes->group(
    "admin",
    ["namespace" => "App\Controllers", "filter" => "auth:admin"],
    function ($routes) {
        $routes->get("dashboard", "Admin::dashboard");
        $routes->get(
            "pending-dokter-verifications",
            "Admin::pendingdokterVerifications"
        );
        $routes->get("verify-dokter/(:num)", "Admin::verifyDoctor/$1");
        $routes->post(
            "process-dokter-verification",
            "Admin::processdokterVerification"
        );
        $routes->get("dokter-management", "Admin::doctorManagement");
        $routes->get("pasien-management", "Admin::patientManagement");
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
