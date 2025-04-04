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
            $this->Image('assets/inafarm_long logo.png', 15, 13, 60);
            
            // Header Text
            //$this->SetFont('times', 'B', 12);
            //$this->SetXY(35, 10);
            //$this->Cell(0, 6, 'Ina Farm Employee', 0, 1, 'L');
            
            $this->SetFont('times', '', 10);
            $this->SetXY(35, 16);
            
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
$pdf->SetFont('Times', 'B', 16);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 10, 'INA FARM EMPLOYEE PROFILE', 0, 1, 'C');
$pdf->SetTextColor(0, 0, 0);

//line below
$pdf->Line(15, 40, 195, 40); 
$pdf->Line(15, 40.5, 195, 40.5);

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
$pdf->Cell(0, 8, '1. Employee Profile', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

// Name Fields
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(20, 10, 'Name:', 0, 0);

$pdf->SetFont('Times', '', 5);
$pdf->SetXY(50, 113);
// Use WrapCell to handle text wrapping
$pdf->Cell(49, 6, substr($user['lastname'], 0, 30), 1, 0, 'C');
$pdf->Cell(48, 6, substr($user['firstname'], 0, 30), 1, 0, 'C'); 
$pdf->Cell(48, 6, substr($user['middlename'], 0, 30), 1, 1, 'C'); 

// Move down before adding labels
$pdf->Ln(2);

// Set new X position for labels to align with the boxes
$pdf->SetXY(50, 119); 
$pdf->SetFont('Times', '', 5);
$pdf->Cell(49, 5, 'Last', 0, 0, 'C'); 
$pdf->Cell(48, 5, 'First', 0, 0, 'C'); 
$pdf->Cell(48, 5, 'Middle', 0, 1, 'C'); 

// Address
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(30, 10, 'Mailing Address:', 0, 0);

$pdf->SetFont('Times', '', 5);
$pdf->SetXY(50, 127);
$pdf->Cell(49, 5, substr($user['address_street'], 0, 30), 1, 0, 'C');
$pdf->Cell(48, 5, substr($user['address_barangay'], 0, 30), 1, 0, 'C');
$pdf->Cell(48, 5, substr($user['address_district'], 0, 30), 1, 1, 'C'); 

$pdf->SetXY(50, 132);
$pdf->SetFont('Times', '', 8);
$pdf->Cell(49, 5, 'Number, Street', 0, 0, 'C'); 
$pdf->Cell(48, 5, 'Barangay', 0, 0, 'C'); 
$pdf->Cell(48, 5, 'Congressional District', 0, 1, 'C');

// City, Province, Region
$pdf->SetFont('Times', '', 5);
$pdf->SetXY(50, 140);
$pdf->Cell(49, 5, substr($user['address_city'], 0, 30), 1, 0, 'C');
$pdf->Cell(48, 5, substr($user['address_province'], 0, 30), 1, 0, 'C');
$pdf->Cell(48, 5, substr($user['address_region'], 0, 30), 1, 1, 'C');

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
$boxHeight = 40; // Adjust height based on content
$boxWidth = 30;  // Half of the page width (approx)

// Draw rectangle for "Sex"
$pdf->Rect($startX, $startY, $boxWidth, $boxHeight);
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

// Draw rectangle for "Civil Status"
$pdf->Rect($startX + $boxWidth + 0, $startY, $boxWidth, $boxHeight);
$pdf->SetXY($startX + $boxWidth + 0, $startY);
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(35, 8, 'Civil Status', 0, 1);
$pdf->SetTextColor(0, 0, 0);

// Draw Civil Status checkboxes
$pdf->SetFont('Times', '', 10);
$statuses = ['Single', 'Married', 'Widow/er', 'Separated'];
$yPosition = $startY + 8;
foreach ($statuses as $status) {
    $pdf->SetXY($startX + $boxWidth + 3, $yPosition);
    $pdf->Cell(5, 5, $user['civil_status'] == $status ? 'X' : '', 1, 0, 'C'); // Checkbox
    $pdf->Cell(25, 5, $status, 0, 1);
    $yPosition += 8;
}

// Draw rectangle for "Contact Number(s)"
$pdf->Rect($startX + ($boxWidth * 2) + 0, $startY, $boxWidth + 30, $boxHeight);
$pdf->SetXY($startX + ($boxWidth * 2) + 0, $startY);
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(40, 8, 'Contact Number(s)', 0, 1);
$pdf->SetTextColor(0, 0, 0);

// Move cursor below the title
$pdf->SetFont('Times', '', 5);
$yPosition = $startY + 8;

// Telephone Number
$pdf->SetXY($startX + ($boxWidth * 2) + 1, $yPosition);
$pdf->Cell(15, 5, 'Tel. No.:', 0, 0);
$pdf->Cell(38, 5, substr($user['tel_number'], 0, 20), 'B', 1);

// Move down for Cellular Number
$yPosition += 8;
$pdf->SetXY($startX + ($boxWidth * 2) + 1, $yPosition);
$pdf->Cell(15, 5, 'Cellular:', 0, 0);
$pdf->Cell(38, 5, substr($user['contact_number'], 0, 20), 'B', 1);

// Move down for Email
$yPosition += 8;
$pdf->SetXY($startX + ($boxWidth * 2) + 1, $yPosition);
$pdf->Cell(15, 5, 'Email:', 0, 0);
$pdf->Cell(38, 5, substr($user['email'], 0, 20), 'B', 1);

// Move down fo Fax
$yPosition += 8;
$pdf->SetXY($startX + ($boxWidth * 2) + 1, $yPosition);
$pdf->Cell(15, 5, 'Fax:', 0, 0);
$pdf->Cell(38, 5, substr($user['fax_number'], 0, 20), 'B', 1);

// Set dimensions
$boxWidth = 40;  
$boxHeight = 40; 

// Draw rectangle for "Employment Type"
$pdf->Rect($startX + ($boxWidth * 3) + 0, $startY, $boxWidth + 20, $boxHeight);
$pdf->SetXY($startX + ($boxWidth * 3) + 0, $startY);
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(40, 8, 'Employment Type', 0, 1);
$pdf->SetTextColor(0, 0, 0);

// Draw Employment Type checkboxes
$pdf->SetFont('Times', '', 10);
$employment_types = ['Employed', 'Self-Employed', 'Unemployed'];
$yPosition = $startY + 8;
foreach ($employment_types as $type) {
    $pdf->SetXY($startX + ($boxWidth * 3) + 3, $yPosition);
    $pdf->Cell(5, 5, $user['employment_type'] == $type ? 'X' : '', 1, 0, 'C'); // Checkbox
    $pdf->Cell(30, 5, $type, 0, 1);
    $yPosition += 8;
}

// 3. Personal Information
$pdf->Ln(15);
$pdf->SetFont('Times', '', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '2. Personal Information', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1);
$x = $pdf->GetX();
$y = $pdf->GetY();
$width = 180;  
$height = 40;

// Draw the rectangle 
$pdf->Rect($x, $y, $width, $height);
$pdf->Ln(3);
$pdf->SetFont('Times', '', 5);

$pdf->SetFont('Times', '', 8);
$pdf->Cell(30, 5, 'Birthdate:', 0, 0);
$pdf->Cell(40, 5, substr($user['birthdate'], 0, 20), 'B', 1);
$pdf->Ln(1);

$pdf->Cell(30, 5, 'Birthplace:', 0, 0);
$pdf->Cell(40, 5, substr($user['birth_place'], 0, 30), 'B', 1);
$pdf->Ln(1);

$pdf->Cell(30, 5, 'Citizenship:', 0, 0);
$pdf->Cell(40, 5, substr($user['citizenship'], 0, 20), 'B', 1);
$pdf->Ln(1);

$pdf->Cell(30, 5, 'Religion:', 0, 0);
$pdf->Cell(40, 5, substr($user['religion'], 0, 20), 'B', 1);
$pdf->Ln(1);

$pdf->Cell(30, 5, 'Height:', 0, 0);
$pdf->Cell(40, 5, substr($user['height'], 0, 10), 'B', 1);
$pdf->Ln(1);

$pdf->Ln(25); 

// Educational Background
$pdf->Ln(5);
$pdf->SetFont('Times', 'B', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '3. Educational Background', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1); 

$pdf->SetFont('Times', '', 7); // Smaller font for table headers

// First Row Headers
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(25, 10, "School", 1, 'C'); 
$pdf->SetXY($x + 25, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(18, 10, "Educational\nLevel", 1, 'C'); 
$pdf->SetXY($x + 18, $y); 

// "School Year" Header Spanning Two Columns
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(16, 10, "School\nYear", 1, 'C'); 
$pdf->SetXY($x + 16, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(25, 10, "Degree", 1, 'C'); 
$pdf->SetXY($x + 25, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(25, 10, "Minor", 1, 'C'); 
$pdf->SetXY($x + 25, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(25, 10, "Major", 1, 'C'); 
$pdf->SetXY($x + 25, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(25, 10, "Units\nEarned", 1, 'C'); 
$pdf->SetXY($x + 25, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(21, 10, "Honor\nReceived", 1, 'C');
$pdf->Ln(10); 

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
$pdf->Cell(0, 8, '4. Working Experience', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1); 

$pdf->SetFont('Times', '', 7); // Smaller font for table content

// Table headers
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(25, 12, "Name of Company", 1, 'C'); 
$pdf->SetXY($x + 25, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(20, 12, "Position", 1, 'C'); 
$pdf->SetXY($x + 20, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(20, 12, "Inclusive Dates", 1, 'C'); 
$pdf->SetXY($x + 20, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(20, 12, "Monthly\nSalary", 1, 'C'); 
$pdf->SetXY($x + 20, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(35, 12, "Occupation Type\n(Teaching; Non-Teaching;\nIndustrial Experience)", 1, 'C'); 
$pdf->SetXY($x + 35, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(25, 12, "Status of\nAppointment", 1, 'C'); 
$pdf->SetXY($x + 25, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(35, 12, "No. of Yrs.\nWorking\nExp", 1, 'C'); 
$pdf->Ln(12); 

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

$pdf->Cell(0, 5, '(For more information, indicate on a separate sheet)', 0, 1);

//6. Training/Seminars Attended
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

// 7. Licenses/Examination Passed
$pdf->Ln(5);
$pdf->SetFont('Times', 'B', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '6. Licenses/Examination Passed', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1); 

$pdf->SetFont('Times', '', 7);

// Table headers for licenses
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(32, 10, "Title", 1, 'C'); 
$pdf->SetXY($x + 32, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(25, 10, "Year Taken", 1, 'C'); 
$pdf->SetXY($x + 25, $y);

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(32, 10, "Examination Venue", 1, 'C'); 
$pdf->SetXY($x + 32, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(25, 10, "Ratings", 1, 'C'); 
$pdf->SetXY($x + 25, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(25, 10, "Remarks", 1, 'C'); 
$pdf->SetXY($x + 25, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(25, 10, "Expiry Date", 1, 'C'); 
$pdf->Ln(10); 

// Table data for licenses
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
$pdf->Cell(0, 5, '(For more information, indicate on a separate sheet)', 0, 1);

// 8. Competency Assessment Passed
$pdf->Ln(5);
$pdf->SetFont('Times', 'B', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '7. Competency Assessment Passed', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1); 

$pdf->SetFont('Times', '', 7);

// Table headers for competency
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(32, 10, "Industry Sector", 1, 'C'); 
$pdf->SetXY($x + 32, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(25, 10, "Trade Area", 1, 'C'); 
$pdf->SetXY($x + 25, $y);

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(32, 10, "Occupation", 1, 'C'); 
$pdf->SetXY($x + 32, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(25, 10, "Classification Level", 1, 'C'); 
$pdf->SetXY($x + 25, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(25, 10, "Competency", 1, 'C'); 
$pdf->SetXY($x + 25, $y); 

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(25, 10, "Specialization", 1, 'C'); 
$pdf->Ln(10); 

// Table data for competency
foreach ($competency_assessment as $competency) {
    // Store X position
    $startX = $pdf->GetX();
    $startY = $pdf->GetY();
    
    // Row height
    $rowHeight = 7;
    
    // Industry sector
    $pdf->MultiCell(32, $rowHeight, substr($competency['industry_sector'], 0, 40), 1, 'L');
    $pdf->SetXY($startX + 32, $startY);
    
    // Trade area
    $pdf->MultiCell(25, $rowHeight, substr($competency['trade_area'], 0, 30), 1, 'L');
    $pdf->SetXY($startX + 57, $startY);
    
    // Occupation
    $pdf->MultiCell(32, $rowHeight, substr($competency['occupation'], 0, 40), 1, 'L');
    $pdf->SetXY($startX + 89, $startY);
    
    // Classification level
    $pdf->MultiCell(25, $rowHeight, substr($competency['classification_level'], 0, 30), 1, 'L');
    $pdf->SetXY($startX + 114, $startY);
    
    // Competency
    $pdf->MultiCell(25, $rowHeight, substr($competency['competency'], 0, 30), 1, 'L');
    $pdf->SetXY($startX + 139, $startY);
    
    // Specialization
    $pdf->MultiCell(25, $rowHeight, substr($competency['specialization'], 0, 30), 1, 'L');
    
    // Move to next row
    $pdf->SetY($startY + $rowHeight);
}
$pdf->Cell(0, 5, '(For more information, indicate on a separate sheet)', 0, 1);

// 9. Family Background
$pdf->Ln(5);
$pdf->SetFont('Times', '', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '8. Family Background', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1);
$x = $pdf->GetX();
$y = $pdf->GetY();
$width = 180;  
$height = 18; // Smaller height for family sections
$section_height = $height * 4 + 10; // Total height needed for all 4 sections + some spacing

// Check if we need a page break before drawing family sections
if ($y + $section_height > $pdf->getPageHeight() - 25) {
    $pdf->AddPage();
    $y = $pdf->GetY();
}

// Draw the rectangle for spouse
$pdf->Rect($x, $y, $width, $height);
$pdf->Ln(0);

$pdf->SetFont('Times', '', 8);

$pdf->Cell(30, 5, 'Spouse\'s Name:', 0, 0);
$pdf->Cell(65, 5, substr($family['spouse_name'], 0, 40), 'B', 0);
$pdf->SetX(110); 
$pdf->Cell(25, 5, 'Occupation:', 0, 0);
$pdf->Cell(60, 5, substr($family['spouse_occupation'], 0, 30), 'B', 1);
$pdf->Ln(3);

$pdf->SetX($x);

$pdf->Cell(40, 5, 'Educational Attainment:', 0, 0);
$pdf->Cell(55, 5, substr($family['spouse_educational_attainment'], 0, 30), 'B', 0);
$pdf->SetX(110); 
$pdf->Cell(40, 5, 'Ave. Monthly Income:', 0, 0);
$pdf->Cell(45, 5, substr($family['spouse_monthly_income'], 0, 20), 'B', 1);
$pdf->Ln(2);

// Draw the rectangle for father
$new_y = $y + $height;
$pdf->Rect($x, $new_y, $width, $height);
$pdf->SetY($new_y + 3); 
$pdf->SetX($x); 

// Father information
$pdf->SetFont('Times', '', 8);

$pdf->Cell(30, 5, 'Father\'s Name:', 0, 0);
$pdf->Cell(65, 5, substr($family['father_name'], 0, 40), 'B', 0);
$pdf->SetX(110); 
$pdf->Cell(25, 5, 'Occupation:', 0, 0);
$pdf->Cell(60, 5, substr($family['father_occupation'], 0, 30), 'B', 1);
$pdf->Ln(2);

$pdf->SetX($x);

$pdf->Cell(40, 5, 'Educational Attainment:', 0, 0);
$pdf->Cell(55, 5, substr($family['father_educational_attainment'], 0, 30), 'B', 0);
$pdf->SetX(110); 
$pdf->Cell(40, 5, 'Ave. Monthly Income:', 0, 0);
$pdf->Cell(45, 5, substr($family['father_monthly_income'], 0, 20), 'B', 1);
$pdf->Ln(2);

// Draw the rectangle for mother
$mother_y = $new_y + $height;
$pdf->Rect($x, $mother_y, $width, $height);
$pdf->SetY($mother_y + 3); 
$pdf->SetX($x); 

// Mother information
$pdf->SetFont('Times', '', 8);

$pdf->Cell(30, 5, 'Mother\'s Name:', 0, 0);
$pdf->Cell(65, 5, substr($family['mother_name'], 0, 40), 'B', 0);
$pdf->SetX(110); 
$pdf->Cell(25, 5, 'Occupation:', 0, 0);
$pdf->Cell(60, 5, substr($family['mother_occupation'], 0, 30), 'B', 1);
$pdf->Ln(2);

$pdf->SetX($x);

$pdf->Cell(40, 5, 'Educational Attainment:', 0, 0);
$pdf->Cell(55, 5, substr($family['mother_educational_attainment'], 0, 30), 'B', 0);
$pdf->SetX(110); 
$pdf->Cell(40, 5, 'Ave. Monthly Income:', 0, 0);
$pdf->Cell(45, 5, substr($family['mother_monthly_income'], 0, 20), 'B', 1);
$pdf->Ln(2);

// Draw the rectangle for guardian
$guardian_y = $mother_y + $height;
$pdf->Rect($x, $guardian_y, $width, $height);
$pdf->SetY($guardian_y + 3); 
$pdf->SetX($x); 

// Guardian information
$pdf->SetFont('Times', '', 8);

$pdf->Cell(45, 5, 'Name of Guardian\'s Name:', 0, 0);
$pdf->Cell(50, 5, substr($family['guardian_name'], 0, 40), 'B', 0);
$pdf->SetX(110); 
$pdf->Cell(25, 5, 'Occupation:', 0, 0);
$pdf->Cell(60, 5, substr($family['guardian_occupation'], 0, 30), 'B', 1);
$pdf->Ln(2);

$pdf->SetX($x);

$pdf->Cell(40, 5, 'Educational Attainment:', 0, 0);
$pdf->Cell(55, 5, substr($family['guardian_educational_attainment'], 0, 30), 'B', 0);
$pdf->SetX(110); 
$pdf->Cell(40, 5, 'Ave. Monthly Income:', 0, 0);
$pdf->Cell(45, 5, substr($family['guardian_monthly_income'], 0, 20), 'B', 1);
$pdf->Ln(4);

// Draw table for dependents
$dependents_y = $guardian_y + $height + 1; 
$pdf->SetY($dependents_y);
$x = $pdf->GetX();
$y = $pdf->GetY();

// Dependents headers
$pdf->MultiCell(90, 12, "Dependents", 1, 'C'); 
$pdf->SetXY($x + 90, $y); 
$pdf->MultiCell(90, 12, "Age", 1, 'C'); 
$pdf->SetXY($x, $y + 12); 

// Handle potentially long text with MultiCell
$pdf->MultiCell(90, 7, substr($family['dependents'], 0, 180), 1, 'L');
$currentY = $pdf->GetY();
$pdf->SetXY($x + 90, $y + 12);
$pdf->MultiCell(90, 7, substr($family['dependents_age'], 0, 180), 1, 'L');

// Make sure we move to the right position after dependents
$pdf->SetY(max($currentY, $pdf->GetY()));
$pdf->Cell(0, 5, '(For more information, indicate on a separate sheet)', 0, 1);

// Output the PDF
$pdf->Output('Employee_Profile.pdf', 'D');