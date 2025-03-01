<?php
session_start();
require_once __DIR__ . '/../../controllers/CustomerController.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['order_id'])) {
    header("Location: /");
    exit();
}

$customerController = new CustomerController();
$orderDetails = $customerController->getOrderDetails($_POST['order_id']);

if (!$orderDetails) {
    echo "Invalid order items.";
    exit();
}
$_SESSION['cart'] = [];
foreach ($orderDetails['items'] as $item) {
    $_SESSION['cart'][] = [
        'id' => $item['menu_item_id'],
        'name' => $item['name'], 
        'price' => $item['price'],
        'quantity' => $item['quantity']
    ];
}

header("Location: /views/customer/cart.php?Reorder=success");
exit();

?>
<?php include '../layouts/header.php'; ?>

<?php include '../layouts/footer.php'; ?>
