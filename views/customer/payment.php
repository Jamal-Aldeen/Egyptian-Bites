<?php
session_start();
require_once __DIR__ . '/../../controllers/PaymentController.php';
// require_once __DIR__ . '/../controllers/config/stripe.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['order_id']) || !isset($_SESSION['amount'])) {
    header("Location: /");
    exit();
}

$paymentController = new PaymentController();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $payment_method = $_POST['payment_method'];
    
    if ($payment_method == 'card' && isset($_POST['stripeToken'])) {

        $result = $paymentController->processPayment($_SESSION['order_id'], $_SESSION['user_id'], $payment_method, $_SESSION['amount']);
    } else if ($payment_method == 'cash') {

        $result = $paymentController->processPayment($_SESSION['order_id'], $_SESSION['user_id'], $payment_method, $_SESSION['amount']);
    }
    
    if ($result['status'] == 'success') {
        $user_id = $_SESSION['user_id'];  // Assuming the user is logged in
$title = "New Order Confirmation";
$message = "Your order has been successfully placed! We will notify you once it's ready.";

// Insert the notification into the database
$notification_query = "INSERT INTO notifications (user_id, title, message) VALUES (:user_id, :title, :message)";
$stmt = $pdo->prepare($notification_query);
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':title', $title);
$stmt->bindParam(':message', $message);
$stmt->execute();
        header("Location: /views/customer/order-tracking.php"); 
        exit();
    } else {
        $error = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Choose Payment Method</h2>
    
    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <form method="POST" action="">
        <div class="form-check">
            <input type="radio" name="payment_method" value="card" class="form-check-input" id="card" required>
            <label class="form-check-label" for="card">Credit/Debit Card</label>
        </div>

        <div class="form-check">
            <input type="radio" name="payment_method" value="cash" class="form-check-input" id="cash" required>
            <label class="form-check-label" for="cash">Cash on Delivery</label>
        </div>

        <div id="card-section" style="display: none;">
            <div class="form-group mt-4">
                <div id="card-element" class="form-control"></div> 
                <div id="card-errors" class="text-danger" role="alert"></div> 
            </div>
            <button id="stripe-button" class="btn btn-primary mt-3">Pay with Stripe</button>
        </div>

        <button type="submit" class="btn btn-success mt-3">Proceed to Payment</button>
    </form>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
document.querySelectorAll('input[name="payment_method"]').forEach(input => {
    input.addEventListener('change', function() {
        document.getElementById('card-section').style.display = this.value === 'card' ? 'block' : 'none';
    });
});

var stripe = Stripe('<?php echo STRIPE_PUBLISHABLE_KEY; ?>');
var elements = stripe.elements();
var card = elements.create('card');
card.mount('#card-element');

var form = document.querySelector('form');
form.addEventListener('submit', function(event) {
    event.preventDefault();  

    if (document.querySelector('input[name="payment_method"]:checked').value === 'card') {

        stripe.createToken(card).then(function(result) {
            if (result.error) {
                var errorElement = document.getElementById('card-errors');
                errorElement.textContent = result.error.message;
            } else {

                var tokenInput = document.createElement('input');
                tokenInput.setAttribute('type', 'hidden');
                tokenInput.setAttribute('name', 'stripeToken');
                tokenInput.setAttribute('value', result.token.id);
                form.appendChild(tokenInput);

                form.submit();
            }
        });
    } else {
        form.submit();
    }
});
</script>



</body>
</html>
