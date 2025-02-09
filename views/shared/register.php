<?php
session_start();
include('../../config/db.php');
include '../../models/Validation.php';

$validation = new Validation();
$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $user_type = htmlspecialchars(trim($_POST['user_type']));
    $profile_pic = $_FILES['profile_pic'];

    $validation->checkEmptyFields([
        "Full Name" => $full_name,
        "Email" => $email,
        "Password" => $password
    ]);
    $validation->validateEmail($email, $pdo); // Pass $pdo here
    $validation->validatePassword($password, $confirm_password);
    $validation->validateProfilePic($profile_pic);

    if ($validation->isValid()) {
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE email = :email");
        $stmt->execute(['email' => $email]);

        if ($stmt->rowCount() > 0) {
            $errors[] = "Email is already registered!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $profile_pic_name = "default.jpg";

            if (!empty($profile_pic['name'])) {
                $target_dir = __DIR__ . "/../../public/uploads/"; // Ensure this path is correct
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                $imageFileType = strtolower(pathinfo($profile_pic['name'], PATHINFO_EXTENSION));
                $profile_pic_name = time() . "_" . bin2hex(random_bytes(5)) . "." . $imageFileType;

                if ($profile_pic['size'] > 2 * 1024 * 1024) {
                    $errors[] = "Profile picture must be less than 2MB.";
                } else {
                    move_uploaded_file($profile_pic['tmp_name'], $target_dir . $profile_pic_name);
                }
            }

            if (empty($errors)) {
                $activation_code = md5(uniqid(rand(), true));

                $stmt = $pdo->prepare("INSERT INTO Users (full_name, email, password, role, profile_picture, verification_token, verified) 
                                       VALUES (:full_name, :email, :password, :role, :profile_picture, :verification_token, 0)");
                $stmt->execute([
                    'full_name' => $full_name,
                    'email' => $email,
                    'password' => $hashed_password,
                    'role' => $user_type,
                    'profile_picture' => $profile_pic_name,
                    'verification_token' => $activation_code
                ]);

                $verification_link = "https://yourwebsite.com/verify.php?code=$activation_code";
                $message = "Click on this link to verify your email: $verification_link";
                mail($email, "Verify Your Email", $message);

                $_SESSION['success'] = "Registration successful! Please check your email to verify your account.";
                
                header("Location: login.php"); // Update the redirect path
                exit();
            }
        }
    } else {
        $errors = $validation->getErrors();
    }

    // Store errors in session and redirect back to the registration form
    $_SESSION['errors'] = $errors;
    header("Location: register-form.php");
    exit();
}
?>