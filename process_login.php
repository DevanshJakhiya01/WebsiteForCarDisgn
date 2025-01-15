<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "car_customization_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve data
$user = $_POST['username'];
$pass = $_POST['password'];

// Verify user
$sql = "SELECT * FROM users WHERE username='$user' AND password='$pass'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Redirect to home page
    header("Location: First page for project website.php");
    exit();
} else {
    echo "Invalid username or password.";
}

$conn->close();
?>
