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
            $this->Image('assets/inafarm_long logo.png', 10, 13, 60);
            
            // Header Text
            //$this->SetFont('times', 'B', 12);
            //$this->SetXY(35, 10);
            //$this->Cell(0, 6, 'Ina Farm Employee', 0, 1, 'L');
            
            $this->SetFont('times', '', 12);
            $this->SetXY(35, 16);
            
            // Double line border
            $this->Line(10, 25, 200, 25);
            $this->Line(10, 26, 200, 26);
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

    // Function to handle text wrapping in table cells
    public function WrapCell($w, $h, $txt, $border=0, $align='L', $fill=false) {
        $this->MultiCell($w, $h, $txt, $border, $align, $fill, 0);
    }
}

// Create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Employee System');
$pdf->SetAuthor('Employee');
$pdf->SetTitle('Ina Farm Employee Profile');

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(10, 35, 10);
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
$pdf->SetFont('Times', 'B', 16);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 10, 'INA FARM EMPLOYEE PROFILE', 0, 1, 'C');
$pdf->SetTextColor(0, 0, 0);

//line below
$pdf->Line(10, 40, 200, 40); 
$pdf->Line(10, 40.5, 200, 40.5);

// Add signature box
$pdf->SetXY(30, 68);
$pdf->SetFont('Times', '', 10);
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

// ID Photo box
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

// Section 1 - Student Information
$pdf->SetXY(10, 84);
$pdf->SetFont('Times', 'B', 10);
$pdf->SetFillColor(177, 176, 176); 
$pdf->SetTextColor(255, 0, 0);
//$pdf->Cell(0, 8, '1. To be accomplished by TESDA', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

// NMIS Code and Entry Date
$pdf->SetXY(15, 93);
$pdf->SetFont('Times', 'B', 10);
$y = $pdf->GetY();
//$pdf->Cell(40, 6, 'NMIS Manpower Code:',  0, 0);
$pdf->SetFont('Times', '', 10);
//$pdf->Cell(40, 6, $user['nmis_code'], 1, 0);

$pdf->SetX(105);
$pdf->SetFont('Times', 'B', 10);
//$pdf->Cell(30, 6, 'NMIS Entry Date:', 0, 0);
//$pdf->Cell(60, 6, $user['nmis_entry'], 1, 0);
$pdf->Ln(5);

// Personal Information Section
$pdf->Ln(5);
$pdf->SetFont('Times', 'B', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '1. Employee Profile', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

// Name Fields
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(20, 10, 'Name:', 0, 0);

$pdf->SetFont('Times', '', 5);
$pdf->SetXY(50, 113);
$pdf->SetFont('Times', '', 9);
$pdf->Cell(49, 6, substr($user['lastname'], 0, 30), 1, 0, 'C');
$pdf->Cell(48, 6, substr($user['firstname'], 0, 30), 1, 0, 'C'); 
$pdf->Cell(48, 6, substr($user['middlename'], 0, 30), 1, 1, 'C'); 

// Move down before adding labels
$pdf->Ln(2);

// Set new X position for labels to align with the boxes
$pdf->SetXY(50, 119); 
$pdf->SetFont('Times', '', 10);
$pdf->Cell(49, 5, 'Last', 0, 0, 'C'); 
$pdf->Cell(48, 5, 'First', 0, 0, 'C'); 
$pdf->Cell(48, 5, 'Middle', 0, 1, 'C'); 

// Address
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(30, 10, 'Mailing Address:', 0, 0);

$pdf->SetFont('Times', '', 9);
$pdf->SetXY(50, 127);
$pdf->Cell(49, 5, substr($user['address_street'], 0, 30), 1, 0, 'C');
$pdf->Cell(48, 5, substr($user['address_barangay'], 0, 30), 1, 0, 'C');
$pdf->Cell(48, 5, substr($user['address_district'], 0, 30), 1, 1, 'C'); 

$pdf->SetXY(50, 132);
$pdf->SetFont('Times', '', 10);
$pdf->Cell(49, 5, 'Number, Street', 0, 0, 'C'); 
$pdf->Cell(48, 5, 'Barangay', 0, 0, 'C'); 
$pdf->Cell(48, 5, 'Congressional District', 0, 1, 'C');

// City, Province, Region
$pdf->SetFont('Times', '', 9);
$pdf->SetXY(50, 140);
$pdf->Cell(49, 5, substr($user['address_city'], 0, 30), 1, 0, 'C');
$pdf->Cell(48, 5, substr($user['address_province'], 0, 30), 1, 0, 'C');
$pdf->Cell(48, 5, substr($user['address_region'], 0, 30), 1, 1, 'C');

$pdf->SetXY(50, 145);
$pdf->SetFont('Times', '', 10);
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

////

// Set starting position
$startX = 10;
$startY = $pdf->GetY();
$boxHeight = 70; 
$baseBoxWidth = 20;  

// Define box widths
$sexBoxWidth = 25;
$civilStatusBoxWidth = 30;
$contactBoxWidth = $baseBoxWidth + 40;
$employmentTypeWidth = $baseBoxWidth + 17;
$employmentStatusWidth = $baseBoxWidth + 18;

// Draw rectangle for "Sex"
$pdf->Rect($startX, $startY, $sexBoxWidth, $boxHeight);
$pdf->SetXY($startX, $startY);
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
    $yPosition += 10; // Increased spacing between options
}

// Calculate Civil Status position - right after Sex box
$civilStatusX = $startX + $sexBoxWidth;

// Draw rectangle for "Civil Status" 
$pdf->Rect($civilStatusX, $startY, $civilStatusBoxWidth, $boxHeight);
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
    $yPosition += 10; 
}

// Calculate Contact Numbers position - right after Civil Status box
$contactX = $civilStatusX + $civilStatusBoxWidth;

// Draw rectangle for "Contact Number(s)"
$pdf->Rect($contactX, $startY, $contactBoxWidth, $boxHeight);
$pdf->SetXY($contactX, $startY);
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(40, 8, 'Contact Number(s)', 0, 1);
$pdf->SetTextColor(0, 0, 0);

// Move cursor below the title
$pdf->SetFont('Times', '', 10);
$yPosition = $startY + 10; // Increased spacing after title

// Telephone Number
$pdf->SetXY($contactX + 1, $yPosition);
$pdf->SetFont('Times', '', 10); 
$pdf->Cell(15, 5, 'Tel. No.:', 0, 0);
$pdf->SetFont('Times', '', 9); 
$pdf->Cell(38, 5, $user['tel_number'], 'B', 1);

// Move down for Cellular Number
$yPosition += 10; // Increased spacing
$pdf->SetXY($contactX + 1, $yPosition);
$pdf->SetFont('Times', '', 10); 
$pdf->Cell(15, 5, 'Cellular:', 0, 0);
$pdf->SetFont('Times', '', 9); 
$pdf->Cell(38, 5, $user['contact_number'], 'B', 1);

// Move down for Email
$yPosition += 10; // Increased spacing
$pdf->SetXY($contactX + 1, $yPosition);
$pdf->SetFont('Times', '', 10); 
$pdf->Cell(15, 5, 'Email:', 0, 0);
$pdf->SetFont('Times', '', 9); 
$pdf->Cell(38, 5, $user['email'], 'B', 1);

// Move down for Fax
$yPosition += 10; // Increased spacing
$pdf->SetXY($contactX + 1, $yPosition);
$pdf->SetFont('Times', '', 10); 
$pdf->Cell(15, 5, 'Fax:', 0, 0);
$pdf->SetFont('Times', '', 9); 
$pdf->Cell(38, 5, $user['fax_number'], 'B', 1); 

// Calculate Employment Type position - right after Contact Numbers box
$employmentTypeX = $contactX + $contactBoxWidth;

// Draw rectangle for "Employment Type"
$pdf->Rect($employmentTypeX, $startY, $employmentTypeWidth, $boxHeight); 
$pdf->SetXY($employmentTypeX, $startY);
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(40, 8, 'Employment Type', 0, 1);
$pdf->SetTextColor(0, 0, 0);

// Draw Employment Type checkboxes
$pdf->SetFont('Times', '', 10);
$employment_types = ['Employed', 'Self-employed', 'Unemployed', 'Other'];
$yPosition = $startY + 10; // Increased spacing after title
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
    
    $yPosition += 10; // Increased spacing
}

// Calculate Employment Status position - right after Employment Type box
$employmentStatusX = $employmentTypeX + $employmentTypeWidth;

// Draw rectangle for "Employment Status" with SAME height as others
$pdf->Rect($employmentStatusX, $startY, $employmentStatusWidth, $boxHeight);
$pdf->SetXY($employmentStatusX, $startY);
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(40, 8, 'Employment Status', 0, 1);
$pdf->SetTextColor(0, 0, 0);

// Draw Employment Status checkboxes - with more spacing between options
$pdf->SetFont('Times', 'I', 10); // Set italic font for all status options
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

$yPosition = $startY + 10;
$statusSpacing = 6;

foreach ($employment_statuses as $index => $status) {
    // For Trainee/OJT, add a small label
    if ($status == 'Trainee/OJT') {
        $pdf->SetXY($employmentStatusX + 3, $yPosition);
        $pdf->SetFont('Times', 'I', 8); 
        $pdf->Cell(35, 3, 'If Student:', 0, 1);
        $yPosition += 4;
        
        $pdf->SetXY($employmentStatusX + 3, $yPosition);
        $pdf->Cell(5, 5, $user['employment_status'] == $status ? 'X' : '', 1, 0, 'C');
        $pdf->SetFont('Times', 'I', 10); 
        $pdf->Cell(30, 5, $status, 0, 1);
    } else {
        $pdf->SetXY($employmentStatusX + 3, $yPosition);
        $pdf->Cell(5, 5, $user['employment_status'] == $status ? 'X' : '', 1, 0, 'C');
        $pdf->SetFont('Times', 'I', 10); 
        $pdf->Cell(30, 5, $status, 0, 1);
    }
    $yPosition += $statusSpacing;
    $pdf->Ln();
}


// 2. Personal Information
$pdf->Ln(5);
$pdf->SetFont('Times', '', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '2. Personal Information', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1);
$x = $pdf->GetX();
$y = $pdf->GetY();
$width = 190;  
$height = 40;

// Draw the rectangle 
$pdf->Rect($x, $y, $width, $height);
$pdf->Ln(3);

// Left side fields
$leftX = $pdf->GetX();
$midX = $leftX + 65; // Position for middle column fields
$rightX = $midX + 65; // Position for right column fields
$currentY = $pdf->GetY();

// First row - three fields
// Birthdate - left side
$pdf->SetXY($leftX, $currentY);
$pdf->SetFont('Times', '', 10);
$pdf->Cell(20
, 5, 'Birthdate:', 0, 0);
$pdf->SetFont('Times', '', 9);
$pdf->Cell(40, 5, $user['birthdate'], 'B', 0);

// Weight - middle
$pdf->SetXY($midX, $currentY);
$pdf->SetFont('Times', '', 10);
$pdf->Cell(20, 5, 'Weight:', 0, 0);
$pdf->SetFont('Times', '', 9);
$pdf->Cell(40, 5, $user['weight'], 'B', 0);

// Distinguishing Marks - right side
$pdf->SetXY($rightX, $currentY);
$pdf->SetFont('Times', '', 10);
$pdf->Cell(60, 5, 'Distinguishing Marks:', 0, 1);

// Position the value field below the label
$pdf->SetXY($rightX, $currentY + 5); // Move down 5 units
$pdf->SetFont('Times', '', 9);
$pdf->Cell(50, 5, $user['distinguish_marks'], 'B', 1);

$currentY += 6; // Move to next line

// Second row
// Birthplace - left side
$pdf->SetXY($leftX, $currentY);
$pdf->SetFont('Times', '', 10);
$pdf->Cell(20, 5, 'Birthplace:', 0, 0);
$pdf->SetFont('Times', '', 9);
$pdf->Cell(40, 5, $user['birth_place'], 'B', 0);

// Blood Type - middle
$pdf->SetXY($midX, $currentY);
$pdf->SetFont('Times', '', 10);
$pdf->Cell(20, 5, 'Blood Type:', 0, 0);
$pdf->SetFont('Times', '', 9);
$pdf->Cell(40, 5, $user['blood_type'], 'B', 1);

$currentY += 6; // Move to next line

// Third row
// Citizenship - left side
$pdf->SetXY($leftX, $currentY);
$pdf->SetFont('Times', '', 10);
$pdf->Cell(20, 5, 'Citizenship:', 0, 0);
$pdf->SetFont('Times', '', 9);
$pdf->Cell(40, 5, $user['citizenship'], 'B', 0);

// SSS No - middle
$pdf->SetXY($midX, $currentY);
$pdf->SetFont('Times', '', 10);
$pdf->Cell(20, 5, 'SSS No:', 0, 0);
$pdf->SetFont('Times', '', 9);
$pdf->Cell(40, 5, $user['sss_no'], 'B', 1);

$currentY += 6; // Move to next line

// Fourth row
// Religion - left side
$pdf->SetXY($leftX, $currentY);
$pdf->SetFont('Times', '', 10);
$pdf->Cell(20, 5, 'Religion:', 0, 0);
$pdf->SetFont('Times', '', 9);
$pdf->Cell(40, 5, $user['religion'], 'B', 0);

// GSIS No - middle
$pdf->SetXY($midX, $currentY);
$pdf->SetFont('Times', '', 10);
$pdf->Cell(20, 5, 'GSIS No:', 0, 0);
$pdf->SetFont('Times', '', 9);
$pdf->Cell(40, 5, $user['gsis_no'], 'B', 1);

$currentY += 6; // Move to next line

// Fifth row
// Height - left side
$pdf->SetXY($leftX, $currentY);
$pdf->SetFont('Times', '', 10);
$pdf->Cell(20, 5, 'Height:', 0, 0);
$pdf->SetFont('Times', '', 9);
$pdf->Cell(40, 5, $user['height'], 'B', 0);

// TIN No - middle
$pdf->SetXY($midX, $currentY);
$pdf->SetFont('Times', '', 10);
$pdf->Cell(20, 5, 'TIN No:', 0, 0);
$pdf->SetFont('Times', '', 9);
$pdf->Cell(40, 5, $user['tin_no'], 'B', 1);

$pdf->Ln(1); 

$pdf->Ln(25); 

// 3. Educational Background
$pdf->Ln(5);
$pdf->SetFont('Times', 'B', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '3. Educational Background', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1); 

$pdf->SetFont('Times', '', 10);

// First Row Headers
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(33, 14, "School", 1, 'C'); 
$pdf->SetXY($x + 33, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(22, 14, "Educational\nLevel", 1, 'C'); 
$pdf->SetXY($x + 22, $y); 

// School Year as a single column
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(26, 14, "School\nYear", 1, 'C'); 
$pdf->SetXY($x + 26, $y);

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(25, 14, "Degree", 1, 'C'); 
$pdf->SetXY($x + 25, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(21, 14, "Minor", 1, 'C'); 
$pdf->SetXY($x + 21, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(21, 14, "Major", 1, 'C'); 
$pdf->SetXY($x + 21, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(21, 14, "Units\nEarned", 1, 'C'); 
$pdf->SetXY($x + 21, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(21, 14, "Honor\nReceived", 1, 'C');

$pdf->SetFont('Times', '', 7);
$fixedRowHeight = 20; 

foreach ($education as $edu) {
    $startX = $pdf->GetX();
    $startY = $pdf->GetY();

    // School name with wrapping
    $pdf->MultiCell(33, $fixedRowHeight, substr($edu['school_name'], 0, 40), 1, 'L');
    $pdf->SetXY($startX + 33, $startY);
    
    // Educational level
    $pdf->Cell(22, $fixedRowHeight, substr($edu['educational_level'], 0, 30), 1, 0, 'L');
    
    // School year as single column
    $pdf->Cell(26, $fixedRowHeight, substr($edu['year_from'], 0, 4) . '-' . substr($edu['year_to'], 0, 4), 1, 0, 'C');
    
    // Degree with wrapping
    $pdf->Cell(25, $fixedRowHeight, substr($edu['degree'], 0, 30), 1, 0, 'L');
    
    // Remaining cells
    $pdf->Cell(21, $fixedRowHeight, substr($edu['minor'], 0, 25), 1, 0, 'L');
    $pdf->Cell(21, $fixedRowHeight, substr($edu['major'], 0, 25), 1, 0, 'L');
    $pdf->Cell(21, $fixedRowHeight, substr($edu['units_earned'], 0, 15), 1, 0, 'L');
    $pdf->Cell(21, $fixedRowHeight, substr($edu['honors'], 0, 30), 1, 0, 'L');
    
    $pdf->Ln();
}

//4. Working Experience
$pdf->Ln(5);
$pdf->SetFont('Times', 'B', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '4. Working Experience (For Trainers, mandatory field 5.5)', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1); 

$pdf->SetFont('Times', '', 10);

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(31, 24, "Name of Company", 1, 'C'); 
$pdf->SetXY($x + 31, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(27, 24, "Position", 1, 'C'); 
$pdf->SetXY($x + 27, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(28, 24, "Inclusive Dates", 1, 'C'); 
$pdf->SetXY($x + 28, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(25, 24, "Monthly\nSalary", 1, 'C'); 
$pdf->SetXY($x + 25, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(30, 24, "Occupation Type\n(Teaching; Non-Teaching;\nIndustrial Experience)", 1, 'C'); 
$pdf->SetXY($x + 30, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(25, 24, "Status of\nAppointment", 1, 'C'); 
$pdf->SetXY($x + 25, $y + 0); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(24, 24, "No. of Yrs.\nWorking\nExp", 1, 'C'); 
$pdf->SetXY($x + 24, $y + 0); 

$pdf->Ln(); 

// Table Data
$pdf->SetFont('Times', '', 7);
$fixedRowHeight = 20; 

foreach ($work_experience as $work) {
    $startX = $pdf->GetX();
    $startY = $pdf->GetY();

    // Company name with wrapping
    $pdf->MultiCell(31, $fixedRowHeight, substr($work['company_name'], 0, 40), 1, 'L');
    $pdf->SetXY($startX + 31, $startY);
    
    // Other cells as regular Cells with the same fixed height
    $pdf->MultiCell(27, $fixedRowHeight, substr($work['position'], 0, 30), 1, 'L');
    $pdf->SetXY($startX + 31 + 27, $startY);    
    
    // Combined inclusive dates into a single cell
    $pdf->Cell(28, $fixedRowHeight, substr($work['inclusive_dates_past'], 0, 10) . '-' . substr($work['inclusive_dates_present'], 0, 10), 1, 0, 'C');
    
    $pdf->Cell(25, $fixedRowHeight, substr($work['monthly_salary'], 0, 20), 1, 0, 'L');

    $pdf->MultiCell(30, $fixedRowHeight, substr($work['occupation'], 0, 30), 1, 'L');
    $pdf->SetXY($startX + 31 + 27 + 28 + 25 + 30, $startY);

    $pdf->Cell(25, $fixedRowHeight, substr($work['status'], 0, 25), 1, 0, 'L');
    $pdf->Cell(24, $fixedRowHeight, substr($work['working_experience'], 0, 20), 1, 0, 'L');
    
    $pdf->Ln();
}

$pdf->Cell(0, 5, '(For more information, indicate on a sperate sheet)', 0, 1);

//5. Training/Seminars Attended
$pdf->Ln(5);
$pdf->SetFont('Times', 'B', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '5. Training/Seminars Attended', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1); 

$pdf->SetFont('Times', '', 6); // Even smaller font for training table

// Table headers
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(18, 15, "Title", 1, 'C'); 
$pdf->SetXY($x + 18, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(18, 15, "Venue", 1, 'C'); 
$pdf->SetXY($x + 18, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(24, 15, "Inclusive Dates", 1, 'C'); 
$pdf->SetXY($x + 24, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(14, 15, "Certificate Received", 1, 'C'); 
$pdf->SetXY($x +14, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(14, 15, "# of\nHours", 1, 'C'); 
$pdf->SetXY($x + 14, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(18, 15, "Training\nBase", 1, 'C'); 
$pdf->SetXY($x + 18, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(18, 15, "Category", 1, 'C'); 
$pdf->SetXY($x + 18, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(18, 15, "Conducted By", 1, 'C'); 
$pdf->SetXY($x + 18, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(18, 15, "Proficiency", 1, 'C'); 
$pdf->Ln(15); 

// Table data for training/seminars
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
$pdf->Cell(0, 5, '(For more information, indicate on a separate sheet)', 0, 1);

// Legend for training certificates
$pdf->SetFont('Times', '', 6);
$pdf->Cell(80, 5, '(*Certificate Received', 0, 0); 
$pdf->Cell(35, 5, 'Training Base', 0, 0);
$pdf->Cell(45, 5, 'Category', 0, 0);
$pdf->Cell(20, 5, 'Proficiency)', 0, 1);  

// Legend rows with smaller text
$pdf->SetX($pdf->GetX() + 5);
$pdf->Cell(30, 5, 'A  - Certificate of Attendance', 0, 0);
$pdf->Cell(45, 5, 'S  - Skills Training Certificate', 0, 0);
$pdf->Cell(35, 5, 'L  - Local', 0, 0);
$pdf->Cell(45, 5, 'T  - Trade Skills Upgrading Program', 0, 0);
$pdf->Cell(20, 5, 'B  - Beginner', 0, 1);

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

$pdf->Ln();


// 6. Licenses/Examination Passed
//6.  Licenses/Examination Passed
if ($pdf->GetY() > 220) {
    $pdf->AddPage();
}

$pdf->Ln(4); // Reduced spacing
$pdf->SetFont('Times', 'B', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '6.  Licenses/Examination Passed', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1); 

// Define column widths for better layout
$colWidth1 = 45;  // Title
$colWidth2 = 22;  // Year Taken
$colWidth3 = 45;  // Examination Venue
$colWidth4 = 25;  // Ratings
$colWidth5 = 25;  // Remarks
$colWidth6 = 28;  // Expiry Date

$pdf->SetFont('Times', '', 10);

// Store the starting X position for alignment
$startX = $pdf->GetX();
$y = $pdf->GetY();

// Headers - use MultiCell for all headers that might need wrapping
$pdf->SetXY($startX, $y);
$pdf->MultiCell($colWidth1, 10, "Title", 1, 'C');
$pdf->SetXY($startX + $colWidth1, $y); 

$pdf->MultiCell($colWidth2, 10, "Year Taken", 1, 'C');
$pdf->SetXY($startX + $colWidth1 + $colWidth2, $y);

$pdf->MultiCell($colWidth3, 10, "Examination Venue", 1, 'C');
$pdf->SetXY($startX + $colWidth1 + $colWidth2 + $colWidth3, $y);

$pdf->MultiCell($colWidth4, 10, "Ratings", 1, 'C');
$pdf->SetXY($startX + $colWidth1 + $colWidth2 + $colWidth3 + $colWidth4, $y);

$pdf->MultiCell($colWidth5, 10, "Remarks", 1, 'C');
$pdf->SetXY($startX + $colWidth1 + $colWidth2 + $colWidth3 + $colWidth4 + $colWidth5, $y);

$pdf->MultiCell($colWidth6, 10, "Expiry Date", 1, 'C');


$pdf->SetFont('Times', '', 7);
$fixedRowHeight = 15; // Row height

foreach ($license_examination as $license) {
    // Check if we need to add a page break before this row
    if ($pdf->GetY() + $fixedRowHeight > 270) {
        $pdf->AddPage();
        
        // Redraw the header on the new page
        $pdf->SetFont('Times', '', 10);
        $y = $pdf->GetY();
        
        $pdf->SetXY($startX, $y);
        $pdf->MultiCell($colWidth1, 10, "Title", 1, 'C');
        $pdf->SetXY($startX + $colWidth1, $y); 
        
        $pdf->MultiCell($colWidth2, 10, "Year Taken", 1, 'C');
        $pdf->SetXY($startX + $colWidth1 + $colWidth2, $y);
        
        $pdf->MultiCell($colWidth3, 10, "Examination Venue", 1, 'C');
        $pdf->SetXY($startX + $colWidth1 + $colWidth2 + $colWidth3, $y);
        
        $pdf->MultiCell($colWidth4, 10, "Ratings", 1, 'C');
        $pdf->SetXY($startX + $colWidth1 + $colWidth2 + $colWidth3 + $colWidth4, $y);
        
        $pdf->MultiCell($colWidth5, 10, "Remarks", 1, 'C');
        $pdf->SetXY($startX + $colWidth1 + $colWidth2 + $colWidth3 + $colWidth4 + $colWidth5, $y);
        
        $pdf->MultiCell($colWidth6, 10, "Expiry Date", 1, 'C');
        
        $pdf->Ln();
        $pdf->SetFont('Times', '', 7);
    }

    $startX = $pdf->GetX();
    $startY = $pdf->GetY();

    // Title with wrapping - use MultiCell for proper text wrapping
    $pdf->MultiCell($colWidth1, $fixedRowHeight, substr($license['license_tittle'], 0, 50), 1, 'L');
    $pdf->SetXY($startX + $colWidth1, $startY);
    
    // Year taken
    $pdf->Cell($colWidth2, $fixedRowHeight, substr($license['year_taken'], 0, 10), 1, 0, 'C');
    
    // Examination venue with wrapping - use MultiCell for proper text wrapping
    $pdf->MultiCell($colWidth3, $fixedRowHeight, substr($license['examination_venue'], 0, 50), 1, 'L');
    $pdf->SetXY($startX + $colWidth1 + $colWidth2 + $colWidth3, $startY);

    // Ratings
    $pdf->Cell($colWidth4, $fixedRowHeight, substr($license['ratings'], 0, 10), 1, 0, 'R');
    
    // Remarks
    $pdf->Cell($colWidth5, $fixedRowHeight, substr($license['remarks'], 0, 15), 1, 0, 'L');
    
    // Expiry date
    $pdf->Cell($colWidth6, $fixedRowHeight, substr($license['expiry_date'], 0, 15), 1, 1, 'L');
}

$pdf->Cell(0, 5, '(For more information, indicate on a separate sheet)', 0, 1);

// 7. Competency Assessment Passed
$pdf->Ln(5);
$pdf->SetFont('Times', 'B', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '7. Competency Assessment Passed', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1); 

// Define column widths for consistency
$colWidth1 = 40; // Industry Sector
$colWidth2 = 28; // Trade Area
$colWidth3 = 40; // Occupation 
$colWidth4 = 28; // Classification Level
$colWidth5 = 27; // Competency
$colWidth6 = 27; // Specialization

$pdf->SetFont('Times', '', 9);

// Table headers
$startX = $pdf->GetX();
$startY = $pdf->GetY();

// Industry Sector
$pdf->MultiCell($colWidth1, 15, "Industry Sector", 1, 'C'); 
$pdf->SetXY($startX + $colWidth1, $startY); 

// Trade Area
$pdf->MultiCell($colWidth2, 15, "Trade Area", 1, 'C'); 
$pdf->SetXY($startX + $colWidth1 + $colWidth2, $startY);

// Occupation
$pdf->MultiCell($colWidth3, 15, "Occupation", 1, 'C'); 
$pdf->SetXY($startX + $colWidth1 + $colWidth2 + $colWidth3, $startY); 

// Classification Level
$pdf->MultiCell($colWidth4, 15, "Classification Level", 1, 'C'); 
$pdf->SetXY($startX + $colWidth1 + $colWidth2 + $colWidth3 + $colWidth4, $startY); 

// Competency
$pdf->MultiCell($colWidth5, 15, "Competency", 1, 'C'); 
$pdf->SetXY($startX + $colWidth1 + $colWidth2 + $colWidth3 + $colWidth4 + $colWidth5, $startY); 

// Specialization
$pdf->MultiCell($colWidth6, 15, "Specialization", 1, 'C'); 

$pdf->SetFont('Times', '', 7);
$fixedRowHeight = 20;

foreach ($competency_assessment as $competency) {
    $startX = $pdf->GetX();
    $startY = $pdf->GetY();

    // Industry sector with wrapping
    $pdf->MultiCell($colWidth1, $fixedRowHeight, substr($competency['industry_sector'], 0, 50), 1, 'L');
    $pdf->SetXY($startX + $colWidth1, $startY);
    
    // Trade Area
    $pdf->Cell($colWidth2, $fixedRowHeight, substr($competency['trade_area'], 0, 30), 1, 0, 'L');
    
    // Occupation
    $pdf->Cell($colWidth3, $fixedRowHeight, substr($competency['occupation'], 0, 40), 1, 0, 'L');
    
    // Classification Level
    $pdf->Cell($colWidth4, $fixedRowHeight, substr($competency['classification_level'], 0, 30), 1, 0, 'L');
    
    // Competency with wrapping
    $pdf->MultiCell($colWidth5, $fixedRowHeight, substr($competency['competency'], 0, 40), 1, 'L');
    $pdf->SetXY($startX + $colWidth1 + $colWidth2 + $colWidth3 + $colWidth4 + $colWidth5, $startY);
    
    // Specialization
    $pdf->Cell($colWidth6, $fixedRowHeight, substr($competency['specialization'], 0, 30), 1, 1, 'L');
}
$pdf->Cell(0, 5, '(For more information, indicate on a sperate sheet)', 0, 1);

// 8. Family Background
$pdf->Ln(5);
$pdf->SetFont('Times', '', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '8. Family Background', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1);
$x = $pdf->GetX();
$y = $pdf->GetY();
$width = 190;  
$height = 20;
$section_height = $height * 4 + 10; // Total height needed for all 4 sections + some spacing

// Check if we need a page break before drawing family sections
if ($y + $section_height > $pdf->getPageHeight() - 25) {
    $pdf->AddPage();
    $y = $pdf->GetY();
}

// Draw the rectangle for spouse
$pdf->Rect($x, $y, $width, $height);

$pdf->SetFont('Times', '', 9);

$pdf->Cell(30, 5, 'Spouse\'s Name:', 0, 0);
$pdf->Cell(65, 5, $family['spouse_name'], 'B', 0);
$pdf->SetX(110); 
$pdf->Cell(25, 5, 'Occupation:', 0, 0);
$pdf->Cell(60, 5, $family['spouse_occupation'], 'B', 1);
$pdf->Ln(3);

$pdf->SetX($x);

$pdf->Cell(40, 5, 'Educational Attainment:', 0, 0);
$pdf->Cell(55, 5, $family['spouse_educational_attainment'], 'B', 0);
$pdf->SetX(110); 
$pdf->Cell(40, 5, 'Ave. Monthly Income:', 0, 0);
$pdf->Cell(45, 5, $family['spouse_monthly_income'], 'B', 1);

// Draw the rectangle for father
$new_y = $y + $height;
$pdf->Rect($x, $new_y, $width, $height);
$pdf->SetY($new_y + 3); 
$pdf->SetX($x); 

// Father information
$pdf->SetFont('Times', '', 9);

$pdf->Cell(30, 5, 'Father\'s Name:', 0, 0);
$pdf->Cell(65, 5, $family['father_name'], 'B', 0);
$pdf->SetX(110); 
$pdf->Cell(25, 5, 'Occupation:', 0, 0);
$pdf->Cell(60, 5, $family['father_occupation'], 'B', 1);
$pdf->Ln(3);

$pdf->SetX($x);

$pdf->Cell(40, 5, 'Educational Attainment:', 0, 0);
$pdf->Cell(55, 5, $family['father_educational_attainment'], 'B', 0);
$pdf->SetX(110); 
$pdf->Cell(40, 5, 'Ave. Monthly Income:', 0, 0);
$pdf->Cell(45, 5, $family['father_monthly_income'], 'B', 1);
$pdf->Ln(3);

// Draw the rectangle for mother
$mother_y = $new_y + $height;
$pdf->Rect($x, $mother_y, $width, $height);
$pdf->SetY($mother_y + 3); 
$pdf->SetX($x); 

// Mother information
$pdf->SetFont('Times', '', 9);

$pdf->Cell(30, 5, 'Mother\'s Name:', 0, 0);
$pdf->Cell(65, 5, $family['mother_name'], 'B', 0);
$pdf->SetX(110); 
$pdf->Cell(25, 5, 'Occupation:', 0, 0);
$pdf->Cell(60, 5, $family['mother_occupation'], 'B', 1);
$pdf->Ln(3);

$pdf->SetX($x);

$pdf->Cell(40, 5, 'Educational Attainment:', 0, 0);
$pdf->Cell(55, 5, $family['mother_educational_attainment'], 'B', 0);
$pdf->SetX(110); 
$pdf->Cell(40, 5, 'Ave. Monthly Income:', 0, 0);
$pdf->Cell(45, 5, $family['mother_monthly_income'], 'B', 1);
$pdf->Ln(3);

// Draw the rectangle for guardian
$guardian_y = $mother_y + $height;
$pdf->Rect($x, $guardian_y, $width, $height);
$pdf->SetY($guardian_y + 3); 
$pdf->SetX($x); 

// Guardian information
$pdf->SetFont('Times', '', 9);

$pdf->Cell(45, 5, 'Name of Guardian\'s Name:', 0, 0);
$pdf->Cell(50, 5, $family['guardian_name'], 'B', 0);
$pdf->SetX(110); 
$pdf->Cell(25, 5, 'Occupation:', 0, 0);
$pdf->Cell(60, 5, $family['guardian_occupation'], 'B', 1);
$pdf->Ln(3);

$pdf->SetX($x);

$pdf->Cell(40, 5, 'Educational Attainment:', 0, 0);
$pdf->Cell(55, 5, $family['guardian_educational_attainment'], 'B', 0);
$pdf->SetX(110); 
$pdf->Cell(40, 5, 'Ave. Monthly Income:', 0, 0);
$pdf->Cell(45, 5, $family['guardian_monthly_income'], 'B', 1);
$pdf->Ln(4);

// Draw table for dependents
$dependents_y = $guardian_y + $height + 1; 
$pdf->SetY($dependents_y);
$x = $pdf->GetX();
$y = $pdf->GetY();

// Dependents headers
$pdf->MultiCell(100, 15, "Dependents", 1, 'C'); 
$pdf->SetXY($x + 100, $y); 
$pdf->MultiCell(90, 15, "Age", 1, 'C'); 
$pdf->SetXY($x, $y + 15); 

$pdf->Cell(100, 7, $family['dependents'], 1);
$pdf->Cell(90, 7, $family['dependents_age'], 1);
$pdf->Ln();
$pdf->Cell(0, 5, '(For more information, indicate on a sperate sheet)', 0, 1);

// Output the PDF
$pdf->Output('Employee_Profile.pdf', 'D');