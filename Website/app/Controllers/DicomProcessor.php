<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class DicomProcessor extends BaseController
{
    // Render the upload form
    public function uploadForm()
    {
        return view('upload_form');
    }

    // Handle file upload
    public function upload()
    {
        // Handle file upload
        $file = $this->request->getFile('dicom_file');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $file->move(WRITEPATH . 'uploads/'); // Add trailing slash
            $filePath = WRITEPATH . 'uploads/' . $file->getName();

            // Call Flask API
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://localhost:5000/process-dicom');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'file' => new \CURLFile($filePath)
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            // Decode JSON response
            $data = json_decode($response, true);

            // Pass data to view
            return view('dicom_result', $data);
        } else {
            // Handle errors
            return "File upload failed. Check file selection or permissions.";
        }
    }
}
?>
