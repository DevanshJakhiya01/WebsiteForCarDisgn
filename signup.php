<?php
$servername = "localhost";
$username = "root"; 
$password = "";     
$dbname = "car_customization_db"; 


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $username = $_POST['username'];
    $password = $_POST['password'];


    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashed_password); 

    if ($stmt->execute()) {
        echo "Signup successful. You can now <a href='login_signup.html'>login</a>.";
    } else {
        echo "Error: " . $stmt->error; 
    }

    $stmt->close();
}

$conn->close();
?>