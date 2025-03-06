document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const startCameraBtn = document.getElementById('startCamera');
    const capturePhotoBtn = document.getElementById('capturePhoto');
    const retakePhotoBtn = document.getElementById('retakePhoto');
    const videoElement = document.getElementById('videoElement');
    const capturedPhoto = document.getElementById('capturedPhoto');
    const photoPlaceholder = document.getElementById('photoPlaceholder');
    const canvas = document.getElementById('canvas');
    const photoDataInput = document.getElementById('photoData');
    let stream = null;

    // Check if elements exist (might be viewing page, not form)
    if (!startCameraBtn || !canvas) return;

    // Set canvas dimensions to match expected photo size
    canvas.width = 200;
    canvas.height = 200;
    const ctx = canvas.getContext('2d');

    // Start camera
    startCameraBtn.addEventListener('click', async function() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { 
                    width: { ideal: 200 },
                    height: { ideal: 200 },
                    facingMode: 'user'
                } 
            });
            videoElement.srcObject = stream;
            videoElement.style.display = 'block';
            photoPlaceholder.style.display = 'none';
            capturedPhoto.style.display = 'none';
            startCameraBtn.style.display = 'none';
            capturePhotoBtn.style.display = 'inline-block';
        } catch (err) {
            console.error("Error accessing the camera: ", err);
            alert("Could not access the camera. Please check your camera permissions.");
        }
    });

    // Capture photo
    capturePhotoBtn.addEventListener('click', function() {
        // Draw the video frame to the canvas
        ctx.drawImage(videoElement, 0, 0, canvas.width, canvas.height);
        
        // Convert the canvas to data URL and set it as the image source
        const imageDataURL = canvas.toDataURL('image/png');
        capturedPhoto.src = imageDataURL;
        photoDataInput.value = imageDataURL; // Store the image data for form submission
        
        // Stop the camera stream
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
        
        // Update UI
        videoElement.style.display = 'none';
        capturedPhoto.style.display = 'block';
        photoPlaceholder.style.display = 'none';
        capturePhotoBtn.style.display = 'none';
        retakePhotoBtn.style.display = 'inline-block';
    });

    // Retake photo
    retakePhotoBtn.addEventListener('click', function() {
        // Reset the photo data
        photoDataInput.value = '';
        retakePhotoBtn.style.display = 'none';
        
        // Re-start the camera
        startCameraBtn.click();
    });
});