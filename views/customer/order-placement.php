<?php
session_start();
require_once __DIR__ . '/../../controllers/OrderController.php';

$cart_data = json_decode($_POST['cart_data'], true);
$user_id = $_SESSION['user_id']; // تأكد من أن المستخدم مسجل الدخول

$total_price = array_reduce($cart_data, function ($acc, $item) {
    return $acc + ($item['price'] * $item['quantity']);
}, 0);

$orderController = new OrderController();
$orderController->placeOrder($user_id, $cart_data, $total_price);

// توجيه المستخدم إلى صفحة تأكيد الطلب
header("Location: /views/customer/order-confirmation.php");
exit();
?>