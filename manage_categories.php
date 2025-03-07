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

// Fetch categories from the database
$sql = "SELECT id, name FROM categories";
$result = $conn->query($sql);

// Handle category deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM categories WHERE id = $delete_id";
    if ($conn->query($delete_sql) {
        echo "<script>alert('Category deleted successfully!');</script>";
        echo "<script>window.location.href = 'manage_categories.php';</script>";
    } else {
        echo "<script>alert('Error deleting category: " . $conn->error . "');</script>";
    }
}

// Handle form submission for adding a new category
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = $_POST['category_name'];

    // Insert new category into the database
    $insert_sql = "INSERT INTO categories (name) VALUES ('$category_name')";
    if ($conn->query($insert_sql)) {
        echo "<script>alert('Category added successfully!');</script>";
        echo "<script>window.location.href = 'manage_categories.php';</script>";
    } else {
        echo "<script>alert('Error adding category: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
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
        .form-container {
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            width: 80%;
            max-width: 500px;
            margin: 0 auto 20px;
        }
        .form-container h2 {
            text-align: center;
        }
        .form-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-container input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: red;
            color: white;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: darkred;
        }
        .table-container {
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            width: 80%;
            max-width: 800px;
            margin: 0 auto;
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
    </style>
</head>

<body>
    <div class="sidebar">
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

        <div class="form-container">
            <h2>Add New Category</h2>
            <form method="POST" action="">
                <label for="category_name">Category Name:</label>
                <input type="text" id="category_name" name="category_name" required>
                <button type="submit">Add Category</button>
            </form>
        </div>

        <div class="table-container">
            <h2>Manage Categories</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['name']}</td>
                                    <td>
                                        <a href='manage_categories.php?delete_id={$row['id']}' onclick=\"return confirm('Are you sure you want to delete this category?');\"><button>Delete</button></a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No categories found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>