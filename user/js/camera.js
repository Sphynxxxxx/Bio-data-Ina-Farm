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

    // Check if elements exist
    if (!startCameraBtn || !canvas) {
        console.log("Camera elements not found");
        return;
    }

    // Set canvas dimensions to match expected photo size
    canvas.width = 200;
    canvas.height = 200;
    const ctx = canvas.getContext('2d');

    // Start camera
    startCameraBtn.addEventListener('click', async function() {
        try {
            console.log("Attempting to access camera...");
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
            console.log("Camera started successfully");
        } catch (err) {
            console.error("Error accessing the camera: ", err);
            alert("Could not access the camera. Please check your camera permissions.");
        }
    });

    // Capture photo
    capturePhotoBtn.addEventListener('click', function() {
        try {
            console.log("Capturing photo...");
            
            // Clear canvas and set white background
            ctx.fillStyle = '#FFFFFF';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            console.log("White background applied");
            
            // Get video dimensions
            const videoWidth = videoElement.videoWidth;
            const videoHeight = videoElement.videoHeight;
            console.log("Video dimensions:", videoWidth, "x", videoHeight);
            
            // Calculate dimensions for ID photo (centered, proper aspect ratio)
            let drawWidth, drawHeight, startX, startY;
            const ratio = videoWidth / videoHeight;
            
            if (ratio > 1) {
                // Video is wider than tall - fit to height and center horizontally
                drawHeight = canvas.height;
                drawWidth = videoWidth * (canvas.height / videoHeight);
                startX = (canvas.width - drawWidth) / 2;
                startY = 0;
            } else {
                // Video is taller than wide - fit to width and center vertically
                drawWidth = canvas.width;
                drawHeight = videoHeight * (canvas.width / videoWidth);
                startX = 0;
                startY = (canvas.height - drawHeight) / 2;
            }
            
            // Draw video onto canvas with the white background
            ctx.drawImage(videoElement, startX, startY, drawWidth, drawHeight);
            console.log("Video drawn on canvas at", startX, startY, drawWidth, drawHeight);
            
            // Add a thin gray border for ID photo look
            ctx.strokeStyle = '#DDDDDD';
            ctx.lineWidth = 2;
            ctx.strokeRect(0, 0, canvas.width, canvas.height);
            
            // Convert the canvas to data URL and set it as the image source
            const imageDataURL = canvas.toDataURL('image/png', 1.0);
            console.log("Image captured and converted to data URL");
            
            // Set the image and store data for form submission
            capturedPhoto.src = imageDataURL;
            photoDataInput.value = imageDataURL;
            
            // Add white background to the image display element
            capturedPhoto.style.backgroundColor = 'white';
            
            // Stop the camera stream
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                console.log("Camera stream stopped");
            }
            
            // Update UI
            videoElement.style.display = 'none';
            capturedPhoto.style.display = 'block';
            photoPlaceholder.style.display = 'none';
            capturePhotoBtn.style.display = 'none';
            retakePhotoBtn.style.display = 'inline-block';
            
            console.log("Photo capture complete");
        } catch (err) {
            console.error("Error capturing photo:", err);
            alert("Error capturing photo. Please try again.");
        }
    });

    // Retake photo
    retakePhotoBtn.addEventListener('click', function() {
        console.log("Retaking photo...");
        // Reset the photo data
        photoDataInput.value = '';
        retakePhotoBtn.style.display = 'none';
        
        // Re-start the camera
        startCameraBtn.click();
    });
    
    // Ensure clean exit by stopping camera when leaving the page
    window.addEventListener('beforeunload', function() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            console.log("Camera stream stopped on page unload");
        }
    });
    
    console.log("Camera functionality initialized");
});