// let cart = JSON.parse(localStorage.getItem("cart")) || [];

// function updateCartCount() {
//     document.getElementById("cart-count").innerText = cart.length;
// }

// function updateCartDisplay() {
//     let cartList = document.getElementById("cart-items");
//     let totalPrice = 0;
//     cartList.innerHTML = "";

//     cart.forEach((item, index) => {
//         let itemTotal = item.price * item.quantity;
//         totalPrice += itemTotal;
//         cartList.innerHTML += `
//             <li>${item.quantity} × ${item.name} - $${itemTotal.toFixed(2)}
//                 <button onclick="removeFromCart(${index})">Remove</button>
//             </li>`;
//     });

//     document.getElementById("total-price").innerText = totalPrice.toFixed(2);
//     document.getElementById("cart_data").value = JSON.stringify(cart);
// }

// function addToCart(menuItemId, name, price) {
//     let quantity = parseInt(document.getElementById(`qty-${menuItemId}`).value);
//     let customizations = document.getElementById(`custom-${menuItemId}`)?.value || "";

//     let existingItem = cart.find(item => item.menu_item_id === menuItemId);
//     if (existingItem) {
//         existingItem.quantity += quantity;
//         existingItem.customizations = customizations;
//     } else {
//         cart.push({ menu_item_id: menuItemId, name, price, quantity, customizations });
//     }

//     localStorage.setItem("cart", JSON.stringify(cart));
//     updateCartCount();
//     updateCartDisplay();
// }

// function removeFromCart(index) {
//     cart.splice(index, 1);
//     localStorage.setItem("cart", JSON.stringify(cart));
//     updateCartCount();
//     updateCartDisplay();
// }

// document.addEventListener("DOMContentLoaded", function() {
//     updateCartCount();
//     updateCartDisplay();
// });
