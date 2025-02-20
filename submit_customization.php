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

// Create users table if not exists
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "Table users created successfully\n";
} else {
    echo "Error creating users table: " . $conn->error;
}

// Create orders table if not exists
$sql = "CREATE TABLE IF NOT EXISTS orders (
    order_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    quantity INT(11) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'Pending',
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table orders created successfully\n";
} else {
    echo "Error creating orders table: " . $conn->error;
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
