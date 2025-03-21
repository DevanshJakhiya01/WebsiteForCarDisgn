<?php
session_start();

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

// Insert admin user (run this only once)
$password = "Sonakshi01";
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO admins (admin_username, admin_password) VALUES ('Devansh', '$hashed_password')";
if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Admin user added successfully.');</script>";
} else {
    echo "<script>alert('Error adding admin user: " . $conn->error . "');</script>";
}
// End of admin user insertion code

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $admin_username = trim($_POST['admin_username']);
    $admin_password = trim($_POST['admin_password']);

    // Validate inputs
    if (empty($admin_username) || empty($admin_password)) {
        $error_message = "Please fill in all fields.";
    } else {
        // Fetch admin details from the database
        $sql = "SELECT id, admin_username, admin_password FROM admins WHERE admin_username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $admin_username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            // Verify password
            if (password_verify($admin_password, $row['admin_password'])) {
                // Login successful
                $_SESSION['admin_id'] = $row['id'];
                $_SESSION['admin_username'] = $row['admin_username'];
                header("Location: admin_dashboard.php"); // Redirect to admin dashboard
                exit();
            } else {
                $error_message = "Invalid username or password.";
            }
        } else {
            $error_message = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
            background-image: url("Images/doddles\ of\ car\ in\ whole\ page\ in\ pink\ and\ red\ color\ for\ website\ background.jpg");
            background-size: cover;
            color: red;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
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
            width: 100%;
            max-width: 300px;
            border-radius: 10px;
            text-align: center;
        }
        .form-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-container button {
            width: 100%;
            padding: 10px;
            background-color: darksalmon;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .form-container button:hover {
            background-color: #e9967a;
        }
        .error-message {
            color: red;
            margin-bottom: 10px;
        }
        .admin-panel-link {
            display: inline-block;
            margin-top: 10px;
            color: white;
            background-color: darksalmon;
            padding: 10px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }
        .admin-panel-link:hover {
            background-color: #e9967a;
        }
    </style>
</head>

<body>
    <div class="logo">
        <img src="Images/Devansh%20Car%20Customization%20logo%201.jpg" alt="Devansh Car Customization Logo">
    </div>

    <div class="form-container">
        <h2>Admin Login</h2>
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
        <form action="admin_login.php" method="POST">
            <input type="text" name="admin_username" placeholder="Admin Username" required>
            <input type="password" name="admin_password" placeholder="Admin Password" required>
            <button type="submit">Login</button>
        </form>
        <a href="admin_dashboard.php" class="admin-panel-link">Admin Panel</a>
    </div>
</body>
</html>