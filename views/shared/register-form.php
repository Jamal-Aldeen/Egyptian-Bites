<?php include '../layouts/header.php'; ?>

<div class="container">
    <div class="register-container">
        <h2 class="text-center">Register</h2>

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

        <form action="/views/shared/register.php" method="POST" enctype="multipart/form-data">
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
                <label class="form-label">User Type</label>
                <select class="form-select" name="user_type" required>
                    <option value="customer">Customer</option>
                    <option value="staff">Staff</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Profile Picture (Optional)</label>
                <input type="file" class="form-control" name="profile_pic">
            </div>

            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>

        <div class="text-center mt-3">
            <a href="login.php">Already have an account? Login here</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include '../layouts/footer.php'; ?>
