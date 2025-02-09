<?php
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
    public function register($full_name, $email, $password, $role) {
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

        // Validate new password
        $this->validation->validateNewPassword($newPassword);

        if (!$this->validation->isValid()) {
            throw new Exception(implode(" ", $this->validation->getErrors()));
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