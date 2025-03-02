<?php
session_start();
require_once __DIR__ . '/../../connections/config.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if a specific user ID is provided
    $userId = isset($_GET['id']) ? intval($_GET['id']) : null;
    
    // If no ID is provided, redirect or show an error
    if (!$userId) {
        $error_message = "No user ID specified.";
        // Optionally redirect to a user list page
        // header("Location: user_list.php");
        // exit();
    } else {
        // Fetch the specific user's record
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // If user not found, handle the error
        if (!$user) {
            $error_message = "User not found.";
        } else {
        
        // Fetch education records
        $eduStmt = $pdo->prepare("SELECT * FROM education WHERE user_id = ?");
        $eduStmt->execute([$userId]);
        $education = $eduStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch working experience
        $workStmt = $pdo->prepare("SELECT * FROM work_experience WHERE user_id = ?");
        $workStmt->execute([$userId]);
        $work_experience = $workStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Fetch training/Seminar Attended
        $trainingStmt = $pdo->prepare("SELECT * FROM training_seminar WHERE user_id = ?");
        $trainingStmt->execute([$userId]);
        $training_seminar = $trainingStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Fetch Licenses/Examinations Passed
        $licenseStmt = $pdo->prepare("SELECT * FROM license_examination WHERE user_id = ?");
        $licenseStmt->execute([$userId]);
        $license_examination = $licenseStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch Competency Assessment Passed
        $competencyStmt = $pdo->prepare("SELECT * FROM competency_assessment WHERE user_id = ?");
        $competencyStmt->execute([$userId]);
        $competency_assessment = $competencyStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch Family Background
        $familyStmt = $pdo->prepare("SELECT * FROM family_background WHERE user_id = ?");
        $familyStmt->execute([$userId]);
        $family_background = $familyStmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
    
    // Handle form submission for updates
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            // Begin transaction
            $pdo->beginTransaction();
            
            // Update user data
            $updateUserSql = "UPDATE users SET 
                nmis_code = ?,
                lastname = ?,
                firstname = ?,
                middlename = ?,
                address_street = ?,
                address_barangay = ?,
                address_district = ?,
                address_city = ?,
                address_province = ?,
                address_region = ?,
                address_zip = ?,
                address_boxNo = ?,
                sex = ?,
                civil_status = ?,
                tel_number = ?,
                contact_number = ?,
                email = ?,
                fax_number = ?,
                other_contact = ?,
                employment_type = ?,
                employment_status = ?,
                birthdate = ?,
                birth_place = ?,
                citizenship = ?,
                religion = ?,
                height = ?,
                weight = ?,
                blood_type = ?,
                distinguish_marks = ?,
                sss_no = ?,
                gsis_no = ?,
                tin_no = ?
                WHERE id = ?";
            
            $updateUserStmt = $pdo->prepare($updateUserSql);
            
            $updateUserParams = [
                $_POST['nmis_code'],
                $_POST['lastname'],
                $_POST['firstname'],
                $_POST['middlename'],
                $_POST['address_street'],
                $_POST['address_barangay'],
                $_POST['address_district'],
                $_POST['address_city'],
                $_POST['address_province'],
                $_POST['address_region'],
                $_POST['address_zip'],
                $_POST['address_boxNo'],
                $_POST['sex'],
                $_POST['civil_status'],
                $_POST['tel_number'],
                $_POST['contact_number'],
                $_POST['email'],
                $_POST['fax_number'],
                $_POST['other_contact'],
                $_POST['employment_type'],
                $_POST['employment_status'],
                $_POST['birthdate'],
                $_POST['birth_place'],
                $_POST['citizenship'],
                $_POST['religion'],
                $_POST['height'],
                $_POST['weight'],
                $_POST['blood_type'],
                $_POST['distinguish_marks'],
                $_POST['sss_no'],
                $_POST['gsis_no'],
                $_POST['tin_no'],
                $userId
            ];
            
            $updateUserStmt->execute($updateUserParams);
            
            // For each section, first delete existing records, then insert new ones
            
            // Delete existing education records
            $pdo->prepare("DELETE FROM education WHERE user_id = ?")->execute([$userId]);
            
            // Re-insert education records
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
            
            // Delete existing work experience records
            $pdo->prepare("DELETE FROM work_experience WHERE user_id = ?")->execute([$userId]);
            
            // Re-insert work experience records
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
                            $_POST['work_date_from'][$key] ?? '',
                            $_POST['work_date_to'][$key] ?? '',
                            $_POST['monthly_salary'][$key] ?? '',
                            $_POST['occupation'][$key] ?? '',
                            $_POST['status'][$key] ?? '',
                            $_POST['working_experience'][$key] ?? ''
                        ];
                        $workStmt->execute($workData);
                    }
                }
            }
            
            // Delete existing training/seminar records
            $pdo->prepare("DELETE FROM training_seminar WHERE user_id = ?")->execute([$userId]);
            
            // Re-insert training/seminar records
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
                            $title,
                            $_POST['training_venue'][$key] ?? '',
                            $_POST['training_date_from'][$key] ?? '',
                            $_POST['training_date_to'][$key] ?? '',
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
            
            // Delete existing license/examination records
            $pdo->prepare("DELETE FROM license_examination WHERE user_id = ?")->execute([$userId]);
            
            // Re-insert license/examination records
            if (isset($_POST['license_title']) && is_array($_POST['license_title'])) {
                $licenseSql = "INSERT INTO license_examination (user_id, license_tittle, year_taken, 
                              examination_venue, ratings, remarks, expiry_date) 
                              VALUES (?, ?, ?, ?, ?, ?, ?)";
                
                $licenseStmt = $pdo->prepare($licenseSql);
                
                foreach ($_POST['license_title'] as $key => $title) {
                    if (!empty($title)) {
                        $licenseData = [
                            $userId,
                            $title,
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
            
            // Delete existing competency assessment records
            $pdo->prepare("DELETE FROM competency_assessment WHERE user_id = ?")->execute([$userId]);
            
            // Re-insert competency assessment records
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
            
            // Delete existing family background record
            $pdo->prepare("DELETE FROM family_background WHERE user_id = ?")->execute([$userId]);
            
            // Re-insert family background record
            if (isset($_POST['spouse_name'])) {
                $familySql = "INSERT INTO family_background (user_id, spouse_name, spouse_educational_attainment, spouse_occupation, 
                                spouse_monthly_income, father_name, father_educational_attainment, father_occupation, father_monthly_income,
                                mother_name, mother_educational_attainment, mother_occupation, mother_monthly_income, guardian_name, 
                                guardian_educational_attainment, guardian_occupation, guardian_monthly_income, dependents, dependents_age) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $familyStmt = $pdo->prepare($familySql);
                
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
                    $_POST['dependents'] ?? '',
                    $_POST['dependents_age'] ?? ''
                ];
                
                $familyStmt->execute($familyData);
            }
            
            // Commit transaction
            $pdo->commit();
            
            // Set success message and redirect
            $success_message = "Profile updated successfully!";
            header("Location: admin_edit.php");
            exit();
            
        } catch (Exception $e) {
            // Rollback transaction on error
            if (isset($pdo)) {
                $pdo->rollBack();
            }
            $error_message = "Error: " . $e->getMessage();
        }
    }
} catch (PDOException $e) {
    $error_message = "Database error: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit NMIS Manpower Profile</title>
    <link rel="stylesheet" href="../../admin/css/index.css">
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <img src="../../admin/assets/tesda_logo.png" alt="TESDA Logo">
            <div class="header-text">
                <h2>Technical Education and Skills Development Authority</h2>
                <p>Pangasiwaan sa Edukasyong Teknikal at Pagpapaunlad ng Kasanayan</p>
            </div>
            <div class="form-title"><strong>NMIS FORM -01A</strong> <br> <span style="font-size: 10px;">(For TPIS)</span></div>
        </div>
        
        <h2 class="manpower-profile">EDIT MANPOWER PROFILE</h2>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($user) && !empty($user)): ?>
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
                        <input type="text" name="nmis_code" class="value" value="<?php echo htmlspecialchars($user['nmis_code'] ?? ''); ?>" required>
                    </div>
                    <h3 class="name-title" style="font-size: 0.7em; font-weight: normal;">NMIS Entry Date:</h3>
                    <div class="form-group">
                        <input type="date" name="entry_date" class="value" value="<?php echo htmlspecialchars($user['nmis_entry'] ?? ''); ?>">
                    </div>
                </div>
            </div>
            
            <!-- Personal Information -->
            <div class="section">
                <div class="form-row">
                    <h3 class="name-title">Name:</h3>
                    <div class="form-group">
                        <input type="text" name="lastname" class="value" value="<?php echo htmlspecialchars($user['lastname'] ?? ''); ?>" required>
                        <div class="label">Last</div>
                    </div>
                    <div class="form-group">
                        <input type="text" name="firstname" class="value" value="<?php echo htmlspecialchars($user['firstname'] ?? ''); ?>" required>
                        <div class="label">First</div>
                    </div>
                    <div class="form-group">
                        <input type="text" name="middlename" class="value" value="<?php echo htmlspecialchars($user['middlename'] ?? ''); ?>">
                        <div class="label">Middle</div>
                    </div>
                </div>
                
                <div class="form-row">
                    <h3 class="name-title">Mailing Address:</h3>
                    <div class="form-group">
                        <input type="text" name="address_street" class="value" value="<?php echo htmlspecialchars($user['address_street'] ?? ''); ?>">
                        <div class="label">Number, Street</div>
                    </div>
                    <div class="form-group">
                        <input type="text" name="address_barangay" class="value" value="<?php echo htmlspecialchars($user['address_barangay'] ?? ''); ?>">
                        <div class="label">Barangay</div>
                    </div>
                    <div class="form-group">
                        <input type="text" name="address_district" class="value" value="<?php echo htmlspecialchars($user['address_district'] ?? ''); ?>">
                        <div class="label">Congressional District</div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <input type="text" name="address_city" class="value" value="<?php echo htmlspecialchars($user['address_city'] ?? ''); ?>">
                        <div class="label">City/Municipality</div>
                    </div>
                    <div class="form-group">
                        <input type="text" name="address_province" class="value" value="<?php echo htmlspecialchars($user['address_province'] ?? ''); ?>">
                        <div class="label">Province</div>
                    </div>
                    <div class="form-group">
                        <input type="text" name="address_region" class="value" value="<?php echo htmlspecialchars($user['address_region'] ?? ''); ?>">
                        <div class="label">Region</div>
                    </div>
                    <div class="form-group">
                        <input type="text" name="address_zip" class="value" value="<?php echo htmlspecialchars($user['address_zip'] ?? ''); ?>">
                        <div class="label">Zip Code</div>
                    </div>
                    <div class="form-group">
                        <input type="text" name="address_boxNo" class="value" value="<?php echo htmlspecialchars($user['address_boxNo'] ?? ''); ?>">
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
                                <input type="radio" name="sex" value="Male" class="checkbox" <?php echo ($user['sex'] == 'Male') ? 'checked' : ''; ?> required>
                                <div class="label-check">Male</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="radio" name="sex" value="Female" class="checkbox" <?php echo ($user['sex'] == 'Female') ? 'checked' : ''; ?>>
                                <div class="label-check">Female</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <h3 class="name-title" style="font-size: 0.7em;">Civil Status:</h3>
                        <div class="form-checkbox">
                            <div class="checkbox-group">
                                <input type="radio" name="civil_status" value="Single" class="checkbox" <?php echo ($user['civil_status'] == 'Single') ? 'checked' : ''; ?> required>
                                <div class="label-check">Single</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="radio" name="civil_status" value="Married" class="checkbox" <?php echo ($user['civil_status'] == 'Married') ? 'checked' : ''; ?>>
                                <div class="label-check">Married</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="radio" name="civil_status" value="Widow/er" class="checkbox" <?php echo ($user['civil_status'] == 'Widow/er') ? 'checked' : ''; ?>>
                                <div class="label-check">Widow/er</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="radio" name="civil_status" value="Separated" class="checkbox" <?php echo ($user['civil_status'] == 'Separated') ? 'checked' : ''; ?>>
                                <div class="label-check">Separated</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <h3 class="name-title" style="font-size: 0.7em;">Contact Number(s):</h3>
                        <div class="form-contact">
                            <div class="label-contact">Tel:</div>
                            <div class="value-value">
                                <input type="text" name="tel_number" value="<?php echo htmlspecialchars($user['tel_number'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-contact">
                            <div class="label-contact">Cellular:</div>
                            <div class="value-value">
                                <input type="text" name="contact_number" value="<?php echo htmlspecialchars($user['contact_number'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-contact">
                            <div class="label-contact">E-mail:</div>
                            <div class="value-value">
                                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-contact">
                            <div class="label-contact">Fax:</div>
                            <div class="value-value">
                                <input type="text" name="fax_number" value="<?php echo htmlspecialchars($user['fax_number'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-contact">
                            <div class="label-contact">Others:</div>
                            <div class="value-value">
                                <input type="text" name="other_contact" value="<?php echo htmlspecialchars($user['other_contact'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <h3 class="name-title" style="font-size: 0.7em;">Employment Type:</h3>
                        <div class="form-checkbox">
                            <div class="checkbox-group">
                                <input type="radio" name="employment_type" value="Employed" class="checkbox" <?php echo ($user['employment_type'] == 'Employed') ? 'checked' : ''; ?> required>
                                <div class="label-check">Employed</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="radio" name="employment_type" value="Self-employed" class="checkbox" <?php echo ($user['employment_type'] == 'Self-employed') ? 'checked' : ''; ?>>
                                <div class="label-check">Self-employed</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="radio" name="employment_type" value="Unemployed" class="checkbox" <?php echo ($user['employment_type'] == 'Unemployed') ? 'checked' : ''; ?>>
                                <div class="label-check">Unemployed</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <h3 class="name-title" style="font-size: 0.7em;">Employment Status:</h3>
                        <div class="form-checkbox">
                            <div class="checkbox-group">
                                <input type="radio" name="employment_status" value="Casual" class="checkbox" <?php echo ($user['employment_status'] == 'Casual') ? 'checked' : ''; ?> required>
                                <div class="label-check">Casual</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="radio" name="employment_status" value="Contractual" class="checkbox" <?php echo ($user['employment_status'] == 'Contractual') ? 'checked' : ''; ?>>
                                <div class="label-check">Contractual</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="radio" name="employment_status" value="Job-Order" class="checkbox" <?php echo ($user['employment_status'] == 'Job-Order') ? 'checked' : ''; ?>>
                                <div class="label-check">Job Order</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="radio" name="employment_status" value="Temporary" class="checkbox" <?php echo ($user['employment_status'] == 'Temporary') ? 'checked' : ''; ?>>
                                <div class="label-check">Temporary</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="radio" name="employment_status" value="Probationary" class="checkbox" <?php echo ($user['employment_status'] == 'Probationary') ? 'checked' : ''; ?>>
                                <div class="label-check">Probationary</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="radio" name="employment_status" value="Regular" class="checkbox" <?php echo ($user['employment_status'] == 'Regular') ? 'checked' : ''; ?>>
                                <div class="label-check">Regular</div>
                            </div>
                            <div class="checkbox-group">
                                <input type="radio" name="employment_status" value="Permanent" class="checkbox" <?php echo ($user['employment_status'] == 'Permanent') ? 'checked' : ''; ?>>
                                <div class="label-check">Permanent</div>
                            </div>
                            <h3 class="name-title" style="font-size: 0.7em;">If Student</h3>
                            <div class="checkbox-group">
                                <input type="radio" name="employment_status" value="Trainee" class="checkbox" <?php echo ($user['employment_status'] == 'Trainee') ? 'checked' : ''; ?>>
                                <div class="label-check">Trainee/OJT</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-personal">
                            <div class="label-personal">Weight:</div>
                            <div class="value-value">
                                <input type="text" name="weight" value="<?php echo htmlspecialchars($user['weight'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-personal">
                            <div class="label-personal">Blood Type: </div>
                            <div class="value-value">
                                <input type="text" name="blood_type" value="<?php echo htmlspecialchars($user['blood_type'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-personal">
                            <div class="label-personal">SSS No.: </div>
                            <div class="value-value">
                                <input type="text" name="sss_no" value="<?php echo htmlspecialchars($user['sss_no'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-personal">
                            <div class="label-personal">GSIS No.:</div>
                            <div class="value-value">
                                <input type="text" name="gsis_no" value="<?php echo htmlspecialchars($user['gsis_no'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-personal">
                            <div class="label-personal">TIN No.:</div>
                            <div class="value-value">
                                <input type="text" name="tin_no" value="<?php echo htmlspecialchars($user['tin_no'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-personal">
                            <div class="label-personal">Distinguishing Marks:</div>
                            <div class="value-value">
                                <input type="text" name="distinguish_marks" value="<?php echo htmlspecialchars($user['distinguish_marks'] ?? ''); ?>">
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
                        <?php if (!empty($education)): ?>
                            <?php foreach ($education as $edu): ?>
                                <tr class="education-entry">
                                    <td><input type="text" name="school_name[]" value="<?php echo htmlspecialchars($edu['school_name'] ?? ''); ?>"></td>
                                    <td>
                                        <select name="educational_level[]">
                                            <option value="">Select Level</option>
                                            <option value="Elementary" <?php echo ($edu['educational_level'] == 'Elementary') ? 'selected' : ''; ?>>Elementary</option>
                                            <option value="Secondary" <?php echo ($edu['educational_level'] == 'Secondary') ? 'selected' : ''; ?>>Secondary</option>
                                            <option value="Vocational" <?php echo ($edu['educational_level'] == 'Vocational') ? 'selected' : ''; ?>>Vocational</option>
                                            <option value="College" <?php echo ($edu['educational_level'] == 'College') ? 'selected' : ''; ?>>College</option>
                                            <option value="Graduate" <?php echo ($edu['educational_level'] == 'Graduate') ? 'selected' : ''; ?>>Graduate</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="year_from[]" placeholder="From" value="<?php echo htmlspecialchars($edu['year_from'] ?? ''); ?>"></td>
                                    <td><input type="text" name="year_to[]" placeholder="To" value="<?php echo htmlspecialchars($edu['year_to'] ?? ''); ?>"></td>
                                    <td><input type="text" name="degree[]" value="<?php echo htmlspecialchars($edu['degree'] ?? ''); ?>"></td>
                                    <td><input type="text" name="major[]" value="<?php echo htmlspecialchars($edu['major'] ?? ''); ?>"></td>
                                    <td><input type="text" name="minor[]" value="<?php echo htmlspecialchars($edu['minor'] ?? ''); ?>"></td>
                                    <td><input type="text" name="units_earned[]" value="<?php echo htmlspecialchars($edu['units_earned'] ?? ''); ?>"></td>
                                    <td><input type="text" name="honors[]" value="<?php echo htmlspecialchars($edu['honors'] ?? ''); ?>"></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
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
                        <?php endif; ?>
                    </tbody>
                </table>
                <button type="button" class="btn btn-secondary" onclick="addEducation()">Add More Education</button>
            </div>
            
            <!-- 5. Work Experience -->
            <div class="section">
                <div class="section-title">5. Working Experience</div>
                <table>
                    <thead>
                        <tr>
                            <th>Name of the Company</th>
                            <th>Position</th>
                            <th colspan="2">Inclusive Dates</th>
                            <th>Monthly Salary</th>
                            <th>Occupation Type</th>
                            <th>Status of Appointment</th>
                            <th>No. of Yrs Working Exp</th>
                        </tr>
                    </thead>
                    <tbody id="work-container">
                        <?php if (!empty($work_experience)): ?>
                            <?php foreach ($work_experience as $work): ?>
                                <tr class="work-entry">
                                    <td><input type="text" name="company_name[]" value="<?php echo htmlspecialchars($work['company_name'] ?? ''); ?>"></td>
                                    <td><input type="text" name="position[]" value="<?php echo htmlspecialchars($work['position'] ?? ''); ?>"></td>
                                    <td><input type="text" name="work_date_from[]" value="<?php echo htmlspecialchars($work['inclusive_dates_past'] ?? ''); ?>"></td>
                                    <td><input type="text" name="work_date_to[]" value="<?php echo htmlspecialchars($work['inclusive_dates_present'] ?? ''); ?>"></td>
                                    <td><input type="text" name="monthly_salary[]" value="<?php echo htmlspecialchars($work['monthly_salary'] ?? ''); ?>"></td>
                                    <td><input type="text" name="occupation[]" value="<?php echo htmlspecialchars($work['occupation'] ?? ''); ?>"></td>
                                    <td><input type="text" name="status[]" value="<?php echo htmlspecialchars($work['status'] ?? ''); ?>"></td>
                                    <td><input type="text" name="working_experience[]" value="<?php echo htmlspecialchars($work['working_experience'] ?? ''); ?>"></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr class="work-entry">
                                <td><input type="text" name="company_name[]"></td>
                                <td><input type="text" name="position[]"></td>
                                <td><input type="text" name="work_date_from[]"></td>
                                <td><input type="text" name="work_date_to[]"></td>
                                <td><input type="text" name="monthly_salary[]"></td>
                                <td><input type="text" name="occupation[]"></td>
                                <td><input type="text" name="status[]"></td>
                                <td><input type="text" name="working_experience[]"></td>
                            </tr>
                        <?php endif; ?>
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
                        <?php if (!empty($training_seminar)): ?>
                            <?php foreach ($training_seminar as $training): ?>
                                <tr class="training-entry">
                                    <td><input type="text" name="training_title[]" value="<?php echo htmlspecialchars($training['tittle'] ?? ''); ?>"></td>
                                    <td><input type="text" name="training_venue[]" value="<?php echo htmlspecialchars($training['venue'] ?? ''); ?>"></td>
                                    <td><input type="text" name="training_date_from[]" value="<?php echo htmlspecialchars($training['inclusive_dates_past'] ?? ''); ?>"></td>
                                    <td><input type="text" name="training_date_to[]" value="<?php echo htmlspecialchars($training['inclusive_dates_present'] ?? ''); ?>"></td>
                                    <td><input type="text" name="certificate[]" value="<?php echo htmlspecialchars($training['certificate'] ?? ''); ?>"></td>
                                    <td><input type="text" name="no_of_hours[]" value="<?php echo htmlspecialchars($training['no_of_hours'] ?? ''); ?>"></td>
                                    <td><input type="text" name="training_base[]" value="<?php echo htmlspecialchars($training['training_base'] ?? ''); ?>"></td>
                                    <td><input type="text" name="category[]" value="<?php echo htmlspecialchars($training['category'] ?? ''); ?>"></td>
                                    <td><input type="text" name="conducted_by[]" value="<?php echo htmlspecialchars($training['conducted_by'] ?? ''); ?>"></td>
                                    <td><input type="text" name="proficiency[]" value="<?php echo htmlspecialchars($training['proficiency'] ?? ''); ?>"></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr class="training-entry">
                                <td><input type="text" name="training_title[]"></td>
                                <td><input type="text" name="training_venue[]"></td>
                                <td><input type="text" name="training_date_from[]"></td>
                                <td><input type="text" name="training_date_to[]"></td>
                                <td><input type="text" name="certificate[]"></td>
                                <td><input type="text" name="no_of_hours[]"></td>
                                <td><input type="text" name="training_base[]"></td>
                                <td><input type="text" name="category[]"></td>
                                <td><input type="text" name="conducted_by[]"></td>
                                <td><input type="text" name="proficiency[]"></td>
                            </tr>
                        <?php endif; ?>
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
                        <?php if (!empty($license_examination)): ?>
                            <?php foreach ($license_examination as $license): ?>
                                <tr class="license-entry">
                                    <td><input type="text" name="license_title[]" value="<?php echo htmlspecialchars($license['license_tittle'] ?? ''); ?>"></td>
                                    <td><input type="text" name="year_taken[]" value="<?php echo htmlspecialchars($license['year_taken'] ?? ''); ?>"></td>
                                    <td><input type="text" name="examination_venue[]" value="<?php echo htmlspecialchars($license['examination_venue'] ?? ''); ?>"></td>
                                    <td><input type="text" name="ratings[]" value="<?php echo htmlspecialchars($license['ratings'] ?? ''); ?>"></td>
                                    <td><input type="text" name="remarks[]" value="<?php echo htmlspecialchars($license['remarks'] ?? ''); ?>"></td>
                                    <td><input type="date" name="expiry_date[]" value="<?php echo htmlspecialchars($license['expiry_date'] ?? ''); ?>"></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr class="license-entry">
                                <td><input type="text" name="license_title[]"></td>
                                <td><input type="text" name="year_taken[]"></td>
                                <td><input type="text" name="examination_venue[]"></td>
                                <td><input type="text" name="ratings[]"></td>
                                <td><input type="text" name="remarks[]"></td>
                                <td><input type="date" name="expiry_date[]"></td>
                            </tr>
                        <?php endif; ?>
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
                        <?php if (!empty($competency_assessment)): ?>
                            <?php foreach ($competency_assessment as $competency): ?>
                                <tr class="competency-entry">
                                    <td><input type="text" name="industry_sector[]" value="<?php echo htmlspecialchars($competency['industry_sector'] ?? ''); ?>"></td>
                                    <td><input type="text" name="trade_area[]" value="<?php echo htmlspecialchars($competency['trade_area'] ?? ''); ?>"></td>
                                    <td><input type="text" name="occupation[]" value="<?php echo htmlspecialchars($competency['occupation'] ?? ''); ?>"></td>
                                    <td><input type="text" name="classification_level[]" value="<?php echo htmlspecialchars($competency['classification_level'] ?? ''); ?>"></td>
                                    <td><input type="text" name="competency[]" value="<?php echo htmlspecialchars($competency['competency'] ?? ''); ?>"></td>
                                    <td><input type="text" name="specialization[]" value="<?php echo htmlspecialchars($competency['specialization'] ?? ''); ?>"></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr class="competency-entry">
                                <td><input type="text" name="industry_sector[]"></td>
                                <td><input type="text" name="trade_area[]"></td>
                                <td><input type="text" name="occupation[]"></td>
                                <td><input type="text" name="classification_level[]"></td>
                                <td><input type="text" name="competency[]"></td>
                                <td><input type="text" name="specialization[]"></td>
                            </tr>
                        <?php endif; ?>
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
                                    <input type="text" name="spouse_name" value="<?php echo htmlspecialchars($family_background[0]['spouse_name'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Educational Attainment:</div>
                                <div class="value-value">
                                    <input type="text" name="spouse_educational_attainment" value="<?php echo htmlspecialchars($family_background[0]['spouse_educational_attainment'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Occupation: </div>
                                <div class="value-value">
                                    <input type="text" name="spouse_occupation" value="<?php echo htmlspecialchars($family_background[0]['spouse_occupation'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Ave. Monthly Income: </div>
                                <div class="value-value">
                                    <input type="text" name="spouse_monthly_income" value="<?php echo htmlspecialchars($family_background[0]['spouse_monthly_income'] ?? ''); ?>">
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
                                    <input type="text" name="father_name" value="<?php echo htmlspecialchars($family_background[0]['father_name'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Educational Attainment:</div>
                                <div class="value-value">
                                    <input type="text" name="father_educational_attainment" value="<?php echo htmlspecialchars($family_background[0]['father_educational_attainment'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Occupation: </div>
                                <div class="value-value">
                                    <input type="text" name="father_occupation" value="<?php echo htmlspecialchars($family_background[0]['father_occupation'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Ave. Monthly Income: </div>
                                <div class="value-value">
                                    <input type="text" name="father_monthly_income" value="<?php echo htmlspecialchars($family_background[0]['father_monthly_income'] ?? ''); ?>">
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
                                    <input type="text" name="mother_name" value="<?php echo htmlspecialchars($family_background[0]['mother_name'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Educational Attainment: </div>
                                <div class="value-value">
                                    <input type="text" name="mother_educational_attainment" value="<?php echo htmlspecialchars($family_background[0]['mother_educational_attainment'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Occupation: </div>
                                <div class="value-value">
                                    <input type="text" name="mother_occupation" value="<?php echo htmlspecialchars($family_background[0]['mother_occupation'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Ave. Monthly Income: </div>
                                <div class="value-value">
                                    <input type="text" name="mother_monthly_income" value="<?php echo htmlspecialchars($family_background[0]['mother_monthly_income'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <div class="form-row">
                        <div class="form-group">
                            <div class="form-personal">
                                <div class="label-personal">Guardian's Name:</div>
                                <div class="value-value">
                                    <input type="text" name="guardian_name" value="<?php echo htmlspecialchars($family_background[0]['guardian_name'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Educational Attainment:</div>
                                <div class="value-value">
                                    <input type="text" name="guardian_educational_attainment" value="<?php echo htmlspecialchars($family_background[0]['guardian_educational_attainment'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Occupation: </div>
                                <div class="value-value">
                                    <input type="text" name="guardian_occupation" value="<?php echo htmlspecialchars($family_background[0]['guardian_occupation'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-personal">
                                <div class="label-personal">Ave. Monthly Income: </div>
                                <div class="value-value">
                                    <input type="text" name="guardian_monthly_income" value="<?php echo htmlspecialchars($family_background[0]['guardian_monthly_income'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Dependents</th>
                            <th>Age</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" name="dependents" value="<?php echo htmlspecialchars($family_background[0]['dependents'] ?? ''); ?>"></td>
                            <td><input type="text" name="dependents_age" value="<?php echo htmlspecialchars($family_background[0]['dependents_age'] ?? ''); ?>"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div style="text-align: center; margin-top: 30px; margin-bottom: 20px;">
                <button type="submit" class="btn btn-primary">Update Profile</button>
                <a href="../../admin/admin_dashboard.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
        <?php else: ?>
            <div class="alert alert-warning">No user profile found to edit.</div>
        <?php endif; ?>
        
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
    </script>
</body>
</html>