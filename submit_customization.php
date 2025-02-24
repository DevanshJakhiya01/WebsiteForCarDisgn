<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "car_customization_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => "Connection failed: " . $conn->connect_error]));
}

// Get data from the request
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($data === null) {
    die(json_encode(['success' => false, 'message' => 'Invalid JSON data.']));
}

$name = $data['name'];
$wheels = $data['wheels'];
$paint = $data['paint'];

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO orders (car_name, wheels, paint) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $wheels, $paint);

// Execute the statement
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Order placed successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error placing order: ' . $stmt->error]);
}

// Close connection
$stmt->close();
$conn->close();
?>
