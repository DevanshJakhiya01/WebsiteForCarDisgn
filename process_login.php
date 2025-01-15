<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "car_customization_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user = $_POST['username'];
$pass = $_POST['password'];

$sql = "SELECT * FROM users WHERE username='$user' AND password='$pass'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Login successful.";
} else {
    echo "Invalid username or password.";
}

$conn->close();
?>
