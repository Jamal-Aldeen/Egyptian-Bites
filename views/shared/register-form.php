<?php
session_start(); // Start the session
// include '../layouts/header.php';
?>

<div class="container">
    <div class="register-form-container">
        <h3 class="text-center">Register</h3>

        <?php if (!empty($_SESSION['errors'])) { ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($_SESSION['errors'] as $error) {
                        echo "<li>$error</li>";
                    } ?>
                </ul>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php } ?>

        <?php if (!empty($_SESSION['success'])) { ?>
            <div class="alert alert-success text-center">
                <p><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
            </div>
        <?php } ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>register.php</title>
    <link rel="stylesheet" href="../../public/css/register-style.css">
</head>
<body>
<div class="login-form-container">
        <!-- Logo Image (Rounded) -->
        <img src="../../public/assets/images/profile-icon-design-free-vector.jpg" alt="Logo" class="rounded-image">


        <form action="register.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control input-field" name="full_name" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control input-field" name="email" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control input-field" name="password" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" class="form-control input-field" name="confirm_password" required>
            </div>

            <div class="mb-3">
                <label class="form-label">User Type</label>
                <select class="form-select input-field" name="user_type" required>
                    <option value="customer">Customer</option>
                    <option value="staff">Staff</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Profile Picture (Optional)</label>
                <input type="file" class="form-control input-field" name="profile_pic">
            </div>

            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>

        <div class="text-center mt-3">
            <a href="login.php">Already have an account? Login here</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<!-- -->