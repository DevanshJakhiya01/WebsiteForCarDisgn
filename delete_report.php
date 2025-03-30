<?php
session_start();
require_once 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if report_id was provided
if (!isset($_POST['report_id']) || empty($_POST['report_id'])) {
    $_SESSION['error_message'] = "No report specified for deletion";
    header("Location: reports.php");
    exit();
}

$report_id = $_POST['report_id'];
$user_id = $_SESSION['user_id'];

try {
    // Check if report exists and belongs to user (or user has permission)
    $check_stmt = $conn->prepare("SELECT file_path FROM reports WHERE report_id = ? AND user_id = ?");
    $check_stmt->bind_param("ii", $report_id, $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['error_message'] = "Report not found or you don't have permission to delete it";
        header("Location: reports.php");
        exit();
    }
    
    $report = $result->fetch_assoc();
    
    // Delete the report file if it exists
    if (!empty($report['file_path']) && file_exists($report['file_path'])) {
        unlink($report['file_path']);
    }
    
    // Delete the report record
    $delete_stmt = $conn->prepare("DELETE FROM reports WHERE report_id = ?");
    $delete_stmt->bind_param("i", $report_id);
    $delete_stmt->execute();
    
    $_SESSION['success_message'] = "Report deleted successfully";
    header("Location: reports.php");
    exit();
    
} catch (Exception $e) {
    $_SESSION['error_message'] = "Error deleting report: " . $e->getMessage();
    header("Location: reports.php");
    exit();
}
?>