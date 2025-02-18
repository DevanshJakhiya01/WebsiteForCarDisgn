<?php
// ... (database connection code)

$user = $_POST['username'];
$pass = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

// Check if user already exists (using parameterized query - see below)
$sql = "SELECT * FROM users WHERE username=?"; // Use a placeholder
$stmt = $conn->prepare($sql); // Prepare the statement
$stmt->bind_param("s", $user); // Bind the parameter
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows > 0) {
    echo "Username already exists.";
} else {
    // Insert user (using parameterized query)
    $sql = "INSERT INTO users (username, password) VALUES (?, ?)"; // Placeholders
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user, $pass); // Bind both parameters
    if ($stmt->execute()) {
        header("Location: First page for project website.php");
        exit();
    } else {
        echo "Error: " . $stmt->error; // More specific error message
    }
}

$stmt->close(); // Close the statement
$conn->close();
?>