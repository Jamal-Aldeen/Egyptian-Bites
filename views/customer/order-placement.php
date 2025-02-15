<?php
session_start();
require_once __DIR__ . '/../../controllers/OrderController.php';
require_once __DIR__ . '/../../config/db.php';

$orderController = new OrderController();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order'])) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = "You must be logged in to place an order!";
        header("Location: /views/customer/order-placement.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $items = json_decode($_POST['cart_data'], true);
    $total_price = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $items));

    if ($orderController->placeOrder($user_id, $items, $total_price)) {
        $_SESSION['success'] = "Your order has been placed successfully!";
    } else {
        $_SESSION['error'] = "An error occurred while placing the order.";
    }

    header("Location: /views/customer/order-placement.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Placement</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>

    <?php include '../layouts/header.php'; ?>

    <div class="container">
        <h2>Place an Order</h2>

        <?php
        if (isset($_SESSION['success'])) {
            echo "<p class='success'>" . $_SESSION['success'] . "</p>";
            unset($_SESSION['success']);
        }

        if (isset($_SESSION['error'])) {
            echo "<p class='error'>" . $_SESSION['error'] . "</p>";
            unset($_SESSION['error']);
        }
        ?>

        <div id="menu">
            <h3>Menu Items</h3>
            <div id="menu-items">
                <?php
                $stmt = $pdo->query("SELECT id, name, description, price FROM MenuItems WHERE availability = 1");
                $menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($menuItems as $item) {
                    echo "
                    <div class='menu-item'>
                        <h4>{$item['name']} - \${$item['price']}</h4>
                        <p>{$item['description']}</p>
                        <input type='number' class='quantity' id='qty-{$item['id']}' min='1' value='1'>
                        <input type='text' class='customization' id='custom-{$item['id']}' placeholder='Customizations (optional)'>
                        <button onclick='addToCart({$item['id']}, \"{$item['name']}\", {$item['price']})'>Add to Cart</button>
                    </div>";
                }
                ?>
            </div>
        </div>

        <form method="POST" action="">
            <div id="order-summary">
                <h3>Order Summary</h3>
                <ul id="cart-list"></ul>
                <p><strong>Total Price: $<span id="total-price">0.00</span></strong></p>
                <input type="hidden" name="cart_data" id="cart_data">
                <button type="submit" name="place_order">Place Order</button>
            </div>
        </form>
    </div>

    <?php include '../layouts/footer.php'; ?>

    <script src="/public/js/order.js"></script>

</body>
</html>
