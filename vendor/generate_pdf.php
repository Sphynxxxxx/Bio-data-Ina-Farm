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
        $this->Image('../admin/assets/tesda_logo.png', 15, 10, 20);
        
        // Header Text
        $this->SetFont('times', 'B', 16);
        $this->SetXY(40, 10);
        $this->Cell(0, 6, 'Technical Education and Skills Development Authority', 0, 1, 'L');
        
        $this->SetFont('times', '', 12);
        $this->SetXY(40, 16);
        $this->Cell(0, 6, 'Pangasiwaan sa Edukasyong Teknikal at Pagpapaunlad ng Kasanayan', 0, 1, 'L');
        
        // Form Title (right aligned)
        $this->SetFont('helvetica', 'B', 14);
        $this->SetXY(150, 10);
        $this->Cell(50, 6, 'NMIS FORM -01A', 0, 1, 'R');
        $this->SetFont('helvetica', '', 10);
        $this->SetXY(150, 16);
        $this->Cell(50, 6, '(For TPIS)', 0, 1, 'R');
        
        // Double line border
        $this->Line(15, 25, 195, 25);
        $this->Line(15, 26, 195, 26);
    }
    
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
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
$pdf->SetFont('helvetica', 'B', 20);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 10, 'MANPOWER PROFILE', 0, 1, 'C');
$pdf->SetTextColor(0, 0, 0);

// Add signature box
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(140, 10, 'Signature', 0, 0, 'C');
$pdf->Rect(155, 45, 40, 50); // Photo box
$pdf->SetFont('helvetica', '', 8);
$pdf->Text(160, 65, 'ID PICTURE');
$pdf->Text(158, 70, '(Passport Size)');

// Section 1 - TESDA Information
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetFillColor(177, 176, 176); // Matching #b1b0b0
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '1. To be accomplished by TESDA', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

// NMIS Code and Entry Date
$pdf->SetFont('helvetica', '', 10);
$y = $pdf->GetY();
$pdf->Cell(40, 6, 'NMIS Manpower Code:', 0, 0);
$pdf->Cell(60, 6, $user['nmis_code'], 'B', 0);
$pdf->Cell(30, 6, 'NMIS Entry Date:', 0, 0);
$pdf->Cell(60, 6, '', 'B', 1);

// Personal Information Section
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '2. Personal Information', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

// Name Fields
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(20, 10, 'Name:', 0, 0);
$pdf->Cell(55, 10, $user['lastname'], 'B', 0, 'C');
$pdf->Cell(55, 10, $user['firstname'], 'B', 0, 'C');
$pdf->Cell(55, 10, $user['middlename'], 'B', 1, 'C');

$pdf->SetX(35);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(55, 5, 'Last', 0, 0, 'C');
$pdf->Cell(55, 5, 'First', 0, 0, 'C');
$pdf->Cell(55, 5, 'Middle', 0, 1, 'C');

// Address
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(30, 10, 'Mailing Address:', 0, 0);
$pdf->Cell(75, 10, $user['address_street'], 'B', 0, 'C');
$pdf->Cell(75, 10, $user['address_barangay'], 'B', 1, 'C');

$pdf->SetX(45);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(75, 5, 'Number, Street', 0, 0, 'C');
$pdf->Cell(75, 5, 'Barangay', 0, 1, 'C');

// City, Province, Region
$pdf->SetFont('helvetica', '', 10);
$pdf->SetX(45);
$pdf->Cell(45, 10, $user['address_city'], 'B', 0, 'C');
$pdf->Cell(45, 10, $user['address_province'], 'B', 0, 'C');
$pdf->Cell(45, 10, $user['address_region'], 'B', 1, 'C');

$pdf->SetX(45);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(45, 5, 'City/Municipality', 0, 0, 'C');
$pdf->Cell(45, 5, 'Province', 0, 0, 'C');
$pdf->Cell(45, 5, 'Region', 0, 1, 'C');

// Personal Details Section
$pdf->Ln(5);
$pdf->SetFont('helvetica', '', 10);

// Create two columns for details
$pdf->SetX(15);
$leftColumn = 95;
$rightColumn = 95;

// Left Column
$pdf->Cell($leftColumn, 8, 'Sex:', 0, 0);
$pdf->Cell(5, 8, $user['sex'] == 'Male' ? 'X' : '', 1, 0, 'C');
$pdf->Cell(15, 8, 'Male', 0, 0);
$pdf->Cell(5, 8, $user['sex'] == 'Female' ? 'X' : '', 1, 0, 'C');
$pdf->Cell(15, 8, 'Female', 0, 1);

// Civil Status
$pdf->Cell($leftColumn, 8, 'Civil Status:', 0, 0);
$statuses = ['Single', 'Married', 'Widow/er', 'Separated'];
foreach($statuses as $status) {
    $pdf->Cell(5, 8, $user['civil_status'] == $status ? 'X' : '', 1, 0, 'C');
    $pdf->Cell(20, 8, $status, 0, 0);
}
$pdf->Ln();

// Contact Information
/*
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(30, 8, 'Tel. No.:', 0, 0);
$pdf->Cell(65, 8, $user['tel_number'], 'B', 1);
*/

$pdf->Cell(30, 8, 'Cellular:', 0, 0);
$pdf->Cell(65, 8, $user['contact_number'], 'B', 1);

$pdf->Cell(30, 8, 'Email:', 0, 0);
$pdf->Cell(65, 8, $user['email'], 'B', 1);

// Educational Background
$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetFillColor(177, 176, 176);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(0, 8, '4. Educational Background', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0);

// Table Headers
$pdf->SetFont('helvetica', '', 8);
$headers = array('School', 'Level', 'From', 'To', 'Degree', 'Major', 'Minor', 'Units', 'Honors');
$widths = array(40, 20, 15, 15, 25, 25, 20, 15, 25);

// Draw Table Header
foreach(array_combine($headers, $widths) as $header => $width) {
    $pdf->Cell($width, 7, $header, 1, 0, 'C');
}
$pdf->Ln();

// Table Data
foreach($education as $edu) {
    $pdf->Cell(40, 6, $edu['school_name'], 1);
    $pdf->Cell(20, 6, $edu['educational_level'], 1);
    $pdf->Cell(15, 6, $edu['year_from'], 1);
    $pdf->Cell(15, 6, $edu['year_to'], 1);
    $pdf->Cell(25, 6, $edu['degree'], 1);
    $pdf->Cell(25, 6, $edu['major'], 1);
    $pdf->Cell(20, 6, $edu['minor'], 1);
    $pdf->Cell(15, 6, $edu['units_earned'], 1);
    $pdf->Cell(25, 6, $edu['honors'], 1);
    $pdf->Ln();
}

// Output the PDF
$pdf->Output('NMIS_Profile.pdf', 'D');
?>