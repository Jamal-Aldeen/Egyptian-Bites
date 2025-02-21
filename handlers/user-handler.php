<?php
session_start();
require_once __DIR__ . '/../controllers/UserController.php';

// Set response header to JSON
header('Content-Type: application/json');

// Authorization check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Staff') {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

$userController = new UserController();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Log the incoming POST data for debugging
        error_log(print_r($_POST, true));

        if (isset($_POST['update_role'])) {
            // Validate user ID and role
            $userId = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
            $newRole = htmlspecialchars($_POST['role']);

            if (!$userId || !in_array($newRole, ['Staff', 'Customer'])) {
                throw new Exception('Invalid input data');
            }

            // Update user role
            $success = $userController->updateUserRole($userId, $newRole);

            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Role updated successfully']);
            } else {
                throw new Exception('Failed to update role');
            }
        } elseif (isset($_POST['delete_user'])) {
            // Validate user ID
            $userId = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);

            if (!$userId) {
                throw new Exception('Invalid user ID');
            }

            // Delete user
            $success = $userController->deleteUser($userId);

            if ($success) {
                echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
            } else {
                throw new Exception('Failed to delete user');
            }
        } else {
            throw new Exception('Invalid action: No valid action detected in POST data');
        }
    } else {
        throw new Exception('Invalid request method: Only POST requests are allowed');
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit();
}