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

// First, let's examine the orders table structure
$orders_columns = [];
$columns_result = $conn->query("SHOW COLUMNS FROM orders");
if ($columns_result) {
    while ($row = $columns_result->fetch_assoc()) {
        $orders_columns[] = $row['Field'];
    }
}

// Determine the correct product name column
$product_column = "";
if (in_array('product_name', $orders_columns)) {
    $product_column = 'product_name';
} elseif (in_array('car_model', $orders_columns)) {
    $product_column = 'car_model';
} elseif (in_array('model_name', $orders_columns)) {
    $product_column = 'model_name';
} elseif (in_array('product_id', $orders_columns)) {
    // If product is referenced by ID, we'll need to join with products table
    $product_column = 'product_id';
} else {
    die("Error: Could not determine product information column in orders table.");
}

// Build the appropriate query
if ($product_column == 'product_id' && in_array('products', $conn->query("SHOW TABLES")->fetch_all())) {
    // Join with products table if product_id exists
    $sql = "SELECT orders.id, users.username, 
                   products.name AS product_name, 
                   orders.total_amount, orders.status, orders.created_at
            FROM orders
            INNER JOIN users ON orders.user_id = users.id
            LEFT JOIN products ON orders.product_id = products.id";
} else {
    // Use direct column from orders table
    $sql = "SELECT orders.id, users.username, 
                   orders.$product_column AS product_name, 
                   orders.total_amount, orders.status, orders.created_at
            FROM orders
            INNER JOIN users ON orders.user_id = users.id";
}

$result = $conn->query($sql);
if (!$result) {
    die("Error executing query: " . $conn->error);
}

// [Rest of your existing code for handling deletions and updates...]
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <style>
        /* [Your existing CSS styles...] */
    </style>
</head>
<body>
    <div class="sidebar">
        <!-- [Your existing sidebar...] -->
    </div>

    <div class="main-content">
        <div class="logo">
            <!-- [Your existing logo...] -->
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
                                    <td>".htmlspecialchars($row['id'])."</td>
                                    <td>".htmlspecialchars($row['username'])."</td>
                                    <td>".htmlspecialchars($row['product_name'])."</td>
                                    <td>".htmlspecialchars($row['total_amount'])."</td>
                                    <td>
                                        <form class='status-form' method='POST' action=''>
                                            <input type='hidden' name='order_id' value='".htmlspecialchars($row['id'])."'>
                                            <select name='status'>
                                                <option value='Pending'".($row['status'] == 'Pending' ? ' selected' : '').">Pending</option>
                                                <option value='Processing'".($row['status'] == 'Processing' ? ' selected' : '').">Processing</option>
                                                <option value='Completed'".($row['status'] == 'Completed' ? ' selected' : '').">Completed</option>
                                                <option value='Cancelled'".($row['status'] == 'Cancelled' ? ' selected' : '').">Cancelled</option>
                                            </select>
                                            <button type='submit' name='update_status'>Update</button>
                                        </form>
                                    </td>
                                    <td>".htmlspecialchars($row['created_at'])."</td>
                                    <td>
                                        <a href='manage_orders.php?delete_id=".htmlspecialchars($row['id'])."' onclick=\"return confirm('Are you sure you want to delete this order?');\"><button>Delete</button></a>
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

<?php
$conn->close();
?>