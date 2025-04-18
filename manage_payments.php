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

// Determine the correct column names to use for orders
$amountColumn = in_array('total_amount', $ordersColumns) ? 'total_amount' : 
               (in_array('amount', $ordersColumns) ? 'amount' : 
               (in_array('price', $ordersColumns) ? 'price' : 'total'));

$productColumn = in_array('product_name', $ordersColumns) ? 'product_name' : 
                (in_array('car_model', $ordersColumns) ? 'car_model' : 
                (in_array('model_name', $ordersColumns) ? 'model_name' : 'product'));

$statusColumn = in_array('status', $ordersColumns) ? 'status' : 'order_status';
$dateColumn = in_array('created_at', $ordersColumns) ? 'created_at' : 
             (in_array('order_date', $ordersColumns) ? 'order_date' : 'date_created');

// Build the SQL query for orders
$ordersSql = "SELECT orders.id, users.username, 
               orders.$productColumn AS product_name, 
               orders.$amountColumn AS total_amount, 
               orders.$statusColumn AS status, 
               orders.$dateColumn AS created_at
        FROM orders
        INNER JOIN users ON orders.user_id = users.id";

$ordersResult = $conn->query($ordersSql);
if (!$ordersResult) {
    die("Error executing orders query: " . $conn->error);
}

// Build the SQL query for payments
$paymentsSql = "SELECT payment.id, payment.order_id, payment.payment_amount, 
                payment.payment_method, payment.payment_status, payment.created_at 
                FROM payment";
$paymentsResult = $conn->query($paymentsSql);
if (!$paymentsResult) {
    die("Error executing payments query: " . $conn->error);
}

// Handle order deletion
if (isset($_GET['delete_order_id'])) {
    $delete_id = $_GET['delete_order_id'];
    $delete_sql = "DELETE FROM orders WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        echo "<script>alert('Order deleted successfully!');</script>";
        echo "<script>window.location.href = 'manage_orders.php';</script>";
    } else {
        echo "<script>alert('Error deleting order: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle payment deletion
if (isset($_GET['delete_payment_id'])) {
    $delete_id = $_GET['delete_payment_id'];
    $delete_sql = "DELETE FROM payment WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        echo "<script>alert('Payment deleted successfully!');</script>";
        echo "<script>window.location.href = 'manage_orders.php';</script>";
    } else {
        echo "<script>alert('Error deleting payment: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle order status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_order_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    $update_sql = "UPDATE orders SET $statusColumn = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $order_id);

    if ($stmt->execute()) {
        echo "<script>alert('Order status updated successfully!');</script>";
        echo "<script>window.location.href = 'manage_orders.php';</script>";
    } else {
        echo "<script>alert('Error updating order status: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle payment status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_payment_status'])) {
    $payment_id = $_POST['payment_id'];
    $new_status = $_POST['status'];

    $update_sql = "UPDATE payment SET payment_status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $payment_id);

    if ($stmt->execute()) {
        echo "<script>alert('Payment status updated successfully!');</script>";
        echo "<script>window.location.href = 'manage_orders.php';</script>";
    } else {
        echo "<script>alert('Error updating payment status: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders & Payments</title>
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
            background-color: rgba(51, 51, 51, 0.9);
            color: white;
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
            position: fixed;
            height: 100%;
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
            margin-left: 250px;
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(5px);
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
        
        .container {
            background-color: white;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        h1 {
            text-align: center;
            color: #d81b60;
            margin-bottom: 30px;
        }
        
        h2 {
            color: #e91e63;
            border-bottom: 2px solid #f8bbd0;
            padding-bottom: 10px;
            margin-top: 40px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background-color: #e91e63;
            color: white;
            font-weight: 600;
        }
        
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        
        tr:hover {
            background-color: #fff5f7;
        }
        
        .status-form {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        
        select {
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #ddd;
            background-color: white;
            font-size: 14px;
        }
        
        button {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
            font-size: 14px;
        }
        
        .update-btn {
            background-color: #4CAF50;
            color: white;
        }
        
        .update-btn:hover {
            background-color: #388E3C;
        }
        
        .delete-btn {
            background-color: #f44336;
            color: white;
        }
        
        .delete-btn:hover {
            background-color: #d32f2f;
        }
        
        .success-message {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .no-data {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
        
        .tab-container {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            border-bottom: none;
            margin-right: 5px;
            border-radius: 5px 5px 0 0;
            transition: all 0.3s;
        }
        
        .tab.active {
            background-color: #e91e63;
            color: white;
            border-color: #e91e63;
        }
        
        .tab:hover:not(.active) {
            background-color: #f8bbd0;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                position: relative;
                height: auto;
            }
            
            .main-content {
                margin-left: 0;
                padding: 20px;
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
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="manage_users.php">Users</a></li>
            <li><a href="manage_products.php">Products</a></li>
            <li><a href="manage_orders.php">Orders & Payments</a></li>
            <li><a href="reports.php">Reports</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="logo">
            <img src="Images/Devansh%20Car%20Customization%20logo%201.jpg" alt="Car Customization Logo">
        </div>
        
        <h1>Manage Orders & Payments</h1>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <?= htmlspecialchars(urldecode($_GET['success'])) ?>
            </div>
        <?php endif; ?>
        
        <div class="tab-container">
            <div class="tab active" onclick="openTab('orders')">Orders</div>
            <div class="tab" onclick="openTab('payments')">Payments</div>
        </div>
        
        <div id="orders" class="tab-content active">
            <div class="container">
                <h2>Order Management</h2>
                
                <?php if ($ordersResult->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $ordersResult->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td><?= htmlspecialchars($row['product_name']) ?></td>
                                <td>$<?= number_format($row['total_amount'], 2) ?></td>
                                <td>
                                    <form class="status-form" method="POST">
                                        <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                        <select name="status">
                                            <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="Processing" <?= $row['status'] == 'Processing' ? 'selected' : '' ?>>Processing</option>
                                            <option value="Completed" <?= $row['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                            <option value="Cancelled" <?= $row['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                        </select>
                                        <button type="submit" name="update_order_status" class="update-btn">Update</button>
                                    </form>
                                </td>
                                <td><?= date('M j, Y h:i A', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <a href="manage_orders.php?delete_order_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this order?');">
                                        <button class="delete-btn">Delete</button>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="no-data">No orders found in the system</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div id="payments" class="tab-content">
            <div class="container">
                <h2>Payment Management</h2>
                
                <?php if ($paymentsResult->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Order ID</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $paymentsResult->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['order_id']) ?></td>
                                <td>$<?= number_format($row['payment_amount'], 2) ?></td>
                                <td><?= htmlspecialchars($row['payment_method']) ?></td>
                                <td>
                                    <form class="status-form" method="POST">
                                        <input type="hidden" name="payment_id" value="<?= $row['id'] ?>">
                                        <select name="status">
                                            <option value="Pending" <?= $row['payment_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="Completed" <?= $row['payment_status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                            <option value="Failed" <?= $row['payment_status'] == 'Failed' ? 'selected' : '' ?>>Failed</option>
                                        </select>
                                        <button type="submit" name="update_payment_status" class="update-btn">Update</button>
                                    </form>
                                </td>
                                <td><?= date('M j, Y h:i A', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <a href="manage_orders.php?delete_payment_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this payment?');">
                                        <button class="delete-btn">Delete</button>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="no-data">No payments found in the system</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function openTab(tabName) {
            // Hide all tab contents
            const tabContents = document.getElementsByClassName('tab-content');
            for (let i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove('active');
            }
            
            // Remove active class from all tabs
            const tabs = document.getElementsByClassName('tab');
            for (let i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('active');
            }
            
            // Show the selected tab content and mark tab as active
            document.getElementById(tabName).classList.add('active');
            event.currentTarget.classList.add('active');
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>