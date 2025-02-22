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


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../public/css/profile-style.css">

</head>

<body>


    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profile Page</title>
        <link rel="stylesheet" href="../../public/css/profile-style.css">
    </head>

    <body>

        <!-- Main Container for Profile and Form Sections -->
        <div class="container input-field py-5">

            <!-- Profile Section -->
            <div class="row profile-section">
                <div class="col-md-4">
                    <div class="card shadow profile-card">
                        <div class="card-body text-center">
                            <img src="/public/uploads/<?= htmlspecialchars($user['profile_picture'] ?? 'default.jpg') ?>"
                                class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                            <h4><?= htmlspecialchars($user['full_name']) ?></h4>
                            <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>

                            <!-- Reserve a Table Button -->
                            <a href="/views/customer/reservation.php" class="btn btn-primary w-100 manage">
                                <i class="fas fa-calendar-check me-2 text-center">   Reserve a Table </i> 
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Profile Management Section -->
                <div class="col-md-8">
                    <!-- Update Profile Picture -->
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <h4>Update Profile Picture</h4>
                            <form action="/controllers/AuthController.php?action=update_profile" method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label">Profile Picture</label>
                                    <input type="file" name="profile_picture" class="form-control">
                                </div>
                                <button type="submit" class="btn btn-primary manage">Update Profile Picture</button>
                            </form>
                        </div>
                    </div>

                    <!-- Password Update Section -->
                    <div class="card shadow-lg mb-4">
                        <div class="card-body">
                            <h4 class="fw-bold mb-4">Change Password</h4>
                            <form method="post" action="" onsubmit="return validatePassword()">
                                <div class="validation-message text-center">
                                    <?php if (isset($message)) {
                                        echo $message;
                                    } ?>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" name="currentPassword" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="newPassword" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" name="confirmPassword" class="form-control" required>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 manage">Submit</button>
                            </form>
                        </div>
                    </div>

                    <!-- Address Management Section -->
                    <div class="card shadow input-field">
                        <div class="card-body">
                            <h4>Manage Addresses</h4>

                            <!-- Address List -->
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
                                        <button type="submit" class="btn btn-primary w-100 manage">Delete</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>

                            <!-- Add New Address Button -->
                            <button class="btn btn-primary w-100 manage " data-bs-toggle="modal" data-bs-target="#addressModal">
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
                        <!-- <button class="btn btn-primary w-100 manage" data-bs-toggle="modal" data-bs-target="#addressModal">
    Add New Address
</button> -->
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

    </body>

    </html>