<?php
session_start();
require_once __DIR__ . '/../connections/config.php';

if (!isset($_SESSION['admin'])) {
    header('Location: admin.php');
    exit();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch the latest record (you can modify this to fetch specific records)
    $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC LIMIT 1");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Fetch education records
    $eduStmt = $pdo->prepare("SELECT * FROM education WHERE user_id = ? ORDER BY year_from");
    $eduStmt->execute([$user['id']]);
    $education = $eduStmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NMIS Manpower Profile</title>
    <link rel="stylesheet" href="css/view.css">    
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="assets/tesda_logo.png" alt="TESDA Logo">
            <div class="header-text">
                <h2>Technical Education and Skills Development Authority</h2>
                <p>Pangasiwaan sa Edukasyong Teknikal at Pagpapaunlad ng Kasanayan</p>
            </div>
            <div class="form-title"><strong>NMIS FORM -01A</strong> <br> <span style="font-size: 10px;">(For TPIS)</span></div>
        </div>
        <h2 class="manpower-profile">MANPOWER PROFILE</h2>
        <div class="signature-container">
            <h2 class="signature-title">Signature</h2>
            <div class="signature-box">ID PICTURE <br> (Passport Size)</div>
        </div>
        <div class="section">
            <div class="section-title">1. To be accomplished by TESDA</div>
            <div class="form-row">
                <div class="form-group">
                    <div class="label">NMIS Manpower Code:</div>
                    <div class="value"><?php echo htmlspecialchars($user['nmis_code'] ?? ''); ?></div>
                </div>
                <div class="form-group">
                    <div class="label">NMIS Entry Date:</div>
                    <div class="value"></div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">2. Manpower Profile</div>
            <div class="form-row">
                <div class="form-group">
                    <div class="label">Last Name:</div>
                    <div class="value"><?php echo htmlspecialchars($user['lastname'] ?? ''); ?></div>
                </div>
                <div class="form-group">
                    <div class="label">First Name:</div>
                    <div class="value"><?php echo htmlspecialchars($user['firstname'] ?? ''); ?></div>
                </div>
                <div class="form-group">
                    <div class="label">Middle Name:</div>
                    <div class="value"><?php echo htmlspecialchars($user['middlename'] ?? ''); ?></div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <div class="label">Address:</div>
                    <div class="value">
                        <?php 
                        echo htmlspecialchars($user['address_street'] ?? '') . ', ' .
                             htmlspecialchars($user['address_barangay'] ?? '') . ', ' .
                             htmlspecialchars($user['address_city'] ?? '') . ', ' .
                             htmlspecialchars($user['address_province'] ?? '') . ', ' .
                             htmlspecialchars($user['address_region'] ?? '') . ' ' .
                             htmlspecialchars($user['address_zip'] ?? '');
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <div class="label">Sex:</div>
                    <div class="value"><?php echo htmlspecialchars($user['sex'] ?? ''); ?></div>
                </div>
                <div class="form-group">
                    <div class="label">Civil Status:</div>
                    <div class="value"><?php echo htmlspecialchars($user['civil_status'] ?? ''); ?></div>
                </div>
                <div class="form-group">
                    <div class="label">Contact Number:</div>
                    <div class="value"><?php echo htmlspecialchars($user['contact_number'] ?? ''); ?></div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">3. Personal Information</div>
            <div class="form-row">
                <div class="form-group">
                    <div class="label">Birthdate:</div>
                    <div class="value"><?php echo htmlspecialchars($user['birthdate'] ?? ''); ?></div>
                </div>
                <div class="form-group">
                    <div class="label">Birth Place:</div>
                    <div class="value"><?php echo htmlspecialchars($user['birth_place'] ?? ''); ?></div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <div class="label">Citizenship:</div>
                    <div class="value"><?php echo htmlspecialchars($user['citizenship'] ?? ''); ?></div>
                </div>
                <div class="form-group">
                    <div class="label">Religion:</div>
                    <div class="value"><?php echo htmlspecialchars($user['religion'] ?? ''); ?></div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <div class="label">Height:</div>
                    <div class="value"><?php echo htmlspecialchars($user['height'] ?? ''); ?></div>
                </div>
                <div class="form-group">
                    <div class="label">Weight:</div>
                    <div class="value"><?php echo htmlspecialchars($user['weight'] ?? ''); ?></div>
                </div>
                <div class="form-group">
                    <div class="label">Blood Type:</div>
                    <div class="value"><?php echo htmlspecialchars($user['blood_type'] ?? ''); ?></div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">4. Educational Background</div>
            <table>
                <thead>
                    <tr>
                        <th>School</th>
                        <th>Level</th>
                        <th>Year From</th>
                        <th>Year To</th>
                        <th>Degree</th>
                        <th>Major</th>
                        <th>Minor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($education as $edu): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($edu['school_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($edu['educational_level'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($edu['year_from'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($edu['year_to'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($edu['degree'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($edu['major'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($edu['minor'] ?? ''); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Employment Information</div>
            <div class="form-row">
                <div class="form-group">
                    <div class="label">Employment Type:</div>
                    <div class="value"><?php echo htmlspecialchars($user['employment_type'] ?? ''); ?></div>
                </div>
                <div class="form-group">
                    <div class="label">Employment Status:</div>
                    <div class="value"><?php echo htmlspecialchars($user['employment_status'] ?? ''); ?></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>