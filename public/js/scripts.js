document.addEventListener("DOMContentLoaded", function () {
    const stars = document.querySelectorAll(".star");

    stars.forEach(star => {
        star.addEventListener("click", function () {
            const value = this.getAttribute("data-value");
            updateStars(value);
            console.log(`Rated: ${value} stars`);
        });
    });

    function updateStars(value) {
        stars.forEach(star => {
            if (star.getAttribute("data-value") <= value) {
                star.style.color = "#FFD700";
            } else {
                star.style.color = "#ddd";
            }
        });
    }
});
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const menuItems = document.querySelectorAll(".card");

    searchInput.addEventListener("keyup", function () {
        const filter = searchInput.value.toLowerCase();
        menuItems.forEach(item => {
            const title = item.querySelector(".card-title").innerText.toLowerCase();
            item.style.display = title.includes(filter) ? "block" : "none";
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const menuItems = document.querySelectorAll(".card");

    searchInput.addEventListener("keyup", function () {
        const filter = searchInput.value.toLowerCase();
        menuItems.forEach(item => {
            const title = item.querySelector(".card-title").innerText.toLowerCase();
            item.style.display = title.includes(filter) ? "block" : "none";
        });
    });
});
document.querySelectorAll(".btn-add-cart").forEach(button => {
    button.addEventListener("click", function () {
        this.innerHTML = '<i class="fas fa-check"></i> Added!';
        this.style.backgroundColor = "#28a745";
        setTimeout(() => {
            this.innerHTML = '<i class="fas fa-shopping-cart "></i>  Add to Cart';
            this.style.backgroundColor = "";
        }, 2000);
    });
});
document.querySelectorAll(".increase-qty").forEach(btn => {
    btn.addEventListener("click", function () {
        let input = this.previousElementSibling;
        input.value = parseInt(input.value) + 1;
    });
});

document.querySelectorAll(".decrease-qty").forEach(btn => {
    btn.addEventListener("click", function () {
        let input = this.nextElementSibling;
        if (input.value > 1) {
            input.value = parseInt(input.value) - 1;
        }
    });
});

document.getElementById("searchInput").addEventListener("keyup", function () {
    let filter = this.value.toLowerCase();
    document.querySelectorAll(".menu-item").forEach(item => {
        let title = item.querySelector(".card-title").innerText.toLowerCase();
        item.style.display = title.includes(filter) ? "block" : "none";
    });
});
document.addEventListener("DOMContentLoaded", function () {
    updateCartCount();
});

function addToCart(id, name, price) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    const existingItem = cart.find(item => item.id === id);

    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: id,
            name: name,
            price: parseFloat(price),
            quantity: 1
        });
    }

    localStorage.setItem("cart", JSON.stringify(cart));
    updateCartCount();
}

document.querySelectorAll('.cart-actions input').forEach(input => {
    input.addEventListener('change', function() {
        let index = this.closest('.cart-item').dataset.index;
        let newVal = parseInt(this.value);
        updateQuantity(index, newVal);
    });
});

function updateCartCount() {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    const cartCount = cart.reduce((acc, item) => acc + item.quantity, 0);
    document.getElementById("cart-count").innerText = cartCount;

    if (existingItem) {
        existingItem.quantity += 1; 
    } else {
        cart.push({ id, name, price: parseFloat(price.replace('$', '')), quantity: 1 }); 
    }

    localStorage.setItem("cart", JSON.stringify(cart));
    updateCartCount();
}

function removeFromCart(index) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    cart.splice(index, 1);
    localStorage.setItem("cart", JSON.stringify(cart));
    location.reload();
}

function updateQuantity(index, change) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    if (cart[index]) {
        cart[index].quantity += change;
        if (cart[index].quantity < 1) cart[index].quantity = 1; 
        localStorage.setItem("cart", JSON.stringify(cart));
        location.reload(); 
    }
}