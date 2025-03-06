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

// Initialize variables
$row = [];
$error_message = "";

// Fetch user details if ID is provided
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']); // Sanitize the input
    $sql = "SELECT id, username, email FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
    } else {
        $error_message = "User not found.";
    }
} else {
    $error_message = "No user ID provided.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = intval($_POST['id']); // Sanitize the input
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    // Validate inputs
    if (empty($username) || empty($email)) {
        $error_message = "Please fill in all fields.";
    } else {
        // Update user details
        $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $username, $email, $user_id);

        if ($stmt->execute()) {
            echo "<script>alert('User updated successfully!');</script>";
            echo "<script>window.location.href = 'admin_dashboard.php';</script>";
            exit();
        } else {
            $error_message = "Error updating user: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
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
    </style>
</head>

<body>
    <div class="logo">
        <img src="Images/Devansh%20Car%20Customization%20logo%201.jpg" alt="Devansh Car Customization Logo">
    </div>

    <div class="form-container">
        <h2>Edit User</h2>
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
        <?php if (!empty($row)): ?>
            <form method="POST" action="edit_user.php">
                <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                <label for="username">Username:</label>
                <input type="text" name="username" value="<?= htmlspecialchars($row['username']) ?>" required><br>
                <label for="email">Email:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>" required><br>
                <button type="submit">Update</button>
            </form>
        <?php else: ?>
            <p>No user data available.</p>
        <?php endif; ?>
    </div>
</body>
</html>