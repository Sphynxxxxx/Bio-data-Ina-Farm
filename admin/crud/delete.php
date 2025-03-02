<?php
session_start();
require_once __DIR__ . '/../../connections/config.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin'])) {
    header('Location: admin.php');
    exit();
}

// Make sure we're processing a POST request with an ID
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    
    try {
        // Prepare and execute the delete statement
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        // Set success message in session
        $_SESSION['message'] = "Record successfully deleted.";
        $_SESSION['message_type'] = "success";
    } catch (PDOException $e) {
        // Set error message in session
        $_SESSION['message'] = "Error deleting record: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
    
    // Redirect back to the dashboard
    header('Location: ../admin_dashboard.php');
    exit();
} else {
    // Invalid request, redirect back to dashboard
    $_SESSION['message'] = "Invalid request.";
    $_SESSION['message_type'] = "warning";
    header('Location: ../admin_dashboard.php');
    exit();
}
?>