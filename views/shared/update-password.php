<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];
    $email = $_POST['email'];
    $token = $_POST['token'];

    if ($password1 !== $password2) {
        $_SESSION['error'] = 'Passwords do not match';
        header("Location: reset-password.php?token=$token&email=$email");
        exit();
    }

    if (strlen($password1) < 8 || !preg_match('/[A-Z]/', $password1) || !preg_match('/[a-z]/', $password1) || !preg_match('/\d/', $password1)) {
        $_SESSION['error'] = 'Password must be at least 8 characters long, contain one uppercase letter, one lowercase letter, and one number';
        header("Location: reset-password.php?token=$token&email=$email");
        exit();
    }

    $new_password_hash = password_hash($password1, PASSWORD_BCRYPT);

    try {
        $update_stmt = $pdo->prepare("UPDATE users SET password = :password, verification_token = NULL WHERE email = :email AND verification_token = :token");
        $update_stmt->bindParam(':password', $new_password_hash);
        $update_stmt->bindParam(':email', $email);
        $update_stmt->bindParam(':token', $token);

        if ($update_stmt->execute()) {
            $_SESSION['success'] = 'Password successfully updated';
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['error'] = 'Invalid or expired token';
            header("Location: password-reset.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        header("Location: password-reset.php");
        exit();
    }
}
?>
