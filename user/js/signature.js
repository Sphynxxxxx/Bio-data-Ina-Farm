document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const signatureModal = document.getElementById('signatureModal');
    const openSignatureBtn = document.getElementById('openSignatureModal');
    const closeModalBtn = document.querySelector('.close-modal');
    const clearCanvasBtn = document.getElementById('clearCanvas');
    const saveSignatureBtn = document.getElementById('saveSignature');
    const clearSignatureBtn = document.getElementById('clearSignature');
    const signatureCanvas = document.getElementById('signatureCanvas');
    const signaturePlaceholder = document.getElementById('signaturePlaceholder');
    const capturedSignature = document.getElementById('capturedSignature');
    const signatureDataInput = document.getElementById('signatureData');
    
    // Check if all elements exist
    if (!signatureModal || !openSignatureBtn || !signatureCanvas) {
        console.error('Signature elements not found');
        return;
    }
    
    // Canvas context
    const ctx = signatureCanvas.getContext('2d');
    let isDrawing = false;
    let lastX = 0;
    let lastY = 0;
    
    // Set canvas dimensions
    function resizeCanvas() {
        // Set canvas dimensions based on its parent container
        const parentWidth = signatureCanvas.parentElement.clientWidth - 40; // Account for padding
        signatureCanvas.width = parentWidth;
        signatureCanvas.height = 200;
        
        // Set line properties
        ctx.lineWidth = 2;
        ctx.lineJoin = 'round';
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#000';
    }
    
    // Drawing functions
    function startDrawing(e) {
        isDrawing = true;
        [lastX, lastY] = getCoordinates(e);
    }
    
    function draw(e) {
        if (!isDrawing) return;
        e.preventDefault();
        
        const [currentX, currentY] = getCoordinates(e);
        
        ctx.beginPath();
        ctx.moveTo(lastX, lastY);
        ctx.lineTo(currentX, currentY);
        ctx.stroke();
        
        [lastX, lastY] = [currentX, currentY];
    }
    
    function stopDrawing() {
        isDrawing = false;
    }
    
    // Get coordinates whether mouse or touch
    function getCoordinates(e) {
        let x, y;
        
        if (e.type.includes('touch')) {
            x = e.touches[0].clientX - signatureCanvas.getBoundingClientRect().left;
            y = e.touches[0].clientY - signatureCanvas.getBoundingClientRect().top;
        } else {
            x = e.offsetX;
            y = e.offsetY;
        }
        
        return [x, y];
    }
    
    // Clear canvas
    function clearCanvas() {
        ctx.clearRect(0, 0, signatureCanvas.width, signatureCanvas.height);
    }
    
    // Save signature as image
    function saveSignature() {
        // Check if canvas is empty
        const imageData = ctx.getImageData(0, 0, signatureCanvas.width, signatureCanvas.height);
        const data = imageData.data;
        let isEmpty = true;
        
        // Check if all pixel values are 0 (transparent)
        for (let i = 0; i < data.length; i += 4) {
            if (data[i+3] !== 0) {
                isEmpty = false;
                break;
            }
        }
        
        if (isEmpty) {
            alert('Please draw your signature before saving.');
            return;
        }
        
        // Get data URL of the signature
        const dataURL = signatureCanvas.toDataURL('image/png');
        
        // Update hidden input with signature data
        signatureDataInput.value = dataURL;
        
        // Show the signature in the box
        signaturePlaceholder.style.display = 'none';
        capturedSignature.src = dataURL;
        capturedSignature.style.display = 'block';
        
        // Show clear button
        clearSignatureBtn.style.display = 'inline-block';
        
        // Close the modal
        signatureModal.style.display = 'none';
    }
    
    // Clear the signature in the form
    function clearSignatureFromForm() {
        signaturePlaceholder.style.display = 'block';
        capturedSignature.style.display = 'none';
        signatureDataInput.value = '';
        clearSignatureBtn.style.display = 'none';
    }
    
    // Event listeners
    openSignatureBtn.addEventListener('click', function() {
        signatureModal.style.display = 'block';
        resizeCanvas();
        clearCanvas();
    });
    
    closeModalBtn.addEventListener('click', function() {
        signatureModal.style.display = 'none';
    });
    
    clearCanvasBtn.addEventListener('click', clearCanvas);
    saveSignatureBtn.addEventListener('click', saveSignature);
    clearSignatureBtn.addEventListener('click', clearSignatureFromForm);
    
    // Canvas drawing events - mouse
    signatureCanvas.addEventListener('mousedown', startDrawing);
    signatureCanvas.addEventListener('mousemove', draw);
    signatureCanvas.addEventListener('mouseup', stopDrawing);
    signatureCanvas.addEventListener('mouseout', stopDrawing);
    
    // Canvas drawing events - touch
    signatureCanvas.addEventListener('touchstart', startDrawing);
    signatureCanvas.addEventListener('touchmove', draw);
    signatureCanvas.addEventListener('touchend', stopDrawing);
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target === signatureModal) {
            signatureModal.style.display = 'none';
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (signatureModal.style.display === 'block') {
            resizeCanvas();
        }
    });
});