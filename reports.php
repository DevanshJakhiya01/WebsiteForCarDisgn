<?php
// Start the session
session_start();

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
require_once 'db_connection.php';

// Fetch admin profile data
$admin_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email, profile_image FROM users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Fetch reports data (this is just an example - adjust according to your actual reports)
$reports = [];
$report_stmt = $conn->query("
    SELECT 
        report_id, 
        report_name, 
        report_type, 
        generated_date, 
        status 
    FROM reports 
    ORDER BY generated_date DESC 
    LIMIT 10
");
if ($report_stmt) {
    $reports = $report_stmt->fetch_all(MYSQLI_ASSOC);
}

// Get counts for stats cards
$total_reports = $conn->query("SELECT COUNT(*) as count FROM reports")->fetch_assoc()['count'];
$pending_reports = $conn->query("SELECT COUNT(*) as count FROM reports WHERE status = 'pending'")->fetch_assoc()['count'];
$completed_reports = $conn->query("SELECT COUNT(*) as count FROM reports WHERE status = 'completed'")->fetch_assoc()['count'];
$recent_reports = $conn->query("SELECT COUNT(*) as count FROM reports WHERE generated_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background-image: url("Images/doddles%20of%20car%20in%20whole%20page%20in%20pink%20and%20red%20color%20for%20website%20background.jpg");
            background-size: cover;
            color: #333;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background-color: rgba(51, 51, 51, 0.95);
            color: white;
            padding: 20px;
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.2);
            position: fixed;
            height: 100vh;
        }
        
        .admin-profile {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #444;
        }
        
        .admin-profile img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid #ff4081;
            margin-bottom: 10px;
            object-fit: cover;
        }
        
        .admin-profile p {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
            color: #ff4081;
        }
        
        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #ff4081;
            font-size: 1.5rem;
        }
        
        .sidebar ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar ul li {
            margin: 15px 0;
        }
        
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .sidebar ul li a:hover {
            background-color: #ff4081;
            color: white;
            transform: translateX(5px);
        }
        
        .sidebar ul li a.active {
            background-color: #ff4081;
            color: white;
        }
        
        .main-content {
            flex-grow: 1;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(5px);
            margin-left: 250px;
            min-height: 100vh;
        }
        
        .logo {
            width: 300px;
            margin-bottom: 20px;
        }
        
        .logo img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        .dashboard-container {
            background-color: white;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        h1 {
            text-align: center;
            color: #d81b60;
            margin-bottom: 30px;
            font-size: 2.2rem;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            text-align: center;
            border-top: 4px solid #ff8a65;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card h3 {
            margin-top: 0;
            color: #555;
            font-size: 1.1rem;
            margin-bottom: 15px;
        }
        
        .stat-card .value {
            font-size: 2.5rem;
            font-weight: bold;
            color: #d81b60;
            margin: 15px 0;
        }
        
        .recent-orders {
            margin-top: 40px;
        }
        
        .recent-orders h2 {
            color: #d81b60;
            margin-bottom: 20px;
            font-size: 1.8rem;
            border-bottom: 2px solid #ffcdd2;
            padding-bottom: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background-color: #ff8a65;
            color: white;
            font-weight: 600;
        }
        
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        
        tr:hover {
            background-color: #fff5f5;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            font-size: 0.9rem;
        }
        
        .btn-primary {
            background-color: #ff8a65;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #ff7043;
            box-shadow: 0 4px 8px rgba(255, 138, 101, 0.3);
        }
        
        .success-message {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .no-data {
            text-align: center;
            padding: 30px;
            color: #777;
            font-size: 1.1rem;
        }
        
        .view-all {
            text-align: center;
            margin-top: 30px;
        }
        
        @media (max-width: 992px) {
            .sidebar {
                width: 220px;
                padding: 15px;
            }
            
            .main-content {
                margin-left: 220px;
                padding: 20px;
            }
        }
        
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                position: relative;
                height: auto;
                margin-bottom: 20px;
            }
            
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            
            .stats-container {
                grid-template-columns: 1fr 1fr;
            }
            
            .dashboard-container {
                padding: 20px;
            }
        }
        
        @media (max-width: 576px) {
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="admin-profile">
            <img src="<?php echo htmlspecialchars($admin['profile_image'] ?? 'Images/default-profile.jpg'); ?>" alt="Admin Profile">
            <p><?php echo htmlspecialchars($admin['name'] ?? 'Admin'); ?></p>
            <p><?php echo htmlspecialchars($admin['email'] ?? 'admin@example.com'); ?></p>
        </div>
        
        <h2>Dashboard Menu</h2>
        <ul>
            <li><a href="dashboard.php">Overview</a></li>
            <li><a href="orders.php">Orders</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="customers.php">Customers</a></li>
            <li><a href="reports.php" class="active">Reports</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="dashboard-container">
            <h1>Reports Dashboard</h1>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="success-message">
                    <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>
            
            <div class="stats-container">
                <div class="stat-card">
                    <h3>Total Reports</h3>
                    <div class="value"><?php echo $total_reports; ?></div>
                    <p>All generated reports</p>
                </div>
                
                <div class="stat-card">
                    <h3>Pending Reports</h3>
                    <div class="value"><?php echo $pending_reports; ?></div>
                    <p>Reports in progress</p>
                </div>
                
                <div class="stat-card">
                    <h3>Completed Reports</h3>
                    <div class="value"><?php echo $completed_reports; ?></div>
                    <p>Ready for download</p>
                </div>
                
                <div class="stat-card">
                    <h3>Recent Reports</h3>
                    <div class="value"><?php echo $recent_reports; ?></div>
                    <p>Last 7 days</p>
                </div>
            </div>
            
            <div class="recent-orders">
                <h2>Recent Reports</h2>
                
                <?php if (empty($reports)): ?>
                    <div class="no-data">
                        <p>No reports found. Generate your first report to get started.</p>
                        <a href="generate_report.php" class="btn btn-primary">Generate Report</a>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Report ID</th>
                                <th>Report Name</th>
                                <th>Type</th>
                                <th>Generated Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reports as $report): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($report['report_id']); ?></td>
                                    <td><?php echo htmlspecialchars($report['report_name']); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst($report['report_type'])); ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($report['generated_date'])); ?></td>
                                    <td>
                                        <span style="color: <?php echo $report['status'] == 'completed' ? '#4CAF50' : '#FFC107'; ?>">
                                            <?php echo ucfirst($report['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($report['status'] == 'completed'): ?>
                                            <a href="download_report.php?id=<?php echo $report['report_id']; ?>" class="btn btn-primary">Download</a>
                                        <?php else: ?>
                                            <span class="btn" style="background-color: #e0e0e0; cursor: not-allowed;">Processing</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div class="view-all">
                        <a href="all_reports.php" class="btn btn-primary">View All Reports</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>