<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <link rel="stylesheet" href="../../public/css/Password-Reset.css"> <!-- Link to your CSS file -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-header text-white text-center" style="background: linear-gradient(135deg, #6a11cb, #2575fc);">
                        <h3>Password Reset</h3>
                    </div>
                    <div class="card-body">
                        <!-- Display Success/Error Messages -->
                        <?php if (isset($_SESSION['status'])): ?>
                            <div class="alert alert-info text-center">
                                <?= $_SESSION['status']; unset($_SESSION['status']); ?>
                            </div>
                        <?php endif; ?>

                        <p class="text-center">Enter your email address and we'll send you a password reset link.</p>

                        <form action="password-reset-code.php" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
                            </div>

                            <button type="submit" name="password_reset_link" class="btn btn-primary w-100">
                                Send Reset Link
                            </button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="login.php" class="text-decoration-none">Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
