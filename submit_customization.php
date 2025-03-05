<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "car_customization_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    die(json_encode(["success" => false, "message" => "Invalid input data."]));
}

// Extract data
$carName = $conn->real_escape_string($data['name']);
$wheels = $conn->real_escape_string($data['wheels']);
$paint = $conn->real_escape_string($data['paint']);

// Insert into database
$sql = "INSERT INTO customizations (car_name, wheels, paint) VALUES ('$carName', '$wheels', '$paint')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true, "message" => "Customization saved successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Error: " . $sql . "<br>" . $conn->error]);
}

$conn->close();
?>