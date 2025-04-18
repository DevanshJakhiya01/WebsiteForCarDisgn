<?php
// Start the session to access any session variables
session_start();

// Check if payment was actually successful (in a real app, you'd verify with payment gateway)
if (!isset($_SESSION['payment_successful']) || $_SESSION['payment_successful'] !== true) {
    header("Location: checkout.php");
    exit();
}

// Clear the payment flag to prevent refresh issues
unset($_SESSION['payment_successful']);

// You might want to get order details from session or database
$order_number = isset($_SESSION['order_number']) ? $_SESSION['order_number'] : 'N/A';
$amount_paid = isset($_SESSION['amount_paid']) ? number_format($_SESSION['amount_paid'], 2) : '0.00';

// Clear order details from session if needed
unset($_SESSION['order_number']);
unset($_SESSION['amount_paid']);

// Set page title
$page_title = "Payment Successful";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .success-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
            max-width: 600px;
            width: 90%;
        }
        .success-icon {
            color: #4CAF50;
            font-size: 72px;
            margin-bottom: 20px;
        }
        h1 {
            color: #4CAF50;
            margin-bottom: 20px;
        }
        .order-details {
            background-color: #f9f9f9;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        .btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 24px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 10px 5px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">✓</div>
        <h1>Payment Successful!</h1>
        <p>Thank you for your purchase. Your payment has been processed successfully.</p>
        
        <div class="order-details">
            <h3>Order Details</h3>
            <div class="detail-row">
                <span class="detail-label">Order Number:</span>
                <span><?php echo htmlspecialchars($order_number); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Amount Paid:</span>
                <span>$<?php echo htmlspecialchars($amount_paid); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Date:</span>
                <span><?php echo date('F j, Y, g:i a'); ?></span>
            </div>
        </div>
        
        <p>A confirmation email has been sent to your registered email address.</p>
        
        <div>
            <a href="order_details.php?order=<?php echo urlencode($order_number); ?>" class="btn">View Order Details</a>
            <a href="index.php" class="btn btn-secondary">Back to Home</a>
        </div>
    </div>
</body>
</html>