<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Egyptian Bites</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/public/css/styles.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <!-- Brand Logo -->
        <a class="navbar-brand" href="../../index.php"><i class="fas fa-utensils"></i> Egyptian Bites</a>

        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">   
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Centered Navigation Links -->
            <ul class="navbar-nav mx-auto">
                <li class="nav-item on"><a class="nav-link " href="#home">Home</a></li>
                <li class="nav-item on"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item on"><a class="nav-link" href="#menu">Menu</a></li>
                <li class="nav-item on"><a class="nav-link" href="#booking">Book a Table</a></li>

                 <li class="nav-item">
                    <a class="nav-link" href="/views/customer/payment.php">Payment</a>
                 </li>
            </ul>

            <!-- Right-Aligned Login/Register & Cart -->
            <ul class="navbar-nav">
          


                <?php if (isset($_SESSION['user_id'])) : ?>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-warning px-3" href="/logout">Logout</a>
                    </li>
                <?php else : ?>
                    <li class="nav-item menu-btn">
                        <a class="nav-link btn btn-outline-warning px-2 mx-2 w " href="/views/shared/login.php">Login</a>
                    </li>
                    <li class="nav-item menu-btn">
                        <a class="nav-link btn btn-outline-warning px-2 mx-2 active-now" style="width:auto;" href="/views/shared/register-form.php">Register</a>
                    </li>
                     <!-- Order Cart Icon -->
                <li class="nav-item cart-container">
                    <a class="nav-link cart-link" href="/views/customer/cart.php">
                    <div class="cart-card">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-badge" id="cart-count">0</span>
                    </div>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- JavaScript to Close Navbar After Click -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const navLinks = document.querySelectorAll(".nav-link");
    const navbarCollapse = document.querySelector(".navbar-collapse");

    navLinks.forEach(function (link) {
        link.addEventListener("click", function () {
            if (navbarCollapse.classList.contains("show")) {
                new bootstrap.Collapse(navbarCollapse).hide();
            }
        });
    });

    updateCartCount();
});

function updateCartCount() {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    const cartCount = cart.reduce((acc, item) => acc + item.quantity, 0);
    document.getElementById("cart-count").innerText = cartCount;
}

document.addEventListener("DOMContentLoaded", function () {
    updateCartCount();
});

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
