<?php
require_once('tecnickcom\tcpdf\tcpdf.php');
session_start();
require_once __DIR__ . '/../connections/config.php';

if (!isset($_SESSION['admin'])) {
    header('Location: admin.php');
    exit();
}

class MYPDF extends TCPDF {
    public function Header() {
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
    
    $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC LIMIT 1");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $eduStmt = $pdo->prepare("SELECT * FROM education WHERE user_id = ? ORDER BY year_from");
    $eduStmt->execute([$user['id']]);
    $education = $eduStmt->fetchAll(PDO::FETCH_ASSOC);
    
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


$pdf->Rect(157, 42, 38, 40); // Photo box
$pdf->SetFont('Times', '', 8);
$pdf->Text(167.5, 60, 'ID PICTURE');
$pdf->Text(167, 65, '(Passport Size)');

// Section 1 - TESDA Information
$pdf->SetXY(15, 84);
$pdf->SetFont('Times', 'B', 12);
$pdf->SetFillColor(177, 176, 176); // Matching #b1b0b0
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '1. To be accomplished by TESDA', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

// NMIS Code and Entry Date
$pdf->SetXY(15, 93);
$pdf->SetFont('Times', 'B', 10);
$y = $pdf->GetY();
$pdf->Cell(40, 6, 'NMIS Manpower Code:',  0, 0);
$pdf->SetFont('Times', '', 10);
$pdf->Cell(40, 6, $user['nmis_code'], 1, 0);

$pdf->SetX(105);
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(30, 6, 'NMIS Entry Date:', 0, 0);
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

$pdf->SetFont('Times', '', 10);
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

$pdf->SetFont('Times', '', 10);
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
$pdf->SetFont('Times', '', 10);
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
$boxHeight = 40; // Adjust height based on content
$boxWidth = 30;  // Half of the page width (approx)

// Draw rectangle for "Sex"
$pdf->Rect($startX, $startY, $boxWidth, $boxHeight);
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(25, 8, '2.3 Sex', 0, 1);
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
$pdf->Cell(35, 8, '2.4 Civil Status', 0, 1);
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
$pdf->Cell(40, 8, '2.5 Contact Number(s)', 0, 1);
$pdf->SetTextColor(0, 0, 0);

// Move cursor below the title
$pdf->SetFont('Times', '', 10);
$yPosition = $startY + 8;

// Telephone Number
$pdf->SetXY($startX + ($boxWidth * 2) + 1, $yPosition);
$pdf->Cell(15, 5, 'Tel. No.:', 0, 0);
$pdf->Cell(38, 5, $user['tel_number'], 'B', 1);

// Move down for Cellular Number
$yPosition += 8;
$pdf->SetXY($startX + ($boxWidth * 2) + 1, $yPosition);
$pdf->Cell(15, 5, 'Cellular:', 0, 0);
$pdf->Cell(38, 5, $user['contact_number'], 'B', 1);

// Move down for Email
$yPosition += 8;
$pdf->SetXY($startX + ($boxWidth * 2) + 1, $yPosition);
$pdf->Cell(15, 5, 'Email:', 0, 0);
$pdf->Cell(38, 5, $user['email'], 'B', 1);

// Move down fo Fax
$yPosition += 8;
$pdf->SetXY($startX + ($boxWidth * 2) + 1, $yPosition);
$pdf->Cell(15, 5, 'Fax:', 0, 0);
$pdf->Cell(38, 5, $user['fax'], 'B', 1);


// Set dimensions
$boxWidth = 40;  
$boxHeight = 40; 

// Draw rectangle for "Employment Type"
$pdf->Rect($startX + ($boxWidth * 3) + 0, $startY, $boxWidth + 20, $boxHeight);
$pdf->SetXY($startX + ($boxWidth * 3) + 0, $startY);
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(40, 8, '2.6 Employment Type', 0, 1);
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
$pdf->Cell(0, 8, '3. Personal Information', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1);
$x = $pdf->GetX();
$y = $pdf->GetY();
$width = 180;  
$height = 40;

// Draw the rectangle 
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
$pdf->AddPage();
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
foreach ($education as $edu) {
    $pdf->Cell(31, 7, $edu['school_name'], 1);
    $pdf->Cell(22, 7, $edu['educational_level'], 1);

    // "School Year" Split into Two Columns
    $pdf->Cell(10, 7, $edu['year_from'], 1, 0, 'C');
    $pdf->Cell(10, 7, $edu['year_to'], 1, 0, 'C');

    $pdf->Cell(22, 7, $edu['degree'], 1);
    $pdf->Cell(21, 7, $edu['minor'], 1);
    $pdf->Cell(21, 7, $edu['major'], 1);
    $pdf->Cell(21, 7, $edu['units_earned'], 1);
    $pdf->Cell(22, 7, $edu['honors'], 1);
    
    $pdf->Ln();
}






// Output the PDF
$pdf->Output('NMIS_Profile.pdf', 'D');
?>