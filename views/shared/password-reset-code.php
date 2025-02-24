<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

// Load Composer's autoloader
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function send_password_reset($get_name, $get_email, $token) {
    // Initialize PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                         // Gmail SMTP server
        $mail->SMTPAuth   = true;                                    // Enable SMTP authentication
        $mail->Username   = 'bitesegyptian@gmail.com';               // Your Gmail address
        $mail->Password   = '';                                       // Your Gmail app password (not your regular Gmail password if using 2FA)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;          // Enable STARTTLS encryption
        $mail->Port       = 587;                                     // SMTP port for TLS (use 465 for implicit SSL)

        // Recipients
        $mail->setFrom('bitesegyptian@gmail.com', $get_name);        // Sender's email and name
        $mail->addAddress($get_email);                                // Add the user's email address

        // Content
        $mail->isHTML(true);                                         // Set email format to HTML
        $mail->Subject = 'Password Reset Notification';

        // Define the email content template
        $email_template = "
        <h2>Hello, $get_name</h2>
        <h3>You are receiving this email because we received a password reset request for your account.</h3>
        <br/><br/>
        <a href='http://localhost:8080/views/shared/reset-password.php?token=$token&email=$get_email'>Click here to reset your password</a>";

        // Set the email body
        $mail->Body = $email_template;  // Use the template for the body of the email

        // Enable verbose debug output
        $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Debug mode on
        $mail->Debugoutput = 'html';           // Debug output in HTML format

        // Send the email
        if ($mail->send()) {
            echo 'Message has been sent successfully.';
        } else {
            echo 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
        }
    } catch (Exception $e) {
        // Error handling if sending the email fails
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

if (isset($_POST['password_reset_link'])) {
    // Sanitize the email using PDO
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);  // Sanitize the email

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {  // Validate the email format
        // Continue the process after validating the email
        $token = md5(rand());  // Generate a random token

        try {
            // Check if the email exists in the database using PDO
            $check_email_query = "SELECT email, full_name FROM users WHERE email = :email LIMIT 1";
            $stmt = $pdo->prepare($check_email_query);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            // Check if the email exists in the database
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $get_name = $row['full_name'];
                $get_email = $row['email'];

                // Update the token in the database
                $update_token_query = "UPDATE users SET verification_token = :token WHERE email = :email LIMIT 1";
                $update_stmt = $pdo->prepare($update_token_query);
                $update_stmt->bindParam(':token', $token, PDO::PARAM_STR);
                $update_stmt->bindParam(':email', $get_email, PDO::PARAM_STR);

                // Execute the update query
                if ($update_stmt->execute()) {
                    // Send the password reset link
                    send_password_reset($get_name, $get_email, $token);
                    $_SESSION['status'] = 'A password reset link has been sent to your email';
                    header("Location: password-reset.php");
                    exit(0);
                } else {
                    $_SESSION['status'] = 'An error occurred during the update #1';
                    header("Location: password-reset.php");
                    exit(0);
                }
            } else {
                $_SESSION['status'] = 'Email not found in the database';
                header("Location: password-reset.php");
                exit(0);
            }
        } catch (PDOException $e) {
            // Handle errors
            $_SESSION['status'] = 'An error occurred while connecting to the database: ' . $e->getMessage();
            header("Location: password-reset.php");
            exit(0);
        }
    } else {
        die("Invalid email.");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];

    // Validate password fields
    if (empty($password1) || empty($password2)) {
        $_SESSION['error'] = 'Please fill in both password fields';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

    if ($password1 !== $password2) {
        $_SESSION['error'] = 'Passwords do not match';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

    // Validate password strength
    if (strlen($password1) < 8 || !preg_match('/[A-Z]/', $password1) || !preg_match('/[a-z]/', $password1) || !preg_match('/\d/', $password1)) {
        $_SESSION['error'] = 'Password must be at least 8 characters long, contain one uppercase letter, one lowercase letter, and one number';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

    // Hash the new password
    $new_password_hash = password_hash($password1, PASSWORD_BCRYPT);

    // Update the password in the database
    $update_stmt = $pdo->prepare("UPDATE users SET password = :password, verification_token = NULL WHERE email = :email");
    $update_stmt->bindParam(':password', $new_password_hash);
    $update_stmt->bindParam(':email', $email);

    if ($update_stmt->execute()) {
        $_SESSION['success'] = 'Password has been successfully updated';
        header('Location: /login.php');
        exit();
    } else {
        $_SESSION['error'] = 'Failed to update password';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>
