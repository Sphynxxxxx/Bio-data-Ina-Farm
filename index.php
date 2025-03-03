<?php
session_start();
require_once 'connections/config.php';

// Database connection
if (!isset($host, $dbname, $username, $password)) {
    $host = 'localhost';
    $dbname = 'biodata_db';
    $username = 'root';
    $password = '';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Begin transaction
        $pdo->beginTransaction();
        
        // Initialize arrays to store form data with default values
        $userData = [
            'nmis_code' => $_POST['nmis_code'] ?? '',
            'lastname' => $_POST['lastname'] ?? '',
            'firstname' => $_POST['firstname'] ?? '',
            'middlename' => $_POST['middlename'] ?? '',
            'address_street' => $_POST['address_street'] ?? '',
            'address_barangay' => $_POST['address_barangay'] ?? '',
            'address_district' => $_POST['address_district'] ?? '',
            'address_city' => $_POST['address_city'] ?? '',
            'address_province' => $_POST['address_province'] ?? '',
            'address_region' => $_POST['address_region'] ?? '',
            'address_zip' => $_POST['address_zip'] ?? '',
            'address_boxNo' => $_POST['address_boxNo'] ?? '',
            'sex' => $_POST['sex'] ?? '',
            'civil_status' => $_POST['civil_status'] ?? '',
            'tel_number' => $_POST['tel_number'] ?? '',
            'contact_number' => $_POST['contact_number'] ?? '',
            'email' => $_POST['email'] ?? '',
            'fax_number' => $_POST['fax_number'] ?? '',
            'other_contact' => $_POST['other_contact'] ?? '',
            'employment_type' => $_POST['employment_type'] ?? '',
            'employment_status' => $_POST['employment_status'] ?? '',
            'birthdate' => $_POST['birthdate'] ?? '',
            'birth_place' => $_POST['birth_place'] ?? '',
            'citizenship' => $_POST['citizenship'] ?? '',
            'religion' => $_POST['religion'] ?? '',
            'height' => $_POST['height'] ?? '',
            'weight' => $_POST['weight'] ?? '',
            'blood_type' => $_POST['blood_type'] ?? '',
            'distinguish_marks' => $_POST['distinguish_marks'] ?? '',
            'sss_no' => $_POST['sss_no'] ?? '',
            'gsis_no' => $_POST['gsis_no'] ?? '',
            'tin_no' => $_POST['tin_no'] ?? ''
        ];
        
        // Insert into users table
        $sql = "INSERT INTO users (nmis_code, lastname, firstname, middlename, address_street, 
                address_barangay, address_district, address_city, address_province, address_region, address_zip, address_boxNo,
                sex, civil_status, tel_number, contact_number, email, fax_number, other_contact, 
                employment_type, employment_status, birthdate, birth_place, citizenship, religion, height, weight, 
                blood_type, distinguish_marks, sss_no, gsis_no, tin_no) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        
        // Ensure we're passing the exact number of parameters in the correct order
        $params = [
            $userData['nmis_code'],
            $userData['lastname'],
            $userData['firstname'],
            $userData['middlename'],
            $userData['address_street'],
            $userData['address_barangay'],
            $userData['address_district'],
            $userData['address_city'],
            $userData['address_province'],
            $userData['address_region'],
            $userData['address_zip'],
            $userData['address_boxNo'],
            $userData['sex'],
            $userData['civil_status'],
            $userData['tel_number'],
            $userData['contact_number'],
            $userData['email'],
            $userData['fax_number'],
            $userData['other_contact'],
            $userData['employment_type'],
            $userData['employment_status'],
            $userData['birthdate'],
            $userData['birth_place'],
            $userData['citizenship'],
            $userData['religion'],
            $userData['height'],
            $userData['weight'],
            $userData['blood_type'],
            $userData['distinguish_marks'],
            $userData['sss_no'],
            $userData['gsis_no'],
            $userData['tin_no']
        ];
        
        $stmt->execute($params);
        
        $userId = $pdo->lastInsertId();
        
        // Handle education records
        if (isset($_POST['school_name']) && is_array($_POST['school_name'])) {
            $eduSql = "INSERT INTO education (user_id, school_name, educational_level, 
                      year_from, year_to, degree, major, minor, units_earned, honors) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $eduStmt = $pdo->prepare($eduSql);
            
            foreach ($_POST['school_name'] as $key => $school) {
                if (!empty($school)) {
                    $eduData = [
                        $userId,
                        $school,
                        $_POST['educational_level'][$key] ?? '',
                        $_POST['year_from'][$key] ?? '',
                        $_POST['year_to'][$key] ?? '',
                        $_POST['degree'][$key] ?? '',
                        $_POST['major'][$key] ?? '',
                        $_POST['minor'][$key] ?? '',
                        $_POST['units_earned'][$key] ?? '',
                        $_POST['honors'][$key] ?? ''
                    ];
                    $eduStmt->execute($eduData);
                }
            }
        }
        
        // Handle work experience records
        if (isset($_POST['company_name']) && is_array($_POST['company_name'])) {
            $workSql = "INSERT INTO work_experience (user_id, company_name, position, 
                      inclusive_dates_past, inclusive_dates_present, monthly_salary, occupation, status, working_experience) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $workStmt = $pdo->prepare($workSql);
            
            foreach ($_POST['company_name'] as $key => $company) {
                if (!empty($company)) {
                    $workData = [
                        $userId,
                        $company,
                        $_POST['position'][$key] ?? '',
                        $_POST['work_date_from'][$key] ?? '', // Maps to inclusive_dates_past
                        $_POST['work_date_to'][$key] ?? '',   // Maps to inclusive_dates_present
                        $_POST['monthly_salary'][$key] ?? '',
                        $_POST['occupation'][$key] ?? '',
                        $_POST['status'][$key] ?? '',
                        $_POST['working_experience'][$key] ?? ''
                    ];
                    $workStmt->execute($workData);
                }
            }
        }
        
        // Handle training/seminar records
        if (isset($_POST['training_title']) && is_array($_POST['training_title'])) {
            $trainingSql = "INSERT INTO training_seminar (user_id, tittle, venue, 
                           inclusive_dates_past, inclusive_dates_present, certificate, no_of_hours, 
                           training_base, category, conducted_by, proficiency) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $trainingStmt = $pdo->prepare($trainingSql);
            
            foreach ($_POST['training_title'] as $key => $title) {
                if (!empty($title)) {
                    $trainingData = [
                        $userId,
                        $title, // Maps to tittle in the database (note the spelling)
                        $_POST['training_venue'][$key] ?? '',
                        $_POST['training_date_from'][$key] ?? '', // Maps to inclusive_dates_past
                        $_POST['training_date_to'][$key] ?? '',   // Maps to inclusive_dates_present
                        $_POST['certificate'][$key] ?? '',
                        $_POST['no_of_hours'][$key] ?? '',
                        $_POST['training_base'][$key] ?? '',
                        $_POST['category'][$key] ?? '',
                        $_POST['conducted_by'][$key] ?? '',
                        $_POST['proficiency'][$key] ?? ''
                    ];
                    $trainingStmt->execute($trainingData);
                }
            }
        }
        
        // Handle license/examination records
        if (isset($_POST['license_title']) && is_array($_POST['license_title'])) {
            $licenseSql = "INSERT INTO license_examination (user_id, license_tittle, year_taken, 
                          examination_venue, ratings, remarks, expiry_date) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $licenseStmt = $pdo->prepare($licenseSql);
            
            foreach ($_POST['license_title'] as $key => $title) {
                if (!empty($title)) {
                    $licenseData = [
                        $userId,
                        $title, // Maps to license_tittle in database (note the spelling)
                        $_POST['year_taken'][$key] ?? '',
                        $_POST['examination_venue'][$key] ?? '',
                        $_POST['ratings'][$key] ?? '',
                        $_POST['remarks'][$key] ?? '',
                        $_POST['expiry_date'][$key] ?? ''
                    ];
                    $licenseStmt->execute($licenseData);
                }
            }
        }
        
        // Handle competency assessment records
        if (isset($_POST['industry_sector']) && is_array($_POST['industry_sector'])) {
            $competencySql = "INSERT INTO competency_assessment (user_id, industry_sector, 
                             trade_area, occupation, classification_level, competency, specialization) 
                             VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $competencyStmt = $pdo->prepare($competencySql);
            
            foreach ($_POST['industry_sector'] as $key => $sector) {
                if (!empty($sector)) {
                    $competencyData = [
                        $userId,
                        $sector,
                        $_POST['trade_area'][$key] ?? '',
                        $_POST['occupation'][$key] ?? '',
                        $_POST['classification_level'][$key] ?? '',
                        $_POST['competency'][$key] ?? '',
                        $_POST['specialization'][$key] ?? ''
                    ];
                    $competencyStmt->execute($competencyData);
                }
            }
        }

        // Handle family background records
        if (isset($_POST['spouse_name'])) {
            $familySql = "INSERT INTO family_background (user_id, spouse_name, spouse_educational_attainment, spouse_occupation, 
                            spouse_monthly_income, father_name, father_educational_attainment, father_occupation, father_monthly_income,
                            mother_name, mother_educational_attainment, mother_occupation, mother_monthly_income, guardian_name, 
                            guardian_educational_attainment, guardian_occupation, guardian_monthly_income, dependents, dependents_age) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $familyStmt = $pdo->prepare($familySql);
            
            // If dependent names were submitted as an array, join them into a comma-separated string
            $dependents = isset($_POST['dependent_name']) && is_array($_POST['dependent_name']) 
                        ? implode(', ', array_filter($_POST['dependent_name'])) 
                        : ($_POST['dependents'] ?? '');
                        
            $dependentsAge = isset($_POST['dependent_age']) && is_array($_POST['dependent_age'])
                        ? implode(', ', array_filter($_POST['dependent_age']))
                        : ($_POST['dependents_age'] ?? '');
            
            $familyData = [
                $userId,
                $_POST['spouse_name'] ?? '',
                $_POST['spouse_educational_attainment'] ?? '',
                $_POST['spouse_occupation'] ?? '',
                $_POST['spouse_monthly_income'] ?? '',
                $_POST['father_name'] ?? '',
                $_POST['father_educational_attainment'] ?? '',
                $_POST['father_occupation'] ?? '',
                $_POST['father_monthly_income'] ?? '',
                $_POST['mother_name'] ?? '',
                $_POST['mother_educational_attainment'] ?? '',
                $_POST['mother_occupation'] ?? '',
                $_POST['mother_monthly_income'] ?? '',
                $_POST['guardian_name'] ?? '',
                $_POST['guardian_educational_attainment'] ?? '',
                $_POST['guardian_occupation'] ?? '',
                $_POST['guardian_monthly_income'] ?? '',
                $dependents,
                $dependentsAge
            ];
            
            $familyStmt->execute($familyData);
        }
        // Commit transaction
        $pdo->commit();
        $success_message = "Manpower profile submitted successfully!";
        
        // Redirect to view page
        header('Location: user/user_view.php');
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        if (isset($pdo)) {
            $pdo->rollBack();
        }
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NMIS Manpower Profile</title>
    <link rel="stylesheet" href="admin/css/index.css">
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <img src="admin/assets/tesda_logo.png" alt="TESDA Logo">
            <div class="header-text">
                <h2>Technical Education and Skills Development Authority</h2>
                <p>Pangasiwaan sa Edukasyong Teknikal at Pagpapaunlad ng Kasanayan</p>
            </div>
            <div class="form-title"><strong>NMIS FORM -01A</strong> <br> <span style="font-size: 10px;">(For TPIS)</span></div>
        </div>
        
        <h2 class="manpower-profile">MANPOWER PROFILE</h2>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <!-- Profile Photo -->
            <div class="signature-container">
                <h2 class="signature-title">Signature</h2>
                <div class="signature-box">ID PICTURE <br> (Passport Size)</div>
            </div>
            
            <!-- TESDA Section -->
            <div class="section">
                <div class="section-title">1. To be accomplished by TESDA</div>
                <div class="form-row">
                    <h3 class="name-title" style="font-size: 0.7em;">NMIS Manpower Code:</h3>
                    <div class="form-group">
                        <input type="text" name="nmis_code" class="value" required>
                    </div>
                    <h3 class="name-title" style="font-size: 0.7em; font-weight: normal;">NMIS Entry Date:</h3>
                    <div class="form-group">
                        <input type="date" name="entry_date" class="value">
                    </div>
                </div>
            </div>
            
            <!-- Personal Information -->
            <div class="section">
                <div class="form-row">
                    <h3 class="name-title">Name:</h3>
                    <div class="form-group">
                        <input type="text" name="lastname" class="value" required>
                        <div class="label">Last</div>
                    </div>
                    <div class="form-group">
                        <input type="text" name="firstname" class="value" required>
                        <div class="label">First</div>
                    </div>
                    <div class="form-group">
                        <input type="text" name="middlename" class="value">
                        <div class="label">Middle</div>
                    </div>
                </div>
                
                <div class="form-row">
                    <h3 class="name-title">Mailing Address:</h3>
                    <div class="form-group">
                        <input type="text" name="address_street" class="value">
                        <div class="label">Number, Street</div>
                    </div>
                    <div class="form-group">
                        <input type="text" name="address_barangay" class="value">
                        <div class="label">Barangay</div>
                    </div>
                    <div class="form-group">
                        <input type="text" name="address_district" class="value">
                        <div class="label">Congressional District</div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <input type="text" name="address_city" class="value">
                        <div class="label">City/Municipality</div>
                    </div>
                    <div class="form-group">
                        <input type="text" name="address_province" class="value">
                        <div class="label">Province</div>
                    </div>
                    <div class="form-group">
                        <input type="text" name="address_region" class="value">
                        <div class="label">Region</div>
                    </div>
                    <div class="form-group">
                        <input type="text" name="address_zip" class="value">
                        <div class="label">Zip Code</div>
                    </div>
                    <div class="form-group">
                        <input type="text" name="address_boxNo" class="value">
                        <div class="label">P.O Box No.</div>
                    </div>
                </div>
            </div>
            
            <!-- Additional Information -->
            <div class="section">
                <div class="form-row">
                    <div class="form-group">
                        <h3 class="name-title" style="font-size: 0.7em;">Sex:</h3>
                        <div class="form-checkbox">
                            <div class="checkbox-group">
                                <input type="radio" name="sex" value="Male" class="checkbox" required>
                                <div class="label-check">Male</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="radio" name="sex" value="Female" class="checkbox">
                                <div class="label-check">Female</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <h3 class="name-title" style="font-size: 0.7em;">Civil Status:</h3>
                        <div class="form-checkbox">
                            <div class="checkbox-group">
                                <input type="radio" name="civil_status" value="Single" class="checkbox" required>
                                <div class="label-check">Single</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="radio" name="civil_status" value="Married" class="checkbox">
                                <div class="label-check">Married</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="radio" name="civil_status" value="Widow/er" class="checkbox">
                                <div class="label-check">Widow/er</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="radio" name="civil_status" value="Separated" class="checkbox">
                                <div class="label-check">Separated</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <h3 class="name-title" style="font-size: 0.7em;">Contact Number(s):</h3>
                        <div class="form-contact">
                            <div class="label-contact">Tel:</div>
                            <div class="value-value">
                                <input type="text" name="tel_number">
                            </div>
                        </div>
                        <div class="form-contact">
                            <div class="label-contact">Cellular:</div>
                            <div class="value-value">
                                <input type="text" name="contact_number">
                            </div>
                        </div>
                        <div class="form-contact">
                            <div class="label-contact">E-mail:</div>
                            <div class="value-value">
                                <input type="email" name="email">
                            </div>
                        </div>
                        <div class="form-contact">
                            <div class="label-contact">Fax:</div>
                            <div class="value-value">
                                <input type="text" name="fax_number">
                            </div>
                        </div>
                        <div class="form-contact">
                            <div class="label-contact">Others:</div>
                            <div class="value-value">
                                <input type="text" name="other_contact">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <h3 class="name-title" style="font-size: 0.7em;">Employment Type:</h3>
                        <div class="form-checkbox">
                            <div class="checkbox-group">
                                <input type="radio" name="employment_type" value="Employed" class="checkbox" required>
                                <div class="label-check">Employed</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="radio" name="employment_type" value="Self-employed" class="checkbox">
                                <div class="label-check">Self-employed</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="radio" name="employment_type" value="Unemployed" class="checkbox">
                                <div class="label-check">Unemployed</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <h3 class="name-title" style="font-size: 0.7em;">Employment Status:</h3>
                        <div class="form-checkbox">
                            <div class="checkbox-group">
                                <input type="radio" name="employment_status" value="Casual" class="checkbox" required>
                                <div class="label-check">Casual</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="radio" name="employment_status" value="Contractual" class="checkbox">
                                <div class="label-check">Contractual</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="radio" name="employment_status" value="Job-Order" class="checkbox">
                                <div class="label-check">Job Order</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="radio" name="employment_status" value="Temporary" class="checkbox">
                                <div class="label-check">Temporary</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="radio" name="employment_status" value="Probationary" class="checkbox">
                                <div class="label-check">Probationary</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="radio" name="employment_status" value="Regular" class="checkbox">
                                <div class="label-check">Regular</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="radio" name="employment_status" value="Permanent" class="checkbox">
                                <div class="label-check">Permanent</div>
                            </div>
                            <h3 class="name-title" style="font-size: 0.7em;">If Student</h3>
                            <div class="checkbox-group">
                                <input type="radio" name="employment_status" value="Trainee" class="checkbox">
                                <div class="label-check">Trainee/OJT</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Personal Details Section -->
            <div class="section">
                <div class="form-row">
                    <div class="form-group">
                        <div class="form-personal">
                            <div class="label-personal">Birthdate:</div>
                            <div class="value-value">
                                <input type="date" name="birthdate">
                            </div>
                        </div>
                        <div class="form-personal">
                            <div class="label-personal">Birth Place:</div>
                            <div class="value-value">
                                <input type="text" name="birth_place">
                            </div>
                        </div>
                        <div class="form-personal">
                            <div class="label-personal">Citizenship:</div>
                            <div class="value-value">
                                <input type="text" name="citizenship">
                            </div>
                        </div>
                        <div class="form-personal">
                            <div class="label-personal">Religion:</div>
                            <div class="value-value">
                                <input type="text" name="religion">
                            </div>
                        </div>
                        <div class="form-personal">
                            <div class="label-personal">Height:</div>
                            <div class="value-value">
                                <input type="text" name="height">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-personal">
                            <div class="label-personal">Weight:</div>
                            <div class="value-value">
                                <input type="text" name="weight">
                            </div>
                        </div>
                        <div class="form-personal">
                            <div class="label-personal">Blood Type: </div>
                            <div class="value-value">
                                <input type="text" name="blood_type">
                            </div>
                        </div>
                        <div class="form-personal">
                            <div class="label-personal">SSS No.: </div>
                            <div class="value-value">
                                <input type="text" name="sss_no">
                            </div>
                        </div>
                        <div class="form-personal">
                            <div class="label-personal">GSIS No.:</div>
                            <div class="value-value">
                                <input type="text" name="gsis_no">
                            </div>
                        </div>
                        <div class="form-personal">
                            <div class="label-personal">TIN No.:</div>
                            <div class="value-value">
                                <input type="text" name="tin_no">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-personal">
                            <div class="label-personal">Distinguishing Marks:</div>
                            <div class="value-value">
                                <input type="text" name="distinguish_marks">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 4. Educational Background-->
            <div class="section">
                <div class="section-title">4. Educational Background</div>
                <table>
                    <thead>
                        <tr>
                            <th>School</th>
                            <th>Educational Level</th>
                            <th colspan="2">School Year</th>
                            <th>Degree</th>
                            <th>Major</th>
                            <th>Minor</th>
                            <th>Units Earned</th>
                            <th>Honors Received</th>
                        </tr>
                    </thead>
                    <tbody id="education-container">
                        <tr class="education-entry">
                            <td><input type="text" name="school_name[]"></td>
                            <td>
                                <select name="educational_level[]">
                                    <option value="">Select Level</option>
                                    <option value="Elementary">Elementary</option>
                                    <option value="Secondary">Secondary</option>
                                    <option value="Vocational">Vocational</option>
                                    <option value="College">College</option>
                                    <option value="Graduate">Graduate</option>
                                </select>
                            </td>
                            <td><input type="text" name="year_from[]" placeholder="From"></td>
                            <td><input type="text" name="year_to[]" placeholder="To"></td>
                            <td><input type="text" name="degree[]"></td>
                            <td><input type="text" name="major[]"></td>
                            <td><input type="text" name="minor[]"></td>
                            <td><input type="text" name="units_earned[]"></td>
                            <td><input type="text" name="honors[]"></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-secondary" onclick="addWork()">Add More Work Experience</button>
            </div>
            
            <!-- 6. Training Seminar Attended -->
            <div class="section">
                <div class="section-title">6. Training Seminar Attended</div>
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
                    <tbody id="training-container">
                        <tr class="training-entry">
                            <td><input type="text" name="training_title[]"></td>
                            <td><input type="text" name="training_venue[]"></td>
                            <td><input type="date" name="training_date_from[]" placeholder="From"></td>
                            <td><input type="date" name="training_date_to[]" placeholder="To"></td>
                            <td><input type="text" name="certificate[]"></td>
                            <td><input type="text" name="no_of_hours[]"></td>
                            <td><input type="text" name="training_base[]"></td>
                            <td><input type="text" name="category[]"></td>
                            <td><input type="text" name="conducted_by[]"></td>
                            <td><input type="text" name="proficiency[]"></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-secondary" onclick="addTraining()">Add More Training</button>
            </div>
            
            <!-- 7. License/Examination -->
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
                    <tbody id="license-container">
                        <tr class="license-entry">
                            <td><input type="text" name="license_title[]"></td>
                            <td><input type="text" name="year_taken[]"></td>
                            <td><input type="text" name="examination_venue[]"></td>
                            <td><input type="text" name="ratings[]"></td>
                            <td><input type="text" name="remarks[]"></td>
                            <td><input type="date" name="expiry_date[]"></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-secondary" onclick="addLicense()">Add More License/Examination</button>
            </div>
            
            <!-- 8. Competency Assessment Passed -->
            <div class="section">
                <div class="section-title">8. Competency Assessment Passed</div>
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
                    <tbody id="competency-container">
                        <tr class="competency-entry">
                            <td><input type="text" name="industry_sector[]"></td>
                            <td><input type="text" name="trade_area[]"></td>
                            <td><input type="text" name="occupation[]"></td>
                            <td><input type="text" name="classification_level[]"></td>
                            <td><input type="text" name="competency[]"></td>
                            <td><input type="text" name="specialization[]"></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-secondary" onclick="addCompetency()">Add More Competency Assessment</button>
            </div>
            
            <!-- 9. Family Background -->
            <div class="section">
                <div class="section-title">9. Family Background</div>

                <div class="section"> 
                    <div class="form-row">
                        <div class="form-group">
                            <div class="form-personal">
                                <div class="label-personal">Spouse's Name:</div>
                                <div class="value-value">
                                    <input type="text" name="spouse_name">
                                </div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Educational Attainment:</div>
                                <div class="value-value">
                                    <input type="text" name="spouse_educational_attainment">
                                </div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Occupation: </div>
                                <div class="value-value">
                                    <input type="text" name="spouse_occupation">
                                </div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Ave. Monthly Income: </div>
                                <div class="value-value">
                                    <input type="text" name="spouse_monthly_income">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <div class="form-row">
                        <div class="form-group">
                            <div class="form-personal">
                                <div class="label-personal">Father's Name:</div>
                                <div class="value-value">
                                    <input type="text" name="father_name">
                                </div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Educational Attainment::</div>
                                <div class="value-value">
                                    <input type="text" name="father_educational_attainment">
                                </div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Occupation: </div>
                                <div class="value-value">
                                    <input type="text" name="father_occupation">
                                </div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Ave. Monthly Income: </div>
                                <div class="value-value">
                                    <input type="text" name="father_monthly_income">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="section">
                    <div class="form-row">
                        <div class="form-group">
                            <div class="form-personal">
                                <div class="label-personal">Mother's Name:</div>
                                <div class="value-value">
                                    <input type="text" name="mother_name">
                                </div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Educational Attainment: </div>
                                <div class="value-value">
                                    <input type="text" name="mother_educational_attainment">
                                </div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Occupation: </div>
                                <div class="value-value">
                                    <input type="text" name="mother_occupation">
                                </div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Ave. Monthly Income: </div>
                                <div class="value-value">
                                    <input type="text" name="mother_monthly_income">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <div class="form-row">
                        <div class="form-group">
                            <div class="form-personal">
                                <div class="label-personal">Name of Guardian: </div>
                                <div class="value-value">
                                    <input type="text" name="guardian_name">
                                </div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Educational Attainment::</div>
                                <div class="value-value">
                                    <input type="text" name="guardian_educational_attainment">
                                </div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Occupation: </div>
                                <div class="value-value">
                                    <input type="text" name="guardian_occupation">
                                </div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Ave. Monthly Income: </div>
                                <div class="value-value">
                                    <input type="text" name="guardian_monthly_income">
                                </div>
                            </div>
                        </div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Dependents</th>
                                <th>Age</th>

                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" name="dependents"></td>
                                <td><input type="text" name="dependents_age"></td>
                            </tr>
                    </table>
                    <button type="button" class="btn btn-secondary" onclick="addDependant()">Add More Dependant</button>
                </div>
                <!-- Add family background fields as needed -->
            </div>
            
            <div style="text-align: center; margin-top: 30px; margin-bottom: 20px;">
                <button type="submit" class="btn btn-primary">Submit Manpower Profile</button>
                <a href="user/user_view.php" class="btn btn-secondary">View Submitted Profile</a>
                <a href="user/crud/edit.php" class="btn btn-third">Edit Submitted Profile</a>
            </div>
        </form>
    </div>

    <script>
    function addEducation() {
        const container = document.getElementById('education-container');
        const newEntry = container.querySelector('.education-entry').cloneNode(true);
        // Clear input values
        newEntry.querySelectorAll('input, select').forEach(input => input.value = '');
        container.appendChild(newEntry);
    }

    function addWork() {
        const container = document.getElementById('work-container');
        const newEntry = container.querySelector('.work-entry').cloneNode(true);
        // Clear input values
        newEntry.querySelectorAll('input').forEach(input => input.value = '');
        container.appendChild(newEntry);
    }
    
    function addTraining() {
        const container = document.getElementById('training-container');
        const newEntry = container.querySelector('.training-entry').cloneNode(true);
        // Clear input values
        newEntry.querySelectorAll('input').forEach(input => input.value = '');
        container.appendChild(newEntry);
    }
    
    function addLicense() {
        const container = document.getElementById('license-container');
        const newEntry = container.querySelector('.license-entry').cloneNode(true);
        // Clear input values
        newEntry.querySelectorAll('input').forEach(input => input.value = '');
        container.appendChild(newEntry);
    }
    
    function addCompetency() {
        const container = document.getElementById('competency-container');
        const newEntry = container.querySelector('.competency-entry').cloneNode(true);
        // Clear input values
        newEntry.querySelectorAll('input').forEach(input => input.value = '');
        container.appendChild(newEntry);
    }


    function addDependant() {
        const container = document.getElementById('dependant-container');
        const newEntry = container.querySelector('.dependant-entry').cloneNode(true);
        // Clear input values
        newEntry.querySelectorAll('input').forEach(input => input.value = '');
        container.appendChild(newEntry);
    }
    </script>
</body>
</html> 