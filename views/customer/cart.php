<?php
session_start();
require_once __DIR__ . '/../../controllers/OrderController.php';
require_once __DIR__ . '/../../config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_data'])) {
    try {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('You must be logged in to place an order');
        }

        $cart = json_decode($_POST['cart_data'], true);
        if (empty($cart)) {
            throw new Exception('Cart is empty');
        }

        $total = array_reduce($cart, function ($sum, $item) {
            if (!isset($item['id'], $item['quantity'], $item['price'])) {
                throw new Exception('Invalid item in cart');
            }
            return $sum + ($item['price'] * $item['quantity']);
        }, 0);

        global $pdo;
        $orderController = new OrderController($pdo);
        $result = $orderController->placeOrder($_SESSION['user_id'], $cart, $total);

        if ($result) {
            $_SESSION['cart'] = [];
            echo '<script>localStorage.removeItem("cart");</script>';
            // $success = 'Order placed successfully!';
            // header("Location: /views/customer/order-history.php");
            // exit();
        } else {
            throw new Exception('Failed to place order');
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$cart = $_SESSION['cart'] ?? [];
?>
<?php include '../layouts/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your Cart</title>
    <link rel="stylesheet" href="/public/css/order-payment.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="container mt-5">
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <h2>Your Cart</h2>
    <ul id="cart-items" class="list-group mb-3"></ul>

    <h3 class="total-price">Total: $<span id="total-price">0.00</span></h3>
    <form method="POST" action="">
        <input type="hidden" name="cart_data" id="cart_data">
        <button type="submit" class="btn btn-success">Confirm Order</button>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    const cartList = document.getElementById("cart-items");
    const totalPriceElement = document.getElementById("total-price");
    const cartDataInput = document.getElementById("cart_data");

    function renderCart() {
        cartList.innerHTML = "";
        let total = 0;
        
        if (cart.length === 0) {
            cartList.innerHTML = '<li class="list-group-item text-danger empty-cart-message">Your cart is empty.</li>';
        } else {
            cart.forEach((item, index) => {
                total += item.price * item.quantity;
                const li = document.createElement("li");
                li.className = "list-group-item cart-item d-flex justify-content-between align-items-center";
                li.innerHTML = `
                    <div>
                        <div class="item-name">${item.name}</div>
                        <div class="price">$${(item.price * item.quantity).toFixed(2)}</div>
                    </div>
                    <div class="d-flex align-items-center cart-actions">
                        <button class="btn btn-sm btn-outline-secondary update-quantity" data-index="${index}" data-change="-1">-</button>
                        <input type="number" class="form-control mx-2 text-center" value="${item.quantity}" min="1" readonly style="width: 60px;">
                        <button class="btn btn-sm btn-outline-secondary update-quantity" data-index="${index}" data-change="1">+</button>
                        <button class="btn btn-danger btn-sm remove-item" data-index="${index}">Remove</button>
                    </div>
                `;
                cartList.appendChild(li);
            });
        }
        totalPriceElement.innerText = total.toFixed(2);
        cartDataInput.value = JSON.stringify(cart);
    }

    cartList.addEventListener("click", function (event) {
        if (event.target.classList.contains("update-quantity")) {
            const index = event.target.dataset.index;
            const change = parseInt(event.target.dataset.change);
            cart[index].quantity = Math.max(1, cart[index].quantity + change);
            localStorage.setItem("cart", JSON.stringify(cart));
            renderCart();
        }
        if (event.target.classList.contains("remove-item")) {
            const index = event.target.dataset.index;
            cart.splice(index, 1);
            localStorage.setItem("cart", JSON.stringify(cart));
            renderCart();
        }
    });

    renderCart();
});
</script>

</body>
</html>
