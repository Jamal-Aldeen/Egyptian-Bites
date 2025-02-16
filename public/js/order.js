let cart = [];
let totalPrice = 0;

function addToCart(menuItemId, name, price) {
    let quantity = parseInt(document.getElementById(`qty-${menuItemId}`).value);
    let customizations = document.getElementById(`custom-${menuItemId}`).value;
    cart.push({menu_item_id: menuItemId, name, quantity, customizations, price});
    updateCart();
}

function updateCart() {
    let output = "";
    totalPrice = 0;
    cart.forEach((item, index) => {
        totalPrice += item.price * item.quantity;
        output += `<li>${item.quantity} x ${item.name} - $${(item.price * item.quantity).toFixed(2)}
            <button onclick="removeFromCart(${index})">Remove</button></li>`;
    });

    document.getElementById("cart-list").innerHTML = output;
    document.getElementById("total-price").innerText = totalPrice.toFixed(2);
    document.getElementById("cart_data").value = JSON.stringify(cart);
}

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCart();
}

document.addEventListener("DOMContentLoaded", function () {
    updateCartCount();
});

function updateCartCount() {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    document.getElementById("cart-count").innerText = cart.length;
}

function addToCart(menuItemId, name, price) {
    let quantity = parseInt(document.getElementById(`qty-${menuItemId}`).value);
    let customizations = document.getElementById(`custom-${menuItemId}`).value;

    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    cart.push({menu_item_id: menuItemId, name, quantity, customizations, price});
    
    localStorage.setItem("cart", JSON.stringify(cart));
    updateCartCount();
}
