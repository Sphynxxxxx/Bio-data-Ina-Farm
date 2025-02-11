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
                <?php foreach ($records as $record): ?>
                <tr>
                    <td><?php echo htmlspecialchars($record['nmis_code']); ?></td>
                    <td><?php echo htmlspecialchars($record['lastname'] . ', ' . $record['firstname']); ?></td>
                    <td><?php echo htmlspecialchars($record['contact_number']); ?></td>
                    <td><?php echo htmlspecialchars($record['employment_status']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($record['created_at'])); ?></td>
                    <td>
                        <a href="view.php?id=<?php echo $record['id']; ?>" class="btn btn-sm btn-primary">View</a>
                        <a href="edit.php?id=<?php echo $record['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete.php?id=<?php echo $record['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>