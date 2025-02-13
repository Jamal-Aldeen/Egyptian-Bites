<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Placement</title>
    <link rel="stylesheet" href="/public/css/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

   
    <?php include '../layouts/header.php'; ?>

    <div class="container">
        <h2>Place an Order</h2>
        <p>Select items from the menu and customize your order.</p>

       
        <div id="menu">
            <h3>Menu Items</h3>
            <div id="menu-items"></div>
        </div>

        
        <div id="order-summary">
            <h3>Order Summary</h3>
            <ul id="cart-list"></ul>
            <p><strong>Total Price: $<span id="total-price">0.00</span></strong></p>
            <button onclick="placeOrder()">Place Order</button>
        </div>
    </div>

   
    <?php include '../layouts/footer.php'; ?>

</body>
</html>
