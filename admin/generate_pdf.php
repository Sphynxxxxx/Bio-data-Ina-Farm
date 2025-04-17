<?php
require_once __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php';
session_start();
require_once __DIR__ . '/../connections/config.php';

if (!isset($_SESSION['admin'])) {
    header('Location: admin.php');
    exit();
}
$userId = isset($_GET['id']) ? intval($_GET['id']) : null;

class MYPDF extends TCPDF {
    public function Header() {
        if ($this->PageNo() == 1) {

        // Set background color for header
        $this->SetFillColor(255, 255, 255);
        $this->Rect(0, 0, $this->getPageWidth(), 35, 'F');
        
        // Logo
        $this->Image('../admin/assets/tesda_logo.png', 15, 5, 17);
        
        // Header Text
        $this->SetFont('times', 'B', 12);
        $this->SetXY(35, 10);
        $this->Cell(0, 6, 'Technical Education and Skills Development Authority', 0, 1, 'L');
        
        $this->SetFont('times', '', 10);
        $this->SetXY(35, 16);
        $this->Cell(0, 6, 'Pangasiwaan sa Edukasyong Teknikal at Pagpapaunlad ng Kasanayan', 0, 1, 'L');
        
        // Form Title (right aligned)
        $this->SetFont('Times', 'B', 9);
        $this->SetXY(145, 20);
        $this->Cell(50, 6, 'NMIS FORM -01A', 0, 1, 'R');
        $this->SetFont('Times', '', 8);
        $this->SetXY(145, 25);
        $this->Cell(50, 6, '(For TPIS)', 0, 1, 'R');
        
        // Double line border
        $this->Line(15, 25, 195, 25);
        $this->Line(15, 26, 195, 26);
    }
}
    
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('Times', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('NMIS System');
$pdf->SetAuthor('TESDA');
$pdf->SetTitle('NMIS Manpower Profile');

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(15, 35, 15);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Add a page
$pdf->AddPage();

// Get data from database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // If userId is provided, fetch that specific user, otherwise get the latest
    if ($userId) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
    } else {
        $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC LIMIT 1");
    }
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // If no user found, display an error
    if (!$user) {
        die("User not found");
    }
    
    // fetch from education
    $eduStmt = $pdo->prepare("SELECT * FROM education WHERE user_id = ? ORDER BY year_from");
    $eduStmt->execute([$user['id']]);
    $education = $eduStmt->fetchAll(PDO::FETCH_ASSOC);

    // fetch from work_experience
    $workStmt = $pdo->prepare("SELECT * FROM work_experience WHERE user_id = ?");
    $workStmt->execute([$user['id']]);
    $work_experience = $workStmt->fetchAll(PDO::FETCH_ASSOC);

    // fetch from training_seminar
    $trainingStmt = $pdo->prepare("SELECT * FROM training_seminar WHERE user_id = ?");
    $trainingStmt->execute([$user['id']]);
    $training_seminar = $trainingStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // fetch from license_examination
    $licenseStmt = $pdo->prepare("SELECT * FROM license_examination WHERE user_id = ?");
    $licenseStmt->execute([$user['id']]);
    $license_examination = $licenseStmt->fetchAll(PDO::FETCH_ASSOC);

    // fetch from competency_assessment
    $competencyStmt = $pdo->prepare("SELECT * FROM competency_assessment WHERE user_id = ?");
    $competencyStmt->execute([$user['id']]);
    $competency_assessment = $competencyStmt->fetchAll(PDO::FETCH_ASSOC);

    // fetch from family background
    $familyStmt = $pdo->prepare("SELECT * FROM family_background WHERE user_id = ?");
    $familyStmt->execute([$user['id']]);
    $family = $familyStmt->fetch(PDO::FETCH_ASSOC);

    // fetch from user photo
    $photoStmt = $pdo->prepare("SELECT photo_data FROM user_photos WHERE user_id = ?");
    $photoStmt->execute([$user['id']]);
    $photo = $photoStmt->fetch(PDO::FETCH_ASSOC);

    // fetch from user signature
    $signatureStmt = $pdo->prepare("SELECT signature_data FROM user_signatures WHERE user_id = ?");
    $signatureStmt->execute([$user['id']]);
    $signature = $signatureStmt->fetch(PDO::FETCH_ASSOC);

    if (!$family) {
        $family = [
            'spouse_name' => '',
            'spouse_educational_attainment' => '',
            'spouse_occupation' => '',
            'spouse_monthly_income' => '',
            'father_name' => '',
            'father_educational_attainment' => '',
            'father_occupation' => '',
            'father_monthly_income' => '',
            'mother_name' => '',
            'mother_educational_attainment' => '',
            'mother_occupation' => '',
            'mother_monthly_income' => '',
            'guardian_name' => '',
            'guardian_educational_attainment' => '',
            'guardian_occupation' => '',
            'guardian_monthly_income' => '',
            'dependents' => '',
            'dependents_age' => ''
        ];
    }

} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}


// Title with red color
$pdf->SetXY(15, 30);
$pdf->SetFont('Times', 'B', 20);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 10, 'MANPOWER PROFILE', 0, 1, 'C');
$pdf->SetTextColor(0, 0, 0);

//line below
$pdf->Line(15, 40, 195, 40); 
$pdf->Line(15, 40.5, 195, 40.5);


// Add signature box
$pdf->SetXY(30, 68);
$pdf->SetFont('Times', '', 12);
$pdf->Cell(140, 10, 'Signature', 0, 0, 'C');
$pdf->Line(65, 70, 138, 70); 

// Display signature if available
if (!empty($signature) && !empty($signature['signature_data'])) {
    $sigData = $signature['signature_data'];
    
    if (strpos($sigData, 'data:') === 0) {
        $sigData = preg_replace('/^data:image\/\w+;base64,/', '', $sigData);
    }
    
    $sigData = base64_decode($sigData);
    
    // Create a temporary file
    $tempSigFile = tempnam(sys_get_temp_dir(), 'pdf_sig');
    file_put_contents($tempSigFile, $sigData);
    
    // Add the signature image to the PDF, positioning it above the line
    $pdf->Image($tempSigFile, 65, 55, 73, 15, '', '', '', false, 300, '', false, false, 0);
    
    unlink($tempSigFile);
}


$pdf->Rect(157, 42, 38, 40); 

// Add ID Photo if available
if (!empty($photo) && !empty($photo['photo_data'])) {
    $imgData = $photo['photo_data'];
    
    if (strpos($imgData, 'data:') === 0) {
        $imgData = preg_replace('/^data:image\/\w+;base64,/', '', $imgData);
    }
    
    $imgData = base64_decode($imgData);
    
    // Create a temporary file
    $tempFile = tempnam(sys_get_temp_dir(), 'pdf_img');
    file_put_contents($tempFile, $imgData);
    
    // Add the image to the PDF, positioning it within the rectangle
    $pdf->Image($tempFile, 157, 42, 38, 40, '', '', '', false, 300, '', false, false, 0);
    
    // Clean up temporary file
    unlink($tempFile);
} else {
    $pdf->SetFont('Times', '', 8);
    $pdf->Text(167.5, 60, 'ID PICTURE');
    $pdf->Text(167, 65, '(Passport Size)');
}

// Section 1 - TESDA Information
$pdf->SetXY(15, 84);
$pdf->SetFont('Times', 'B', 12);
$pdf->SetFillColor(177, 176, 176); 
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '1. To be accomplished by TESDA', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

// NMIS Code and Entry Date
$pdf->SetXY(15, 93);
$pdf->SetFont('Times', 'B', 10);
$y = $pdf->GetY();
$pdf->Cell(40, 6, 'NMIS Manpower Code:',  0, 0);
$pdf->SetFont('Times', '', 5);
$pdf->Cell(40, 6, $user['nmis_code'], 1, 0);

$pdf->SetX(105);
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(30, 6, 'NMIS Entry Date:', 0, 0);
$pdf->SetFont('Times', '', 5);
$pdf->Cell(60, 6, $user['nmis_entry'], 1, 0);
$pdf->Ln(5);


// Personal Information Section
$pdf->Ln(5);
$pdf->SetFont('Times', 'B', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '2. Manpower Profile', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

// Name Fields
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(20, 10, 'Name:', 0, 0);

$pdf->SetFont('Times', '', 5);
$pdf->SetXY(50, 113);
$pdf->Cell(49, 6, $user['lastname'], 1, 0, 'C');
$pdf->Cell(48, 6, $user['firstname'], 1, 0, 'C'); 
$pdf->Cell(48, 6, $user['middlename'], 1, 1, 'C'); 

// Move down before adding labels
$pdf->Ln(2);

// Set new X position for labels to align with the boxes
$pdf->SetXY(50, 119); 
$pdf->SetFont('Times', '', 8);
$pdf->Cell(49, 5, 'Last', 0, 0, 'C'); 
$pdf->Cell(48, 5, 'First', 0, 0, 'C'); 
$pdf->Cell(48, 5, 'Middle', 0, 1, 'C'); 


// Address
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(30, 10, 'Mailing Address:', 0, 0);

$pdf->SetFont('Times', '', 5);
$pdf->SetXY(50, 127);
$pdf->Cell(49, 5, $user['address_street'], 1, 0, 'C');
$pdf->Cell(48, 5, $user['address_barangay'], 1, 0, 'C');
$pdf->Cell(48, 5, $user['address_district'], 1, 1, 'C'); 

$pdf->SetXY(50, 132);
$pdf->SetFont('Times', '', 8);
$pdf->Cell(49, 5, 'Number, Street', 0, 0, 'C'); 
$pdf->Cell(48, 5, 'Barangay', 0, 0, 'C'); 
$pdf->Cell(48, 5, 'Congressional District', 0, 1, 'C');

// City, Province, Region
$pdf->SetFont('Times', '', 5);
$pdf->SetXY(50, 140);
$pdf->Cell(49, 5, $user['address_city'], 1, 0, 'C');
$pdf->Cell(48, 5, $user['address_province'], 1, 0, 'C');
$pdf->Cell(48, 5, $user['address_region'], 1, 1, 'C');

$pdf->SetXY(50, 145);
$pdf->SetFont('Times', '', 8);
$pdf->Cell(49, 5, 'City/Municipality', 0, 0, 'C');
$pdf->Cell(48, 5, 'Province', 0, 0, 'C');
$pdf->Cell(48, 5, 'Region', 0, 1, 'C');

// Personal Details Section
$pdf->Ln(5);
$pdf->SetFont('Times', '', 10);

// Create two columns for details
$pdf->SetX(15);
$leftColumn = 95;
$rightColumn = 95;


// Set starting position
$startX = 15;
$startY = $pdf->GetY();
$boxHeight = 40; 
$boxWidth = 20;  

// Draw rectangle for "Sex"
$pdf->Rect($startX, $startY, $boxWidth, $boxHeight);
$pdf->SetXY($startX, $startY);  // Added explicit SetXY position
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(25, 8, 'Sex', 0, 1);
$pdf->SetTextColor(0, 0, 0);

// Draw Sex checkboxes
$pdf->SetFont('Times', '', 10);
$sexes = ['Male', 'Female'];
$yPosition = $startY + 8;
foreach ($sexes as $sex) {
    $pdf->SetXY($startX + 3, $yPosition);
    $pdf->Cell(5, 5, $user['sex'] == $sex ? 'X' : '', 1, 0, 'C'); // Checkbox
    $pdf->Cell(20, 5, $sex, 0, 1);
    $yPosition += 8;
}

// Draw rectangle for "Civil Status" immediately after Sex box with no gap
$civilStatusX = $startX + $boxWidth; // This places it right after the Sex box
$pdf->Rect($civilStatusX, $startY, $boxWidth + 5, $boxHeight); // Added +5 to width to fit "Civil Status" text
$pdf->SetXY($civilStatusX, $startY);
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(35, 8, 'Civil Status', 0, 1);
$pdf->SetTextColor(0, 0, 0);

// Draw Civil Status checkboxes
$pdf->SetFont('Times', '', 10);
$statuses = ['Single', 'Married', 'Widow/er', 'Separated'];
$yPosition = $startY + 8;
foreach ($statuses as $status) {
    $pdf->SetXY($civilStatusX + 3, $yPosition);
    $pdf->Cell(5, 5, $user['civil_status'] == $status ? 'X' : '', 1, 0, 'C'); // Checkbox
    $pdf->Cell(25, 5, $status, 0, 1);
    $yPosition += 8;
}

// Define the positions for all boxes
$startX = 15;
$boxHeight = 40; 

// Define box widths
$sexBoxWidth = 20;
$civilStatusBoxWidth = 25;
$contactBoxWidth = $boxWidth + 40; // Explicitly define the contact box width

// Calculate the starting position for Contact Numbers box
$contactX = $startX + $sexBoxWidth + $civilStatusBoxWidth; // Position it right after Civil Status

// Draw rectangle for "Contact Number(s)"
$pdf->Rect($contactX, $startY, $contactBoxWidth, $boxHeight);
$pdf->SetXY($contactX, $startY);
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(40, 8, 'Contact Number(s)', 0, 1);
$pdf->SetTextColor(0, 0, 0);

// Move cursor below the title
$pdf->SetFont('Times', '', 10);
$yPosition = $startY + 8;

// Telephone Number
$pdf->SetXY($contactX + 1, $yPosition);
$pdf->SetFont('Times', '', 10); 
$pdf->Cell(15, 5, 'Tel. No.:', 0, 0);
$pdf->SetFont('Times', '', 9); 
$pdf->Cell(38, 5, $user['tel_number'], 'B', 1);

// Move down for Cellular Number
$yPosition += 8;
$pdf->SetXY($contactX + 1, $yPosition);
$pdf->SetFont('Times', '', 10); 
$pdf->Cell(15, 5, 'Cellular:', 0, 0);
$pdf->SetFont('Times', '', 9); 
$pdf->Cell(38, 5, $user['contact_number'], 'B', 1);

// Move down for Email
$yPosition += 8;
$pdf->SetXY($contactX + 1, $yPosition);
$pdf->SetFont('Times', '', 10); 
$pdf->Cell(15, 5, 'Email:', 0, 0);
$pdf->SetFont('Times', '', 9); 
$pdf->Cell(38, 5, $user['email'], 'B', 1);

// Move down for Fax
$yPosition += 8;
$pdf->SetXY($contactX + 1, $yPosition);
$pdf->SetFont('Times', '', 10); 
$pdf->Cell(15, 5, 'Fax:', 0, 0);
$pdf->SetFont('Times', '', 9); 
$pdf->Cell(38, 5, $user['fax_number'], 'B', 1); 

// Position the Employment Type box immediately after Contact Numbers box with no gap
$employmentTypeX = $contactX + $contactBoxWidth;

// Draw rectangle for "Employment Type"
$pdf->Rect($employmentTypeX, $startY, $boxWidth + 20, $boxHeight + 8); 
$pdf->SetXY($employmentTypeX, $startY);
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(40, 8, '2.6 Employment Type', 0, 1);
$pdf->SetTextColor(0, 0, 0);

// Draw Employment Type checkboxes
$pdf->SetFont('Times', '', 10);
$employment_types = ['Employed', 'Self-employed', 'Unemployed', 'Other'];
$yPosition = $startY + 8;
foreach ($employment_types as $type) {
    if ($type == 'Other') {
        // Add "Other than above" label
        $pdf->SetXY($employmentTypeX + 3, $yPosition);
        $pdf->Cell(35, 3, 'Other than above', 0, 1);
        $yPosition += 5;
        
        // Draw checkbox with "pls. specify" label
        $pdf->SetXY($employmentTypeX + 3, $yPosition);
        $pdf->Cell(5, 5, $user['employment_type'] == $type ? 'X' : '', 1, 0, 'C'); // Checkbox
        $pdf->Cell(30, 5, 'pls. specify:', 0, 1);
        
        // If "Other" is selected, display the specified text below
        if ($user['employment_type'] == 'Other' && !empty($user['employment_type_other'])) {
            $pdf->SetXY($employmentTypeX + 8, $yPosition + 5);
            $pdf->Cell(50, 5, $user['employment_type_other'], 'B', 1); // Added underline
        }
    } else {
        // Normal options
        $pdf->SetXY($employmentTypeX + 3, $yPosition);
        $pdf->Cell(5, 5, $user['employment_type'] == $type ? 'X' : '', 1, 0, 'C'); // Checkbox
        $pdf->Cell(30, 5, $type, 0, 1);
    }
    
    $yPosition += 8;
}

// Define the width of the Employment Type box
$employmentTypeWidth = $boxWidth + 20;

// Calculate position for Employment Status - right after Employment Type with no gap
$employmentStatusX = $employmentTypeX + $employmentTypeWidth;

// Draw rectangle for "Employment Status" - make it taller to fit more options
$pdf->Rect($employmentStatusX, $startY, $boxWidth + 20, $boxHeight + 30); // Increased height
$pdf->SetXY($employmentStatusX, $startY);
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(40, 8, '2.7 Employment Status', 0, 1);
$pdf->SetTextColor(0, 0, 0);

// Draw Employment Status checkboxes with expanded list
$pdf->SetFont('Times', '', 10);
$employment_statuses = [
    'Casual',
    'Contractual',
    'Job Order',
    'Temporary',
    'Probationary',
    'Regular',
    'Permanent',
    'Trainee/OJT'
];

$yPosition = $startY + 8;
foreach ($employment_statuses as $index => $status) {
    // Add a divider before Trainee/OJT
    if ($status == 'Trainee/OJT') {
        $pdf->SetXY($employmentStatusX + 3, $yPosition);
        $pdf->Cell(35, 3, 'If Student', 0, 1);
        $yPosition += 5;
    }
    
    $pdf->SetXY($employmentStatusX + 3, $yPosition);
    $pdf->Cell(5, 5, $user['employment_status'] == $status ? 'X' : '', 1, 0, 'C'); // Checkbox
    $pdf->Cell(30, 5, $status, 0, 1);
    $yPosition += 7; // Slightly reduced spacing to fit all options
}




// 3. Personal Information
$pdf->Ln(15);
$pdf->SetFont('Times', '', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '3. Personal Information', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1);
$x = $pdf->GetX();
$y = $pdf->GetY();
$width = 180;  
$height = 40;

// Draw the rectangle 
$pdf->SetFont('Times', '', 5);
$pdf->Rect($x, $y, $width, $height);
$pdf->Ln(3);

$pdf->Cell(30, 5, '3.1 Birthdate:', 0, 0);
$pdf->Cell(40, 5, $user['birthdate'], 'B', 1);
$pdf->Ln(1);

$pdf->Cell(30, 5, '3.2 Birthplace:', 0, 0);
$pdf->Cell(40, 5, $user['birth_place'], 'B', 1);
$pdf->Ln(1);

$pdf->Cell(30, 5, '3.3 Citizenship:', 0, 0);
$pdf->Cell(40, 5, $user['citizenship'], 'B', 1);
$pdf->Ln(1);

$pdf->Cell(30, 5, '3.4 Religion:', 0, 0);
$pdf->Cell(40, 5, $user['religion'], 'B', 1);
$pdf->Ln(1);

$pdf->Cell(30, 5, '3.5 Height:', 0, 0);
$pdf->Cell(40, 5, $user['height'], 'B', 1);
$pdf->Ln(1);




$pdf->Ln(25); 


// Educational Background
$pdf->Ln(5);
$pdf->SetFont('Times', 'B', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '4. Educational Background', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1); 

$pdf->SetFont('Times', '', 9);

// First Row Headers
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(31, 14, "4.1\nSchool", 1, 'C'); 
$pdf->SetXY($x + 31, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(22, 14, "4.2\nEducational\nLevel", 1, 'C'); 
$pdf->SetXY($x + 22, $y + 0); 

// "School Year" Header Spanning Two Columns
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(20, 14, "4.3\nSchool\nYear", 1, 'C'); 
$pdf->SetXY($x + 20, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(22, 14, "4.4\nDegree", 1, 'C'); 
$pdf->SetXY($x + 22, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(21, 14, "4.5\nMinor", 1, 'C'); 
$pdf->SetXY($x + 21, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(21, 14, "4.6\nMajor", 1, 'C'); 
$pdf->SetXY($x + 21, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(21, 14, "4.7\nUnits\nEarned", 1, 'C'); 
$pdf->SetXY($x + 21, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(22, 14, "4.8\nHonor\nReceived", 1, 'C');
$pdf->SetXY($x + 22, $y); 

$pdf->Cell(31, 14, '', 0, 0); 
$pdf->Cell(22, 14, '', 0, 0); 
$pdf->Cell(20, 14, '', 0, 0); 
$pdf->Cell(25, 14, '', 0, 0);
$pdf->Cell(25, 14, '', 0, 0);
$pdf->Cell(20, 14, '', 0, 0);
$pdf->Cell(25, 14, '', 0, 0);

$pdf->Ln(); 

// Table Data
$pdf->SetFont('Times', '', 5);
$fixedRowHeight = 7; // Set a fixed height for all rows

foreach ($education as $edu) {
    $startX = $pdf->GetX();
    $startY = $pdf->GetY();

    // School name with wrapping
    $pdf->MultiCell(31, $fixedRowHeight, substr($edu['school_name'], 0, 40), 1, 'L');
    $pdf->SetXY($startX + 31, $startY);
    
    // Educational level
    $pdf->Cell(22, $fixedRowHeight, substr($edu['educational_level'], 0, 30), 1, 0, 'L');
    
    // School years
    $pdf->Cell(10, $fixedRowHeight, substr($edu['year_from'], 0, 4), 1, 0, 'C');
    $pdf->Cell(10, $fixedRowHeight, substr($edu['year_to'], 0, 4), 1, 0, 'C');
    
    // Degree with wrapping
    $pdf->SetXY($startX + 73, $startY);
    $pdf->MultiCell(22, $fixedRowHeight, substr($edu['degree'], 0, 30), 1, 'L');
    $pdf->SetXY($startX + 95, $startY);
    
    // Remaining cells
    $pdf->Cell(21, $fixedRowHeight, substr($edu['minor'], 0, 25), 1, 0, 'L');
    $pdf->Cell(21, $fixedRowHeight, substr($edu['major'], 0, 25), 1, 0, 'L');
    $pdf->Cell(21, $fixedRowHeight, substr($edu['units_earned'], 0, 15), 1, 0, 'L');
    $pdf->Cell(22, $fixedRowHeight, substr($edu['honors'], 0, 30), 1, 0, 'L');
    
    $pdf->Ln();
}

//5. Working Experience
$pdf->Ln(5);
$pdf->SetFont('Times', 'B', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '5. Working Experience (For Trainers, mandatory field 5.5)', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1); 

$pdf->SetFont('Times', '', 9);

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(31, 24, "5.1\nName of Company", 1, 'C'); 
$pdf->SetXY($x + 31, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(25, 24, "5.2\nPosition", 1, 'C'); 
$pdf->SetXY($x + 25, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(25, 24, "5.3\nInclusive Dates", 1, 'C'); 
$pdf->SetXY($x + 25, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(25, 24, "5.4\nMonthly\nSalary", 1, 'C'); 
$pdf->SetXY($x + 25, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(25, 18, "5.5\nOccupation Type\n(Teaching; Non-Teaching;\nIndustrial Experience)", 1, 'C'); 
$pdf->SetXY($x + 25, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(25, 24, "5.6\nStatus of\nAppointment", 1, 'C'); 
$pdf->SetXY($x + 25, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(24, 24, "No. of Yrs.\nWorking\nExp", 1, 'C'); 
$pdf->SetXY($x + 24, $y + 0); 

$pdf->Ln(); 

// Table Data
$pdf->SetFont('Times', '', 5);
$fixedRowHeight = 7; 

foreach ($work_experience as $work) {
    $startX = $pdf->GetX();
    $startY = $pdf->GetY();

    // Company name with wrapping
    $pdf->MultiCell(31, $fixedRowHeight, substr($work['company_name'], 0, 40), 1, 'L');
    $pdf->SetXY($startX + 31, $startY);
    
    // Other cells as regular Cells with the same fixed height
    $pdf->Cell(25, $fixedRowHeight, substr($work['position'], 0, 30), 1, 0, 'L');
    $pdf->Cell(13, $fixedRowHeight, substr($work['inclusive_dates_past'], 0, 10), 1, 0, 'C');
    $pdf->Cell(12, $fixedRowHeight, substr($work['inclusive_dates_present'], 0, 10), 1, 0, 'C');
    $pdf->Cell(25, $fixedRowHeight, substr($work['monthly_salary'], 0, 20), 1, 0, 'L');
    $pdf->Cell(25, $fixedRowHeight, substr($work['occupation'], 0, 30), 1, 0, 'L');
    $pdf->Cell(25, $fixedRowHeight, substr($work['status'], 0, 25), 1, 0, 'L');
    $pdf->Cell(24, $fixedRowHeight, substr($work['working_experience'], 0, 20), 1, 0, 'L');
    
    $pdf->Ln();
}
$pdf->Cell(0, 5, '(For more information, indicate on a sperate sheet)', 0, 1);

//6. Training/Seminars Attended
$pdf->Ln(5);
$pdf->SetFont('Times', 'B', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '6. Training/Seminars Attended', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1); 

$pdf->SetFont('Times', '', 6);

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(20, 15, "6.1\nTittle", 1, 'C'); 
$pdf->SetXY($x + 20, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(20, 15, "6.2\nVenue", 1, 'C'); 
$pdf->SetXY($x + 20, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(26, 15, "6.3\nInclusive Dates", 1, 'C'); 
$pdf->SetXY($x + 26, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(15, 15, "6.4\nCertificate Received", 1, 'C'); 
$pdf->SetXY($x +15, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(19, 15, "6.5\n# of\nHours", 1, 'C'); 
$pdf->SetXY($x + 19, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(20, 15, "6.6\nTraining\nBase", 1, 'C'); 
$pdf->SetXY($x + 20, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(20, 15, "6.7\nCategory", 1, 'C'); 
$pdf->SetXY($x + 20, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(20, 15, "6.8\nConducted By", 1, 'C'); 
$pdf->SetXY($x + 20, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(20, 15, "6.9\nProficiency", 1, 'C'); 
$pdf->SetXY($x + 20, $y + 0);

$pdf->Ln(); 

$pdf->SetFont('Times', '', 5);
$fixedRowHeight = 10;
foreach ($training_seminar as $training) {
    $startX = $pdf->GetX();
    $startY = $pdf->GetY();

    // Title with wrapping
    $pdf->MultiCell(20, $fixedRowHeight, substr($training['tittle'], 0, 40), 1, 'L');
    $pdf->SetXY($startX + 20, $startY);
    
    // Venue with wrapping
    $pdf->MultiCell(20, $fixedRowHeight, substr($training['venue'], 0, 40), 1, 'L');
    $pdf->SetXY($startX + 40, $startY);
    
    // Regular cells
    $pdf->Cell(13, $fixedRowHeight, substr($training['inclusive_dates_past'], 0, 10), 1, 0, 'C');
    $pdf->Cell(13, $fixedRowHeight, substr($training['inclusive_dates_present'], 0, 10), 1, 0, 'C');
    $pdf->Cell(15, $fixedRowHeight, substr($training['certificate'], 0, 15), 1, 0, 'L');
    $pdf->Cell(19, $fixedRowHeight, substr($training['no_of_hours'], 0, 10), 1, 0, 'L');
    $pdf->Cell(20, $fixedRowHeight, substr($training['training_base'], 0, 15), 1, 0, 'L');
    $pdf->Cell(20, $fixedRowHeight, substr($training['category'], 0, 15), 1, 0, 'L');
    
    // Conducted 
    $pdf->SetXY($startX + 140, $startY);
    $pdf->MultiCell(20, $fixedRowHeight, substr($training['conducted_by'], 0, 40), 1, 'L');
    $pdf->SetXY($startX + 160, $startY);
    
    // Last regular cell
    $pdf->Cell(20, $fixedRowHeight, substr($training['proficiency'], 0, 15), 1, 0, 'L');
    

    $pdf->Ln();
}
$pdf->Cell(0, 5, '(For more information, indicate on a sperate sheet)', 0, 1);
// Alternative approach with cells of specific widths
$pdf->Cell(80, 5, '(*Certificate Received', 0, 0); 
$pdf->Cell(35, 5, 'Training Base', 0, 0);
$pdf->Cell(45, 5, 'Category', 0, 0);
$pdf->Cell(20, 5, 'Proficiency)', 0, 1);  

// Now add your rows below, with appropriate indentation and alignment
$pdf->SetX($pdf->GetX() + 5);
$pdf->Cell(30, 5, 'A  - Certificate of Attendance', 0, 0);
$pdf->Cell(45, 5, 'S  - Skills Training Certificate', 0, 0);
$pdf->Cell(35, 5, 'L  - Local', 0, 0);
$pdf->Cell(45, 5, 'T  - Trade Skills Upgrading Program', 0, 0);
$pdf->Cell(20, 5, 'B  - Beginner', 0, 1);

// And so on for other rows...
$pdf->SetX($pdf->GetX() + 5);
$pdf->Cell(30, 5, 'C  - Certificate of Competencies', 0, 0);
$pdf->Cell(45, 5, 'T  - Training Certificate', 0, 0);
$pdf->Cell(35, 5, 'F  - Foreign', 0, 0);
$pdf->Cell(45, 5, 'N  - Non-Trade Skills Upgrading Program', 0, 0);
$pdf->Cell(20, 5, 'I  - Intermediate', 0, 1);

$pdf->SetX($pdf->GetX() + 5);
$pdf->Cell(110, 5, 'P  - Certificate of Proficiency', 0, 0);
$pdf->Cell(45, 5, 'M  - Training Management', 0, 0);
$pdf->Cell(20, 5, 'A  - Advanced', 0, 1);

// 7. Licenses/Examination Passed
$pdf->Ln(5);
$pdf->SetFont('Times', 'B', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '7. Licenses/Examination Passed', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1); 

$pdf->SetFont('Times', '', 9);


$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(36, 15, "7.1\nTitle", 1, 'C'); 
$pdf->SetXY($x + 36, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(28, 15, "7.2\nYear Taken", 1, 'C'); 
$pdf->SetXY($x + 28, $y + 0);

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(34, 15, "7.3\nExamination Venue", 1, 'C'); 
$pdf->SetXY($x + 34, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(28, 15, "7.4\nRatings", 1, 'C'); 
$pdf->SetXY($x + 28, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(27, 15, "7.5\nRemarks", 1, 'C'); 
$pdf->SetXY($x + 27, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(27, 15, "7.6\nExpiry Date", 1, 'C'); 
$pdf->SetXY($x + 27, $y + 0); 

$pdf->Ln(); 

$pdf->SetFont('Times', '', 5);
$fixedRowHeight = 7;

foreach ($license_examination as $license) {
    $startX = $pdf->GetX();
    $startY = $pdf->GetY();

    // License title with wrapping
    $pdf->MultiCell(36, $fixedRowHeight, substr($license['license_tittle'], 0, 50), 1, 'L');
    $pdf->SetXY($startX + 36, $startY);
    
    // Regular cell (no wrapping)
    $pdf->Cell(28, $fixedRowHeight, substr($license['year_taken'], 0, 15), 1, 0, 'L');
    
    // Examination venue with wrapping
    $pdf->SetXY($startX + 64, $startY);
    $pdf->MultiCell(34, $fixedRowHeight, substr($license['examination_venue'], 0, 50), 1, 'L');
    $pdf->SetXY($startX + 98, $startY);
    
    // Remaining regular cells
    $pdf->Cell(28, $fixedRowHeight, substr($license['ratings'], 0, 15), 1, 0, 'L');
    $pdf->Cell(27, $fixedRowHeight, substr($license['remarks'], 0, 25), 1, 0, 'L');
    $pdf->Cell(27, $fixedRowHeight, substr($license['expiry_date'], 0, 15), 1, 0, 'L');
    
    $pdf->Ln();
}
$pdf->Cell(0, 5, '(For more information, indicate on a sperate sheet)', 0, 1);

// 8. Competency Assessment Passed
$pdf->Ln(5);
$pdf->SetFont('Times', 'B', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '8. Competency Assessment Passed', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1); 

$pdf->SetFont('Times', '', 9);


$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(36, 15, "8.1\nIndustry Sector", 1, 'C'); 
$pdf->SetXY($x + 36, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(28, 15, "8.2\nTrade Area", 1, 'C'); 
$pdf->SetXY($x + 28, $y + 0);

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(34, 15, "8.3\nOccupation", 1, 'C'); 
$pdf->SetXY($x + 34, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(28, 15, "8.4\nClassification Level", 1, 'C'); 
$pdf->SetXY($x + 28, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(27, 15, "8.5\nCompetency", 1, 'C'); 
$pdf->SetXY($x + 27, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(27, 15, "8.6\nSpecialization", 1, 'C'); 
$pdf->SetXY($x + 27, $y + 0); 
$pdf->Ln(); 

$pdf->SetFont('Times', '', 5);
$fixedRowHeight = 7; 

foreach ($competency_assessment as $competency) {
    $startX = $pdf->GetX();
    $startY = $pdf->GetY();

    // Industry sector with wrapping
    $pdf->MultiCell(36, $fixedRowHeight, substr($competency['industry_sector'], 0, 50), 1, 'L');
    $pdf->SetXY($startX + 36, $startY);
    
    // Regular cells (no wrapping)
    $pdf->Cell(28, $fixedRowHeight, substr($competency['trade_area'], 0, 30), 1, 0, 'L');
    $pdf->Cell(34, $fixedRowHeight, substr($competency['occupation'], 0, 40), 1, 0, 'L');
    $pdf->Cell(28, $fixedRowHeight, substr($competency['classification_level'], 0, 30), 1, 0, 'L');
    
    // Competency with wrapping
    $pdf->SetXY($startX + 126, $startY);
    $pdf->MultiCell(27, $fixedRowHeight, substr($competency['competency'], 0, 40), 1, 'L');
    $pdf->SetXY($startX + 153, $startY);
    
    // Last regular cell
    $pdf->Cell(27, $fixedRowHeight, substr($competency['specialization'], 0, 30), 1, 0, 'L');
    
    $pdf->SetY($startY + $fixedRowHeight);
}

$pdf->Cell(0, 5, '(For more information, indicate on a sperate sheet)', 0, 1);

// 9. Family Background
$pdf->Ln(5);
$pdf->SetFont('Times', '', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '9. Family Background', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1);
$x = $pdf->GetX();
$y = $pdf->GetY();
$width = 180;  
$height = 20;
$section_height = $height * 4 + 10; // Total height needed for all 4 sections + some spacing

// Check if we need a page break before drawing family sections
if ($y + $section_height > $pdf->getPageHeight() - 25) {
    $pdf->AddPage();
    $y = $pdf->GetY();
}

// Draw the rectangle for spouse
$pdf->Rect($x, $y, $width, $height);
$pdf->Ln(0);

$pdf->SetFont('Times', '', 9);

$pdf->Cell(30, 5, '9.1. Spouse\'s Name:', 0, 0);
$pdf->SetFont('Times', '', 5);
$pdf->Cell(65, 5, $family['spouse_name'], 'B', 0);
$pdf->SetX(110); 
$pdf->Cell(25, 5, '9.3. Occupation:', 0, 0);
$pdf->SetFont('Times', '', 5);
$pdf->Cell(60, 5, $family['spouse_occupation'], 'B', 1);
$pdf->Ln(3);

$pdf->SetX($x);

$pdf->Cell(40, 5, '9.2. Educational Attainment:', 0, 0);
$pdf->SetFont('Times', '', 5);
$pdf->Cell(55, 5, $family['spouse_educational_attainment'], 'B', 0);
$pdf->SetX(110); 
$pdf->Cell(40, 5, '9.4. Ave. Monthly Income:', 0, 0);
$pdf->SetFont('Times', '', 5);
$pdf->Cell(45, 5, $family['spouse_monthly_income'], 'B', 1);
$pdf->Ln(3);

// Draw the rectangle for father
$new_y = $y + $height;
$pdf->Rect($x, $new_y, $width, $height);
$pdf->SetY($new_y + 3); 
$pdf->SetX($x); 

// Father information
$pdf->SetFont('Times', '', 9);

$pdf->Cell(30, 5, '9.5 Father\'s Name:', 0, 0);
$pdf->SetFont('Times', '', 5);
$pdf->Cell(65, 5, $family['father_name'], 'B', 0);
$pdf->SetX(110); 
$pdf->Cell(25, 5, '9.7 Occupation:', 0, 0);
$pdf->SetFont('Times', '', 5);
$pdf->Cell(60, 5, $family['father_occupation'], 'B', 1);
$pdf->Ln(3);

$pdf->SetX($x);

$pdf->Cell(40, 5, '9.6 Educational Attainment:', 0, 0);
$pdf->SetFont('Times', '', 5);
$pdf->Cell(55, 5, $family['father_educational_attainment'], 'B', 0);
$pdf->SetX(110); 
$pdf->Cell(40, 5, '9.8 Ave. Monthly Income:', 0, 0);
$pdf->SetFont('Times', '', 5);
$pdf->Cell(45, 5, $family['father_monthly_income'], 'B', 1);
$pdf->Ln(3);

// Draw the rectangle for mother
$mother_y = $new_y + $height;
$pdf->Rect($x, $mother_y, $width, $height);
$pdf->SetY($mother_y + 3); 
$pdf->SetX($x); 

// Mother information
$pdf->SetFont('Times', '', 9);

$pdf->Cell(30, 5, '9.9 Mother\'s Name:', 0, 0);
$pdf->SetFont('Times', '', 5);
$pdf->Cell(65, 5, $family['mother_name'], 'B', 0);
$pdf->SetX(110); 
$pdf->Cell(25, 5, '9.11 Occupation:', 0, 0);
$pdf->SetFont('Times', '', 5);
$pdf->Cell(60, 5, $family['mother_occupation'], 'B', 1);
$pdf->Ln(3);

$pdf->SetX($x);

$pdf->Cell(40, 5, '9.10 Educational Attainment:', 0, 0);
$pdf->SetFont('Times', '', 5);
$pdf->Cell(55, 5, $family['mother_educational_attainment'], 'B', 0);
$pdf->SetX(110); 
$pdf->Cell(40, 5, '9.12 Ave. Monthly Income:', 0, 0);
$pdf->SetFont('Times', '', 5);
$pdf->Cell(45, 5, $family['mother_monthly_income'], 'B', 1);
$pdf->Ln(3);

// Draw the rectangle for guardian
$guardian_y = $mother_y + $height;
$pdf->Rect($x, $guardian_y, $width, $height);
$pdf->SetY($guardian_y + 3); 
$pdf->SetX($x); 

// Guardian information
$pdf->SetFont('Times', '', 9);

$pdf->Cell(45, 5, '9.13 Name of Guardian\'s Name:', 0, 0);
$pdf->SetFont('Times', '', 5);
$pdf->Cell(50, 5, $family['guardian_name'], 'B', 0);
$pdf->SetX(110); 
$pdf->Cell(25, 5, '9.15 Occupation:', 0, 0);
$pdf->SetFont('Times', '', 5);
$pdf->Cell(60, 5, $family['guardian_occupation'], 'B', 1);
$pdf->Ln(3);

$pdf->SetX($x);

$pdf->Cell(40, 5, '9.14 Educational Attainment:', 0, 0);
$pdf->SetFont('Times', '', 5);
$pdf->Cell(55, 5, $family['guardian_educational_attainment'], 'B', 0);
$pdf->SetX(110); 
$pdf->Cell(40, 5, '9.16 Ave. Monthly Income:', 0, 0);
$pdf->SetFont('Times', '', 5);
$pdf->Cell(45, 5, $family['guardian_monthly_income'], 'B', 1);
$pdf->Ln(4);

// Draw table for dependents
$dependents_y = $guardian_y + $height + 1; 
$pdf->SetY($dependents_y);
$x = $pdf->GetX();
$y = $pdf->GetY();

// Dependents headers
$pdf->MultiCell(90, 15, "9.17\nDependents", 1, 'C'); 
$pdf->SetXY($x + 90, $y); 
$pdf->MultiCell(90, 15, "9.18\nAge", 1, 'C'); 
$pdf->SetXY($x, $y + 15); 

$pdf->SetFont('Times', '', 5);
$pdf->Cell(90, 7, $family['dependents'], 1);
$pdf->Cell(90, 7, $family['dependents_age'], 1);
$pdf->Ln();
$pdf->Cell(0, 5, '(For more information, indicate on a sperate sheet)', 0, 1);

// Output the PDF
$pdf->Output('NMIS_Profile.pdf', 'D');