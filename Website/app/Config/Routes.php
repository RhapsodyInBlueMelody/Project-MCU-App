<?php
use CodeIgniter\Router\RouteCollection;
use App\Controllers\DicomProcessor;
use App\Controllers\FaceRecognition;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('login', function($routes){
    $routes->get('patient', 'Login::patient');
    $routes->get('doctor', 'Login::doctor');

   $routes->post('patient/auth', 'Login::attemptPatientLogin');
   $routes->post('doctor/auth', 'Login::attemptDoctorLogin');
});

$routes->group('patient', function($routes){
    $routes->get('/patient/dashboard', 'Patient::dashboard');
});

$routes->group('doctor', function($routes){
    $routes->get('/doctor/dashboard', 'Doctor::dashboard');
});


$routes->get('test-db', function() {
    try {
        db_connect()->query('SELECT 1');
        return 'Database connection successful! 🎉';
    } catch (\Exception $e) {
        return 'Connection failed: ' . $e->getMessage();
    }
});

// Render the upload form
$routes->get('/face_upload', [FaceRecognition::class, 'uploadForm']);
$routes->post('/face_upload/upload', [FaceRecognition::class, 'upload']);

// Handle file upload
$routes->get('/dicom-processor', [DicomProcessor::class, 'uploadForm']);
$routes->post('/dicom-processor/upload', [DicomProcessor::class, 'upload']);
?>
