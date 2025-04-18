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
        // Start transaction for atomic operations
        $conn->begin_transaction();
        
        try {
            // Fetch a valid user_id from the users table
            $sql = "SELECT id FROM users LIMIT 1";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $user_id = $row['id'];
                $receipt = "ORD_" . strtoupper(uniqid()); // Better receipt format
                $order_ids = [];
                $total_amount = 0;

                foreach ($_SESSION['cart'] as $item) {
                    // Validate and sanitize data
                    $car_name = $conn->real_escape_string($item['car_name'] ?? '');
                    $wheels = $conn->real_escape_string($item['wheels'] ?? '');
                    $paint = $conn->real_escape_string($item['paint'] ?? '');
                    $price = (float)($item['price'] ?? 0);
                    $total_amount += $price;

                    // Combine car details for product_name
                    $product_name = "$car_name with $wheels wheels and $paint paint";
                    
                    // Prepared statement for security
                    $stmt = $conn->prepare("INSERT INTO orders 
                        (user_id, car_name, product_name, amount, receipt, status, created_at) 
                        VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
                    
                    if (!$stmt) {
                        throw new Exception("Prepare failed: " . $conn->error);
                    }
                    
                    $stmt->bind_param("issds", $user_id, $car_name, $product_name, $price, $receipt);

                    if (!$stmt->execute()) {
                        throw new Exception("Execute failed: " . $stmt->error);
                    }
                    
                    $order_ids[] = $conn->insert_id;
                    $stmt->close();
                }

                // Store order information in session
                $_SESSION['current_order'] = [
                    'receipt' => $receipt,
                    'order_ids' => $order_ids,
                    'total_amount' => $total_amount * 100, // Razorpay expects amount in paise
                    'product_name' => $product_name
                ];

                // Commit transaction
                $conn->commit();

                // Show payment button instead of redirecting
                $show_payment_button = true;
            } else {
                throw new Exception("No users found in the database. Please add a user first.");
            }
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
        }
    } else {
        echo "<script>alert('Your cart is empty.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shopping Cart - Devansh Car Customization</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-image: url("Images/doddles%20of%20car%20in%20whole%20page%20in%20pink%20and%20red%20color%20for%20website%20background.jpg");
            background-size: cover;
            background-attachment: fixed;
            color: #333;
            min-height: 100vh;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 30px auto;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo img {
            max-width: 300px;
            height: auto;
            transition: transform 0.3s;
        }
        .logo img:hover {
            transform: scale(1.05);
        }
        h1 {
            color: #d10000;
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #d10000;
            font-size: 2.2rem;
        }
        .cart-items {
            margin-bottom: 40px;
        }
        .cart-item {
            background-color: white;
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            position: relative;
            border-left: 5px solid #d10000;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .cart-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }
        .remove-button {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #d10000;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
        .remove-button:hover {
            background-color: #a00000;
            transform: scale(1.05);
        }
        .item-title {
            font-size: 1.5rem;
            color: #d10000;
            margin-bottom: 15px;
        }
        .item-detail {
            margin: 10px 0;
            font-size: 1.1rem;
            color: #555;
        }
        .price {
            font-weight: bold;
            color: #d10000;
            font-size: 1.3rem;
            margin-top: 15px;
        }
        .total-section {
            text-align: right;
            margin: 30px 0;
            padding-top: 20px;
            border-top: 2px dashed #ddd;
        }
        .total-amount {
            font-size: 1.8rem;
            color: #d10000;
            font-weight: bold;
        }
        .proceed-button {
            display: block;
            width: 250px;
            margin: 30px auto 0;
            padding: 15px;
            background-color: #d10000;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
            transition: all 0.3s;
        }
        .proceed-button:hover {
            background-color: #a00000;
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(209, 0, 0, 0.3);
        }
        .empty-cart {
            text-align: center;
            padding: 50px 0;
            font-size: 1.3rem;
            color: #666;
        }
        .empty-cart a {
            color: #d10000;
            text-decoration: none;
            font-weight: bold;
        }
        .empty-cart a:hover {
            text-decoration: underline;
        }
        /* Razorpay button styles */
        .razorpay-payment-button {
            display: block;
            width: 250px;
            margin: 30px auto 0;
            padding: 15px;
            background-color: #2d8edd;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
            transition: all 0.3s;
        }
        .razorpay-payment-button:hover {
            background-color: #1a73c8;
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(45, 142, 221, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="Images/Devansh%20Car%20Customization%20logo%201.jpg" alt="Devansh Car Customization Logo">
        </div>
        <h1>Your Shopping Cart</h1>

        <?php if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])): ?>
            <div class="empty-cart">
                <p>Your cart is currently empty.</p>
                <p><a href="http://localhost/project/WebsiteForCarDisgn/First%20page%20for%20project%20website.php">Continue shopping</a></p>
            </div>
        <?php else: ?>
            <div class="cart-items">
                <?php 
                $total = 0;
                foreach ($_SESSION['cart'] as $index => $item): 
                    $total += $item['price'];
                ?>
                    <div class="cart-item">
                        <button class="remove-button" onclick="window.location.href='cart.php?remove_index=<?= $index ?>'">
                            Remove Item
                        </button>
                        <h2 class="item-title"><?= htmlspecialchars($item['car_name']) ?></h2>
                        <p class="item-detail"><strong>Wheels:</strong> <?= htmlspecialchars($item['wheels']) ?></p>
                        <p class="item-detail"><strong>Paint:</strong> <?= htmlspecialchars($item['paint']) ?></p>
                        <p class="price">Price: $<?= number_format($item['price'], 2) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="total-section">
                <h3>Order Total: <span class="total-amount">$<?= number_format($total, 2) ?></span></h3>
            </div>

            <?php if (isset($show_payment_button) && $show_payment_button): ?>
                <!-- Razorpay Payment Button -->
                <button id="rzp-button1" class="razorpay-payment-button">Pay Now</button>
                <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
                <script>
                var options = {
                    "key": "rzp_test_LX93MSrwBis0CQ", // Your Razorpay Key ID
                    "amount": "<?= $_SESSION['current_order']['total_amount'] ?>", // Amount is in paise
                    "currency": "INR",
                    "name": "Devansh Car Customization",
                    "description": "Order #<?= $_SESSION['current_order']['receipt'] ?>",
                    "image": "Images/Devansh%20Car%20Customization%20logo%201.jpg",
                    "order_id": "", // This will be generated server-side
                    "handler": function (response){
                        // Handle successful payment
                        alert("Payment successful! Payment ID: " + response.razorpay_payment_id);
                        // Redirect to thank you page or process further
                        window.location.href = "payment_success.php?payment_id=" + response.razorpay_payment_id;
                    },
                    "prefill": {
                        "name": "Customer Name", // You can get this from your user data
                        "email": "customer@example.com",
                        "contact": "9000000000"
                    },
                    "notes": {
                        "order_id": "<?= $_SESSION['current_order']['receipt'] ?>"
                    },
                    "theme": {
                        "color": "#d10000"
                    }
                };
                
                // Create order first (you'll need to implement this server-side)
                // Then set the order_id in options and open the payment dialog
                var rzp1 = new Razorpay(options);
                
                document.getElementById('rzp-button1').onclick = function(e){
                    // You might want to create an order first via AJAX here
                    // Then set options.order_id and open the dialog
                    rzp1.open();
                    e.preventDefault();
                }
                </script>
            <?php else: ?>
                <form method="POST" action="cart.php">
                    <button type="submit" name="proceed_to_payment" class="proceed-button">
                        Proceed to Payment
                    </button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>