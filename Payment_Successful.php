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

// Verify payment was successful and get payment details
if (!isset($_GET['payment_id']) {
    header("Location: cart.php");
    exit();
}

$payment_id = $conn->real_escape_string($_GET['payment_id']);

// Update orders in database
if (isset($_SESSION['current_order'])) {
    $receipt = $conn->real_escape_string($_SESSION['current_order']['receipt']);
    $order_ids = $_SESSION['current_order']['order_ids'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update each order with payment details
        foreach ($order_ids as $order_id) {
            $stmt = $conn->prepare("UPDATE orders SET 
                                  status = 'completed', 
                                  payment_id = ?, 
                                  updated_at = NOW() 
                                  WHERE id = ?");
            $stmt->bind_param("si", $payment_id, $order_id);
            $stmt->execute();
            $stmt->close();
        }
        
        // Create payment record
        $stmt = $conn->prepare("INSERT INTO payments 
                               (receipt_number, payment_id, amount, status, created_at) 
                               VALUES (?, ?, ?, 'completed', NOW())");
        $amount = $_SESSION['current_order']['total_amount'] / 100; // Convert back from paise
        $stmt->bind_param("ssd", $receipt, $payment_id, $amount);
        $stmt->execute();
        $stmt->close();
        
        // Commit transaction
        $conn->commit();
        
        // Get order details for display
        $order_details = [];
        $stmt = $conn->prepare("SELECT product_name, amount FROM orders WHERE receipt = ?");
        $stmt->bind_param("s", $receipt);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $order_details[] = $row;
        }
        $stmt->close();
        
        // Clear session data
        unset($_SESSION['current_order']);
        unset($_SESSION['cart']);
        
        // Set success flag
        $payment_successful = true;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $error_message = "Error processing your payment: " . $e->getMessage();
        $payment_successful = false;
    }
} else {
    $error_message = "No order found to process. Please try again.";
    $payment_successful = false;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Devansh Car Customization</title>
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
            max-width: 800px;
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
        h1 {
            color: #4CAF50;
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #4CAF50;
            font-size: 2.2rem;
        }
        .success-icon {
            text-align: center;
            margin: 20px 0;
        }
        .success-icon svg {
            width: 100px;
            height: 100px;
            fill: #4CAF50;
        }
        .order-summary {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .summary-label {
            font-weight: bold;
            color: #555;
        }
        .order-items {
            margin-top: 20px;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed #ddd;
        }
        .item-name {
            flex: 2;
        }
        .item-price {
            flex: 1;
            text-align: right;
            font-weight: bold;
        }
        .total-row {
            font-size: 1.2rem;
            font-weight: bold;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid #ddd;
        }
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }
        .btn {
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-primary {
            background-color: #4CAF50;
            color: white;
            border: none;
        }
        .btn-primary:hover {
            background-color: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-secondary {
            background-color: #f8f9fa;
            color: #333;
            border: 1px solid #ddd;
        }
        .btn-secondary:hover {
            background-color: #e9ecef;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .error-message {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="Images/Devansh%20Car%20Customization%20logo%201.jpg" alt="Devansh Car Customization Logo">
        </div>
        
        <?php if ($payment_successful): ?>
            <h1>Payment Successful!</h1>
            
            <div class="success-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
            </div>
            
            <p style="text-align: center; font-size: 1.1rem;">Thank you for your purchase! Your order has been confirmed.</p>
            
            <div class="order-summary">
                <h3>Order Summary</h3>
                
                <div class="summary-row">
                    <span class="summary-label">Order Number:</span>
                    <span><?= htmlspecialchars($receipt) ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Payment ID:</span>
                    <span><?= htmlspecialchars($payment_id) ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Date:</span>
                    <span><?= date('F j, Y, g:i a') ?></span>
                </div>
                
                <div class="order-items">
                    <h4>Order Items:</h4>
                    <?php foreach ($order_details as $item): ?>
                        <div class="order-item">
                            <span class="item-name"><?= htmlspecialchars($item['product_name']) ?></span>
                            <span class="item-price">$<?= number_format($item['amount'], 2) ?></span>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="order-item total-row">
                        <span class="item-name">Total Paid:</span>
                        <span class="item-price">$<?= number_format($amount, 2) ?></span>
                    </div>
                </div>
            </div>
            
            <p style="text-align: center; margin-top: 20px;">A confirmation email has been sent to your registered email address.</p>
            
            <div class="action-buttons">
                <a href="order_details.php?order=<?= urlencode($receipt) ?>" class="btn btn-primary">View Order Details</a>
                <a href="index.php" class="btn btn-secondary">Continue Shopping</a>
            </div>
            
        <?php else: ?>
            <h1>Payment Processing Error</h1>
            <div class="error-message">
                <?= htmlspecialchars($error_message) ?>
            </div>
            <div class="action-buttons">
                <a href="cart.php" class="btn btn-primary">Back to Cart</a>
                <a href="index.php" class="btn btn-secondary">Continue Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>