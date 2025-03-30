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

// Build the SQL query
$sql = "SELECT orders.id, users.username, 
               orders.$productColumn AS product_name, 
               orders.$amountColumn AS total_amount, 
               orders.$statusColumn AS status, 
               orders.$dateColumn AS created_at
        FROM orders
        INNER JOIN users ON orders.user_id = users.id";

$result = $conn->query($sql);
if (!$result) {
    die("Error executing query: " . $conn->error);
}

// Handle order deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
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

// Handle order status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
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
            margin: 20px;
            background-image: url("Images/doddles%20of%20car%20in%20whole%20page%20in%20pink%20and%20red%20color%20for%20website%20background.jpg");
            background-size: cover;
            color: red;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
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
        
        .order-container {
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 1200px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        h1 {
            text-align: center;
            color: red;
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
            .order-container {
                padding: 15px;
                width: 95%;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="logo">
        <img src="Images/Devansh%20Car%20Customization%20logo%201.jpg" alt="Car Customization Logo">
    </div>
    
    <div class="order-container">
        <h1>Manage Orders</h1>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <?= htmlspecialchars(urldecode($_GET['success'])) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($result->num_rows > 0): ?>
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
                    <?php while ($row = $result->fetch_assoc()): ?>
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
                                <button type="submit" name="update_status" class="update-btn">Update</button>
                            </form>
                        </td>
                        <td><?= date('M j, Y h:i A', strtotime($row['created_at'])) ?></td>
                        <td>
                            <a href="manage_orders.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this order?');">
                                <button class="delete-btn">Delete</button>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-orders">No orders found in the system</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>