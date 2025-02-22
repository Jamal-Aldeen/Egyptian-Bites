<!-- Password Reset 8 - Bootstrap Brain Component -->
<?php
session_start();
$page_title = "password reset form ";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../public/css/Password-Reset.css">

</head>
<body>
    

<div class='py-5'>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <?php
                if (isset($_SESSION['status'])) {
                ?>
                    <div class="alert alert-success">
                        <h5><?= $_SESSION['status']; ?></h5>
                    </div>
                <?php
                    unset($_SESSION['status']);
                }
                ?>

                <div class="card">
                    <div class="card-header">
                        <h2 class="h4 text-center">Password Reset</h2>
                    </div>
                    <div class="card-body p-4">


                        <form action="password-reset-code.php" method="POST">
                            <div class="row gy-3 overflow-hidden">
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com" required>

                                    </div>


                                    <div class="form-group mb-3">
                                        <button class="btn btn-dark btn-lg" type="submit" name="password_reset_link">Reset Password reset link</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2 gap-md-4 flex-column flex-md-row justify-content-md-center mt-5">
                                    <a href="login.php" class="link-secondary text-decoration-none">Login</a>
                                    <a href="register-form.php" class="link-secondary text-decoration-none">Register</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        </section>
        </body>
</html>