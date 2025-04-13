<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class FaceRecognition extends BaseController
{
    public function uploadForm()
    {
        return view("upload_picture");
    }

    public function upload()
    {
        $file = $this->request->getFile("picture_file");

        // Validate file
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                "match" => false,
                "error" => "Invalid file upload!",
            ]);
        }

        // Secure filename & move to uploads
        $newName = $file->getRandomName(); // Prevents overwrites
        $file->move(WRITEPATH . "uploads", $newName);
        $filePath = WRITEPATH . "uploads/" . $newName;

        // Call Flask API
        $response = $this->callFaceRecognitionAPI($filePath);

        // Handle API failure
        if (!$response) {
            return redirect()
                ->back()
                ->with("error", "Face recognition service failed!");
        }

        // Decode response
        $data = json_decode($response, true);

        // Pass to view
        return $this->response->setJSON($data);
    }

    /**
     * Calls Flask Face Recognition API
     */
    private function callFaceRecognitionAPI(string $filePath)
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "http://localhost:5000/recognize",
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                "file" => new \CURLFile($filePath),
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10, // 10s timeout
            CURLOPT_HTTPHEADER => ["Accept: application/json"],
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            log_message("error", "CURL Error: $error");
            return false;
        }

        return $response;
    }
}
