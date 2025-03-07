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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = intval($_POST['order_id']); // Sanitize input
    $payment_amount = floatval($_POST['payment_amount']); // Sanitize input
    $payment_method = trim($_POST['payment_method']);
    $card_number = trim($_POST['card_number']);
    $expiry_date = trim($_POST['expiry_date']);
    $cvv = trim($_POST['cvv']);
    $upi_id = trim($_POST['upi_id']); // UPI ID field

    // Validate inputs
    if (empty($order_id) || empty($payment_amount) || empty($payment_method)) {
        $error_message = "Please fill in all fields.";
    } else {
        // Additional validation for card details if payment method is not UPI
        if ($payment_method !== 'upi' && (empty($card_number) || empty($expiry_date) || empty($cvv))) {
            $error_message = "Please fill in all card details.";
        } elseif ($payment_method === 'upi' && empty($upi_id)) {
            $error_message = "Please enter your UPI ID.";
        } else {
            // Insert payment details into the database
            $sql = "INSERT INTO order_payments (order_id, payment_amount, payment_method, card_number, expiry_date, cvv, upi_id, payment_status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("idsssss", $order_id, $payment_amount, $payment_method, $card_number, $expiry_date, $cvv, $upi_id);

            if ($stmt->execute()) {
                $success_message = "Payment submitted successfully!";
            } else {
                $error_message = "Error submitting payment: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Payment</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
            background-image: url("Images/doddles\ of\ car\ in\ whole\ page\ in\ pink\ and\ red\ color\ for\ website\ background.jpg");
            background-size: cover;
            color: red;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .logo {
            width: 300px;
            margin-bottom: 20px;
        }
        .logo img {
            width: 100%;
            height: auto;
            display: block;
        }
        .form-container {
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            border-radius: 10px;
            text-align: center;
        }
        .form-container input, .form-container select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-container button {
            width: 100%;
            padding: 10px;
            background-color: darksalmon;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .form-container button:hover {
            background-color: #e9967a;
        }
        .error-message {
            color: red;
            margin-bottom: 10px;
        }
        .success-message {
            color: green;
            margin-bottom: 10px;
        }
        .upi-field {
            display: none; /* Hide UPI field by default */
        }
    </style>
    <script>
        function toggleUPIField() {
            const paymentMethod = document.querySelector('select[name="payment_method"]').value;
            const upiField = document.getElementById('upi-field');

            if (paymentMethod === 'upi') {
                upiField.style.display = 'block'; // Show UPI field
            } else {
                upiField.style.display = 'none'; // Hide UPI field
            }
        }
    </script>
</head>

<body>
    <div class="logo">
        <img src="Images/Devansh%20Car%20Customization%20logo%201.jpg" alt="Devansh Car Customization Logo">
    </div>

    <div class="form-container">
        <h2>Order Payment</h2>
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
        <?php if (isset($success_message)): ?>
            <div class="success-message"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>
        <form action="order_payment.php" method="POST" onsubmit="return validateForm()">
            <label for="order_id">Order ID:</label>
            <input type="number" name="order_id" placeholder="Order ID" required>

            <label for="payment_amount">Payment Amount:</label>
            <input type="number" step="0.01" name="payment_amount" placeholder="Payment Amount" required>

            <label for="payment_method">Payment Method:</label>
            <select name="payment_method" onchange="toggleUPIField()" required>
                <option value="credit_card">Credit Card</option>
                <option value="debit_card">Debit Card</option>
                <option value="paypal">PayPal</option>
                <option value="upi">UPI</option>
                <option value="cash_on_delivery">Cash on Delivery</option>
            </select>

            <!-- Card Details (Hidden for UPI) -->
            <div id="card-details">
                <label for="card_number">Card Number:</label>
                <input type="text" name="card_number" placeholder="Card Number">

                <label for="expiry_date">Expiry Date:</label>
                <input type="text" name="expiry_date" placeholder="MM/YY">

                <label for="cvv">CVV:</label>
                <input type="text" name="cvv" placeholder="CVV">
            </div>

            <!-- UPI Field (Hidden by Default) -->
            <div id="upi-field" class="upi-field">
                <label for="upi_id">UPI ID:</label>
                <input type="text" name="upi_id" placeholder="UPI ID">
            </div>

            <button type="submit">Submit Payment</button>
        </form>
    </div>

    <script>
        // Function to toggle UPI field visibility
        function toggleUPIField() {
            const paymentMethod = document.querySelector('select[name="payment_method"]').value;
            const cardDetails = document.getElementById('card-details');
            const upiField = document.getElementById('upi-field');

            if (paymentMethod === 'upi') {
                cardDetails.style.display = 'none'; // Hide card details
                upiField.style.display = 'block'; // Show UPI field
            } else {
                cardDetails.style.display = 'block'; // Show card details
                upiField.style.display = 'none'; // Hide UPI field
            }
        }

        // Function to validate form before submission
        function validateForm() {
            const paymentMethod = document.querySelector('select[name="payment_method"]').value;
            const upiId = document.querySelector('input[name="upi_id"]').value;

            if (paymentMethod === 'upi' && upiId.trim() === '') {
                alert('Please enter your UPI ID.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>