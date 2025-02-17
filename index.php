<?php
// Add to index.php's switch statement
$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'logout':
        session_start();
        session_unset();
        session_destroy();
        header("Location: /login"); // Redirect to login page after logout
        exit();
        break;
    case 'profile':
        if (!isset($_SESSION['user_id'])) {
            header("Location: /views/shared/login.php");
            exit();
        }
        include 'views/customer/profile.php';
        break;
    case 'dashboard':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Staff') {
            header("Location: /views/shared/login.php");
            exit();
        }
        include 'views/staff/dashboard.php';
        break;
    // ... other cases
}



?>
<?php include("views/layouts/header.php"); ?>

<!-- Hero Section -->
<div id="home" class="hero">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1 class="display-4">Welcome to <span class="text-warning">Egyptian Bites</span></h1>
        <p class="lead">Authentic Egyptian flavors straight from the heart of Cairo!</p>
        <div class="button-container">
    <a href="/views/customer/menu.php" class="btn btn-menu">Our Menu</a>
    <a href="/views/customer/reservation.php" class="btn btn-yellow">Book a Table</a>
</div>

    </div>
</div>

</div>
