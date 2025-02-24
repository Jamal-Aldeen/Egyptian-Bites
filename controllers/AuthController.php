<?php
session_start();
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Validation.php';

class AuthController
{
    private $userModel;
    private $validation;
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
        $this->userModel = new User();
        $this->validation = new Validation();
    }

    // User registration
    public function register($full_name, $email, $password, $role)
    {
        $this->validation->checkEmptyFields([
            "Full Name" => $full_name,
            "Email" => $email,
            "Password" => $password
        ]);
        $this->validation->validateEmail($email, $this->pdo);
        $this->validation->validatePassword($password, $password);

        if (!$this->validation->isValid()) {
            throw new Exception(implode(" ", $this->validation->getErrors()));
        }

        if ($this->userModel->findByEmail($email)) {
            throw new Exception("Email already registered.");
        }

        $verificationToken = bin2hex(random_bytes(32));
        $userId = $this->userModel->create($full_name, $email, $password, $role, $verificationToken);
        $this->sendVerificationEmail($email, $verificationToken);

        return $userId;
    }

    // User login
    public function login($email, $password)
    {
        // Sanitize the email input
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        
        // Fetch user by email
        $user = $this->userModel->findByEmail($email);
    
        if (!$user || !password_verify($password, $user['password'])) {
            throw new Exception("Invalid email or password.");
        }
    
        if (!$user['verified']) {
            throw new Exception("Please verify your email before logging in.");
        }
    
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
    
        // Store session data
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
    
        // Redirect based on user role
        if ($user['role'] === 'Staff') {
            header("Location: /views/staff/dashboard.php");
        } else {
            header("Location: /views/customer/profile.php");
        }
        exit();
    }

    // Password reset logic
    public function resetPassword($email, $currentPassword, $newPassword, $confirmNewPassword)
    {
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            throw new Exception("Email not found.");
        }

        // Check if the current password matches the one stored in the database
        if (!password_verify($currentPassword, $user['password'])) {
            throw new Exception("Current password is incorrect.");
        }

        // Check if the new passwords match
        if ($newPassword !== $confirmNewPassword) {
            throw new Exception("New passwords do not match.");
        }

        // Validate new password (e.g., length, complexity)
        $this->validation->validateNewPassword($newPassword);

        if (!$this->validation->isValid()) {
            throw new Exception(implode(" ", $this->validation->getErrors()));
        }

        // Update password in the database
        $result = $this->userModel->changePassword($user['id'], $newPassword);

        if ($result) {
            // After the password is reset successfully, refresh the session data
            $user = $this->userModel->findById($user['id']); // Get updated user data
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['profile_picture'] = $user['profile_picture'] ?? null; // Update profile picture if available
        }

        return $result;
    }

    // Logout method to ensure session is properly cleared if needed
    public function logout()
    {
        session_unset();
        session_destroy();
        header("Location: /login");
        exit();
    }

    // Add address method
    public function addAddress($userId, $addressData)
    {
        return $this->userModel->addAddress(
            $userId,
            $addressData['label'],
            $addressData['address_line1'],
            $addressData['address_line2'],
            $addressData['city']
        );
    }

    // Delete address method
    public function deleteAddress($addressId, $userId)
    {
        return $this->userModel->deleteAddress($addressId, $userId);
    }

    // Private method for handling profile picture upload
    private function handleProfilePicture($file, $userId)
    {
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $targetDir = __DIR__ . "/../public/uploads/";

            if (!is_dir($targetDir)) {
                if (!mkdir($targetDir, 0777, true)) {
                    error_log("Failed to create directory: $targetDir");
                    throw new Exception("Failed to create upload directory.");
                }
            }

            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = "user_{$userId}_" . time() . ".$extension";
            $targetFile = $targetDir . $filename;

            if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
                error_log("Upload failed. Check permissions for: $targetDir");
                throw new Exception("Failed to save profile picture.");
            }
            return $filename;
        } elseif ($file['error'] !== UPLOAD_ERR_NO_FILE) {
            error_log("Upload error code: " . $file['error']);
            throw new Exception("File upload error: " . $file['error']);
        }
        return null;
    }

    // Private method to send verification email
    private function sendVerificationEmail($email, $token)
    {
        $subject = "Verify Your Email";
        $message = "Click the link to verify your email: http://yourapp.com/verify-email?token=$token";
        mail($email, $subject, $message);
    }

    // Update user profile method
    public function updateProfile($userId, $fullName, $email, $profilePicture = null)
    {
        error_log("Updating profile for user ID: $userId");
        error_log("Full Name: $fullName, Email: $email");

        // Validate inputs
        $this->validation->checkEmptyFields(["Full Name" => $fullName, "Email" => $email]);
        $this->validation->validateEmail($email, $this->pdo, $userId);

        if (!$this->validation->isValid()) {
            throw new Exception(implode(" ", $this->validation->getErrors()));
        }

        // Handle profile picture upload
        $profilePictureName = $this->handleProfilePicture($profilePicture, $userId);
        error_log("Profile Picture Name: " . ($profilePictureName ?? "None"));

        // Update the profile
        $result = $this->userModel->updateProfile(
            $userId,
            $fullName,
            $email,
            $profilePictureName
        );

        if ($result) {
            error_log("Profile updated successfully.");
            // Refresh session data after successful update
            $user = $this->userModel->findById($userId);
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            if ($profilePictureName) {
                $_SESSION['profile_picture'] = $profilePictureName;
            }
        } else {
            error_log("Failed to update profile.");
        }

        return $result;
    }
}

// Handle profile actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController = new AuthController();
    $userId = $_SESSION['user_id'];

    try {
        // Perform the action based on the 'action' parameter
        switch ($_GET['action']) {
            case 'update_profile':
                $authController->updateProfile(
                    $userId,
                    $_POST['full_name'],
                    $_POST['email'],
                    $_FILES['profile_picture']
                );
                break;
                
            case 'reset_password':  // Add this case for resetting the password
                $authController->resetPassword(
                    $userId,
                    $_POST['current_password'],
                    $_POST['new_password'],
                    $_POST['confirm_new_password']
                );
                break;

            case 'add_address':
                $authController->addAddress($userId, $_POST);
                break;
                
            case 'delete_address':
                $authController->deleteAddress($_POST['address_id'], $userId);
                break;

            // Handle any unrecognized action
            default:
                throw new Exception("Unknown action: " . $_GET['action']);
        }

        // Redirect after successful execution of the action
        header("Location: /views/customer/profile.php");
        exit();
    } catch (Exception $e) {
        // If an exception occurs, store the error message in the session and redirect
        $_SESSION['error'] = $e->getMessage();
        header("Location: /views/customer/profile.php");
        exit();
    }
}
