<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your Cart</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        /* تحسين الأزرار */
        .btn-add-cart {
            background-color: #28a745;
            color: white;
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-add-cart:hover {
            background-color: #218838;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
            font-size: 14px;
            padding: 6px 12px;
            border-radius: 5px;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-outline-secondary {
            border-color: #ddd;
            color: #333;
            padding: 6px 12px;
            font-size: 14px;
        }

        .btn-outline-secondary:hover {
            background-color: #28a745;
            color: white;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        h2 {
            color: #343a40;
            margin-bottom: 20px;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .cart-item {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-bottom: 15px;
        }

        .cart-item:hover {
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }

        .cart-item .d-flex button {
            margin-right: 5px;
        }

        .total-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #28a745;
            margin-top: 20px;
        }

        .cart-actions input {
            width: 60px;
        }

        .list-group-item {
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            padding: 12px;
            background-color: #f9f9f9;
        }

        .list-group-item:hover {
            background-color: #e9ecef;
        }

        .item-name {
            font-weight: 600;
            font-size: 1.1rem;
        }

        /* إخفاء الكمية من العرض */
        .item-quantity {
            display: none; /* إخفاء الكمية */
        }

        .price {
            font-weight: bold;
            font-size: 1.1rem;
            color: #28a745;
        }

        .confirm-btn .btn-success {
            width: 100%;
            padding: 12px;
            font-size: 1.2rem;
            border-radius: 5px;
        }

        .empty-cart-message {
            text-align: center;
            color: #dc3545;
            font-size: 1.2rem;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2>Your Cart</h2>
    <ul id="cart-items" class="list-group mb-3">
        <?php if (empty($cart)) : ?>
            <li class="list-group-item text-danger empty-cart-message">Your cart is empty.</li>
        <?php else: ?>
            <?php foreach ($cart as $index => $item): ?>
                <li class="list-group-item cart-item d-flex justify-content-between align-items-center">
                    <!-- تفاصيل العنصر -->
                    <div>
                        <div class="item-name"><?= $item['name'] ?></div>
                        <!-- تم إخفاء الكمية هنا -->
                        <div class="item-quantity">(x<?= $item['quantity'] ?>)</div>
                        <div class="price">$<?= number_format($item['price'] * $item['quantity'], 2) ?></div>
                    </div>
                    
                    <!-- أزرار التحكم في الكمية -->
                    <div class="d-flex align-items-center cart-actions">
                        <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(<?= $index ?>, -1)">-</button>
                        <input type="number" class="form-control mx-2 text-center" value="<?= $item['quantity'] ?>" min="1" style="width: 60px;">
                        <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(<?= $index ?>, 1)">+</button>
                        
                        <!-- زر الإزالة -->
                        <button class="btn btn-danger btn-sm ms-2" onclick="removeFromCart(<?= $index ?>)">Remove</button>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>

    <!-- المجموع الكلي -->
    <h3 class="total-price">Total: $<span id="total-price">0.00</span></h3>

    <!-- نموذج تأكيد الطلب -->
    <form method="POST" action="/views/customer/order-placement.php" class="confirm-btn">
        <input type="hidden" name="cart_data" id="cart_data">
        <button type="submit" class="btn btn-success">Confirm Order</button>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let cartList = document.getElementById("cart-items");
    let totalPrice = 0;

    if (cart.length === 0) {
        cartList.innerHTML = '<li class="list-group-item text-danger empty-cart-message">Your cart is empty.</li>';
    } else {
        cartList.innerHTML = "";
        cart.forEach((item, index) => {
            totalPrice += parseFloat(item.price) * item.quantity;
            cartList.innerHTML += `
                <li class="list-group-item cart-item d-flex justify-content-between align-items-center">
                    <div>
                        <div class="item-name">${item.name}</div>
                        <div class="item-quantity">(x${item.quantity})</div>
                        <div class="price">$${(parseFloat(item.price) * item.quantity).toFixed(2)}</div>
                    </div>
                    <div class="d-flex align-items-center cart-actions">
                        <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(${index}, -1)">-</button>
                        <input type="number" class="form-control mx-2 text-center" value="${item.quantity}" min="1" style="width: 60px;">
                        <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(${index}, 1)">+</button>
                        <button class="btn btn-danger btn-sm ms-2" onclick="removeFromCart(${index})">Remove</button>
                    </div>
                </li>
            `;
        });
    }

    document.getElementById("total-price").innerText = totalPrice.toFixed(2);
    document.getElementById("cart_data").value = JSON.stringify(cart);
});

function updateQuantity(index, change) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    if (cart[index]) {
        cart[index].quantity += change;
        if (cart[index].quantity < 1) cart[index].quantity = 1;
        localStorage.setItem("cart", JSON.stringify(cart));
        location.reload();
    }
}

function removeFromCart(index) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    cart.splice(index, 1);
    localStorage.setItem("cart", JSON.stringify(cart));
    location.reload();
}
</script>

</body>
</html>
