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
            'aaddress_district' => $_POST['address_district'] ?? '',
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
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-section { margin-bottom: 2rem; }
        .education-entry, .work-entry { border-bottom: 1px solid #eee; padding: 1rem 0; }
    </style>
</head>
<body>
<div class="container mt-5 mb-5">
    <h2 class="text-center mb-4">Manpower Profile Forms</h2>
    
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <!-- Personal Information Section -->
        <div class="form-section">
            <h4>Personal Information</h4>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>NMIS Code</label>
                    <input type="text" name="nmis_code" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Last Name</label>
                    <input type="text" name="lastname" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>First Name</label>
                    <input type="text" name="firstname" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Middle Name</label>
                    <input type="text" name="middlename" class="form-control">
                </div>
            </div>
        </div>

        <!-- Address Section -->
        <div class="form-section">
            <h4>Address Information</h4>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Street Address</label>
                    <input type="text" name="address_street" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Barangay</label>
                    <input type="text" name="address_barangay" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label>District</label>
                    <input type="text" name="address_district" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label>City/Municipality</label>
                    <input type="text" name="address_city" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Province</label>
                    <input type="text" name="address_province" class="form-control">
                </div>
                <div class="col-md-2 mb-3">
                    <label>Region</label>
                    <input type="text" name="address_region" class="form-control">
                </div>
                <div class="col-md-2 mb-3">
                    <label>ZIP Code</label>
                    <input type="text" name="address_zip" class="form-control">
                </div>
            </div>
        </div>

        <!-- Personal Details Section -->
        <div class="form-section">
            <h4>Personal Details</h4>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label>Sex</label>
                    <select name="sex" class="form-control" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Civil Status</label>
                    <select name="civil_status" class="form-control" required>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Widowed">Widowed</option>
                        <option value="Separated">Separated</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Contact Number</label>
                    <input type="text" name="contact_number" class="form-control">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
            </div>
        </div>

        <!-- Employment Section -->
        <div class="form-section">
            <h4>Employment Information</h4>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Employment Type</label>
                    <select name="employment_type" class="form-control">
                        <option value="Employed">Employed</option>
                        <option value="Self-employed">Self-employed</option>
                        <option value="Unemployed">Unemployed</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Employment Status</label>
                    <select name="employment_status" class="form-control">
                        <option value="Casual">Casual</option>
                        <option value="Contractual">Contractual</option>
                        <option value="Job-Order">Job Order</option>
                        <option value="Temporary">Temporary</option>
                        <option value="Probationary">Probationary</option>
                        <option value="Regular">Regular</option>
                        <option value="Permanent">Permanent</option>
                        <option value="Trainee">Trainee/Ojt</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Education Section -->
        <div class="form-section">
            <h4>Educational Background</h4>
            <div id="education-container">
                <div class="education-entry">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>School Name</label>
                            <input type="text" name="school[]" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Level</label>
                            <select name="educational_level[]" class="form-control">
                                <option value="Elementary">Elementary</option>
                                <option value="Secondary">Secondary</option>
                                <option value="Vocational">Vocational</option>
                                <option value="College">College</option>
                                <option value="Graduate">Graduate</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Year From</label>
                            <input type="text" name="year_from[]" class="form-control">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Year To</label>
                            <input type="text" name="year_to[]" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-secondary" onclick="addEducation()">Add More Education</button>
        </div>

        <!-- Work Experience Section -->
        <div class="form-section">
            <h4>Work Experience</h4>
            <div id="work-container">
                <div class="work-entry">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Company Name</label>
                            <input type="text" name="company[]" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Position</label>
                            <input type="text" name="position[]" class="form-control">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Date From</label>
                            <input type="date" name="work_date_from[]" class="form-control">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Date To</label>
                            <input type="date" name="work_date_to[]" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-secondary" onclick="addWork()">Add More Work Experience</button>
        </div>

        <!-- Additional Personal Information Section -->
        <div class="form-section">
            <h4>Additional Personal Information</h4>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label>Birthdate</label>
                    <input type="date" name="birthdate" class="form-control">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Birth Place</label>
                    <input type="text" name="birth_place" class="form-control">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Citizenship</label>
                    <input type="text" name="citizenship" class="form-control">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Religion</label>
                    <input type="text" name="religion" class="form-control">
                </div>
                <div class="col-md-2 mb-3">
                    <label>Height</label>
                    <input type="text" name="height" class="form-control">
                </div>
                <div class="col-md-2 mb-3">
                    <label>Weight</label>
                    <input type="text" name="weight" class="form-control">
                </div>
                <div class="col-md-2 mb-3">
                    <label>Blood Type</label>
                    <input type="text" name="blood_type" class="form-control">
                </div>
                <div class="col-md-2 mb-3">
                    <label>SSS No.</label>
                    <input type="text" name="sss_no" class="form-control">
                </div>
                <div class="col-md-2 mb-3">
                    <label>GSIS No.</label>
                    <input type="text" name="gsis_no" class="form-control">
                </div>
                <div class="col-md-2 mb-3">
                    <label>TIN No.</label>
                    <input type="text" name="tin_no" class="form-control">
                </div>
            </div>
        </div>

        

        <div class="form-section text-center">
            <button type="submit" class="btn btn-primary btn-lg">Submit Form</button>
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