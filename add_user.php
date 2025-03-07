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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Insert new user into the database
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
    if ($conn->query($sql) {
        echo "<script>alert('User added successfully!');</script>";
        echo "<script>window.location.href = 'admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error adding user: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
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
            margin: 0 auto;
        }
        .form-container h2 {
            text-align: center;
        }
        .form-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-container input[type="text"],
        .form-container input[type="email"],
        .form-container input[type="password"] {
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
            <h2>Add New User</h2>
            <form method="POST" action="">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Add User</button>
            </form>
        </div>
    </div>
</body>
</html>