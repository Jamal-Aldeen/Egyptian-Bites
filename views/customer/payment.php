<?php
session_start();
require_once __DIR__ . '/../../controllers/PaymentController.php';
require_once __DIR__ . '/../controllers/config/stripe.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['order_id']) || !isset($_SESSION['amount'])) {
    header("Location: /");
    exit();
}

$paymentController = new PaymentController();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $payment_method = $_POST['payment_method'];
    $result = $paymentController->processPayment($_SESSION['order_id'], $_SESSION['user_id'], $payment_method, $_SESSION['amount']);
    if ($result['status'] == 'success') {
        header("Location: /views/customer/success.php");
    } else {
        $error = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payment</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>

<div class="container">
    <h2>Choose Payment Method</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="POST" action="">
        <label>
            <input type="radio" name="payment_method" value="card" required> Credit/Debit Card
        </label>
        <label>
            <input type="radio" name="payment_method" value="cash" required> Cash on Delivery
        </label>

        <div id="card-section" style="display: none;">
            <script src="https://js.stripe.com/v3/"></script>
            <button id="stripe-button">Pay with Stripe</button>
        </div>

        <button type="submit">Proceed to Payment</button>
    </form>
</div>

<script>
document.querySelectorAll('input[name="payment_method"]').forEach(input => {
    input.addEventListener('change', function() {
        document.getElementById('card-section').style.display = this.value === 'card' ? 'block' : 'none';
    });
});
</script>

</body>
</html>
