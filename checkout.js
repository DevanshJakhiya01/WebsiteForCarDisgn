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