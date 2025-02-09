<?php
require_once __DIR__ . '/../config/db.php';

class Validation {
    private $errors = [];

    public function checkEmptyFields($fields) {
        foreach ($fields as $field => $value) {
            if (empty($value)) {
                $this->errors[] = ucfirst($field) . " is required.";
            }
        }
    }

    public function validateEmail($email, $pdo) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Invalid email format.";
            return;
        }

        $stmt = $pdo->prepare("SELECT id FROM Users WHERE email = :email");
        $stmt->execute(['email' => $email]);

        if ($stmt->rowCount() > 0) {
            $this->errors[] = "Email is already registered.";
        }
    }

    public function validateFullName($full_name) {
        if (strlen($full_name) < 3 || strlen($full_name) > 50) {
            $this->errors[] = "Full Name must be between 3 and 50 characters.";
        }
        if (!preg_match("/^[a-zA-Z\s]+$/", $full_name)) {
            $this->errors[] = "Full Name can only contain letters and spaces.";
        }
    }

    public function validatePassword($password, $confirm_password) {
        if (strlen($password) < 8 || !preg_match("/[A-Za-z]/", $password) || !preg_match("/\d/", $password)) {
            $this->errors[] = "Password must be at least 8 characters long and contain at least one letter and one number.";
        }
        if ($password !== $confirm_password) {
            $this->errors[] = "Passwords do not match.";
        }
    }

    public function validateNewPassword($password) {
        if (strlen($password) < 8 || !preg_match("/[A-Za-z]/", $password) || !preg_match("/\d/", $password)) {
            $this->errors[] = "New Password must be at least 8 characters long and contain at least one letter and one number.";
        }
    }

    public function validateProfilePic($profile_pic) {
        if (!empty($profile_pic['name'])) {
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            $file_ext = strtolower(pathinfo($profile_pic['name'], PATHINFO_EXTENSION));

            if (!in_array($file_ext, $allowed_types)) {
                $this->errors[] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
            }

            if ($profile_pic['size'] > 2 * 1024 * 1024) { 
                $this->errors[] = "Profile picture must be less than 2MB.";
            }

            list($width, $height) = getimagesize($profile_pic['tmp_name']);
            if ($width > 1000 || $height > 1000) {
                $this->errors[] = "Profile picture dimensions should not exceed 1000x1000 pixels.";
            }
        }
    }

    public function getErrors() {
        return $this->errors;
    }

    public function isValid() {
        return empty($this->errors);
    }
}
?>