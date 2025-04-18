<?php
session_start();

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
    $index = (int)$_GET['remove_index'];
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
    }
    header("Location: cart.php");
    exit();
}

// Handle order submission
if (isset($_POST['proceed_to_payment'])) {
    if (!empty($_SESSION['cart'])) {
        // Fetch a valid user_id from the users table
        $sql = "SELECT id FROM users LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user_id = $row['id'];
            $receipt = "order_" . uniqid(); // Generate unique receipt ID
            $order_ids = []; // To store all order IDs for this transaction

            foreach ($_SESSION['cart'] as $item) {
                // Prepare data for insertion
                $car_name = $conn->real_escape_string($item['car_name']);
                $wheels = $conn->real_escape_string($item['wheels']);
                $paint = $conn->real_escape_string($item['paint']);
                $price = (float)$item['price'];

                // Insert into orders table
                $stmt = $conn->prepare("INSERT INTO orders 
                    (user_id, car_name, product_name, amount, receipt, status) 
                    VALUES (?, ?, ?, ?, ?, 'pending')");
                
                if (!$stmt) {
                    die("Prepare failed: " . $conn->error);
                }
                
                // Combine car details for product_name
                $product_name = "$car_name with $wheels wheels and $paint paint";
                $stmt->bind_param("issds", $user_id, $car_name, $product_name, $price, $receipt);

                if (!$stmt->execute()) {
                    echo "<script>alert('Error submitting order: " . $stmt->error . "');</script>";
                    exit();
                }
                $order_ids[] = $conn->insert_id;
                $stmt->close();
            }

            // Store order IDs in session for payment processing
            $_SESSION['current_order'] = [
                'receipt' => $receipt,
                'order_ids' => $order_ids,
                'total_amount' => array_sum(array_column($_SESSION['cart'], 'price'))
            ];

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shopping Cart</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-image: url("Images/doddles%20of%20car%20in%20whole%20page%20in%20pink%20and%20red%20color%20for%20website%20background.jpg");
            background-size: cover;
            background-attachment: fixed;
            color: #d10000;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 250px;
            height: auto;
        }
        h1 {
            color: #d10000;
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #d10000;
            padding-bottom: 10px;
        }
        .cart-item {
            background-color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            border-left: 5px solid #d10000;
        }
        .remove-button {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: #d10000;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .remove-button:hover {
            background-color: #a00000;
        }
        .proceed-button {
            display: block;
            width: 200px;
            margin: 30px auto 0;
            padding: 15px;
            background-color: #d10000;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            transition: background-color 0.3s;
        }
        .proceed-button:hover {
            background-color: #a00000;
        }
        .empty-cart {
            text-align: center;
            font-size: 18px;
            color: #666;
            padding: 50px 0;
        }
        .item-detail {
            margin: 8px 0;
            font-size: 16px;
        }
        .price {
            font-weight: bold;
            color: #d10000;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="Images/Devansh%20Car%20Customization%20logo%201.jpg" alt="Devansh Car Customization Logo">
        </div>
        <h1>Your Cart</h1>

        <?php foreach ($_SESSION['cart'] as $index => $item): ?>
            <div class="cart-item">
                <button class="remove-button" onclick="window.location.href='cart.php?remove_index=<?= $index ?>'">
                    Remove
                </button>
                <h2><?= htmlspecialchars($item['car_name']) ?></h2>
                <p class="item-detail">Wheels: <?= htmlspecialchars($item['wheels']) ?></p>
                <p class="item-detail">Paint: <?= htmlspecialchars($item['paint']) ?></p>
                <p class="item-detail price">Price: $<?= number_format($item['price'], 2) ?></p>
            </div>
        <?php endforeach; ?>

        <form method="POST" action="">
            <button type="submit" name="proceed_to_payment" class="proceed-button">
                Proceed to Payment
            </button>
        </form>
    </div>
</body>
</html>