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

<script>
    $(document).ready(function() {
        loadMenu();
    });

    function loadMenu() {
        $.get("/public/api/get_menu.php", function(data) {
            let menuItems = JSON.parse(data);
            let output = "";
            menuItems.forEach(item => {
                output += `
                    <div class="menu-item">
                        <h4>${item.name} - $${item.price}</h4>
                        <p>${item.description}</p>
                        <input type="number" id="qty-${item.id}" min="1" value="1">
                        <input type="text" id="custom-${item.id}" placeholder="Customizations (optional)">
                        <button onclick="addToCart(${item.id}, '${item.name}', ${item.price})">Add to Cart</button>
                    </div>
                `;
            });
            $("#menu-items").html(output);
        });
    }

    let cart = [];
let totalPrice = 0;

function addToCart(menuItemId, name, price) {
    let quantity = parseInt($(`#qty-${menuItemId}`).val());
    let customizations = $(`#custom-${menuItemId}`).val();
    cart.push({menu_item_id: menuItemId, quantity, customizations, price});
    updateCart();
}

function updateCart() {
    let output = "";
    totalPrice = 0;
    cart.forEach((item, index) => {
        totalPrice += item.price * item.quantity;
        output += `<li>${item.quantity} x ${item.menu_item_id} - $${item.price * item.quantity} 
            <button onclick="removeFromCart(${index})">Remove</button></li>`;
    });
    $("#cart-list").html(output);
    $("#total-price").text(totalPrice.toFixed(2));
}

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCart();
}
function placeOrder() {
    if (cart.length === 0) {
        alert("Your cart is empty!");
        return;
    }
    $.post("/public/api/place_order.php", {cart: JSON.stringify(cart)}, function(response) {
        alert(response.message);
        if (response.status === "success") {
            cart = [];
            updateCart();
        }
    }, "json");
}


    $(document).ready(function() {
        loadMenu();
    });

    function loadMenu() {
        $.get("/public/api/get_menu.php", function(data) {
            let menuItems = JSON.parse(data);
            let output = "";
            menuItems.forEach(item => {
                output += `
                    <div class="menu-item">
                        <h4>${item.name} - $${item.price}</h4>
                        <p>${item.description}</p>
                        <input type="number" id="qty-${item.id}" min="1" value="1">
                        <input type="text" id="custom-${item.id}" placeholder="Customizations (optional)">
                        <button onclick="addToCart(${item.id}, '${item.name}', ${item.price})">Add to Cart</button>
                    </div>
                `;
            });
            $("#menu-items").html(output);
        });
    }



</script>
