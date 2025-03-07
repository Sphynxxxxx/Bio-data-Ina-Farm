<?php
session_start();
require_once __DIR__ . '/../connections/config.php';

if (!isset($_SESSION['admin'])) {
    header('Location: admin.php');
    exit();
}

// Fetch all biodata records
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Biodata Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Biodata Management Dashboard</h2>
    
    <!-- Display success/error messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
                echo $_SESSION['success']; 
                unset($_SESSION['success']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>NMIS Code</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Employment Status</th>
                    <th>Submission Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                    <tr>
                        <td colspan="6" class="text-center">No records found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($records as $record): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['nmis_code'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars(($record['lastname'] ?? '') . ', ' . ($record['firstname'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($record['contact_number'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($record['employment_status'] ?? 'N/A'); ?></td>
                        <td><?php echo isset($record['created_at']) ? date('M d, Y', strtotime($record['created_at'])) : 'N/A'; ?></td>
                        <td>
                            <a href="view.php?id=<?php echo $record['id']; ?>" class="btn btn-sm btn-primary">View</a>
                            <a href="crud/admin_edit.php?id=<?php echo $record['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <form method="POST" action="crud/delete.php" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $record['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this record? This action cannot be undone.')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>