<!DOCTYPE html>
<html>
<head>
    <title>Webcam Face Recognition</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        #camera { width: 400px; border: 2px solid #333; margin: 20px; }
        button { padding: 10px 20px; background: #4CAF50; color: white; border: none; cursor: pointer; }
        #result { margin-top: 20px; font-weight: bold; }
        #uploadForm { display: none; } /* Hide the form (we submit it via JS) */
    </style>
</head>
<body>
    <h1>👩💻 Face Recognition Login</h1>
    
    <!-- Webcam Stream -->
    <video id="camera" autoplay playsinline></video>
    <br>
    <button id="capture">Capture Photo</button>
    
    <!-- Hidden Canvas (stores captured image) -->
    <canvas id="canvas" hidden></canvas>
    
    <!-- Hidden Form (submits to CodeIgniter) -->
    <form id="uploadForm" action="<?= base_url('face_upload/upload') ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="picture_file" id="pictureFile">
    </form>
    
    <!-- Result Display -->
    <div id="result"></div>

    <script>
        // Access the camera
        const video = document.getElementById('camera');
        const canvas = document.getElementById('canvas');
        const captureBtn = document.getElementById('capture');
        const uploadForm = document.getElementById('uploadForm');
        const pictureFileInput = document.getElementById('pictureFile');
        const resultDiv = document.getElementById('result');

        // Start the webcam
        async function startCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                video.srcObject = stream;
            } catch (err) {
                resultDiv.textContent = "Error: " + err.message;
            }
        }

        // Capture the photo and submit to CodeIgniter
        captureBtn.addEventListener('click', () => {
            // Draw the current video frame on canvas
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

            // Convert canvas to Blob (for file upload)
            canvas.toBlob((blob) => {
                // Create a File object (required for FormData)
                const file = new File([blob], "webcam_capture.jpeg", { type: "image/jpeg" });

                // Create FormData and append the file
                const formData = new FormData();
                formData.append("picture_file", file);

                // Submit to CodeIgniter via fetch()
                resultDiv.textContent = "Sending to server...";
                
                fetch(uploadForm.action, {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.match) {
                        resultDiv.innerHTML = `✅ <strong>Welcome, ${data.name}!</strong>`;
                    } else {
                        resultDiv.innerHTML = "❌ No match found!";
                    }
                })
                .catch(error => {
                    resultDiv.textContent = "Error: " + error.message;
                });
            }, 'image/jpeg', 0.9); // 90% quality JPEG
        });

        // Start the camera when page loads
        startCamera();
    </script>
</body>
</html>
