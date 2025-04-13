// Access the camera
const video = document.getElementById('camera');
const canvas = document.getElementById('canvas');
const captureBtn = document.getElementById('capture');
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

// Capture the photo
captureBtn.addEventListener('click', () => {
    // Draw the current video frame on canvas
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

    // Convert to image (JPEG)
    const imageData = canvas.toDataURL('image/jpeg');

    // Send to API (Step 2)
    recognizeFace(imageData);
});

// Start the camera when page loads
startCamera();
