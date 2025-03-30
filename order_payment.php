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

// 1. First, detect the correct product information column
$product_column = null;
$columns_result = $conn->query("SHOW COLUMNS FROM orders");
$orders_columns = [];
while ($row = $columns_result->fetch_assoc()) {
    $orders_columns[] = $row['Field'];
}

// Check for common product column names
$possible_product_columns = ['car_model', 'model_name', 'product_name', 'vehicle_name', 'item_name'];
foreach ($possible_product_columns as $col) {
    if (in_array($col, $orders_columns)) {
        $product_column = $col;
        break;
    }
}

// 2. Build the appropriate SQL query
if ($product_column) {
    // Case 1: Product name is directly in orders table
    $sql = "SELECT orders.id, users.username, 
                   orders.$product_column AS product_name,
                   orders.total_amount, orders.status, orders.created_at
            FROM orders
            INNER JOIN users ON orders.user_id = users.id";
} elseif (in_array('product_id', $orders_columns)) {
    // Case 2: Product is referenced by ID - join with products table
    if ($conn->query("SHOW TABLES LIKE 'products'")->num_rows > 0) {
        $sql = "SELECT orders.id, users.username, 
                       products.name AS product_name,
                       orders.total_amount, orders.status, orders.created_at
                FROM orders
                INNER JOIN users ON orders.user_id = users.id
                LEFT JOIN products ON orders.product_id = products.id";
    } elseif ($conn->query("SHOW TABLES LIKE 'cars'")->num_rows > 0) {
        $sql = "SELECT orders.id, users.username, 
                       cars.model AS product_name,
                       orders.total_amount, orders.status, orders.created_at
                FROM orders
                INNER JOIN users ON orders.user_id = users.id
                LEFT JOIN cars ON orders.product_id = cars.id";
    } else {
        die("Error: Could not find products or cars table for product information");
    }
} else {
    die("Error: Could not determine how to retrieve product information from orders");
}

// Execute the query
$result = $conn->query($sql);
if (!$result) {
    die("Error executing query: " . $conn->error);
}

// Handle order deletion
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        header("Location: manage_orders.php?success=Order+deleted");
        exit();
    } else {
        echo "<script>alert('Error deleting order: " . addslashes($stmt->error) . "');</script>";
    }
    $stmt->close();
}

// Handle order status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $conn->real_escape_string($_POST['status']);
    
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    
    if ($stmt->execute()) {
        header("Location: manage_orders.php?success=Status+updated");
        exit();
    } else {
        echo "<script>alert('Error updating status: " . addslashes($stmt->error) . "');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
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
            background-color: #3498db;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e8f4fc;
        }
        .status-form {
            display: flex;
            gap: 10px;
        }
        select, button {
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        button {
            background-color: #3498db;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #2980b9;
        }
        .delete-btn {
            background-color: #e74c3c;
        }
        .delete-btn:hover {
            background-color: #c0392b;
        }
        .success-message {
            background-color: #2ecc71;
            color: white;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Orders</h1>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <?= htmlspecialchars(urldecode($_GET['success'])) ?>
            </div>
        <?php endif; ?>
        
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
                <?php if ($result->num_rows > 0): ?>
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
                                <button type="submit" name="update_status">Update</button>
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
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">No orders found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>