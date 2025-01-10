<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "car_customization";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$new_username = $_POST['new_username'];
$new_password = $_POST['new_password'];

$sql = "INSERT INTO users (username, password) VALUES ('$new_username', '$new_password')";

if ($conn->query($sql) === TRUE) {
    echo "Sign up successful!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>

<?php
$conn = new mysqli('localhost', 'root', '', 'car_customization_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";

if ($conn->query($sql) === TRUE) {
    echo "Signup successful. You can now <a href='login_signup.html'>login</a>.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>

