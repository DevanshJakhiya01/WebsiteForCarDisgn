<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "car_customization";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get parameters
$type = isset($_GET['type']) ? $_GET['type'] : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// Validate report type
if (!in_array($type, ['sales', 'products', 'customers'])) {
    die("Invalid report type");
}

// Set appropriate headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $type . '_report_' . date('Y-m-d') . '.csv');

// Create output file pointer
$output = fopen('php://output', 'w');

switch ($type) {
    case 'sales':
        // Sales report data
        $result = $conn->query("
            SELECT 
                DATE(created_at) as order_date,
                COUNT(*) as total_orders,
                SUM(total_amount) as total_sales,
                SUM(total_amount)/COUNT(*) as avg_order_value
            FROM orders
            WHERE created_at BETWEEN '$start_date' AND '$end_date 23:59:59'
            GROUP BY DATE(created_at)
            ORDER BY order_date
        ");
        
        // Write CSV header
        fputcsv($output, ['Date', 'Orders', 'Total Sales', 'Average Order Value']);
        
        // Write data rows
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                date('M j, Y', strtotime($row['order_date'])),
                $row['total_orders'],
                '$' . number_format($row['total_sales'], 2),
                '$' . number_format($row['avg_order_value'], 2)
            ]);
        }
        
        // Add summary row
        $summary = $conn->query("
            SELECT 
                COUNT(*) as total_orders,
                SUM(total_amount) as total_sales,
                SUM(total_amount)/COUNT(*) as avg_order_value
            FROM orders
            WHERE created_at BETWEEN '$start_date' AND '$end_date 23:59:59'
        ")->fetch_assoc();
        
        fputcsv($output, ['']);
        fputcsv($output, ['TOTAL', $summary['total_orders'], '$' . number_format($summary['total_sales'], '$' . number_format($summary['avg_order_value'])]);
        break;
        
    case 'products':
        // Product sales data
        $result = $conn->query("
            SELECT 
                p.name as product_name,
                COUNT(*) as total_orders,
                SUM(o.total_amount) as total_sales
            FROM orders o
            JOIN products p ON o.product_id = p.id
            WHERE o.created_at BETWEEN '$start_date' AND '$end_date 23:59:59'
            GROUP BY p.name
            ORDER BY total_sales DESC
            LIMIT 10
        ");
        
        // Write CSV header
        fputcsv($output, ['Product Name', 'Orders', 'Total Sales']);
        
        // Write data rows
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $row['product_name'],
                $row['total_orders'],
                '$' . number_format($row['total_sales'], 2)
            ]);
        }
        
        // Add summary row
        $summary = $conn->query("
            SELECT 
                COUNT(*) as total_orders,
                SUM(o.total_amount) as total_sales
            FROM orders o
            JOIN products p ON o.product_id = p.id
            WHERE o.created_at BETWEEN '$start_date' AND '$end_date 23:59:59'
        ")->fetch_assoc();
        
        fputcsv($output, ['']);
        fputcsv($output, ['TOTAL', $summary['total_orders'], '$' . number_format($summary['total_sales'])]);
        break;
        
    case 'customers':
        // Customer orders data
        $result = $conn->query("
            SELECT 
                u.username,
                COUNT(*) as total_orders,
                SUM(o.total_amount) as total_spent
            FROM orders o
            JOIN users u ON o.user_id = u.id
            WHERE o.created_at BETWEEN '$start_date' AND '$end_date 23:59:59'
            GROUP BY u.username
            ORDER BY total_spent DESC
            LIMIT 10
        ");
        
        // Write CSV header
        fputcsv($output, ['Customer', 'Orders', 'Total Spent']);
        
        // Write data rows
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $row['username'],
                $row['total_orders'],
                '$' . number_format($row['total_spent'], 2)
            ]);
        }
        
        // Add summary row
        $summary = $conn->query("
            SELECT 
                COUNT(*) as total_orders,
                SUM(o.total_amount) as total_spent
            FROM orders o
            JOIN users u ON o.user_id = u.id
            WHERE o.created_at BETWEEN '$start_date' AND '$end_date 23:59:59'
        ")->fetch_assoc();
        
        fputcsv($output, ['']);
        fputcsv($output, ['TOTAL', $summary['total_orders'], '$' . number_format($summary['total_spent'])]);
        break;
}

$conn->close();
exit();
?>