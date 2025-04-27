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
        $this->SetFont('times', 'B', 12);
        $this->SetXY(35, 10);
        //$this->Cell(0, 6, 'Student Internship', 0, 1, 'L');
        
        $this->SetFont('times', '', 10);
        $this->SetXY(35, 16);
        //$this->Cell(0, 6, 'Pangasiwaan sa Edukasyong Teknikal at Pagpapaunlad ng Kasanayan', 0, 1, 'L');
        
        // Form Title (right aligned)
        $this->SetFont('Times', 'B', 9);
        $this->SetXY(145, 20);
       // $this->Cell(50, 6, 'NMIS FORM -01A', 0, 1, 'R');
        $this->SetFont('Times', '', 8);
        $this->SetXY(145, 25);
        //$this->Cell(50, 6, '(For TPIS)', 0, 1, 'R');
        
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
}

// Create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Internship System');
$pdf->SetAuthor('Internship');
$pdf->SetTitle('Student Internship Profile');

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
$pdf->SetFont('Times', 'B', 20);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 10, 'STUDENT INTERNSHIP PROFILE', 0, 1, 'C');
$pdf->SetTextColor(0, 0, 0);

//line below
$pdf->Line(10, 40, 200, 40); 
$pdf->Line(10, 40.5, 200, 40.5);


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

// Section 1 - Student Information
$pdf->SetXY(15, 84);
$pdf->SetFont('Times', 'B', 12);
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
$pdf->Cell(0, 8, '1. Student Profile', 0, 1, 'L', true); 
$pdf->SetTextColor(0, 0, 0);

// Name Fields
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(20, 10, 'Name:', 0, 0);

$pdf->SetFont('Times', '', 5);
$pdf->SetXY(50, 113);
$pdf->SetFont('Times', '', 9);
$pdf->Cell(50, 6, $user['lastname'], 1, 0, 'C');
$pdf->Cell(50, 6, $user['firstname'], 1, 0, 'C'); 
$pdf->Cell(50, 6, $user['middlename'], 1, 1, 'C'); 

// Move down before adding labels
$pdf->Ln(2);

// Set new X position for labels to align with the boxes
$pdf->SetXY(50, 119); 
$pdf->SetFont('Times', '', 10);
$pdf->Cell(50, 5, 'Last', 0, 0, 'C'); 
$pdf->Cell(50, 5, 'First', 0, 0, 'C'); 
$pdf->Cell(50, 5, 'Middle', 0, 1, 'C'); 


// Address
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(30, 10, 'Mailing Address:', 0, 0);

$pdf->SetFont('Times', '', 9);
$pdf->SetXY(50, 127);
$pdf->Cell(50, 5, $user['address_street'], 1, 0, 'C');
$pdf->Cell(50, 5, $user['address_barangay'], 1, 0, 'C');
$pdf->Cell(50, 5, $user['address_district'], 1, 1, 'C'); 

$pdf->SetXY(50, 132);
$pdf->SetFont('Times', '', 10);
$pdf->Cell(50, 5, 'Number, Street', 0, 0, 'C'); 
$pdf->Cell(50, 5, 'Barangay', 0, 0, 'C'); 
$pdf->Cell(50, 5, 'Congressional District', 0, 1, 'C');

// City, Province, Region
$pdf->SetFont('Times', '', 9);
$pdf->SetXY(50, 140);
$pdf->Cell(50, 5, $user['address_city'], 1, 0, 'C');
$pdf->Cell(50, 5, $user['address_province'], 1, 0, 'C');
$pdf->Cell(50, 5, $user['address_region'], 1, 1, 'C');

$pdf->SetXY(50, 145);
$pdf->SetFont('Times', '', 10);
$pdf->Cell(50, 5, 'City/Municipality', 0, 0, 'C');
$pdf->Cell(50, 5, 'Province', 0, 0, 'C');
$pdf->Cell(50, 5, 'Region', 0, 1, 'C');

// Personal Details Section
$pdf->Ln(5);
$pdf->SetFont('Times', '', 10);

// Create two columns for details
$pdf->SetX(15);
$leftColumn = 95;
$rightColumn = 95;


//////

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

// Define column widths
$colWidths = [
    'school' => 33,
    'level' => 22,
    'year' => 26,
    'degree' => 25,
    'minor' => 21,
    'major' => 21,
    'units' => 21,
    'honors' => 21
];

$fixedRowHeight = 20; // Fixed height for each row
$headerHeight = 14; // Height for header row

// Function to draw the header
function drawEducationHeader($pdf, $colWidths, $headerHeight) {
    $pdf->SetFont('Times', '', 10);
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    
    $pdf->MultiCell($colWidths['school'], $headerHeight, "School", 1, 'C'); 
    $pdf->SetXY($x + $colWidths['school'], $y); 
    $pdf->MultiCell($colWidths['level'], $headerHeight, "Educational\nLevel", 1, 'C'); 
    $pdf->SetXY($x + $colWidths['school'] + $colWidths['level'], $y);
    $pdf->MultiCell($colWidths['year'], $headerHeight, "School\nYear", 1, 'C'); 
    $pdf->SetXY($x + $colWidths['school'] + $colWidths['level'] + $colWidths['year'], $y);
    $pdf->MultiCell($colWidths['degree'], $headerHeight, "Degree", 1, 'C'); 
    $pdf->SetXY($x + $colWidths['school'] + $colWidths['level'] + $colWidths['year'] + $colWidths['degree'], $y);
    $pdf->MultiCell($colWidths['minor'], $headerHeight, "Minor", 1, 'C'); 
    $pdf->SetXY($x + $colWidths['school'] + $colWidths['level'] + $colWidths['year'] + $colWidths['degree'] + $colWidths['minor'], $y);
    $pdf->MultiCell($colWidths['major'], $headerHeight, "Major", 1, 'C'); 
    $pdf->SetXY($x + $colWidths['school'] + $colWidths['level'] + $colWidths['year'] + $colWidths['degree'] + $colWidths['minor'] + $colWidths['major'], $y);
    $pdf->MultiCell($colWidths['units'], $headerHeight, "Units\nEarned", 1, 'C'); 
    $pdf->SetXY($x + $colWidths['school'] + $colWidths['level'] + $colWidths['year'] + $colWidths['degree'] + $colWidths['minor'] + $colWidths['major'] + $colWidths['units'], $y);
    $pdf->MultiCell($colWidths['honors'], $headerHeight, "Honor\nReceived", 1, 'C');
    
    $pdf->SetFont('Times', '', 7);
    $pdf->SetY($y + $headerHeight);
}

// Draw initial header
drawEducationHeader($pdf, $colWidths, $headerHeight);

foreach ($education as $edu) {
    $startX = $pdf->GetX();
    $startY = $pdf->GetY();
    
    // Check if we need a page break (including space for header if needed)
    $margins = $pdf->getMargins();
    if ($startY + $fixedRowHeight + $headerHeight > $pdf->GetPageHeight() - $margins['bottom']) {
        $pdf->AddPage();
        drawEducationHeader($pdf, $colWidths, $headerHeight);
        $startX = $pdf->GetX();
        $startY = $pdf->GetY();
    }

    // School name with wrapping
    $pdf->MultiCell($colWidths['school'], $fixedRowHeight, substr($edu['school_name'], 0, 40), 1, 'L');
    $pdf->SetXY($startX + $colWidths['school'], $startY);
    
    // Educational level
    $pdf->Cell($colWidths['level'], $fixedRowHeight, substr($edu['educational_level'], 0, 30), 1, 0, 'L');
    
    // School year
    $pdf->Cell($colWidths['year'], $fixedRowHeight, substr($edu['year_from'], 0, 4) . '-' . substr($edu['year_to'], 0, 4), 1, 0, 'C');
    
    // Degree - using MultiCell for text wrapping
    $pdf->MultiCell($colWidths['degree'], $fixedRowHeight, substr($edu['degree'], 0, 50), 1, 'L');
    $pdf->SetXY($startX + $colWidths['school'] + $colWidths['level'] + $colWidths['year'] + $colWidths['degree'], $startY);
    
    // Minor
    $pdf->Cell($colWidths['minor'], $fixedRowHeight, substr($edu['minor'], 0, 25), 1, 0, 'L');
    
    // Major
    $pdf->Cell($colWidths['major'], $fixedRowHeight, substr($edu['major'], 0, 25), 1, 0, 'L');
    
    // Units Earned
    $pdf->Cell($colWidths['units'], $fixedRowHeight, substr($edu['units_earned'], 0, 15), 1, 0, 'L');
    
    // Honors Received
    $pdf->Cell($colWidths['honors'], $fixedRowHeight, substr($edu['honors'], 0, 30), 1, 1, 'L');
}

// 4. Working Experience
$pdf->Ln(5);
$pdf->SetFont('Times', 'B', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '4. Working Experience (For Trainers, mandatory field 5.5)', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1); 

// Define column widths
$colWidths = [
    'company' => 31,
    'position' => 27,
    'dates' => 28,
    'salary' => 25,
    'occupation' => 30,
    'status' => 25,
    'experience' => 24
];

$fixedRowHeight = 20; // Fixed height for each row
$headerHeight = 24; // Height for header row

// Function to draw the header
function drawWorkExperienceHeader($pdf, $colWidths, $headerHeight) {
    $pdf->SetFont('Times', '', 10);
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // Name of Company
    $pdf->MultiCell($colWidths['company'], $headerHeight, "Name of Company", 1, 'C');
    $pdf->SetXY($x + $colWidths['company'], $y);

    // Position
    $pdf->MultiCell($colWidths['position'], $headerHeight, "Position", 1, 'C');
    $pdf->SetXY($x + $colWidths['company'] + $colWidths['position'], $y);

    // Inclusive Dates
    $pdf->MultiCell($colWidths['dates'], $headerHeight, "Inclusive Dates", 1, 'C');
    $pdf->SetXY($x + $colWidths['company'] + $colWidths['position'] + $colWidths['dates'], $y);

    // Monthly Salary
    $pdf->MultiCell($colWidths['salary'], $headerHeight, "Monthly Salary", 1, 'C');
    $pdf->SetXY($x + $colWidths['company'] + $colWidths['position'] + $colWidths['dates'] + $colWidths['salary'], $y);

    // Occupation Type
    $pdf->MultiCell($colWidths['occupation'], $headerHeight, "Occupation Type\n(Teaching; Non-Teaching;\nIndustrial Experience)", 1, 'C');
    $pdf->SetXY($x + $colWidths['company'] + $colWidths['position'] + $colWidths['dates'] + $colWidths['salary'] + $colWidths['occupation'], $y);

    // Status of Appointment
    $pdf->MultiCell($colWidths['status'], $headerHeight, "Status of\nAppointment", 1, 'C');
    $pdf->SetXY($x + $colWidths['company'] + $colWidths['position'] + $colWidths['dates'] + $colWidths['salary'] + $colWidths['occupation'] + $colWidths['status'], $y);

    // No. of Yrs. Working Exp
    $pdf->MultiCell($colWidths['experience'], $headerHeight, "No. of Yrs.\nWorking\nExp", 1, 'C');

    $pdf->SetFont('Times', '', 7);
    $pdf->SetY($y + $headerHeight);
}

// Draw initial header
drawWorkExperienceHeader($pdf, $colWidths, $headerHeight);

foreach ($work_experience as $work) {
    $startX = $pdf->GetX();
    $startY = $pdf->GetY();
    
    // Check if we need a page break (including space for header if needed)
    $margins = $pdf->getMargins();
    if ($startY + $fixedRowHeight + $headerHeight > $pdf->GetPageHeight() - $margins['bottom']) {
        $pdf->AddPage();
        drawWorkExperienceHeader($pdf, $colWidths, $headerHeight);
        $startX = $pdf->GetX();
        $startY = $pdf->GetY();
    }

    // Company name with wrapping
    $pdf->MultiCell($colWidths['company'], $fixedRowHeight, substr($work['company_name'], 0, 40), 1, 'L');
    $pdf->SetXY($startX + $colWidths['company'], $startY);
    
    // Position with wrapping
    $pdf->MultiCell($colWidths['position'], $fixedRowHeight, substr($work['position'], 0, 30), 1, 'L');
    $pdf->SetXY($startX + $colWidths['company'] + $colWidths['position'], $startY);
    
    // Inclusive Dates - using MultiCell for text wrapping
    $datesText = substr($work['inclusive_dates_past'], 0, 10) . ' - ' . substr($work['inclusive_dates_present'], 0, 10);
    $pdf->MultiCell($colWidths['dates'], $fixedRowHeight, $datesText, 1, 'C');
    $pdf->SetXY($startX + $colWidths['company'] + $colWidths['position'] + $colWidths['dates'], $startY);
    
    // Monthly Salary - Show "CONFIDENTIAL" if empty
    $salaryText = (!empty(trim($work['monthly_salary']))) ? substr($work['monthly_salary'], 0, 20) : 'CONFIDENTIAL';
    $pdf->Cell($colWidths['salary'], $fixedRowHeight, $salaryText, 1, 0, 'C');
    
    // Occupation Type with wrapping
    $pdf->MultiCell($colWidths['occupation'], $fixedRowHeight, substr($work['occupation'], 0, 30), 1, 'L');
    $pdf->SetXY($startX + $colWidths['company'] + $colWidths['position'] + $colWidths['dates'] + $colWidths['salary'] + $colWidths['occupation'], $startY);
    
    // Status of Appointment
    $pdf->Cell($colWidths['status'], $fixedRowHeight, substr($work['status'], 0, 25), 1, 0, 'L');
    
    // No. of Yrs. Working Exp
    $pdf->Cell($colWidths['experience'], $fixedRowHeight, substr($work['working_experience'], 0, 20), 1, 1, 'L');
}

$pdf->Cell(0, 5, '(For more information, indicate on a separate sheet)', 0, 1);

// 5. Training/Seminars Attended
$pdf->Ln(5);
$pdf->SetFont('Times', 'B', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '5. Training/Seminars Attended', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1); 

// Define column widths (adjusted for better text wrapping)
$colWidths = [
    'title' => 30,       // Increased from 25
    'venue' => 25,       // Increased from 25
    'dates' => 25,       // Combined dates
    'certificate' => 20, 
    'hours' => 15,
    'base' => 20,
    'category' => 17,
    'conducted' => 22,   // Increased from 20
    'proficiency' => 16
];

$fixedRowHeight = 20;    
$headerHeight = 15;      

// Function to draw the training header
function drawTrainingHeader($pdf, $colWidths, $headerHeight) {
    $pdf->SetFont('Times', '', 8); // Slightly larger font for headers
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // Title (now with more width)
    $pdf->MultiCell($colWidths['title'], $headerHeight, "Title", 1, 'C');
    $pdf->SetXY($x + $colWidths['title'], $y);

    // Venue
    $pdf->MultiCell($colWidths['venue'], $headerHeight, "Venue", 1, 'C');
    $pdf->SetXY($x + $colWidths['title'] + $colWidths['venue'], $y);

    // Inclusive Dates (combined into one cell)
    $pdf->MultiCell($colWidths['dates'], $headerHeight, "Inclusive Dates", 1, 'C');
    $pdf->SetXY($x + $colWidths['title'] + $colWidths['venue'] + $colWidths['dates'], $y);

    // Certificate Received
    $pdf->MultiCell($colWidths['certificate'], $headerHeight, "Certificate", 1, 'C');
    $pdf->SetXY($x + $colWidths['title'] + $colWidths['venue'] + $colWidths['dates'] + $colWidths['certificate'], $y);

    // # of Hours
    $pdf->MultiCell($colWidths['hours'], $headerHeight, "Hours", 1, 'C');
    $pdf->SetXY($x + $colWidths['title'] + $colWidths['venue'] + $colWidths['dates'] + $colWidths['certificate'] + $colWidths['hours'], $y);

    // Training Base
    $pdf->MultiCell($colWidths['base'], $headerHeight, "Training Base", 1, 'C');
    $pdf->SetXY($x + $colWidths['title'] + $colWidths['venue'] + $colWidths['dates'] + $colWidths['certificate'] + $colWidths['hours'] + $colWidths['base'], $y);

    // Category
    $pdf->MultiCell($colWidths['category'], $headerHeight, "Category", 1, 'C');
    $pdf->SetXY($x + $colWidths['title'] + $colWidths['venue'] + $colWidths['dates'] + $colWidths['certificate'] + $colWidths['hours'] + $colWidths['base'] + $colWidths['category'], $y);

    // Conducted By
    $pdf->MultiCell($colWidths['conducted'], $headerHeight, "Conducted By", 1, 'C');
    $pdf->SetXY($x + $colWidths['title'] + $colWidths['venue'] + $colWidths['dates'] + $colWidths['certificate'] + $colWidths['hours'] + $colWidths['base'] + $colWidths['category'] + $colWidths['conducted'], $y);

    // Proficiency
    $pdf->MultiCell($colWidths['proficiency'], $headerHeight, "Proficiency", 1, 'C');
    
    return $y + $headerHeight; // Return the Y position after drawing the header
}

// Draw the initial header
$y = drawTrainingHeader($pdf, $colWidths, $headerHeight);

$pdf->SetFont('Times', '', 7); // Data font size
$pdf->SetY($y); // Position cursor right below header

foreach ($training_seminar as $training) {
    $startX = $pdf->GetX();
    $startY = $pdf->GetY();
    
    // Check if we need a page break
    $margins = $pdf->getMargins();
    if ($startY + $fixedRowHeight > $pdf->GetPageHeight() - $margins['bottom']) {
        $pdf->AddPage();
        // Redraw header on new page
        $startY = drawTrainingHeader($pdf, $colWidths, $headerHeight);
        $startX = $pdf->GetX();
        $pdf->SetFont('Times', '', 7); // Reset font for data
    }

    // Title with wrapping (no substr limit)
    $pdf->MultiCell($colWidths['title'], $fixedRowHeight, $training['tittle'], 1, 'L');
    $pdf->SetXY($startX + $colWidths['title'], $startY);
    
    // Venue with wrapping
    $pdf->MultiCell($colWidths['venue'], $fixedRowHeight, substr($training['venue'], 0, 40), 1, 'L');
    $pdf->SetXY($startX + $colWidths['title'] + $colWidths['venue'], $startY);
    
    // Combined dates
    $dates = substr($training['inclusive_dates_past'], 0, 10).' - '.substr($training['inclusive_dates_present'], 0, 10);
    $pdf->MultiCell($colWidths['dates'], $fixedRowHeight, $dates, 1, 'C');
    $pdf->SetXY($startX + $colWidths['title'] + $colWidths['venue'] + $colWidths['dates'], $startY);
    
    // Certificate Received
    $pdf->MultiCell($colWidths['certificate'], $fixedRowHeight, substr($training['certificate'], 0, 20), 1, 'L');
    $pdf->SetXY($startX + $colWidths['title'] + $colWidths['venue'] + $colWidths['dates'] + $colWidths['certificate'], $startY);
    
    // # of Hours
    $pdf->MultiCell($colWidths['hours'], $fixedRowHeight, substr($training['no_of_hours'], 0, 8), 1, 'C');
    $pdf->SetXY($startX + $colWidths['title'] + $colWidths['venue'] + $colWidths['dates'] + $colWidths['certificate'] + $colWidths['hours'], $startY);
    
    // Training Base
    $pdf->MultiCell($colWidths['base'], $fixedRowHeight, substr($training['training_base'], 0, 20), 1, 'L');
    $pdf->SetXY($startX + $colWidths['title'] + $colWidths['venue'] + $colWidths['dates'] + $colWidths['certificate'] + $colWidths['hours'] + $colWidths['base'], $startY);
    
    // Category
    $pdf->MultiCell($colWidths['category'], $fixedRowHeight, substr($training['category'], 0, 15), 1, 'L');
    $pdf->SetXY($startX + $colWidths['title'] + $colWidths['venue'] + $colWidths['dates'] + $colWidths['certificate'] + $colWidths['hours'] + $colWidths['base'] + $colWidths['category'], $startY);
    
    // Conducted By
    $pdf->MultiCell($colWidths['conducted'], $fixedRowHeight, substr($training['conducted_by'], 0, 40), 1, 'L');
    $pdf->SetXY($startX + $colWidths['title'] + $colWidths['venue'] + $colWidths['dates'] + $colWidths['certificate'] + $colWidths['hours'] + $colWidths['base'] + $colWidths['category'] + $colWidths['conducted'], $startY);
    
    // Proficiency
    $pdf->MultiCell($colWidths['proficiency'], $fixedRowHeight, substr($training['proficiency'], 0, 15), 1, 'L');
    
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

$pdf->Ln(1);

// 6. Licenses/Examination Passed
$pdf->Ln(10);
$pdf->SetFont('Times', 'B', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '6. Licenses/Examination Passed', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1);

// Define column widths
$colWidths = [
    'title' => 45,
    'year' => 22,
    'venue' => 45, 
    'ratings' => 25,
    'remarks' => 25,
    'expiry' => 28
];

$fixedRowHeight = 20; // Fixed height for each row
$headerHeight = 15; // Height for header row

// Function to draw the license header
function drawLicenseHeader($pdf, $colWidths, $headerHeight) {
    $pdf->SetFont('Times', '', 10);
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // Title
    $pdf->MultiCell($colWidths['title'], $headerHeight, "Title", 1, 'C');
    $pdf->SetXY($x + $colWidths['title'], $y);

    // Year Taken
    $pdf->MultiCell($colWidths['year'], $headerHeight, "Year Taken", 1, 'C');
    $pdf->SetXY($x + $colWidths['title'] + $colWidths['year'], $y);

    // Examination Venue
    $pdf->MultiCell($colWidths['venue'], $headerHeight, "Examination Venue", 1, 'C');
    $pdf->SetXY($x + $colWidths['title'] + $colWidths['year'] + $colWidths['venue'], $y);

    // Ratings
    $pdf->MultiCell($colWidths['ratings'], $headerHeight, "Ratings", 1, 'C');
    $pdf->SetXY($x + $colWidths['title'] + $colWidths['year'] + $colWidths['venue'] + $colWidths['ratings'], $y);

    // Remarks
    $pdf->MultiCell($colWidths['remarks'], $headerHeight, "Remarks", 1, 'C');
    $pdf->SetXY($x + $colWidths['title'] + $colWidths['year'] + $colWidths['venue'] + $colWidths['ratings'] + $colWidths['remarks'], $y);

    // Expiry Date
    $pdf->MultiCell($colWidths['expiry'], $headerHeight, "Expiry Date", 1, 'C');
    
    return $y + $headerHeight; // Return the Y position after drawing the header
}

// Draw the initial license header
$y = drawLicenseHeader($pdf, $colWidths, $headerHeight);

$pdf->SetFont('Times', '', 7);
$pdf->SetY($y); // Position cursor right below header

foreach ($license_examination as $license) {
    $startX = $pdf->GetX();
    $startY = $pdf->GetY();
    
    // Check if we need a page break
    $margins = $pdf->getMargins();
    if ($startY + $fixedRowHeight > $pdf->GetPageHeight() - $margins['bottom']) {
        $pdf->AddPage();
        // Redraw header on new page
        $startY = drawLicenseHeader($pdf, $colWidths, $headerHeight);
        $startX = $pdf->GetX();
        $pdf->SetFont('Times', '', 7); // Reset font for data
    }

    // Title with wrapping
    $pdf->MultiCell($colWidths['title'], $fixedRowHeight, substr($license['license_tittle'], 0, 50), 1, 'L');
    $pdf->SetXY($startX + $colWidths['title'], $startY);
    
    // Year taken
    $pdf->Cell($colWidths['year'], $fixedRowHeight, substr($license['year_taken'], 0, 10), 1, 0, 'C');
    
    // Examination venue
    $pdf->MultiCell($colWidths['venue'], $fixedRowHeight, substr($license['examination_venue'], 0, 50), 1, 'L');
    $pdf->SetXY($startX + $colWidths['title'] + $colWidths['year'] + $colWidths['venue'], $startY);

    // Ratings
    $pdf->Cell($colWidths['ratings'], $fixedRowHeight, substr($license['ratings'], 0, 10), 1, 0, 'R');
    
    // Remarks
    $pdf->Cell($colWidths['remarks'], $fixedRowHeight, substr($license['remarks'], 0, 15), 1, 0, 'L');
    
    // Expiry date
    $pdf->Cell($colWidths['expiry'], $fixedRowHeight, substr($license['expiry_date'], 0, 15), 1, 1, 'L');
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

// Define column widths
$colWidths = [
    'sector' => 40,
    'trade' => 28,
    'occupation' => 40,
    'level' => 28,
    'competency' => 27,
    'specialization' => 27
];

$fixedRowHeight = 20; // Fixed height for each row
$headerHeight = 10; // Height for header row

// Function to draw the header
function drawCompetencyHeader($pdf, $colWidths, $headerHeight) {
    $pdf->SetFont('Times', '', 10);
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // Industry Sector
    $pdf->MultiCell($colWidths['sector'], $headerHeight, "Industry Sector", 1, 'C');
    $pdf->SetXY($x + $colWidths['sector'], $y);

    // Trade Area
    $pdf->MultiCell($colWidths['trade'], $headerHeight, "Trade Area", 1, 'C');
    $pdf->SetXY($x + $colWidths['sector'] + $colWidths['trade'], $y);

    // Occupation
    $pdf->MultiCell($colWidths['occupation'], $headerHeight, "Occupation", 1, 'C');
    $pdf->SetXY($x + $colWidths['sector'] + $colWidths['trade'] + $colWidths['occupation'], $y);

    // Classification Level
    $pdf->MultiCell($colWidths['level'], $headerHeight, "Classification Level", 1, 'C');
    $pdf->SetXY($x + $colWidths['sector'] + $colWidths['trade'] + $colWidths['occupation'] + $colWidths['level'], $y);

    // Competency
    $pdf->MultiCell($colWidths['competency'], $headerHeight, "Competency", 1, 'C');
    $pdf->SetXY($x + $colWidths['sector'] + $colWidths['trade'] + $colWidths['occupation'] + $colWidths['level'] + $colWidths['competency'], $y);

    // Specialization
    $pdf->MultiCell($colWidths['specialization'], $headerHeight, "Specialization", 1, 'C');

    $pdf->SetFont('Times', '', 8);
    $pdf->SetY($y + $headerHeight);
}

// Draw initial header
drawCompetencyHeader($pdf, $colWidths, $headerHeight);

foreach ($competency_assessment as $competency) {
    $startX = $pdf->GetX();
    $startY = $pdf->GetY();
    
    // Check if we need a page break (including space for header if needed)
    $margins = $pdf->getMargins();
    if ($startY + $fixedRowHeight + $headerHeight > $pdf->GetPageHeight() - $margins['bottom']) {
        $pdf->AddPage();
        drawCompetencyHeader($pdf, $colWidths, $headerHeight);
        $startX = $pdf->GetX();
        $startY = $pdf->GetY();
    }

    // Industry sector with wrapping
    $pdf->MultiCell($colWidths['sector'], $fixedRowHeight, substr($competency['industry_sector'], 0, 50), 1, 'L');
    $pdf->SetXY($startX + $colWidths['sector'], $startY);
    
    // Trade Area
    $pdf->Cell($colWidths['trade'], $fixedRowHeight, substr($competency['trade_area'], 0, 30), 1, 0, 'L');
    
    // Occupation
    $pdf->Cell($colWidths['occupation'], $fixedRowHeight, substr($competency['occupation'], 0, 40), 1, 0, 'L');
    
    // Classification Level
    $pdf->Cell($colWidths['level'], $fixedRowHeight, substr($competency['classification_level'], 0, 30), 1, 0, 'L');
    
    // Competency with wrapping
    $pdf->MultiCell($colWidths['competency'], $fixedRowHeight, substr($competency['competency'], 0, 40), 1, 'L');
    $pdf->SetXY($startX + $colWidths['sector'] + $colWidths['trade'] + $colWidths['occupation'] + $colWidths['level'] + $colWidths['competency'], $startY);
    
    // Specialization
    $pdf->Cell($colWidths['specialization'], $fixedRowHeight, substr($competency['specialization'], 0, 30), 1, 1, 'L');
}

$pdf->Cell(0, 5, '(For more information, indicate on a separate sheet)', 0, 1);

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
$pdf->Output('Student_Internship.pdf', 'D');