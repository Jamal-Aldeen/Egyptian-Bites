<?php
session_start();
include '../layouts/header.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../config/db.php'; // Ensure the database connection is included

$userModel = new User();
$user = $userModel->findById($_SESSION['user_id']);
$addresses = $userModel->getAddresses($_SESSION['user_id']);

if (!$user) {
    $_SESSION['error'] = "User not found!";
    header("Location: /index.php");
    exit();
}

// Display error messages
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}

// Display success messages
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}

$message = "";
if (count($_POST) > 0) {
    // Check if current password, new password, and confirm password are provided
    $currentPassword = $_POST["currentPassword"];
    $newPassword = $_POST["newPassword"];
    $confirmPassword = $_POST["confirmPassword"];

    if (!empty($currentPassword) && !empty($newPassword) && !empty($confirmPassword)) {
        // Fetch the user's current hashed password from the database using PDO
        $sql = "SELECT * FROM users WHERE id = ?";  // Use 'id' as per your table schema
        $statement = $GLOBALS['pdo']->prepare($sql);
        $statement->execute([$_SESSION["user_id"]]);  // Execute with the user ID
        $row = $statement->fetch(PDO::FETCH_ASSOC);  // Fetch the result

        if (!empty($row)) {
            $hashedPassword = $row["password"];
            
            // Check if the current password is correct
            if (password_verify($currentPassword, $hashedPassword)) {
                // Check if the new password and confirm password match
                if ($newPassword == $confirmPassword) {
                    // Hash the new password before updating
                    $newPasswordHashed = password_hash($newPassword, PASSWORD_BCRYPT);
                    
                    // Update the password in the database
                    $sql = "UPDATE users SET password = ? WHERE id = ?";  // Use 'id' instead of 'userId'
                    $statement = $GLOBALS['pdo']->prepare($sql);
                    if ($statement->execute([$newPasswordHashed, $_SESSION["user_id"]])) {
                        $message = "Password Changed Successfully!";
                        $_SESSION['success'] = "Your password has been successfully updated.";
                    } else {
                        $message = "Failed to update password.";
                    }
                } else {
                    $message = "New password and confirm password do not match.";
                }
            } else {
                $message = "Current Password is not correct.";
            }
        }
    } else {
        $message = "All fields are required.";
    }
}
?>



<div class="container py-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body text-center">
                    <img src="/public/uploads/<?= htmlspecialchars($user['profile_picture'] ?? 'default.jpg') ?>"
                         class="rounded-circle mb-3"
                         style="width: 150px; height: 150px; object-fit: cover;">
                    <h4><?= htmlspecialchars($user['full_name']) ?></h4>
                    <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>

                    <!-- Reserve a Table Button -->
                    <a href="/views/customer/reservation.php" class="btn btn-primary w-100 mt-3">
                        <i class="fas fa-calendar-check me-2"></i> Reserve a Table
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Update Profile Form (Only Profile Picture) -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h4>Update Profile Picture</h4>
                    <form action="/controllers/AuthController.php?action=update_profile" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Profile Picture</label>
                            <input type="file" name="profile_picture" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Update Profile Picture</button>
                    </form>
                </div>
            </div>

            <form name="frmChange" method="post" action="" onsubmit="return validatePassword()">
    <div class="validation-message text-center">
        <?php if (isset($message)) { echo $message; } ?>
    </div>
    <h2 class="text-center">Change Password</h2>
    <div>
        <div class="row">
            <label class="inline-block">Current Password</label>
            <span id="currentPassword" class="validation-message"></span>
            <input type="password" name="currentPassword" class="full-width" required>
        </div>
        <div class="row">
            <label class="inline-block">New Password</label>
            <span id="newPassword" class="validation-message"></span>
            <input type="password" name="newPassword" class="full-width" required>
        </div>
        <div class="row">
            <label class="inline-block">Confirm New Password</label>
            <span id="confirmPassword" class="validation-message"></span>
            <input type="password" name="confirmPassword" class="full-width" required>
        </div>
        <div class="row">
            <input type="submit" name="submit" value="Submit" class="full-width">
        </div>
    </div>
</form>

                </div>
            </div>

            <!-- Address Management -->
            <div class="card shadow">
                <div class="card-body">
                    <h4>Manage Addresses</h4>
                    <?php foreach ($addresses as $address): ?>
                        <div class="address-card mb-3 p-3 border rounded">
                            <h5><?= htmlspecialchars($address['label']) ?></h5>
                            <p><?= htmlspecialchars($address['address_line1']) ?></p>
                            <?php if ($address['address_line2']): ?>
                                <p><?= htmlspecialchars($address['address_line2']) ?></p>
                            <?php endif; ?>
                            <p><?= htmlspecialchars($address['city']) ?></p>
                            <form action="/controllers/AuthController.php?action=delete_address" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this address?');">
                                <input type="hidden" name="address_id" value="<?= $address['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    <?php endforeach; ?>

                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addressModal">
                        Add New Address
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Address Modal -->
<div class="modal fade" id="addressModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="/controllers/AuthController.php?action=add_address" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Label (e.g., Home, Office)</label>
                        <input type="text" name="label" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address Line 1</label>
                        <input type="text" name="address_line1" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address Line 2</label>
                        <input type="text" name="address_line2" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Address</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    // Function to validate the password change form
    function validatePassword() {
        var currentPassword, newPassword, confirmPassword, output = true;

        // Get the values of the form fields
        currentPassword = document.frmChange.currentPassword;
        newPassword = document.frmChange.newPassword;
        confirmPassword = document.frmChange.confirmPassword;

        // Check if current password is empty
        if (!currentPassword.value) {
            currentPassword.focus();
            document.getElementById("currentPassword").innerHTML = "Required";
            output = false;
        }
        // Check if new password is empty
        else if (!newPassword.value) {
            newPassword.focus();
            document.getElementById("newPassword").innerHTML = "Required";
            output = false;
        }
        // Check if confirm password is empty
        else if (!confirmPassword.value) {
            confirmPassword.focus();
            document.getElementById("confirmPassword").innerHTML = "Required";
            output = false;
        }

        // Check if new password and confirm password match
        if (newPassword.value != confirmPassword.value) {
            newPassword.value = "";
            confirmPassword.value = "";
            newPassword.focus();
            document.getElementById("confirmPassword").innerHTML = "Passwords do not match";
            output = false;
        }

        return output;
    }
</script>
<?php include '../layouts/footer.php'; ?>
