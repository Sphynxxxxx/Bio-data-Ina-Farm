<?php
session_start();
require_once __DIR__ . '/../connections/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header('Location: ../admin.php');
    exit();
}

// Set default view type
$viewType = isset($_GET['view']) ? $_GET['view'] : 'all';
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$dateFilter = isset($_GET['date_filter']) ? $_GET['date_filter'] : 'all';

// Pagination
$recordsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// Create database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Count total registrations by type
    $stmt = $pdo->query("SELECT COUNT(*) AS count FROM users WHERE program_type = 'internship'");
    $totalInternships = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) AS count FROM users WHERE program_type = 'tesda'");
    $totalTesda = $stmt->fetchColumn();
    
    // Get recent registrations for the dashboard summary
    $stmt = $pdo->query("SELECT id, lastname, firstname, middlename, program_type, employment_status, contact_number, 
                         email, created_at, DATE_FORMAT(created_at, '%M %d, %Y') AS formatted_date 
                         FROM users ORDER BY created_at DESC LIMIT 5");
    $recentRegistrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Build the query based on filters
    $query = "SELECT id, nmis_code, lastname, firstname, middlename, program_type, employment_status, contact_number, 
              email, created_at, DATE_FORMAT(created_at, '%M %d, %Y') AS formatted_date 
              FROM users WHERE 1=1";
    
    $countQuery = "SELECT COUNT(*) FROM users WHERE 1=1";
    $params = [];
    
    // Add program type filter
    if ($viewType == 'internship') {
        $query .= " AND program_type = 'internship'";
        $countQuery .= " AND program_type = 'internship'";
    } elseif ($viewType == 'tesda') {
        $query .= " AND program_type = 'tesda'";
        $countQuery .= " AND program_type = 'tesda'";
    }
    
    // Add search filter
    if (!empty($searchTerm)) {
        $query .= " AND (lastname LIKE :search OR firstname LIKE :search OR email LIKE :search OR contact_number LIKE :search)";
        $countQuery .= " AND (lastname LIKE :search OR firstname LIKE :search OR email LIKE :search OR contact_number LIKE :search)";
        $params[':search'] = "%$searchTerm%";
    }
    
    // Add date filter
    if ($dateFilter != 'all') {
        if ($dateFilter == 'today') {
            $query .= " AND DATE(created_at) = CURDATE()";
            $countQuery .= " AND DATE(created_at) = CURDATE()";
        } elseif ($dateFilter == 'week') {
            $query .= " AND created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
            $countQuery .= " AND created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
        } elseif ($dateFilter == 'month') {
            $query .= " AND created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
            $countQuery .= " AND created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        }
    }
    
    // Add sorting and pagination
    $query .= " ORDER BY created_at DESC LIMIT :offset, :limit";
    
    // Prepare and execute count query
    $countStmt = $pdo->prepare($countQuery);
    foreach($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $totalRecords = $countStmt->fetchColumn();
    $totalPages = ceil($totalRecords / $recordsPerPage);
    
    // Prepare and execute main query
    $stmt = $pdo->prepare($query);
    foreach($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $recordsPerPage, PDO::PARAM_INT);
    $stmt->execute();
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    $message = "Database error: " . $e->getMessage();
}

// Handle deletion if requested
if (isset($_POST['delete_registration']) && isset($_POST['registration_id'])) {
    $registrationId = $_POST['registration_id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $registrationId);
        $stmt->execute();
        
        // Delete related records from other tables
        $tables = ['education', 'work_experience', 'training_seminar', 'license_examination', 
                   'competency_assessment', 'family_background', 'user_photos', 'user_signatures'];
        
        foreach ($tables as $table) {
            $stmt = $pdo->prepare("DELETE FROM $table WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $registrationId);
            $stmt->execute();
        }
        
        $message = "Registration deleted successfully!";
        
        // Redirect to refresh the page and avoid resubmission
        header("Location: admin_dashboard.php?deleted=true");
        exit();
    } catch(PDOException $e) {
        $message = "Error deleting registration: " . $e->getMessage();
    }
}

// Get admin name
$adminName = $_SESSION['admin_name'] ?? 'Administrator';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ina Farmers Bio-Data Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container-fluid {
            flex: 1; 
        }
        
        .header {
            padding: 10px 0;
            background: linear-gradient(90deg, pink, lightgreen, skyblue);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .logo-container {
            display: flex;
            align-items: center;
        }
        
        .logo {
            width: 120px;
            height: auto;
            margin-right: 20px;
            filter: drop-shadow(1px 1px 0 white);
        }
        
        .title-container h1 {
            color: rgb(255, 255, 255);
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0;
        }
        
        .subtitle {
            color: rgb(0, 0, 0);
            font-size: 1rem;
        }
        
        .dashboard-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            height: 100%;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        
        .card-stats {
            background: linear-gradient(135deg, #dc3545, #f8765f);
            color: white;
        }
        
        .table-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .btn-primary {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        
        .btn-primary:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        
        .sidebar {
            background-color: #343a40;
            color: #fff;
            min-height: calc(100vh - 76px - 100px);
            padding-top: 20px;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 10px 20px;
            margin-bottom: 5px;
            border-radius: 5px;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        
        .footer {
            background-color: #343a40;
            color: #fff;
            padding-top: 10px;
            height: 100px;
            margin-top: auto; 
            width: 100%;
        }

        .footer p {
            margin: 0px;
            font-size: 0.85rem;
        }
        
        .developer-credit {
            font-size: 0.75rem;
            color: #adb5bd;
        }
        
        .admin-info {
            color: #fff;
            background-color: rgba(0, 0, 0, 0.2);
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .pagination .page-item.active .page-link {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        
        .pagination .page-link {
            color: #dc3545;
        }
        
        .search-bar {
            margin-bottom: 20px;
        }
        
        .badge-internship {
            background-color: #28a745;
        }
        
        .badge-tesda {
            background-color: #007bff;
        }
        
        .dashboard-stats {
            margin-bottom: 30px;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
        }
        
        .stat-label {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.8);
        }
        
        .program-nav {
            margin-bottom: 20px;
        }
        
        .program-nav .nav-link {
            color: #6c757d;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 30px;
            margin-right: 10px;
        }
        
        .program-nav .nav-link.active {
            background-color: #dc3545;
            color: white;
        }
        
        .program-nav .nav-link:hover:not(.active) {
            background-color: rgba(220, 53, 69, 0.1);
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="logo-container">
                <img src="../Images/ina farm logo.png" alt="INAFARM Logo" class="logo">
                <div class="title-container">
                    <h1>Ina Farmers Learning Site & Agri-Farm Inc.</h1>
                    <p class="subtitle">ADMIN DASHBOARD - Bio-Data Management System</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="admin-info text-center">
                    <i class="bi bi-person-circle" style="font-size: 2rem;"></i>
                    <h5 class="mt-2 mb-0"><?php echo htmlspecialchars($adminName); ?></h5>
                    <p class="small">Administrator</p>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="admin_dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php?view=internship">
                            <i class="bi bi-mortarboard"></i> Internship Registrations
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php?view=tesda">
                            <i class="bi bi-person-vcard"></i> TESDA Registrations
                        </a>
                    </li>
                    <li class="nav-item mt-5">
                        <a class="nav-link text-danger" href="crud/admin_logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <?php if (!empty($message)): ?>
                <div class="alert <?php echo (strpos($message, 'successfully') !== false) ? 'alert-success' : 'alert-danger'; ?> alert-dismissible fade show mt-3" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <!--<a href="export_data.php?view=<?php echo $viewType; ?>&search=<?php echo urlencode($searchTerm); ?>&date_filter=<?php echo $dateFilter; ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-download"></i> Export CSV
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print();">
                                <i class="bi bi-printer"></i> Print
                            </button>-->
                        </div>
                    </div>
                </div>

                <!-- Dashboard stats -->
                <div class="row dashboard-stats">
                    <div class="col-md-4">
                        <div class="dashboard-card card-stats p-3 text-center">
                            <div class="stat-value"><?php echo $totalInternships + $totalTesda; ?></div>
                            <div class="stat-label">Total Registrations</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dashboard-card card-stats p-3 text-center">
                            <div class="stat-value"><?php echo $totalInternships; ?></div>
                            <div class="stat-label">Internship Registrations</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dashboard-card card-stats p-3 text-center">
                            <div class="stat-value"><?php echo $totalTesda; ?></div>
                            <div class="stat-label">TESDA Registrations</div>
                        </div>
                    </div>
                </div>

                <!-- Program type navigation -->
                <div class="program-nav">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($viewType == 'all') ? 'active' : ''; ?>" href="admin_dashboard.php?view=all">All Registrations</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($viewType == 'internship') ? 'active' : ''; ?>" href="admin_dashboard.php?view=internship">Student Internship</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($viewType == 'tesda') ? 'active' : ''; ?>" href="admin_dashboard.php?view=tesda">TESDA Programs</a>
                        </li>
                    </ul>
                </div>

                <!-- Search and filters -->
                <div class="row search-bar">
                    <div class="col-12">
                        <form action="admin_dashboard.php" method="get" class="row g-3">
                            <input type="hidden" name="view" value="<?php echo $viewType; ?>">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search by name, email or contact..." name="search" value="<?php echo htmlspecialchars($searchTerm); ?>">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" name="date_filter" onchange="this.form.submit()">
                                    <option value="all" <?php echo ($dateFilter == 'all') ? 'selected' : ''; ?>>All Time</option>
                                    <option value="today" <?php echo ($dateFilter == 'today') ? 'selected' : ''; ?>>Today</option>
                                    <option value="week" <?php echo ($dateFilter == 'week') ? 'selected' : ''; ?>>This Week</option>
                                    <option value="month" <?php echo ($dateFilter == 'month') ? 'selected' : ''; ?>>This Month</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Registration table -->
                <div class="table-container">
                    <h3 class="mb-3">
                        <?php
                        if ($viewType == 'internship') {
                            echo 'Student Internship Registrations';
                        } elseif ($viewType == 'tesda') {
                            echo 'TESDA Program Registrations';
                        } else {
                            echo 'All Registrations';
                        }
                        ?>
                    </h3>
                    <?php if (empty($registrations)): ?>
                        <div class="alert alert-info">No registrations found.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <?php if ($viewType == 'tesda' || $viewType == 'all'): ?>
                                        <th>NMIS Code</th>
                                        <?php endif; ?>
                                        <th>Full Name</th>
                                        <th>Program Type</th>
                                        <th>Contact</th>
                                        <th>Email</th>
                                        <th>Registration Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($registrations as $reg): ?>
                                    <tr>
                                        <td><?php echo $reg['id']; ?></td>
                                        <?php if ($viewType == 'tesda' || $viewType == 'all'): ?>
                                        <td><?php echo htmlspecialchars($reg['nmis_code'] ?: 'N/A'); ?></td>
                                        <?php endif; ?>
                                        <td><?php echo htmlspecialchars($reg['lastname'] . ', ' . $reg['firstname'] . ' ' . $reg['middlename']); ?></td>
                                        <td>
                                            <?php if ($reg['program_type'] == 'internship'): ?>
                                                <span class="badge bg-success">Internship</span>
                                            <?php else: ?>
                                                <span class="badge bg-primary">TESDA</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($reg['contact_number'] ?: 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($reg['email'] ?: 'N/A'); ?></td>
                                        <td><?php echo $reg['formatted_date']; ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <?php if ($reg['program_type'] == 'internship'): ?>
                                                <a href="internship_view.php?id=<?php echo $reg['id']; ?>" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                                <?php else: ?>
                                                <a href="view.php?id=<?php echo $reg['id']; ?>" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $reg['id']; ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                            
                                            <!-- Delete Confirmation Modal -->
                                            <div class="modal fade" id="deleteModal<?php echo $reg['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $reg['id']; ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteModalLabel<?php echo $reg['id']; ?>">Confirm Deletion</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Are you sure you want to delete the registration for <?php echo htmlspecialchars($reg['firstname'] . ' ' . $reg['lastname']); ?>?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <form action="admin_dashboard.php" method="post">
                                                                <input type="hidden" name="registration_id" value="<?php echo $reg['id']; ?>">
                                                                <button type="submit" name="delete_registration" class="btn btn-danger">Delete</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Recent registrations card -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="dashboard-card p-3">
                            <h3>Recent Registrations</h3>
                            <div class="list-group">
                                <?php if (empty($recentRegistrations)): ?>
                                    <div class="alert alert-info">No recent registrations found.</div>
                                <?php else: ?>
                                    <?php foreach ($recentRegistrations as $recent): ?>
                                    <a href="<?php echo ($recent['program_type'] == 'internship') ? 'internship_view.php?id=' : 'view.php?id='; ?><?php echo $recent['id']; ?>" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1"><?php echo htmlspecialchars($recent['lastname'] . ', ' . $recent['firstname'] . ' ' . $recent['middlename']); ?></h5>
                                            <small><?php echo date('M d, Y', strtotime($recent['created_at'])); ?></small>
                                        </div>
                                        <p class="mb-1"><?php echo htmlspecialchars($recent['email'] ?: 'No email provided'); ?></p>
                                        <small>
                                            <?php if ($recent['program_type'] == 'internship'): ?>
                                                <span class="badge bg-success">Student Internship</span>
                                            <?php else: ?>
                                                <span class="badge bg-primary">TESDA Program</span>
                                            <?php endif; ?>
                                        </small>
                                    </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; 2024 Ina Farmers Learning Site & Agri-Farm Inc.</p> 
                </div>
                <div class="col-md-6 text-md-end">
                    <p>All Rights Reserved</p>
                    <p class="developer-credit">Developed by:<br>Larry Denver Biaco<br>Vince Javier</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-dismiss success alerts after 5 seconds
            const successAlert = document.querySelector('.alert-success');
            if (successAlert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(successAlert);
                    bsAlert.close();
                }, 5000);
            }
        });
    </script>
</body>
</html>