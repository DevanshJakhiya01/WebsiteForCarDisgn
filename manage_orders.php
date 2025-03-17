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

// Fetch orders from the database
$sql = "SELECT orders.id, users.username, orders.car_name AS product_name, orders.total_amount, orders.status, orders.created_at 
        FROM orders 
        INNER JOIN users ON orders.user_id = users.id";
$result = $conn->query($sql);

// Handle order deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM orders WHERE id = $delete_id";
    if ($conn->query($delete_sql)) {
        echo "<script>alert('Order deleted successfully!');</script>";
        echo "<script>window.location.href = 'manage_orders.php';</script>";
    } else {
        echo "<script>alert('Error deleting order: " . $conn->error . "');</script>";
    }
}

// Handle order status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    // Update order status in the database
    $update_sql = "UPDATE orders SET status = '$new_status' WHERE id = $order_id";
    if ($conn->query($update_sql)) {
        echo "<script>alert('Order status updated successfully!');</script>";
        echo "<script>window.location.href = 'manage_orders.php';</script>";
    } else {
        echo "<script>alert('Error updating order status: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background-image: url("Images/doddles\ of\ car\ in\ whole\ page\ in\ pink\ and\ red\ color\ for\ website\ background.jpg");
            background-size: auto;
            color: red;
        }
        .sidebar {
            width: 250px;
            background-color: #333;
            color: white;
            height: 100vh;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }
        .sidebar ul li {
            margin: 15px 0;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            display: block;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .sidebar ul li a:hover {
            background-color: #555;
        }
        .main-content {
            flex-grow: 1;
            padding: 20px;
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
        .table-container {
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            width: 80%;
            max-width: 1200px;
            margin: 0 auto;
        }
        .table-container h2 {
            text-align: center;
        }
        .table-container table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table-container th, .table-container td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        .table-container th {
            background-color: #f2f2f2;
        }
        .table-container button {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            background-color: red;
            color: white;
            cursor: pointer;
        }
        .table-container button:hover {
            background-color: darkred;
        }
        .status-form {
            display: inline-block;
        }
        .status-form select {
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="admin-profile">
            <img src="Images/admin.jpg" alt="Admin Photo">
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
            <h2>Manage Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Product</th>
                        <th>Total Amount</th>
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
                                    <td>{$row['product_name']}</td>
                                    <td>{$row['total_amount']}</td>
                                    <td>
                                        <form class='status-form' method='POST' action=''>
                                            <input type='hidden' name='order_id' value='{$row['id']}'>
                                            <select name='status'>
                                                <option value='Pending'" . ($row['status'] == 'Pending' ? ' selected' : '') . ">Pending</option>
                                                <option value='Processing'" . ($row['status'] == 'Processing' ? ' selected' : '') . ">Processing</option>
                                                <option value='Completed'" . ($row['status'] == 'Completed' ? ' selected' : '') . ">Completed</option>
                                                <option value='Cancelled'" . ($row['status'] == 'Cancelled' ? ' selected' : '') . ">Cancelled</option>
                                            </select>
                                            <button type='submit' name='update_status'>Update</button>
                                        </form>
                                    </td>
                                    <td>{$row['created_at']}</td>
                                    <td>
                                        <a href='manage_orders.php?delete_id={$row['id']}' onclick=\"return confirm('Are you sure you want to delete this order?');\"><button>Delete</button></a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No orders found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>