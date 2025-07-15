<?php

use CodeIgniter\Router\RouteCollection;

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
    $routes->get("petugas_lab/login", "AuthController::labLogin");
    $routes->post("authenticate", "AuthController::authenticate");

    // Dynamic GET and POST for any role
    $routes->match(['get', 'post'], 'register/(:segment)', 'AuthController::register/$1');

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
            "cancel-appointment/(:segment)",
            "Pasien::cancelAppointment/$1"
        );
        $routes->get(
            "cancel-appointment/(:segment)",
            'Pasien::cancelAppointment/$1'
        );
        $routes->get("diagnosis/print/(:segment)", "Pasien::diagnosisPrint/$1");
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

        $routes->post('callback', 'Payment::callback');
        $routes->post('cancel', 'Payment::cancel');   // If you want to handle this
        $routes->get('result', 'Payment::result');
        $routes->post('result', 'Payment::result');   // Optional, for display only
    }
);


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
        $routes->get("appointment/(:segment)", "Dokter::appointmentDetail/$1");
        $routes->post('appointments/accept/(:segment)', 'Dokter::accept/$1');
        $routes->post('appointments/reject', 'Dokter::reject');
        $routes->post(
            "update-appointment-status",
            "Dokter::updateAppointmentStatus"
        );
        $routes->get("diagnosis/(:segment)", "Dokter::diagnosis/$1");
        $routes->post("diagnosis/save-diagnosis", "Dokter::saveDiagnosis");
        $routes->get("lab-results/(:segment)", "Dokter::labResults/$1");
        $routes->get("download_report/(:num)", "Dokter::downloadLabReport/$1");
        $routes->post(
            "complete-lab-results/(:segment)",
            "Dokter::completeWithLabResults/$1"
        );
        $routes->get("schedule", "Dokter::mySchedule");
        $routes->get('schedule/add', 'Dokter::addSchedule');
        $routes->post('schedule/add', 'Dokter::saveSchedule');
        $routes->post('schedule/delete/(:segment)', 'Dokter::deleteSchedule/$1');
        $routes->get("profile", "Dokter::profile");
        $routes->post("update-profile", "Dokter::updateProfile");
        $routes->get('schedule/api/(:segment)', 'JadwalDokterController::getScheduleApi/$1');
    }
);


// dokter Routes (protected)
$routes->group(
    "petugas_lab",
    ["namespace" => "App\Controllers", "filter" => "auth:petugas_lab"],
    function ($routes) {
        $routes->get("dashboard", "LaboratoriumController::dashboard");
        $routes->get("orders", "LaboratoriumController::orders");
        $routes->get("profile", "LaboratoriumController::profile");
        $routes->post('profile/update', 'LaboratoriumController::updateProfile');
        $routes->get('profile/password', 'LaboratoriumController::changePassword');
        $routes->post('profile/password', 'LaboratoriumController::updatePassword');
        $routes->post('take_order/(:num)', 'LaboratoriumController::takeOrder/$1');
        $routes->get('order_work/(:num)', 'LaboratoriumController::orderWork/$1');
        $routes->post('submit_work/(:num)', 'LaboratoriumController::submitWork/$1');
        $routes->get('download_report/(:num)', 'LaboratoriumController::downloadReport/$1');
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
            "Admin::pendingDokterVerifications"
        );
        $routes->get("verify-dokter/(:segment)", "Admin::verifyDokter/$1");
        $routes->post(
            "process-dokter-verification",
            "Admin::processDokterVerification"
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
