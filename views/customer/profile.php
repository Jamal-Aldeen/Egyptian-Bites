<?php
session_start();
include '../layouts/header.php';
require_once __DIR__ . '/../../models/User.php';

$userModel = new User();
$user = $userModel->findById($_SESSION['user_id']);
$addresses = $userModel->getAddresses($_SESSION['user_id']);

if (!$user) {
    $_SESSION['error'] = "User not found!";
    header("Location: /index.php");
    exit();
}
?>

<div class="container py-5">
    <div class="row">
        <!-- Profile Section -->
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body text-center">
                    <img src="/public/uploads/<?= htmlspecialchars($user['profile_picture'] ?? 'default.jpg') ?>"
                        class="rounded-circle mb-3"
                        style="width: 150px; height: 150px; object-fit: cover;">
                    <h4><?= htmlspecialchars($user['full_name']) ?></h4>
                    <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Update Profile Form -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h4>Update Profile</h4>
                    <form action="/controllers/AuthController.php?action=update_profile" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control"
                                value="<?= htmlspecialchars($user['full_name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Profile Picture</label>
                            <input type="file" name="profile_picture" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
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
                            <form action="/controllers/AuthController.php?action=delete_address" method="POST" class="d-inline">
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

<?php include '../layouts/footer.php'; ?>