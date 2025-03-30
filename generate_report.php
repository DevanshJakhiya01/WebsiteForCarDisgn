<?php
session_start();
require_once 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    $required_fields = ['report_name', 'report_type', 'report_period'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['error_message'] = "Please fill in all required fields";
            header("Location: reports.php");
            exit();
        }
    }

    // Get form data
    $report_name = $_POST['report_name'];
    $report_type = $_POST['report_type'];
    $report_period = $_POST['report_period'];
    $user_id = $_SESSION['user_id'];
    $status = 'pending'; // Reports start as pending
    
    // Determine date range based on period
    $date_from = date('Y-m-d');
    $date_to = date('Y-m-d');
    
    switch ($report_period) {
        case 'week':
            $date_from = date('Y-m-d', strtotime('monday this week'));
            break;
        case 'month':
            $date_from = date('Y-m-01');
            break;
        case 'quarter':
            $month = date('n');
            $quarter = ceil($month / 3);
            $date_from = date('Y-m-d', mktime(0, 0, 0, ($quarter * 3) - 2, 1, date('Y')));
            break;
        case 'year':
            $date_from = date('Y-01-01');
            break;
        case 'custom':
            if (!empty($_POST['custom_from']) && !empty($_POST['custom_to'])) {
                $date_from = $_POST['custom_from'];
                $date_to = $_POST['custom_to'];
            }
            break;
    }

    try {
        // Insert report into database
        $stmt = $conn->prepare("INSERT INTO reports 
                              (report_name, report_type, user_id, status, date_from, date_to) 
                              VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisss", $report_name, $report_type, $user_id, $status, $date_from, $date_to);
        $stmt->execute();
        
        $report_id = $conn->insert_id;
        
        // In a real application, you would:
        // 1. Queue the report generation (using cron jobs, queues, or background processes)
        // 2. Process the report data
        // 3. Save the report file
        // 4. Update the report status to 'completed'
        
        // For demo purposes, we'll simulate this with a random delay
        $delay = rand(2, 10);
        sleep($delay);
        
        // Update status to completed and set file path
        $file_path = "reports/report_$report_id.pdf"; // In real app, generate actual file
        $update_stmt = $conn->prepare("UPDATE reports SET status = 'completed', file_path = ? WHERE report_id = ?");
        $update_stmt->bind_param("si", $file_path, $report_id);
        $update_stmt->execute();
        
        $_SESSION['success_message'] = "Report generated successfully!";
        header("Location: reports.php");
        exit();
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error generating report: " . $e->getMessage();
        header("Location: reports.php");
        exit();
    }
} else {
    // Not a POST request
    header("Location: reports.php");
    exit();
}
?>