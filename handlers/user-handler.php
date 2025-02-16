<?php
session_start();
require_once __DIR__ . '/../controllers/UserController.php';

// Authorization check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Staff') {
    header("Location: /views/shared/login.php");
    exit();
}

$userController = new UserController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_role'])) {
        $userId = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
        $newRole = htmlspecialchars($_POST['role']);
        $userController->updateUserRole($userId, $newRole);
    } elseif (isset($_POST['delete_user'])) {
        $userId = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
        $userController->deleteUser($userId);
    }
}

header("Location: /views/staff/user-management.php");
exit();
?>