<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../../controllers/OrderController.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_data'])) {
    try {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('You must be logged in to place an order');
        }

        $cart = json_decode($_POST['cart_data'], true);
        var_dump($cart);  

        foreach ($cart as $item) {
            if (!isset($item['category_id']) || !is_numeric($item['category_id'])) {
                echo "Invalid category ID for item " . $item['name'];
                exit;
            }
        }
        
        if (empty($cart)) {
            throw new Exception('Cart is empty');
        }

        $total = array_reduce($cart, function($sum, $item) {
            return $sum + ($item['price'] * $item['quantity']);
        }, 0);

        $orderController = new OrderController();
        $order_id = $orderController->placeOrder($_SESSION['user_id'], $cart, $total); 

        if ($order_id) {
            $_SESSION['order_id'] = $order_id;
            $_SESSION['amount'] = $total;
            $success = 'Order placed successfully! You can now proceed to payment.';

            header('Location: /views/customer/payment.php');
            exit;
        } else {
            throw new Exception('Failed to place order');
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Order Confirmation</title>
</head>
<body>
    <h2>Order Confirmation</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
</body>
</html>
