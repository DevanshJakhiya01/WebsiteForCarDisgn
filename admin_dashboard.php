<?php
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

// Function to get column names from a table
function getTableColumns($conn, $tableName) {
    $columns = array();
    $result = $conn->query("SHOW COLUMNS FROM $tableName");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
    }
    return $columns;
}

// Get columns from orders table
$ordersColumns = getTableColumns($conn, 'orders');
if (empty($ordersColumns)) {
    die("Error: Could not retrieve columns from orders table.");
}

// Get columns from users table
$usersColumns = getTableColumns($conn, 'users');
if (empty($usersColumns)) {
    die("Error: Could not retrieve columns from users table.");
}

// Determine the correct column names to use
$amountColumn = in_array('total_amount', $ordersColumns) ? 'total_amount' : 
               (in_array('amount', $ordersColumns) ? 'amount' : 
               (in_array('price', $ordersColumns) ? 'price' : 'total'));

$productColumn = in_array('product_name', $ordersColumns) ? 'product_name' : 
                (in_array('car_model', $ordersColumns) ? 'car_model' : 
                (in_array('model_name', $ordersColumns) ? 'model_name' : 'product'));

$statusColumn = in_array('status', $ordersColumns) ? 'status' : 'order_status';
$dateColumn = in_array('created_at', $ordersColumns) ? 'created_at' : 
             (in_array('order_date', $ordersColumns) ? 'order_date' : 'date_created');

// Get statistics for dashboard
$stats = [];
$stats['total_orders'] = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
$stats['pending_orders'] = $conn->query("SELECT COUNT(*) FROM orders WHERE $statusColumn = 'Pending'")->fetch_row()[0];
$stats['total_users'] = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$stats['total_products'] = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Car Customization</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background-image: url("Images/doddles%20of%20car%20in%20whole%20page%20in%20pink%20and%20red%20color%20for%20website%20background.jpg");
            background-size: cover;
            color: red;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background-color: rgba(51, 51, 51, 0.9);
            color: white;
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
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
            display: block;
            padding: 10px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .sidebar ul li a:hover {
            background-color: #ff4081;
            color: white;
            transform: translateX(5px);
        }
        
        .main-content {
            flex-grow: 1;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
            overflow-y: auto;
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
            padding: 20px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        
        h1 {
            text-align: center;
            color: #d81b60;
            margin-bottom: 20px;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
            border-top: 4px solid darksalmon;
        }
        
        .stat-card h3 {
            margin-top: 0;
            color: #333;
        }
        
        .stat-card .value {
            font-size: 36px;
            font-weight: bold;
            color: #d81b60;
            margin: 10px 0;
        }
        
        .recent-orders {
            margin-top: 30px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: darksalmon;
            color: white;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tr:hover {
            background-color: #ffe6ee;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background-color: darksalmon;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #e9967a;
        }
        
        .success-message {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                padding: 15px;
            }
            
            .main-content {
                padding: 20px;
            }
            
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
            <img src="Images/Devansh%203dxx.jpg" alt="Admin Photo">
            <p>Admin Panel</p>
        </div>
        <h2>Navigation</h2>
        <ul>
            <li><a href="admin_dashboard.php" class="active">Dashboard</a></li>
            <li><a href="manage_users.php">Users</a></li>
            <li><a href="manage_products.php">Products</a></li>
            <li><a href="manage_orders.php">Orders</a></li>
            <li><a href="manage_payments.php">Payments</a></li>
            <li><a href="reports.php">Reports</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="logo">
            <img src="Images/Devansh%20Car%20Customization%20logo%201.jpg" alt="Car Customization Logo">
        </div>
        
        <div class="dashboard-container">
            <h1>Admin Dashboard</h1>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message">
                    <?= htmlspecialchars(urldecode($_GET['success'])) ?>
                </div>
            <?php endif; ?>
            
            <div class="stats-container">
                <div class="stat-card">
                    <h3>Total Orders</h3>
                    <div class="value"><?= $stats['total_orders'] ?></div>
                    <a href="manage_orders.php" class="btn btn-primary">View Orders</a>
                </div>
                
                <div class="stat-card">
                    <h3>Pending Orders</h3>
                    <div class="value"><?= $stats['pending_orders'] ?></div>
                    <a href="manage_orders.php?status=Pending" class="btn btn-primary">View Pending</a>
                </div>
                
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <div class="value"><?= $stats['total_users'] ?></div>
                    <a href="manage_users.php" class="btn btn-primary">Manage Users</a>
                </div>
                
                <div class="stat-card">
                    <h3>Total Products</h3>
                    <div class="value"><?= $stats['total_products'] ?></div>
                    <a href="manage_products.php" class="btn btn-primary">Manage Products</a>
                </div>
            </div>
            
            <div class="recent-orders">
                <h2>Recent Orders</h2>
                <?php
                $recent_orders_query = "SELECT orders.id, users.username, 
                                      orders.$productColumn AS product_name, 
                                      orders.$amountColumn AS total_amount, 
                                      orders.$statusColumn AS status, 
                                      orders.$dateColumn AS created_at 
                                      FROM orders 
                                      INNER JOIN users ON orders.user_id = users.id 
                                      ORDER BY $dateColumn DESC LIMIT 5";
                
                $recent_orders = $conn->query($recent_orders_query);
                
                if ($recent_orders->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = $recent_orders->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['id']) ?></td>
                                <td><?= htmlspecialchars($order['username']) ?></td>
                                <td><?= htmlspecialchars($order['product_name']) ?></td>
                                <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                <td><?= htmlspecialchars($order['status']) ?></td>
                                <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <div style="text-align: center; margin-top: 20px;">
                        <a href="manage_orders.php" class="btn btn-primary">View All Orders</a>
                    </div>
                <?php else: ?>
                    <p>No recent orders found</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>