<?php
require_once __DIR__ . '/../../config/db.php';
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
function send_password_reset($get_name, $get_email, $token) {
    require '../../vendor/autoload.php';

    $mail = new PHPMailer(true);

    try {
        // Mailtrap SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'sandbox.smtp.mailtrap.io';  // Mailtrap SMTP Server
        $mail->SMTPAuth   = true;
        $mail->Username   = '91f13f9f5f6498';  // Replace with your actual Mailtrap username
        $mail->Password   = 'e43549ee93bf92';  // Replace with your actual Mailtrap password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 2525;

        // Sender & Recipient
        $mail->setFrom('no-reply@yourwebsite.com', 'Your Website');
        $mail->addAddress($get_email, $get_name);

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body    = "
        <h2>Hello, $get_name</h2>
        <p>You requested a password reset. Click the link below to reset your password:</p>
        <a href='http://localhost/reset-password.php?token=$token&email=$get_email'>Reset Password</a>
        <p>If you did not request this, please ignore this email.</p>";

        // Send Email
        if ($mail->send()) {
            return true;
        } else {
            return "Email sending failed: " . $mail->ErrorInfo;
        }
    } catch (Exception $e) {
        return "Mailer Error: " . $mail->ErrorInfo;
    }


    header("Location: password-reset.php");
    exit();
}

// Handle password reset submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password1'], $_POST['password2'])) {
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];
    $email = $_POST['email'];

    if (empty($password1) || empty($password2)) {
        $_SESSION['error'] = 'Please fill in both password fields';
        header("Location: reset-password.php");
        exit();
    }

    if ($password1 !== $password2) {
        $_SESSION['error'] = 'Passwords do not match';
        header("Location: reset-password.php");
        exit();
    }

    if (strlen($password1) < 8 || !preg_match('/[A-Z]/', $password1) || !preg_match('/[a-z]/', $password1) || !preg_match('/\d/', $password1)) {
        $_SESSION['error'] = 'Password must be at least 8 characters long, contain one uppercase letter, one lowercase letter, and one number';
        header("Location: reset-password.php");
        exit();
    }

    $new_password_hash = password_hash($password1, PASSWORD_BCRYPT);

    $update_stmt = $pdo->prepare("UPDATE users SET password = :password, verification_token = NULL WHERE email = :email");
    $update_stmt->bindParam(':password', $new_password_hash);
    $update_stmt->bindParam(':email', $email);

    if ($update_stmt->execute()) {
        $_SESSION['success'] = 'Password successfully updated';
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = 'Failed to update password';
        header("Location: reset-password.php");
        exit();
    }
}
?>
