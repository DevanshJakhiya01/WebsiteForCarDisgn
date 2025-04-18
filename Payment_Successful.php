<?php
session_start();

// Verify payment was successful (you would typically verify with Razorpay API here)
$payment_id = $_GET['payment_id'] ?? '';
$order_info = $_SESSION['current_order'] ?? null;

// Clear the cart after successful payment
if ($order_info) {
    unset($_SESSION['cart']);
    unset($_SESSION['current_order']);
}
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
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            width: 90%;
            max-width: 800px;
            margin: 30px auto;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo img {
            max-width: 250px;
            height: auto;
        }
        h1 {
            color: #2ecc71;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.2rem;
        }
        .success-icon {
            font-size: 80px;
            color: #2ecc71;
            margin: 20px 0;
        }
        .order-details {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin: 30px 0;
            text-align: left;
            border-left: 5px solid #2ecc71;
        }
        .detail-row {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #ddd;
        }
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .detail-label {
            font-weight: bold;
            width: 150px;
            color: #555;
        }
        .detail-value {
            flex: 1;
            color: #333;
        }
        .total-amount {
            font-size: 1.5rem;
            color: #d10000;
            font-weight: bold;
            margin-top: 10px;
        }
        .home-button {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 30px;
            background-color: #d10000;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.3s;
        }
        .home-button:hover {
            background-color: #a00000;
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(209, 0, 0, 0.3);
        }
        .thank-you-message {
            font-size: 1.2rem;
            line-height: 1.6;
            margin-bottom: 30px;
            color: #555;
        }
        .contact-info {
            margin-top: 40px;
            font-size: 1rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="Images/Devansh%20Car%20Customization%20logo%201.jpg" alt="Devansh Car Customization Logo">
        </div>
        
        <div class="success-icon">✓</div>
        
        <h1>Payment Successful!</h1>
        
        <div class="thank-you-message">
            <p>Thank you for your order! Your payment has been processed successfully.</p>
            <p>We've sent an order confirmation to your email. Your custom car will be ready soon!</p>
        </div>
        
        <?php if ($order_info): ?>
        <div class="order-details">
            <h3 style="margin-top: 0; color: #d10000;">Order Details</h3>
            
            <div class="detail-row">
                <div class="detail-label">Order Number:</div>
                <div class="detail-value"><?= htmlspecialchars($order_info['receipt']) ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Payment ID:</div>
                <div class="detail-value"><?= htmlspecialchars($payment_id) ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Product:</div>
                <div class="detail-value"><?= htmlspecialchars($order_info['product_name']) ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Total Paid:</div>
                <div class="detail-value">
                    ₹<?= number_format($order_info['total_amount'] / 100, 2) ?>
                    <span class="total-amount"></span>
                </div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Status:</div>
                <div class="detail-value" style="color: #2ecc71; font-weight: bold;">Completed</div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="contact-info">
            <p>If you have any questions, please contact us at <strong>support@devanshcustomcars.com</strong></p>
        </div>
        
        <a href="http://localhost/project/WebsiteForCarDisgn/First%20page%20for%20project%20website.php" class="home-button">Back to Home</a>
    </div>
</body>
</html>