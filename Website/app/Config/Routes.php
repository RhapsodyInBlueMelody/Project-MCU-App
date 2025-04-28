<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\DicomProcessor;
use App\Controllers\FaceRecognition;
use App\Controllers\Home;

/**
 * @var RouteCollection $routes
 */


$routes->get("/", "Home::index");

$routes->group("rumah-sakit", function ($routes) {
    $routes->get("cabang-jakarta", "Home::cabangJakarta"); // untuk halaman Cabang Jakarta
    $routes->get("cabang-bandung", "Home::cabangBandung"); // untuk halaman Cabang Bandung
    $routes->get("cabang-surabaya", "Home::cabangSurabaya"); // untuk halaman Cabang Surabaya
    $routes->get("fasilitas-kami", "Home::fasilitasKami"); // Dipindah ke sini
    $routes->get("cari-dokter", "Home::cariDokter"); // Dipindah ke sini
});

$routes->group("auth", function ($routes) {
    $routes->get("login", "Auth::login");
    $routes->post("authenticate", "Auth::authenticate");
    $routes->get("register", "Auth::register");
    $routes->post("save", "Auth::save");
    $routes->get("logout", "Auth::logout");
});

$routes->group("login", function ($routes) {
    $routes->get("patient", "Login::patient");
    $routes->get("doctor", "Login::doctor");

    $routes->post("patient/auth", "Login::attemptPatientLogin");
    $routes->post("doctor/auth", "Login::attemptDoctorLogin");
});

$routes->group("patient", function ($routes) {
    $routes->get("dashboard", "Patient::dashboard");
    $routes->get("beranda", "Patient::beranda");
    $routes->get("pendaftaran", "Patient::pendaftaran");
    $routes->get("jadwal-pemeriksaan", "Patient::jadwalPemeriksaan");
    $routes->get("riwayat-medical-checkup", "Patient::riwayatMedicalCheckup");
});

$routes->group("doctor", function ($routes) {
    $routes->get("dashboard", "Doctor::dashboard");
});

$routes->get("test-db", function () {
    try {
        db_connect()->query("SELECT 1");
        return "Database connection successful! 🎉";
    } catch (\Exception $e) {
        return "Connection failed: " . $e->getMessage();
    }
});

// Render the upload form
$routes->get("/face_upload", [FaceRecognition::class, "uploadForm"]);
$routes->post("/face_upload/upload", [FaceRecognition::class, "upload"]);

// Handle file upload
$routes->get("/dicom-processor", [DicomProcessor::class, "uploadForm"]);
$routes->post("/dicom-processor/upload", [DicomProcessor::class, "upload"]);
