<?php
require_once __DIR__ . '/../../models/User.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$userModel = new User();
$user = $userModel->findById($_SESSION['user_id']);

if (!$user) {
    $_SESSION['error'] = "User not found!";
    header("Location: /index.php");
    exit();
}
?>

<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark text-white sidebar">
    <div class="d-flex flex-column h-100">
        <h5 class="sidebar-heading text-light p-3 text-center">Admin Tools</h5>
        <div class="user-info text-center p-3">
            <a href="../customer/profile.php">
                <img src="/public/uploads/<?= htmlspecialchars($user['profile_picture'] ?? 'default.jpg') ?>"
                     class="rounded-circle mb-3"
                     style="width: 150px; height: 150px; object-fit: cover;">
            </a>
            <h4><?= htmlspecialchars($user['full_name']) ?></h4>
            <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>
        </div>
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
        </ul>
        <div class="mt-auto p-3">
            <button class="btn btn-danger w-100" onclick="window.location.href='logout.php'">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </div>
    </div>
</nav>