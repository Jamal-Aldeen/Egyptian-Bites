<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // User registration
    public function register($username, $email, $password, $role) {
        // Validate password strength
        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
            throw new Exception("Password must be at least 8 characters long and include at least one letter and one number.");
        }

        // Check if email already exists
        if ($this->userModel->findByEmail($email)) {
            throw new Exception("Email already registered.");
        }

        // Generate verification token
        $verificationToken = bin2hex(random_bytes(32));

        // Create user
        $userId = $this->userModel->create($username, $email, $password, $role, $verificationToken);

        // Send verification email
        $this->sendVerificationEmail($email, $verificationToken);

        return $userId;
    }

    // User login
    public function login($email, $password) {
        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            throw new Exception("Invalid email or password.");
        }

        if (!$user['verified']) {
            throw new Exception("Please verify your email before logging in.");
        }

        return $user;
    }

    // Password recovery
    public function resetPassword($email, $newPassword) {
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            throw new Exception("Email not found.");
        }

        return $this->userModel->changePassword($user['id'], $newPassword);
    }

    // Send verification email
    private function sendVerificationEmail($email, $token) {
        $subject = "Verify Your Email";
        $message = "Click the link to verify your email: http://yourapp.com/verify-email?token=$token";
        mail($email, $subject, $message);
    }
}
?>