<?php
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

// Retrieve data
$user = $_POST['username'];
$pass = $_POST['password'];

// Hash the password for security
$hashed_password = password_hash($pass, PASSWORD_DEFAULT);

// Check if user already exists
$sql = "SELECT * FROM users WHERE username='$user'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Username already exists.";
} else {
    // Insert user
    $sql = "INSERT INTO users (username, password) VALUES ('$user', '$hashed_password')";
    if ($conn->query($sql) === TRUE) {
        // Redirect to login page after successful signup
        header("Location: First page for project website.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>