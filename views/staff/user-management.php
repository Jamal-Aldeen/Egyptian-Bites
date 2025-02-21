<?php
session_start();
require_once __DIR__ . '/../../models/User.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /views/shared/login.php"); // Not logged in → login page
    exit();
} elseif ($_SESSION['role'] !== 'Staff') {
    header("Location: /index.php"); // Logged in but not staff → index
    exit();
}
$userModel = new User();
$users = $userModel->getAllUsers();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        <?php
        require_once "../../public/css/dashboard.css";
        ?>
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-light">
    <div class="container-fluid mt-4">
        <div class="row">

            <?php require_once "../layouts/sidebar.php"; ?>

            <div class="col-md-9 ms-sm-auto col-lg-10 px-4 mt-4>
                <div class=" container mt-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">User Management</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($user['id']) ?></td>
                                            <td><?= htmlspecialchars($user['full_name']) ?></td>
                                            <td><?= htmlspecialchars($user['email']) ?></td>
                                            <td>
                                                <form action="../../handlers/user-handler.php" method="POST" class="d-inline role-update-form">
                                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                    <input type="hidden" name="update_role" value="1"> <!-- Add this line -->
                                                    <select name="role" class="form-select form-select-sm">
                                                        <option value="Staff" <?= $user['role'] === 'Staff' ? 'selected' : '' ?>>Staff</option>
                                                        <option value="Customer" <?= $user['role'] === 'Customer' ? 'selected' : '' ?>>Customer</option>
                                                    </select>
                                                </form>
                                            </td>
                                            <td>
                                                <form action="../../handlers/user-handler.php" method="POST" class="d-inline">
                                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                    <button type="submit" name="delete_user" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Bootstrap JS and FontAwesome -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle role updates
            document.querySelectorAll('[name="role"]').forEach(select => {
                select.addEventListener('change', function() {
                    const form = this.closest('form');
                    const formData = new FormData(form);

                    // Show loading state
                    const originalText = this.nextElementSibling?.innerHTML || '';
                    if (this.nextElementSibling) {
                        this.nextElementSibling.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                    }

                    fetch(form.action, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Highlight the updated row
                                const row = form.closest('tr');
                                row.style.backgroundColor = '#d4edda'; // Green background
                                setTimeout(() => row.style.backgroundColor = '', 2000); // Reset after 2 seconds
                            } else {
                                alert(data.error || 'Failed to update role');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while updating the role');
                        })
                        .finally(() => {
                            // Restore original text
                            if (this.nextElementSibling) {
                                this.nextElementSibling.innerHTML = originalText;
                            }
                        });
                });
            });

            // Handle user deletions
            document.querySelectorAll('[name="delete_user"]').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    if (confirm('Are you sure you want to delete this user?')) {
                        const form = this.closest('form');
                        const formData = new FormData(form);

                        // Add the delete_user action to the form data
                        formData.append('delete_user', '1');

                        // Show loading state
                        const originalText = this.innerHTML;
                        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
                        this.disabled = true;

                        fetch(form.action, {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Remove the row from the table
                                    form.closest('tr').remove();
                                } else {
                                    alert(data.error || 'Failed to delete user');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('An error occurred while deleting the user');
                            })
                            .finally(() => {
                                // Restore original button state
                                this.innerHTML = originalText;
                                this.disabled = false;
                            });
                    }
                });
            });
        });
    </script>
</body>

</html>