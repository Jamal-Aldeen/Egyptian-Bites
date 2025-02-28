<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// require_once '../../config/db.php';
// include '../layouts/header.php';
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
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <!-- Brand Logo -->
            <a class="navbar-brand" href="../../index.php"><i class="fas fa-utensils"></i> Egyptian Bites</a>

            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Centered Navigation Links -->
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../views/customer/menu.php">Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../views/customer/reservation.php">Book a Table</a>
                    </li>
                </ul>

                <!-- Right-Aligned Login/Register & Cart -->
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])) : ?>
                        <li class="nav-item">
                            <a href="/handlers/logout.php" class="btn btn-outline-warning px-3">Logout</a>
                        </li>
                    <?php else : ?>
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-warning px-2 mx-2" href="/views/shared/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-warning px-2 mx-2 active-now" href="/views/shared/register-form.php">Register</a>
                        </li>
                    <?php endif; ?>

                    <!-- Notifications Icon -->

                    <li class="nav-item">
                        <a class="nav-link" href="/views/customer/notifications.php">
                            <i class="fas fa-bell"></i>
                            <?php
                            // Get the notification count for the current user
                            if (isset($_SESSION['user_id'])) {
                                $userId = $_SESSION['user_id'];
                                // Fetch notification count dynamically if required
                                $notificationCount = 5; // Replace this with actual dynamic code if needed
                                if ($notificationCount > 0) {
                                    echo '<span class="badge bg-danger" id="notification-count" style="background-color: #d4a017;">' . $notificationCount . '</span>';
                                }
                            }
                            ?>
                        </a>
                    </li>



                    <!-- Profile Icon -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Profile Icon -->
                        <li class="nav-item">
                            <a class="nav-link" href="/views/customer/profile.php">
                                <i class="fas fa-user-circle"></i> Profile
                            </a>
                        </li>
                    <?php endif; ?>


                    <!-- Cart Icon -->
                    <li class="nav-item cart-container">
                        <a class="nav-link cart-link" href="/views/customer/cart.php">
                            <div class="cart-card">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="cart-badge" id="cart-count">0</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- JavaScript to Close Navbar After Click -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const navLinks = document.querySelectorAll(".nav-link");
            const navbarCollapse = document.querySelector(".navbar-collapse");

            navLinks.forEach(function(link) {
                link.addEventListener("click", function() {
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
        // JavaScript to update notification count dynamically
        function updateNotificationCount() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '/controllers/notification_count.php', true); // Path to notification_count.php
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Update the notification count in the page
                    document.getElementById('notification-count').textContent = xhr.responseText;
                }
            };
            xhr.send();
        }

        // Call the function to update the notification count
        updateNotificationCount();

        // Optionally, you can refresh the count every few seconds
        setInterval(updateNotificationCount, 5000); // Update every 5 seconds
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>