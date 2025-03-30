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
// Corrected SQL query: Assuming the product/car details are in a different table or need a different column name.
// Replace 'product_name' with the actual column name from your 'orders' table.
// If the product details are in another table, you'll need to join that table.
$sql = "SELECT orders.id, users.username, orders.product_name, orders.total_amount, orders.status, orders.created_at
        FROM orders
        INNER JOIN users ON orders.user_id = users.id";

$result = $conn->query($sql);

// Handle order deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM orders WHERE id = ?"; // Use prepared statement
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id); // Bind the parameter

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

    // Update order status in the database
    $update_sql = "UPDATE orders SET status = ? WHERE id = ?"; // Use prepared statement
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $order_id); // Bind parameters

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
        /* ... (your CSS styles) ... */
    </style>
</head>

<body>
    <div class="sidebar">
        </div>

    <div class="main-content">
        <div class="logo">
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
<?php
// Close the database connection
$conn->close();
?>