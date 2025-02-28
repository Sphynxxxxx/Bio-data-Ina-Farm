<?php
// Database connection
$host = 'localhost';
$dbname = 'biodata_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
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
            'sex' => $_POST['sex'] ?? '',
            'civil_status' => $_POST['civil_status'] ?? '',
            'contact_number' => $_POST['contact_number'] ?? '',
            'email' => $_POST['email'] ?? '',
            'employment_type' => $_POST['employment_type'] ?? '',
            'employment_status' => $_POST['employment_status'] ?? '',
            'birthdate' => $_POST['birthdate'] ?? '',
            'birth_place' => $_POST['birth_place'] ?? '',
            'citizenship' => $_POST['citizenship'] ?? '',
            'religion' => $_POST['religion'] ?? '',
            'height' => $_POST['height'] ?? '',
            'weight' => $_POST['weight'] ?? '',
            'blood_type' => $_POST['blood_type'] ?? '',
            'sss_no' => $_POST['sss_no'] ?? '',
            'gsis_no' => $_POST['gsis_no'] ?? '',
            'tin_no' => $_POST['tin_no'] ?? ''
        ];
        
        // Insert into users table
        $sql = "INSERT INTO users (nmis_code, lastname, firstname, middlename, address_street, 
                address_barangay, address_district, address_city, address_province, address_region, address_zip, 
                sex, civil_status, contact_number, email, employment_type, employment_status, 
                birthdate, birth_place, citizenship, religion, height, weight, blood_type, 
                sss_no, gsis_no, tin_no) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($userData));
        
        $userId = $pdo->lastInsertId();
        
        // Handle education records
        if (isset($_POST['school']) && is_array($_POST['school'])) {
            $eduSql = "INSERT INTO education (user_id, school_name, educational_level, 
                      year_from, year_to, degree, major, minor, units_earned, honors) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $eduStmt = $pdo->prepare($eduSql);
            
            foreach ($_POST['school'] as $key => $school) {
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
        
        // Handle work experience records
        if (isset($_POST['company']) && is_array($_POST['company'])) {
            $workSql = "INSERT INTO work_experience (user_id, company_name, position, 
                       date_from, date_to) 
                       VALUES (?, ?, ?, ?, ?)";
            
            $workStmt = $pdo->prepare($workSql);
            
            foreach ($_POST['company'] as $key => $company) {
                $workData = [
                    $userId,
                    $company,
                    $_POST['position'][$key] ?? '',
                    $_POST['work_date_from'][$key] ?? '',
                    $_POST['work_date_to'][$key] ?? ''
                ];
                $workStmt->execute($workData);
            }
        }
        
        // Commit transaction
        $pdo->commit();
        $success_message = "Biodata submitted successfully!";
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manpower Profile Form</title>
    <link rel="stylesheet" href="admin/css/index.css">
</head>
<body>
<div class="container">
    <!-- Header Section -->
    <div class="header">
        <img src="https://via.placeholder.com/50" alt="Logo">
        <div class="header-text">
            <h2>REPUBLIC OF THE PHILIPPINES</h2>
            <p>NATIONAL MEAT INSPECTION SERVICE</p>
            <p>DEPARTMENT OF AGRICULTURE</p>
        </div>
    </div>
    
    <!-- Manpower Profile Title -->
    <div class="manpower-profile">MANPOWER PROFILE</div>
    
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <!-- Profile Header with Photo Box -->
        <div class="profile-header">
            <div>
                <div class="form-title">NMIS Form No. 001</div>
            </div>
            <div class="photo-box">
                <span>2x2 ID Photo</span>
            </div>
        </div>
        
        <!-- Personal Information Section -->
        <div class="section">
            <div class="section-title">I. PERSONAL INFORMATION</div>
            
            <!-- Name Row -->
            <div class="form-row">
                <div class="form-group">
                    <label class="label">NMIS CODE</label>
                    <input type="text" name="nmis_code" class="value" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="label">LAST NAME</label>
                    <input type="text" name="lastname" class="value" required>
                </div>
                <div class="form-group">
                    <label class="label">FIRST NAME</label>
                    <input type="text" name="firstname" class="value" required>
                </div>
                <div class="form-group">
                    <label class="label">MIDDLE NAME</label>
                    <input type="text" name="middlename" class="value">
                </div>
            </div>
            
            <!-- Address Section -->
            <div class="form-row">
                <div class="form-group">
                    <label class="label">STREET ADDRESS</label>
                    <input type="text" name="address_street" class="value">
                </div>
                <div class="form-group">
                    <label class="label">BARANGAY</label>
                    <input type="text" name="address_barangay" class="value">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="label">DISTRICT</label>
                    <input type="text" name="address_district" class="value">
                </div>
                <div class="form-group">
                    <label class="label">CITY/MUNICIPALITY</label>
                    <input type="text" name="address_city" class="value">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="label">PROVINCE</label>
                    <input type="text" name="address_province" class="value">
                </div>
                <div class="form-group">
                    <label class="label">REGION</label>
                    <input type="text" name="address_region" class="value">
                </div>
                <div class="form-group">
                    <label class="label">ZIP CODE</label>
                    <input type="text" name="address_zip" class="value">
                </div>
            </div>
            
            <!-- Personal Details Section -->
            <div class="form-row">
                <div class="form-group">
                    <label class="label">SEX</label>
                    <select name="sex" class="value" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="label">CIVIL STATUS</label>
                    <select name="civil_status" class="value" required>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Widowed">Widowed</option>
                        <option value="Separated">Separated</option>
                    </select>
                </div>
            </div>
            
            <div class="form-contact">
                <label class="label-contact">CONTACT NUMBER:</label>
                <div class="value-value">
                    <input type="text" name="contact_number" style="border:none; width:100%; outline:none;">
                </div>
            </div>
            
            <div class="form-contact">
                <label class="label-contact">EMAIL ADDRESS:</label>
                <div class="value-value">
                    <input type="email" name="email" style="border:none; width:100%; outline:none;">
                </div>
            </div>
        </div>
        
        <!-- Employment Section -->
        <div class="section">
            <div class="section-title">II. EMPLOYMENT INFORMATION</div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="label">EMPLOYMENT TYPE</label>
                    <select name="employment_type" class="value">
                        <option value="Employed">Employed</option>
                        <option value="Self-employed">Self-employed</option>
                        <option value="Unemployed">Unemployed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="label">EMPLOYMENT STATUS</label>
                    <select name="employment_status" class="value">
                        <option value="Casual">Casual</option>
                        <option value="Contractual">Contractual</option>
                        <option value="Job-Order">Job Order</option>
                        <option value="Temporary">Temporary</option>
                        <option value="Probationary">Probationary</option>
                        <option value="Regular">Regular</option>
                        <option value="Permanent">Permanent</option>
                        <option value="Trainee">Trainee/OJT</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Education Section -->
        <div class="section">
            <div class="section-title">III. EDUCATIONAL BACKGROUND</div>
            
            <table>
                <thead>
                    <tr>
                        <th>SCHOOL NAME</th>
                        <th>LEVEL</th>
                        <th>YEAR FROM</th>
                        <th>YEAR TO</th>
                        <th>DEGREE/UNITS</th>
                    </tr>
                </thead>
                <tbody id="education-container">
                    <tr class="education-entry">
                        <td><input type="text" name="school[]" class="value"></td>
                        <td>
                            <select name="educational_level[]" class="value">
                                <option value="Elementary">Elementary</option>
                                <option value="Secondary">Secondary</option>
                                <option value="Vocational">Vocational</option>
                                <option value="College">College</option>
                                <option value="Graduate">Graduate</option>
                            </select>
                        </td>
                        <td><input type="text" name="year_from[]" class="value"></td>
                        <td><input type="text" name="year_to[]" class="value"></td>
                        <td><input type="text" name="degree[]" class="value"></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-secondary" onclick="addEducation()">Add More Education</button>
        </div>
        
        <!-- Work Experience Section -->
        <div class="section">
            <div class="section-title">IV. WORK EXPERIENCE</div>
            
            <table>
                <thead>
                    <tr>
                        <th>COMPANY NAME</th>
                        <th>POSITION</th>
                        <th>DATE FROM</th>
                        <th>DATE TO</th>
                    </tr>
                </thead>
                <tbody id="work-container">
                    <tr class="work-entry">
                        <td><input type="text" name="company[]" class="value"></td>
                        <td><input type="text" name="position[]" class="value"></td>
                        <td><input type="date" name="work_date_from[]" class="value"></td>
                        <td><input type="date" name="work_date_to[]" class="value"></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-secondary" onclick="addWork()">Add More Work Experience</button>
        </div>
        
        <!-- Additional Personal Information Section -->
        <div class="section">
            <div class="section-title">V. ADDITIONAL PERSONAL INFORMATION</div>
            
            <div class="form-personal">
                <label class="label-personal">BIRTHDATE:</label>
                <div class="value-value">
                    <input type="date" name="birthdate" style="border:none; width:100%; outline:none;">
                </div>
            </div>
            
            <div class="form-personal">
                <label class="label-personal">BIRTH PLACE:</label>
                <div class="value-value">
                    <input type="text" name="birth_place" style="border:none; width:100%; outline:none;">
                </div>
            </div>
            
            <div class="form-personal">
                <label class="label-personal">CITIZENSHIP:</label>
                <div class="value-value">
                    <input type="text" name="citizenship" style="border:none; width:100%; outline:none;">
                </div>
            </div>
            
            <div class="form-personal">
                <label class="label-personal">RELIGION:</label>
                <div class="value-value">
                    <input type="text" name="religion" style="border:none; width:100%; outline:none;">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="label">HEIGHT (cm)</label>
                    <input type="text" name="height" class="value">
                </div>
                <div class="form-group">
                    <label class="label">WEIGHT (kg)</label>
                    <input type="text" name="weight" class="value">
                </div>
                <div class="form-group">
                    <label class="label">BLOOD TYPE</label>
                    <input type="text" name="blood_type" class="value">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="label">SSS NO.</label>
                    <input type="text" name="sss_no" class="value">
                </div>
                <div class="form-group">
                    <label class="label">GSIS NO.</label>
                    <input type="text" name="gsis_no" class="value">
                </div>
                <div class="form-group">
                    <label class="label">TIN NO.</label>
                    <input type="text" name="tin_no" class="value">
                </div>
            </div>
        </div>
        
        <!-- Signature Section -->
        <div class="signature-container">
            <div class="signature-title">
                SIGNATURE OF EMPLOYEE
            </div>
            <div class="signature-box">
                Affix your signature inside this box
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 30px; margin-bottom: 20px;">
            <button type="submit" class="btn btn-primary">Submit Manpower Profile</button>
        </div>
    </form>
</div>

<script>
function addEducation() {
    const container = document.getElementById('education-container');
    const newEntry = container.children[0].cloneNode(true);
    // Clear input values
    newEntry.querySelectorAll('input').forEach(input => input.value = '');
    container.appendChild(newEntry);
}

function addWork() {
    const container = document.getElementById('work-container');
    const newEntry = container.children[0].cloneNode(true);
    // Clear input values
    newEntry.querySelectorAll('input').forEach(input => input.value = '');
    container.appendChild(newEntry);
}
</script>

</body>
</html>