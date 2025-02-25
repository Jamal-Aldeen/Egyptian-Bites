<?php
session_start();  // Always start the session at the very beginning of the file

include('../../config/db.php');
// include '../layouts/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs to avoid XSS
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        // Prepare the SQL statement
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Password is correct, start session and set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] == 'Staff') {
                header('Location: /views/staff/dashboard.php'); 
            } else {
                header('Location: /views/customer/profile.php'); // Redirect customers to their profile page
            }
            exit;  // Make sure to exit after header to stop further script execution
        } else {
            // If login fails, show error message
            $error = "Invalid email or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../../public/css/login-style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="login-form-container">
        <img src="../../public/assets/images/profile-icon-design-free-vector.jpg" alt="Logo" class="rounded-image">

        <div class="login-form">
            <h3>Login</h3>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <input type="email" class="input-field" name="email" placeholder="Email Address" required>
                <input type="password" class="input-field" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>

            <div class="text-center">
                <a href="password-reset.php">Forgot password?</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
