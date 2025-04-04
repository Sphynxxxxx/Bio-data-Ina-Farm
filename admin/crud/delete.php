<?php
session_start();
require_once __DIR__ . '/../../connections/config.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin'])) {
    header('Location: ../admin.php');
    exit();
}

// Check if ID is provided
if (!isset($_POST['id']) || empty($_POST['id'])) {
    $_SESSION['error'] = "No record ID specified for deletion.";
    header('Location: ../admin_dashboard.php');
    exit();
}

$userId = intval($_POST['id']);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Disable foreign key checks
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0;');
    
    // Begin transaction
    $pdo->beginTransaction();
    
    // List of tables to delete from (in order)
    $tables = [
        'user_signatures',
        'user_photos',
        'family_background',
        'competency_assessment',
        'license_examination',
        'training_seminar',
        'work_experience',
        'education'
    ];
    
    // Delete records from each table
    foreach ($tables as $table) {
        $stmt = $pdo->prepare("DELETE FROM `$table` WHERE user_id = ?");
        $stmt->execute([$userId]);
    }
    
    // Delete the user record
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    
    // Commit the transaction
    $pdo->commit();
    
    // Re-enable foreign key checks
    $pdo->exec('SET FOREIGN_KEY_CHECKS=1;');
    
    $_SESSION['success'] = "Record deleted successfully.";
    header('Location: ../admin_dashboard.php');
    exit();
    
} catch (PDOException $e) {
    // Rollback the transaction on error
    if (isset($pdo)) {
        $pdo->rollBack();
        
        // Re-enable foreign key checks
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1;');
    }
    
    $_SESSION['error'] = "Error deleting record: " . $e->getMessage();
    header('Location: ../admin_dashboard.php');
    exit();
}
?>