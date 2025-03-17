<?php
session_start(); // Start the session to access cart data

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

// Handle item removal
if (isset($_GET['remove_index'])) {
    $index = $_GET['remove_index'];
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]); // Remove the item from the cart
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index the array
    }
    header("Location: cart.php"); // Refresh the page
    exit();
}

// Handle order submission
if (isset($_POST['proceed_to_payment'])) {
    if (!empty($_SESSION['cart'])) {
        // Fetch a valid user_id from the users table (example: first user)
        $sql = "SELECT id FROM users LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user_id = $row['id']; // Use the first user's ID

            // Insert each cart item into the orders table
            foreach ($_SESSION['cart'] as $item) {
                $car_name = $item['car_name'];
                $wheels = $item['wheels'];
                $paint = $item['paint'];
                $price = $item['price'];

                // Insert into orders table
                $stmt = $conn->prepare("INSERT INTO orders (user_id, car_name, wheels, paint, total_amount, status) VALUES (?, ?, ?, ?, ?, 'pending')");
                $stmt->bind_param("isssd", $user_id, $car_name, $wheels, $paint, $price);

                if (!$stmt->execute()) {
                    echo "<script>alert('Error submitting order: " . $stmt->error . "');</script>";
                    exit();
                }
                $stmt->close();
            }

            // Clear the cart after successful order submission
            unset($_SESSION['cart']);

            // Redirect to the payment page
            header("Location: order_payment.php");
            exit();
        } else {
            echo "<script>alert('No users found in the database. Please add a user first.');</script>";
        }
    } else {
        echo "<script>alert('Your cart is empty.');</script>";
    }
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cart</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
            background-image: url("Images/doddles%20of%20car%20in%20whole%20page%20in%20pink%20and%20red%20color%20for%20website%20background.jpg");
            background-size: auto;
            color: red;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .cart-item {
            background-color: white;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            width: 80%;
            max-width: 600px;
            position: relative;
        }
        .remove-button {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
            font-size: 14px;
        }
        .remove-button:hover {
            background-color: darkred;
        }
        .proceed-button {
            padding: 12px 24px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        .proceed-button:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>
    <div class="logo">
        <img src="Images/Devansh%20Car%20Customization%20logo%201.jpg" alt="Devansh Car Customization Logo">
    </div>
    <h1>Your Cart</h1>

    <?php foreach ($_SESSION['cart'] as $index => $item): ?>
        <div class="cart-item">
            <!-- Remove Button -->
            <button class="remove-button" onclick="window.location.href='cart.php?remove_index=<?= $index ?>'">Remove</button>
            <h2><?= htmlspecialchars($item['car_name']) ?></h2>
            <p>Wheels: <?= htmlspecialchars($item['wheels']) ?></p>
            <p>Paint: <?= htmlspecialchars($item['paint']) ?></p>
            <p>Price: $<?= number_format($item['price'], 2) ?></p>
        </div>
    <?php endforeach; ?>

    <!-- Proceed to Payment Form -->
    <form method="POST" action="">
        <button type="submit" name="proceed_to_payment" class="proceed-button">Proceed to Payment</button>
    </form>
</body>
</html>