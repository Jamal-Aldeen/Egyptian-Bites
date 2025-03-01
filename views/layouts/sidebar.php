<?php
require_once __DIR__ . '/../../models/User.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$userModel = new User();
$user = $userModel->findById($_SESSION['user_id']);
$addresses = $userModel->getAddresses($_SESSION['user_id']);

if (!$user) {
    $_SESSION['error'] = "User not found!";
    header("Location: /index.php");
    exit();
}

// Display error messages
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        <?php include __DIR__ . '/../../public/css/dashboard.css'; ?>
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark text-white sidebar">
                <div class="d-flex flex-column h-100">
                    <!-- Sidebar Heading -->
                    <h5 class="sidebar-heading text-light p-3 text-center">Admin Tools</h5>
                    <!-- User Info -->
                    <div class="user-info text-center p-3">
            <a href="../customer/profile.php"> <!-- Add link to profile.php -->
                <img src="/public/uploads/<?= htmlspecialchars($user['profile_picture'] ?? 'default.jpg') ?>"
                     class="rounded-circle mb-3"
                     style="width: 150px; height: 150px; object-fit: cover;">
            </a>
            <h4><?= htmlspecialchars($user['full_name']) ?></h4>
            <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>
        </div>

                    <!-- Sidebar Menu -->
                    <ul class="nav flex-column flex-grow-1">
                        <li class="nav-item">
                            <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>"
                                href="dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'user-management.php' ? 'active' : '' ?>"
                                href="user-management.php">
                                <i class="fas fa-users"></i> Manage Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'menu_items.php' ? 'active' : '' ?>"
                                href="menu_items.php">
                                <i class="fas fa-utensils"></i> Manage Items
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'menu-management.php' ? 'active' : '' ?>"
                                href="menu-management.php">
                                <i class="fas fa-utensils"></i> Manage Menu
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'reservations.php' ? 'active' : '' ?>"
                                href="reservations.php">
                                <i class="fas fa-calendar-alt"></i> Reservations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'inventory-management.php' ? 'active' : '' ?>"
                                href="inventory-management.php">
                                <i class="fas fa-boxes"></i> Manage Inventory
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : '' ?>"
                                href="reports.php">
                                <i class="fas fa-chart-bar"></i> Sales Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'order-management.php' ? 'active' : '' ?>"
                                href="/views/staff/order-management.php">
                                <i class="fas fa-cogs"></i> Order Management
                            </a>
                        </li>
                    </ul>

                    <!-- Logout Button at the Bottom -->
                    <div class="mt-auto p-3">
                        <button class="btn btn-danger w-100" onclick="window.location.href='logout.php'">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <?php
                // Display error messages
                if (isset($_SESSION['error'])) {
                    echo '<div class="alert alert-danger mt-3">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']);
                }
                ?>
                <!-- Your main content goes here -->
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS and FontAwesome -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>

</html>