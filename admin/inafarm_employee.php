<?php
session_start();
require_once __DIR__ . '/../connections/config.php';

if (!isset($_SESSION['admin'])) {
    header('Location: admin.php');
    exit();
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "No user ID specified.";
    header('Location: admin_dashboard.php');
    exit();
}

$userId = intval($_GET['id']);

// Fetch specific user data based on ID
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error'] = "User not found.";
    header('Location: admin_dashboard.php');
    exit();
}

// Fetch related data for this specific user
// Education
$eduStmt = $pdo->prepare("SELECT * FROM education WHERE user_id = ?");
$eduStmt->execute([$userId]);
$education = $eduStmt->fetchAll(PDO::FETCH_ASSOC);

// Work experience
$workStmt = $pdo->prepare("SELECT * FROM work_experience WHERE user_id = ?");
$workStmt->execute([$userId]);
$work_experience = $workStmt->fetchAll(PDO::FETCH_ASSOC);

// Training/seminar
$trainingStmt = $pdo->prepare("SELECT * FROM training_seminar WHERE user_id = ?");
$trainingStmt->execute([$userId]);
$training_seminar = $trainingStmt->fetchAll(PDO::FETCH_ASSOC);

// License/examination
$licenseStmt = $pdo->prepare("SELECT * FROM license_examination WHERE user_id = ?");
$licenseStmt->execute([$userId]);
$license_examination = $licenseStmt->fetchAll(PDO::FETCH_ASSOC);

// Competency assessment
$competencyStmt = $pdo->prepare("SELECT * FROM competency_assessment WHERE user_id = ?");
$competencyStmt->execute([$userId]);
$competency_assessment = $competencyStmt->fetchAll(PDO::FETCH_ASSOC);

// Family background
$familyStmt = $pdo->prepare("SELECT * FROM family_background WHERE user_id = ?");
$familyStmt->execute([$userId]);
$family = $familyStmt->fetch(PDO::FETCH_ASSOC);

// Photo Capture
$photoStmt = $pdo->prepare("SELECT photo_data FROM user_photos WHERE user_id = ?");
$photoStmt->execute([$user['id']]);
$photo = $photoStmt->fetch(PDO::FETCH_ASSOC);

// Fetch user's signature
$signatureStmt = $pdo->prepare("SELECT signature_data FROM user_signatures WHERE user_id = ?");
$signatureStmt->execute([$user['id']]);
$signature = $signatureStmt->fetch(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ina Farm Employee Biodata Form</title>
    <link rel="stylesheet" href="css/view.css">    
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="assets/inafarm_long logo.png" alt="InaFarm Logo">
            <div class="header-text">
                <h2>Ina Farm Biodata Form</h2>
            </div>
        </div>
        <h2 class="manpower-profile">INA FARM EMPLOYEE PROFILE</h2>
        
        <!-- Photo and signature -->
        <div class="signature-container">
            <div class="signature-area">
                <?php if (!empty($signature) && !empty($signature['signature_data'])): ?>
                    <img src="<?php echo $signature['signature_data']; ?>" alt="Signature" class="centered-signature">
                <?php else: ?>
                <?php endif; ?>
                <h2 class="signature-title">Signature</h2>
            </div>
            
            <div class="photo-box">
                <?php if (!empty($photo) && !empty($photo['photo_data'])): ?>
                    <img src="<?php echo $photo['photo_data']; ?>" alt="ID Photo">
                <?php elseif (!empty($user['photo_path']) && file_exists($user['photo_path'])): ?>
                    <img src="<?php echo $user['photo_path']; ?>" alt="ID Photo">
                <?php else: ?>
                    <div class="photo-placeholder">ID PICTURE <br> (Passport Size)</div>
                <?php endif; ?>
            </div>
        </div>

        <!--<div class="section">
            <div class="section-title">1. To be accomplished by TESDA</div>
            <div class="form-row">
                <h3 class="name-title" style="font-size: 0.7em;">NMIS Manpower Code:</h3>
                <div class="form-group">
                    <div class="value"><?php echo htmlspecialchars($user['nmis_code'] ?? ''); ?></div>
                </div>
                <h3 class="name-title" style="font-size: 0.7em; font-weight: normal;">NMIS Entry Date:</h3>
                <div class="form-group">
                    <div class="value"><?php echo htmlspecialchars($user['nmis_entry'] ?? ''); ?></div>
                </div>
            </div>
        </div>-->

        <!---2. Manpower Profile --->
        <div class="section">
            <div class="section-title">1. Manpower Profile</div>
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


        <!---3. Personal Information --->
        <div class="section">
            <div class="section-title">2. Personal Information</div>
            <div class="form-row">                
                    <div class="form-group">
                        <div class="form-personal">
                            <div class="label-personal">Birthdate:</div>
                            <div class="value-value"><?php echo htmlspecialchars($user['birthdate'] ?? ''); ?></div>
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
                    </div>
                </div>
        </div>

        <!-- 4. Educational Background-->
        <div class="section">
            <div class="section-title">3. Educational Background</div>
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

        <!-- 5. Working Experience-->
        <div class="section">
            <div class="section-title">4. Working Experience (For Trainers, mandatory field 5.5)</div>
            <table>
                <thead>
                    <tr>
                        <th>Name of the Company</th>
                        <th>Position</th>
                        <th colspan="2">Inclusive Dates</th> <!-- Spanning two columns -->
                        <th>Monthly Salary</th>
                        <th>Occupation Type</th>
                        <th>Status of Appointment</th>
                        <th>No. of Yrs Working Exp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($work_experience as $work): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($work['company_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($work['position'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($work['inclusive_dates_past'] ?? ''); ?></td> 
                        <td><?php echo htmlspecialchars($work['inclusive_dates_present'] ?? ''); ?></td> 
                        <td><?php echo htmlspecialchars($work['monthly_salary'] ?? ''); ?></td>  
                        <td><?php echo htmlspecialchars($work['occupation'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($work['status'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($work['working_experience'] ?? ''); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- 6. Training Seminar Attendee-->
        <div class="section">
            <div class="section-title">7. Training Seminar Attended</div>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Venue</th>
                        <th colspan="2">Inclusive Dates</th>
                        <th>Certificate Received</th>
                        <th># of Hours</th>
                        <th>Training Base</th>
                        <th>Category</th>
                        <th>Conducted by</th>
                        <th>Proficiency</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($training_seminar as $training): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($training['tittle'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($training['venue'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($training['inclusive_dates_past'] ?? ''); ?></td> 
                        <td><?php echo htmlspecialchars($training['inclusive_dates_present'] ?? ''); ?></td>  
                        <td><?php echo htmlspecialchars($training['certificate'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($training['no_of_hours'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($training['training_base'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($training['category'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($training['conducted_by'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($training['proficiency'] ?? ''); ?></td>
                     </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- 7. License/Examination-->
        <div class="section">
            <div class="section-title">7. License/Examinations Passed</div>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Year Taken</th>
                        <th>Examination Venue</th>
                        <th>Ratings</th>
                        <th>Remarks</th>
                        <th>Expiry Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($license_examination as $license): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($license['license_tittle'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($license['year_taken'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($license['examination_venue'] ?? ''); ?></td> 
                        <td><?php echo htmlspecialchars($license['ratings'] ?? ''); ?></td>  
                        <td><?php echo htmlspecialchars($license['remarks'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($license['expiry_date'] ?? ''); ?></td>
                     </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- 8. Competency Assessment Passed-->
        <div class="section">
            <div class="section-title">7. Competency Assessment Passed</div>
            <table>
                <thead>
                    <tr>
                        <th>Industry Sector</th>
                        <th>Trade Area</th>
                        <th>Occupation</th>
                        <th>Classification Level</th>
                        <th>Competency</th>
                        <th>Specialization Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($competency_assessment as $competency): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($competency['industry_sector'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($competency['trade_area'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($competency['occupation'] ?? ''); ?></td> 
                        <td><?php echo htmlspecialchars($competency['classification_level'] ?? ''); ?></td>  
                        <td><?php echo htmlspecialchars($competency['competency'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($competency['specialization'] ?? ''); ?></td>
                     </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- 9. Family Background-->
        <div class="section">
            <div class="section-title">8. Family Background</div>
                <div class="section">
                    <div class="form-row">                
                        <div class="form-group">
                            <div class="form-personal">
                                <div class="label-personal">Spouse Name:</div>
                                <div class="value-value"><?php echo htmlspecialchars($family['spouse_name'] ?? ''); ?></div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Educational Attainment:</div>
                                <div class="value-value"><?php echo htmlspecialchars($family['spouse_educational_attainment'] ?? ''); ?></div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Occupation:</div>
                                <div class="value-value"><?php echo htmlspecialchars($family['spouse_occupation'] ?? ''); ?></div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Ave. Monthly Income:</div>
                                <div class="value-value"><?php echo htmlspecialchars($family['spouse_monthly_income'] ?? ''); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="section">
                    <div class="form-row">                
                        <div class="form-group">
                            <div class="form-personal">
                                <div class="label-personal">Father's Name:</div>
                                <div class="value-value"><?php echo htmlspecialchars($family['father_name'] ?? ''); ?></div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Educational Attainment:</div>
                                <div class="value-value"><?php echo htmlspecialchars($family['father_educational_attainment'] ?? ''); ?></div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Occupation:</div>
                                <div class="value-value"><?php echo htmlspecialchars($family['father_occupation'] ?? ''); ?></div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Ave. Monthly Income:</div>
                                <div class="value-value"><?php echo htmlspecialchars($family['father_monthly_income'] ?? ''); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="section">
                    <div class="form-row">                
                        <div class="form-group">
                            <div class="form-personal">
                                <div class="label-personal">Mother's Name:</div>
                                <div class="value-value"><?php echo htmlspecialchars($family['mother_name'] ?? ''); ?></div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Educational Attainment:</div>
                                <div class="value-value"><?php echo htmlspecialchars($family['mother_educational_attainment'] ?? ''); ?></div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Occupation:</div>
                                <div class="value-value"><?php echo htmlspecialchars($family['mother_occupation'] ?? ''); ?></div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Ave. Monthly Income:</div>
                                <div class="value-value"><?php echo htmlspecialchars($family['mother_monthly_income'] ?? ''); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="section">
                    <div class="form-row">                
                        <div class="form-group">
                            <div class="form-personal">
                                <div class="label-personal">Name of Guardian:</div>
                                <div class="value-value"><?php echo htmlspecialchars($family['guardian_name'] ?? ''); ?></div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Educational Attainment:</div>
                                <div class="value-value"><?php echo htmlspecialchars($family['guardian_educational_attainment'] ?? ''); ?></div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Occupation:</div>
                                <div class="value-value"><?php echo htmlspecialchars($family['guardian_occupation'] ?? ''); ?></div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Ave. Monthly Income:</div>
                                <div class="value-value"><?php echo htmlspecialchars($family['guardian_monthly_income'] ?? ''); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Dependant</th>
                            <th>Age</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($family)): ?>
                            <?php 
                            $dependents = explode(', ', $family['dependents'] ?? '');
                            $ages = explode(', ', $family['dependents_age'] ?? '');
                            
                            for ($i = 0; $i < count($dependents); $i++): 
                                if (!empty($dependents[$i])): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($dependents[$i]); ?></td>
                                    <td><?php echo htmlspecialchars($ages[$i] ?? ''); ?></td>
                                </tr>
                                <?php endif;
                            endfor; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2">No dependents information found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!---------------------------------------------------------------------------->
        
        <div style="text-align: center; margin-top: 30px; margin-bottom: 20px;">
            <a href="inafarm_employee_pdf.php?id=<?php echo $userId; ?>" class="btn btn-primary" style="display: inline-block; margin-right: 100px;">Download PDF</a>
            <a href="admin_dashboard.php" class="btn btn-secondary" style="display: inline-block;">Back to Dashboard</a>
        </div>

    </div>
</body>
</html>