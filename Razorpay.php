<?php

// Configuration
$razorpayKeyId = 'rzp_test_LX93MSrwBis0CQ';
$razorpayKeySecret = 'GVOVNcNqxlkFhrfQbLz7oowh'; // Added semicolon here
$amount = '16000'; // Amount in paise (e.g., 10000 paise = ₹100)
$currency = 'INR';
$receipt = 'order_' . time();
$order_id = ''; // Initialize order_id

// 1. Create Razorpay Order
require('razorpay-php/Razorpay.php'); // Include Razorpay PHP library (download from GitHub)

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

try {
    $api = new Api($razorpayKeyId, $razorpayKeySecret);

    $orderData = [
        'receipt'         => $receipt,
        'amount'          => $amount,
        'currency'        => $currency,
        'payment_capture' => 1 // auto capture
    ];

    $razorpayOrder = $api->order->create($orderData);
    $order_id = $razorpayOrder['id'];
} catch (Exception $e) {
    echo 'Razorpay Order Creation Error: ' . $e->getMessage();
    exit;
}

// 2. PhonePe Integration (using Razorpay's PhonePe method)
$phonePeData = [
    'order_id' => $order_id,
    'amount'   => $amount,
    'currency' => $currency,
    'method'   => 'phonepe' // Important: Use 'phonepe'
];

// 3. Display PhonePe Payment Form (using Razorpay's checkout.js)
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
                data-amount="<?php echo $amount; ?>"
                data-currency="<?php echo $currency; ?>"
                data-order_id="<?php echo $order_id; ?>"
                data-buttontext="Pay with PhonePe"
                data-name="Your Company Name"
                data-description="Payment for your order"
                data-image="Your Company Logo URL"
                data-prefill.name="Your Customer Name"
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
//4. Verify Payment (webhook or redirect)
// Example Webhook verification (recommended)
// Razorpay sends payment details to your webhook URL
// You should verify the signature and update your order status

// Example verification code (for webhooks or redirects)
if (isset($_POST['razorpay_payment_id']) && isset($_POST['razorpay_order_id']) && isset($_POST['razorpay_signature'])) {

    $razorpay_payment_id = $_POST['razorpay_payment_id'];
    $razorpay_order_id = $_POST['razorpay_order_id'];
    $razorpay_signature = $_POST['razorpay_signature'];

    $attributes = array('razorpay_order_id' => $razorpay_order_id, 'razorpay_payment_id' => $razorpay_payment_id, 'razorpay_signature' => $razorpay_signature);

    try {
        $api->utility->verifyPaymentSignature($attributes);
        // Payment successful
        echo "Payment Successful. Payment ID: " . $razorpay_payment_id;
        // Update your order status in your database
    } catch (SignatureVerificationError $e) {
        $error = 'Razorpay Signature Verification Error: ' . $e->getMessage();
        echo $error;
        // Payment failed
        // Update your order status in your database
    }
}
?>