<?php
session_start(); 
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Validation.php';

class AuthController {
    private $userModel;
    private $validation;
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        $this->userModel = new User();
        $this->validation = new Validation();
    }

    // User registration
    public function register($full_name, $email, $password, $role)
    {
        // Validate inputs
        $this->validation->checkEmptyFields([
            "Full Name" => $full_name,
            "Email" => $email,
            "Password" => $password
        ]);
        $this->validation->validateEmail($email, $this->pdo); // Pass $pdo here
        $this->validation->validatePassword($password, $password); // Confirm password is the same

        if (!$this->validation->isValid()) {
            throw new Exception(implode(" ", $this->validation->getErrors()));
        }

        // Check if email already exists
        if ($this->userModel->findByEmail($email)) {
            throw new Exception("Email already registered.");
        }

        // Generate verification token
        $verificationToken = bin2hex(random_bytes(32));

        // Create user
        $userId = $this->userModel->create($full_name, $email, $password, $role, $verificationToken);

        // Send verification email
        $this->sendVerificationEmail($email, $verificationToken);

        return $userId;
    }

    // User login
    public function login($email, $password)
    {
        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            throw new Exception("Invalid email or password.");
        }

        if (!$user['verified']) {
            throw new Exception("Please verify your email before logging in.");
        }

        // Set session variables
        // In login method
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];

        // Redirect based on role
        if ($user['role'] === 'Staff') {
            header("Location: /views/staff/dashboard.php");
        } else {
            header("Location: /views/customer/profile.php");
        }
        exit();
    }
    
    //  logout method
public function logout() {
    session_start();
    session_unset();
    session_destroy();
    header("Location: /login"); // Redirect to login page
    exit();
}

    // Password recovery
    public function resetPassword($email, $newPassword)
    {
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            throw new Exception("Email not found.");
        }

        // Validate new password
        $this->validation->validateNewPassword($newPassword);

        if (!$this->validation->isValid()) {
            throw new Exception(implode(" ", $this->validation->getErrors()));
        }

        return $this->userModel->changePassword($user['id'], $newPassword);
    }

    // Send verification email
    private function sendVerificationEmail($email, $token)
    {
        $subject = "Verify Your Email";
        $message = "Click the link to verify your email: http://yourapp.com/verify-email?token=$token";
        mail($email, $subject, $message);
    }

    public function updateProfile($userId, $fullName, $email, $profilePicture = null) {
        error_log("Updating profile for user ID: $userId");
        error_log("Full Name: $fullName, Email: $email");
    
        $this->validation->checkEmptyFields(["Full Name" => $fullName, "Email" => $email]);
        $this->validation->validateEmail($email, $this->pdo, $userId);
    
        if (!$this->validation->isValid()) {
            throw new Exception(implode(" ", $this->validation->getErrors()));
        }
    
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
            // Refresh session data
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

    public function deleteAddress($addressId, $userId)
    {
        return $this->userModel->deleteAddress($addressId, $userId);
    }

    private function handleProfilePicture($file, $userId) {
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $targetDir = __DIR__ . "/../../public/uploads/";
    
            // Ensure the uploads directory exists
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true); // Create the directory if it doesn't exist
            }
    
            // Generate a unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = "user_{$userId}_" . time() . ".$extension";
            $targetFile = $targetDir . $filename;
    
            // Move the uploaded file to the target directory
            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                return $filename; // Return the filename for database storage
            } else {
                error_log("Failed to move uploaded file to: $targetFile");
                throw new Exception("Failed to upload profile picture.");
            }
        }
        return null; // No file uploaded
    }
}

// Handle profile actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController = new AuthController();
    $userId = $_SESSION['user_id'];

    try {
        switch ($_GET['action']) {
            case 'update_profile':
                $authController->updateProfile(
                    $userId,
                    $_POST['full_name'],
                    $_POST['email'],
                    $_FILES['profile_picture']
                );
                break;
            case 'add_address':
                $authController->addAddress($userId, $_POST);
                break;
            case 'delete_address':
                $authController->deleteAddress($_POST['address_id'], $userId);
                break;
        }
        header("Location: /views/customer/profile.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: /views/customer/profile.php");
        exit();
    }
}