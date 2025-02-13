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

</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Egyptian Bites</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                
                <li class="nav-item"><a class="nav-link active" href="#home" >Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#menu">Menu</a></li>
                <li class="nav-item"><a class="nav-link" href="#booking">Book a Table</a></li>
                <!-- <li class="nav-item"><a class="nav-link" href="#testimonials">Testimonials</a></li> -->
                <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                <!-- Add to header navigation -->
<?php if (isset($_SESSION['user_id'])) : ?>
    <li class="nav-item">
        <a class="nav-link" href="/views/customer/profile.php">My Profile</a>
    </li>
    <li class="nav-item">
<a href="/logout" class="nav-link">Logout</a>
    </li>
<?php else : ?>
    <li class="nav-item">
        <a class="nav-link" href="/views/shared/login.php">Login</a>
        <li class="nav-item"><a class="nav-link" href="../shared/register-form.php">Register</a></li>
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
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>