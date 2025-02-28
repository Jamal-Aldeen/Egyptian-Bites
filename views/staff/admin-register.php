<?php
session_start();
include('../../config/db.php');
include('../../models/Validation.php');

$validation = new Validation();
$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $profile_pic = $_FILES['profile_pic'];

    // Validate inputs
    $validation->checkEmptyFields([
        "Full Name" => $full_name,
        "Email" => $email,
        "Password" => $password
    ]);
    $validation->validateEmail($email, $pdo); 
    $validation->validatePassword($password, $confirm_password);
    $validation->validateProfilePic($profile_pic);

    // If validation passes, proceed with user registration
    if ($validation->isValid()) {
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE email = :email");
        $stmt->execute(['email' => $email]);

        if ($stmt->rowCount() > 0) {
            $errors[] = "Email is already registered!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $profile_pic_name = "default.jpg";

            if (!empty($profile_pic['name'])) {
                $target_dir = __DIR__ . "/../../public/uploads/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true); // Ensure the target directory exists
                }

                $imageFileType = strtolower(pathinfo($profile_pic['name'], PATHINFO_EXTENSION));
                $profile_pic_name = time() . "_" . bin2hex(random_bytes(5)) . "." . $imageFileType;

                // Check file size (2MB max)
                if ($profile_pic['size'] > 2 * 1024 * 1024) {
                    $errors[] = "Profile picture must be less than 2MB.";
                } else {
                    // Move the uploaded file to the target directory
                    if (!move_uploaded_file($profile_pic['tmp_name'], $target_dir . $profile_pic_name)) {
                        $errors[] = "Failed to upload profile picture.";
                    }
                }
            }

            // If no errors, proceed with registration
            if (empty($errors)) {
                $activation_code = md5(uniqid(rand(), true)); // Generate unique activation code

                // Insert new user as admin (role = 'Staff')
                $stmt = $pdo->prepare("INSERT INTO Users (full_name, email, password, role, profile_picture, verification_token, verified) 
                                       VALUES (:full_name, :email, :password, 'Staff', :profile_picture, :verification_token, 1)");
                $stmt->execute([
                    'full_name' => $full_name,
                    'email' => $email,
                    'password' => $hashed_password,
                    'profile_picture' => $profile_pic_name,
                    'verification_token' => $activation_code
                ]);

                // Generate the verification link
                $verification_link = "https://yourwebsite.com/verify.php?code=$activation_code";
                $message = "Click on this link to verify your email: $verification_link";

                // Send email with verification link
                if (!mail($email, "Verify Your Email", $message)) {
                    $errors[] = "Failed to send verification email.";
                }

                $_SESSION['success'] = "Admin registration successful! Please check your email to verify your account.";
                header("Location: /views/staff/admin-login.php");
                exit();
            }
        }
    } else {
        $errors = $validation->getErrors(); // Get validation errors
    }

    // Store errors in session and redirect back to the registration form
    $_SESSION['errors'] = $errors;
    header("Location: /views/staff/admin-register.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h3 class="text-center">Admin Registration</h3>

        <!-- Display Errors -->
        <div id="error-messages">
            <?php
                if (isset($_SESSION['errors'])) {
                    foreach ($_SESSION['errors'] as $error) {
                        echo "<div class='alert alert-danger'>$error</div>";
                    }
                    unset($_SESSION['errors']);
                }
            ?>
        </div>

        <form method="POST" action="admin-register.php" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" name="full_name" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" class="form-control" name="confirm_password" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Profile Picture (Optional)</label>
                <input type="file" class="form-control" name="profile_pic">
            </div>

            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
