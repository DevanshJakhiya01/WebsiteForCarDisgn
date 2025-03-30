<?php

// Configuration
$razorpayKeyId = 'rzp_test_LX93MSrwBis0CQ';
$razorpayKeySecret = 'GVOVNcNqxlkFhrfQbLz7oowh';
$amount = 16000; // Amount in paise (e.g., 16000 paise = ₹160) - Set a valid amount here!
$currency = 'INR';
$receipt = 'order_' . time();
$order_id = '';

// 1. Create Razorpay Order
$razorpayLibPath = __DIR__ . '/razorpay-php/Razorpay.php';

if (file_exists($razorpayLibPath)) {
    require($razorpayLibPath);

    use Razorpay\Api\Api;
    use Razorpay\Api\Errors\SignatureVerificationError;

    try {
        $api = new Api($razorpayKeyId, $razorpayKeySecret);

        $orderData = [
            'receipt'         => $receipt,
            'amount'          => $amount,
            'currency'        => $currency,
            'payment_capture' => 1
        ];

        $razorpayOrder = $api->order->create($orderData);
        $order_id = $razorpayOrder['id'];

        // 2. PhonePe Integration (using Razorpay's PhonePe method)
        $phonePeData = [
            'order_id' => $order_id,
            'amount'   => $amount,
            'currency' => $currency,
            'method'   => 'phonepe'
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
        // 4. Verify Payment (webhook or redirect)
        if (isset($_POST['razorpay_payment_id']) && isset($_POST['razorpay_order_id']) && isset($_POST['razorpay_signature'])) {

            $razorpay_payment_id = $_POST['razorpay_payment_id'];
            $razorpay_order_id = $_POST['razorpay_order_id'];
            $razorpay_signature = $_POST['razorpay_signature'];

            $attributes = array('razorpay_order_id' => $razorpay_order_id, 'razorpay_payment_id' => $razorpay_payment_id, 'razorpay_signature' => $razorpay_signature);

            try {
                $api->utility->verifyPaymentSignature($attributes);
                echo "Payment Successful. Payment ID: " . $razorpay_payment_id;
                // Update your order status in your database
            } catch (SignatureVerificationError $e) {
                echo 'Razorpay Signature Verification Error: ' . $e->getMessage();
                // Update your order status in your database
            }
        }
    } catch (Exception $e) {
        echo 'Razorpay Order Creation Error: ' . $e->getMessage();
        exit;
    }
} else {
    echo "Error: Razorpay PHP library not found. Please ensure it's placed in the 'razorpay-php' folder within the same directory as this script.";
}
?>