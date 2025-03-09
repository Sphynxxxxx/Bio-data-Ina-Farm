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
    
    // Begin transaction
    $pdo->beginTransaction();
    
    // Delete all related records first
    
    // Delete education records
    $pdo->prepare("DELETE FROM education WHERE user_id = ?")->execute([$userId]);
    
    // Delete work experience records
    $pdo->prepare("DELETE FROM work_experience WHERE user_id = ?")->execute([$userId]);
    
    // Delete training seminar records
    $pdo->prepare("DELETE FROM training_seminar WHERE user_id = ?")->execute([$userId]);
    
    // Delete license examination records
    $pdo->prepare("DELETE FROM license_examination WHERE user_id = ?")->execute([$userId]);
    
    // Delete competency assessment records
    $pdo->prepare("DELETE FROM competency_assessment WHERE user_id = ?")->execute([$userId]);
    
    // Delete family background records
    $pdo->prepare("DELETE FROM family_background WHERE user_id = ?")->execute([$userId]);
    
    // Delete photo records
    $pdo->prepare("DELETE FROM user_photos WHERE user_id = ?")->execute([$userId]);

    //Delete signature records
    $pdo->prepare("DELETE FROM user_signatures WHERE user_id = ?")->execute([$userId]);
    
    // Finally, delete the user record
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    
    // Commit the transaction
    $pdo->commit();
    
    $_SESSION['success'] = "Record deleted successfully.";
    header('Location: ../admin_dashboard.php');
    exit();
    
} catch (PDOException $e) {
    // Rollback the transaction on error
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    
    $_SESSION['error'] = "Error deleting record: " . $e->getMessage();
    header('Location: ../admin_dashboard.php');
    exit();
}
?>