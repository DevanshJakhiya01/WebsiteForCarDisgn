?php
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

// First, let's examine the database structure
$tables = [];
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

// Determine the correct relationship between tables
$join_condition = "";
if (in_array('payment', $tables) && in_array('users', $tables)) {
    // Check if payment has a direct user relationship
    $columns = $conn->query("SHOW COLUMNS FROM payment");
    $has_user_id = false;
    while ($col = $columns->fetch_assoc()) {
        if ($col['Field'] == 'user_id' || $col['Field'] == 'customer_id') {
            $has_user_id = true;
            $user_col = $col['Field'];
            break;
        }
    }
    
    if ($has_user_id) {
        $join_condition = "INNER JOIN users ON payment.$user_col = users.id";
    } elseif (in_array('orders', $tables)) {
        // Check how orders relates to users
        $columns = $conn->query("SHOW COLUMNS FROM orders");
        $has_user_id = false;
        while ($col = $columns->fetch_assoc()) {
            if ($col['Field'] == 'user_id' || $col['Field'] == 'customer_id') {
                $has_user_id = true;
                $user_col = $col['Field'];
                break;
            }
        }
        
        if ($has_user_id) {
            $join_condition = "INNER JOIN orders ON payment.order_id = orders.id 
                              INNER JOIN users ON orders.$user_col = users.id";
        } else {
            die("Error: Could not determine relationship between orders and users tables.");
        }
    } else {
        die("Error: Required tables not found in database.");
    }
} else {
    die("Error: Required tables not found in database.");
}

// Build the SQL query
$sql = "SELECT payment.id, users.username, payment.order_id, 
               payment.payment_amount, payment.payment_method, 
               payment.payment_status, payment.created_at 
        FROM payment 
        $join_condition";

$result = $conn->query($sql);
if (!$result) {
    die("Error executing query: " . $conn->error);
}

// Handle payment deletion (using prepared statement)
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM payment WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        echo "<script>alert('Payment deleted successfully!');</script>";
        echo "<script>window.location.href = 'manage_payments.php';</script>";
    } else {
        echo "<script>alert('Error deleting payment: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle payment status update (using prepared statement)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $payment_id = $_POST['payment_id'];
    $new_status = $_POST['status'];

    $update_sql = "UPDATE payment SET payment_status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $payment_id);

    if ($stmt->execute()) {
        echo "<script>alert('Payment status updated successfully!');</script>";
        echo "<script>window.location.href = 'manage_payments.php';</script>";
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
    <title>Manage Payments</title>
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
        
        .order-container {
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
        
        .status-form {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        
        select {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            background-color: white;
        }
        
        button {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .update-btn {
            background-color: darksalmon;
            color: white;
        }
        
        .update-btn:hover {
            background-color: #e9967a;
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
        
        .no-orders {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
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
            <p>Admin</p>
        </div>
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="admin_dashboard.php">Users</a></li>
            <li><a href="add_user.php">Add User</a></li>
            <li><a href="manage_categories.php">Categories</a></li>
            <li><a href="manage_products.php">Products</a></li>
            <li><a href="manage_orders.php">Orders</a></li>
            <li><a href="manage_payments.php">Payments</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="logo">
            <img src="Images/Devansh%20Car%20Customization%20logo%201.jpg" alt="Devansh Car Customization Logo">
        </div>

        <div class="table-container">
            <h2>Manage Payments</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Order ID</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['username']}</td>
                                    <td>{$row['order_id']}</td>
                                    <td>{$row['payment_amount']}</td>
                                    <td>{$row['payment_method']}</td>
                                    <td>
                                        <form class='status-form' method='POST' action=''>
                                            <input type='hidden' name='payment_id' value='{$row['id']}'>
                                            <select name='status'>
                                                <option value='Pending'" . ($row['payment_status'] == 'Pending' ? ' selected' : '') . ">Pending</option>
                                                <option value='Completed'" . ($row['payment_status'] == 'Completed' ? ' selected' : '') . ">Completed</option>
                                                <option value='Failed'" . ($row['payment_status'] == 'Failed' ? ' selected' : '') . ">Failed</option>
                                            </select>
                                            <button type='submit' name='update_status'>Update</button>
                                        </form>
                                    </td>
                                    <td>{$row['created_at']}</td>
                                    <td>
                                        <a href='manage_payments.php?delete_id={$row['id']}' onclick=\"return confirm('Are you sure you want to delete this payment?');\"><button>Delete</button></a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No payments found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>