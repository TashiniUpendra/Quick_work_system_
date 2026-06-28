<?php
// Stripe Configuration (Test Mode)
// Get your keys from: https://dashboard.stripe.com/test/apikeys

define('STRIPE_PUBLISHABLE_KEY', 'pk_test_51O...your_key_here...'); 
define('STRIPE_SECRET_KEY', 'sk_test_51O...your_key_here...');

// Change this to your local URL
define('STRIPE_SUCCESS_URL', 'http://localhost/QuickWorks/payment_success.php');
define('STRIPE_CANCEL_URL', 'http://localhost/QuickWorks/payment.php');
?>
