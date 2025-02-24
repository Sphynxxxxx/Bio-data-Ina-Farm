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
    
    // Fetch the latest record 
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
                <h3 class="name-title" style="font-size: 0.7em;">NMIS Manpower Code:</h3>
                <div class="form-group">
                    <div class="value"><?php echo htmlspecialchars($user['nmis_code'] ?? ''); ?></div>
                </div>
                <h3 class="name-title" style="font-size: 0.7em; font-weight: normal;">NMIS Entry Date:</h3>
                <div class="form-group">
                    <div class="value"></div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="form-row">
                <h3 class="name-title">Name:</h3>
                    <div class="form-group">
                        <div class="value"><?php echo htmlspecialchars($user['lastname'] ?? ''); ?></div>
                        <div class="label">Last</div>
                    </div>
                    <div class="form-group">
                        <div class="value"><?php echo htmlspecialchars($user['firstname'] ?? ''); ?></div>
                        <div class="label">First</div>
                    </div>
                    <div class="form-group">
                        <div class="value"><?php echo htmlspecialchars($user['middlename'] ?? ''); ?></div>
                        <div class="label">Middle</div>
                    </div>
            </div>

            <div class="form-row">
                <h3 class="name-title">Mailing Address:</h3>
                    <div class="form-group">
                        <div class="value"><?php echo htmlspecialchars($user['address_street'] ?? '')?></div>
                        <div class="label">Number, Street</div>
                    </div>
                    <div class="form-group">
                        <div class="value"><?php echo htmlspecialchars($user['address_barangay'] ?? '')?></div>
                        <div class="label">Barangay</div>
                    </div>
                    <div class="form-group">
                        <div class="value"><?php echo htmlspecialchars($user['address_district'] ?? '')?></div>
                        <div class="label">Congressional District</div>
                    </div>
            </div>
            <div class="form-row">
                    <div class="form-group">
                        <div class="value"><?php echo htmlspecialchars($user['address_city'] ?? '')?></div>
                        <div class="label">City/Municipality</div>
                    </div>
                    <div class="form-group">
                        <div class="value"><?php echo htmlspecialchars($user['address_province'] ?? '')?></div>
                        <div class="label">Province</div>
                    </div>
                    <div class="form-group">
                        <div class="value"><?php echo htmlspecialchars($user['address_region'] ?? '')?></div>
                        <div class="label">Region</div>
                    </div>
                    <div class="form-group">
                        <div class="value"><?php echo htmlspecialchars($user['address_zip'] ?? '')?></div>
                        <div class="label">Zip Code</div>
                    </div>
                    <div class="form-group">
                        <div class="value"><?php echo htmlspecialchars($user['address_boxNo'] ?? '')?></div>
                        <div class="label">P.O Box No.</div>
                    </div>
            </div>
        </div>
        <div class="section">
            <div class="form-row">
                    <div class="form-group">
                        <h3 class="name-title" style="font-size: 0.7em;">Sex:</h3>
                        <div class="form-checkbox">
                            <div class="checkbox-group">
                                <input type="checkbox" class="checkbox" <?php echo ($user['sex'] == 'Male') ? 'checked' : ''; ?>>
                                <div class="label-check">Male</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="checkbox" class="checkbox" <?php echo ($user['sex'] == 'Female') ? 'checked' : ''; ?>>
                                <div class="label-check">Female</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <h3 class="name-title" style="font-size: 0.7em;">Civil Status:</h3>
                        <div class="form-checkbox">
                            <div class="checkbox-group">
                                <input type="checkbox" class="checkbox" <?php echo ($user['civil_status'] == 'Single') ? 'checked' : ''; ?>>
                                <div class="label-check">Single</div>
                            </div>

                            <div class="checkbox-group">
                                <input type="checkbox" class="checkbox" <?php echo ($user['civil_status'] == 'Married') ? 'checked' : ''; ?>>
                                <div class="label-check">Married</div>
                            </div>

                            <div class="checkbox-group">
                                <input type="checkbox" class="checkbox" <?php echo ($user['civil_status'] == 'Widow/er') ? 'checked' : ''; ?>>
                                <div class="label-check">Widow/er</div>
                            </div>

                            <div class="checkbox-group">
                                <input type="checkbox" class="checkbox" <?php echo ($user['civil_status'] == 'Separated') ? 'checked' : ''; ?>>
                                <div class="label-check">Separated</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <h3 class="name-title" style="font-size: 0.7em;">Contact Number(s):</h3>
                        <div class="form-contact">
                            <div class="label-contact">Tel:</div>
                            <div class="value-value"><?php echo htmlspecialchars($user['tel_number'] ?? ''); ?></div>
                        </div>
                        <div class="form-contact">
                            <div class="label-contact">Cellular:</div>
                            <div class="value-value"><?php echo htmlspecialchars($user['contact_number'] ?? ''); ?></div>
                        </div>
                        <div class="form-contact">
                            <div class="label-contact">E-mail:</div>
                            <div class="value-value"><?php echo htmlspecialchars($user['email'] ?? ''); ?></div>
                        </div>
                        <div class="form-contact">
                            <div class="label-contact">Fax:</div>
                            <div class="value-value"><?php echo htmlspecialchars($user['fax_number'] ?? ''); ?></div>
                        </div>
                        <div class="form-contact">
                            <div class="label-contact">Others:</div>
                            <div class="value-value"><?php echo htmlspecialchars($user['other_contact'] ?? ''); ?></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <h3 class="name-title" style="font-size: 0.7em;">Employment Type:</h3>
                        <div class="form-checkbox">
                            <div class="checkbox-group">
                                <input type="checkbox" class="checkbox" <?php echo ($user['employment_type'] == 'Employed') ? 'checked' : ''; ?>>
                                <div class="label-check">Employed</div>
                            </div>

                            <div class="checkbox-group">
                                <input type="checkbox" class="checkbox" <?php echo ($user['employment_type'] == 'Self-employed') ? 'checked' : ''; ?>>
                                <div class="label-check">Self-employed</div>
                            </div>

                            <div class="checkbox-group">
                                <input type="checkbox" class="checkbox" <?php echo ($user['employment_type'] == 'Unemployed') ? 'checked' : ''; ?>>
                                <div class="label-check">Unemployed</div>
                            </div>

                        </div>
                    </div>

                    <div class="form-group">
                        <h3 class="name-title" style="font-size: 0.7em;">Employment Status:</h3>
                        <div class="form-checkbox">
                            <div class="checkbox-group">
                                <input type="checkbox" class="checkbox" <?php echo ($user['employment_status'] == 'Casual') ? 'checked' : ''; ?>>
                                <div class="label-check">Casual</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="checkbox" class="checkbox" <?php echo ($user['employment_status'] == 'Contractual') ? 'checked' : ''; ?>>
                                <div class="label-check">Contractual</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="checkbox" class="checkbox" <?php echo ($user['employment_status'] == 'Job-Order') ? 'checked' : ''; ?>>
                                <div class="label-check">Job Order</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="checkbox" class="checkbox" <?php echo ($user['employment_status'] == 'Temporary') ? 'checked' : ''; ?>>
                                <div class="label-check">Temporary</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="checkbox" class="checkbox" <?php echo ($user['employment_status'] == 'Probationary') ? 'checked' : ''; ?>>
                                <div class="label-check">Probationary</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="checkbox" class="checkbox" <?php echo ($user['employment_status'] == 'Regular') ? 'checked' : ''; ?>>
                                <div class="label-check">Regular</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="checkbox" class="checkbox" <?php echo ($user['employment_status'] == 'Permanent') ? 'checked' : ''; ?>>
                                <div class="label-check">Permanent</div>
                            </div>
                            <h3 class="name-title" style="font-size: 0.7em;">If Student</h3>
                            <div class="checkbox-group">
                                <input type="checkbox" class="checkbox" <?php echo ($user['employment_status'] == 'Trainee') ? 'checked' : ''; ?>>
                                <div class="label-check">Trainee/OJT</div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>


        <!---#################### --->
        <div class="section">
            <div class="form-row">                
                    <div class="form-group">
                        <div class="form-personal">
                            <div class="label-personal">Birthdate:</div>
                            <div class="value-value"><?php echo htmlspecialchars($user['birtdate'] ?? ''); ?></div>
                        </div>
                        <div class="form-personal">
                            <div class="label-personal">Birth Place:</div>
                            <div class="value-value"><?php echo htmlspecialchars($user['birth_place'] ?? ''); ?></div>
                        </div>
                        <div class="form-personal">
                            <div class="label-personal">Citizenship:</div>
                            <div class="value-value"><?php echo htmlspecialchars($user['citizenship'] ?? ''); ?></div>
                        </div>
                        <div class="form-personal">
                            <div class="label-personal">Religion:</div>
                            <div class="value-value"><?php echo htmlspecialchars($user['religion'] ?? ''); ?></div>
                        </div>
                        <div class="form-personal">
                            <div class="label-personal">Height:</div>
                            <div class="value-value"><?php echo htmlspecialchars($user['height'] ?? ''); ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-personal">
                            <div class="label-personal">Weight:</div>
                            <div class="value-value"><?php echo htmlspecialchars($user['weight'] ?? ''); ?></div>
                        </div>
                        <div class="form-personal">
                            <div class="label-personal">Blood Type: </div>
                            <div class="value-value"><?php echo htmlspecialchars($user['blood_type'] ?? ''); ?></div>
                        </div>
                        <div class="form-personal">
                            <div class="label-personal">SSS No.: </div>
                            <div class="value-value"><?php echo htmlspecialchars($user['sss_no'] ?? ''); ?></div>
                        </div>
                        <div class="form-personal">
                            <div class="label-personal">GSIS No.:</div>
                            <div class="value-value"><?php echo htmlspecialchars($user['gsis_no'] ?? ''); ?></div>
                        </div>
                        <div class="form-personal">
                            <div class="label-personal">TIN No.:</div>
                            <div class="value-value"><?php echo htmlspecialchars($user['tin_no'] ?? ''); ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-personal">
                            <div class="label-personal">Distinguishing Marks:</div>
                            <div class="value-value"><?php echo htmlspecialchars($user['distinguish_marks'] ?? ''); ?></div>
                        </div>
            </div>  </div>
        </div>


        <div class="section">
            <div class="section-title">4. Educational Background</div>
            <table>
                <thead>
                    <tr>
                        <th>School</th>
                        <th>Educational Level</th>
                        <th colspan="2">School Year</th> <!-- Spanning two columns -->
                        <th>Degree</th>
                        <th>Major</th>
                        <th>Minor</th>
                        <th>Units Earned</th>
                        <th>Honors Received</th>
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
                        <td><?php echo htmlspecialchars($edu['units_earned'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($edu['honors'] ?? ''); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <a href="generate_pdf.php" class="btn btn-primary">Download PDF</a>
    </div>
</body>
</html>