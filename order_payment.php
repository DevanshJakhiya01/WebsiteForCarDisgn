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
    $payment_amount = floatval($_POST['payment_amount']) * 100; //Amount in paise
    $payment_method = trim($_POST['payment_method']);
    $upi_id = trim($_POST['upi_id']); // UPI ID field

    // Validate inputs
    if (empty($order_id) || empty($payment_amount) || empty($payment_method)) {
        $error_message = "Please fill in all fields.";
    } elseif ($payment_method === 'upi' && empty($upi_id)) {
        $error_message = "Please enter your UPI ID.";
    } else {

        // Razorpay integration
        require('razorpay-php/Razorpay.php'); // Include Razorpay PHP library

        use Razorpay\Api\Api;
        use Razorpay\Api\Errors\SignatureVerificationError;

        $razorpayKeyId = 'YOUR_RAZORPAY_KEY_ID'; // Replace with your key
        $razorpayKeySecret = 'YOUR_RAZORPAY_KEY_SECRET'; // Replace with your secret

        try {
            $api = new Api($razorpayKeyId, $razorpayKeySecret);

            $orderData = [
                'receipt'         => 'order_' . time(),
                'amount'          => $payment_amount, // Amount in paise
                'currency'        => 'INR',
                'payment_capture' => 1 // auto capture
            ];

            $razorpayOrder = $api->order->create($orderData);
            $razorpay_order_id = $razorpayOrder['id'];

            // Store order details in the database
            $sql = "INSERT INTO payment (order_id, payment_amount, payment_method, upi_id, razorpay_order_id, payment_status) VALUES (?, ?, ?, ?, ?, 'pending')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("idsss", $order_id, $payment_amount / 100, $payment_method, $upi_id, $razorpay_order_id); // Divide by 100 to store amount in Rupees.

            if ($stmt->execute()) {

                // Redirect to Razorpay checkout
                ?>
                <!DOCTYPE html>
                <html>
                <head>
                    <title>PhonePe Payment</title>
                </head>
                <body>
                    <form id="paymentForm">
                        <script src="https://checkout.razorpay.com/v1/checkout.js"
                                data-key="<?php echo $razorpayKeyId; ?>"
                                data-amount="<?php echo $payment_amount; ?>"
                                data-currency="INR"
                                data-order_id="<?php echo $razorpay_order_id; ?>"
                                data-buttontext="Pay with UPI"
                                data-name="Your Company Name"
                                data-description="Payment for your order"
                                data-image="Your Company Logo URL"
                                data-prefill.name="Customer Name"
                                data-prefill.email="customer@example.com"
                                data-theme.color="#F37254"></script>
                        <input type="hidden" custom="Hidden Element">
                    </form>

                    <script type="text/javascript">
                        window.onload = function(){
                          document.forms['paymentForm'].submit()
                        };
                    </script>
                </body>
                </html>
                <?php
                exit; // Stop further execution
            } else {
                $error_message = "Error submitting payment details: " . $stmt->error;
            }

        } catch (Exception $e) {
            $error_message = 'Razorpay Order Creation Error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
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
                <option value="upi">UPI</option>
            </select>

            <div id="upi-field" class="upi-field">
                <label for="upi_id">UPI ID:</label>
                <input type="text" name="upi_id" placeholder="UPI ID">
            </div>

            <button type="submit">Submit Payment</button>
        </form>
    </div>

    <script>
        // ... (your JavaScript code) ...
    </script>
</body>
</html>

<?php
// Payment verification (webhook or redirect)
if (isset($_POST['razorpay_payment_id']) && isset($_POST['razorpay_order_id']) && isset($_POST['razorpay_signature'])) {

    $razorpay_payment_id = $_POST['razorpay_payment_id'];
    $razorpay_order_id = $_POST['razorpay_order_id'];
    $razorpay_signature = $_POST['razorpay_signature'];

    $attributes = array('razorpay_order_id' => $razorpay_order_id, 'razorpay_payment_id' => $razorpay_payment_id, 'razorpay_signature' => $razorpay_signature);

    try {
        $api->utility->verifyPaymentSignature($attributes);
        // Payment successful
        echo "Payment Successful. Payment ID: " . $razorpay_payment_id;

        // Update order status in the database
        $sql = "UPDATE payment SET payment_status = 'paid', razorpay_payment_id = ? WHERE razorpay_order_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $razorpay_payment_id, $razorpay_order_id);
        $stmt->execute();
    } catch (SignatureVerificationError $e) {
        $error = 'Razorpay Signature Verification Error: ' . $e->getMessage();
        echo $error;
        // Payment failed.
        $sql = "UPDATE payment SET payment_status = 'failed' WHERE razorpay_order_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $razorpay_order_id);
        $stmt->execute();
    }
    exit; //prevent any further html output.
}
?>