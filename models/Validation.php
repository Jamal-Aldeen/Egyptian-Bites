<?php
require_once __DIR__ . '/../config/db.php';

class Validation {
    private $errors = [];

    // Check for empty fields
    public function checkEmptyFields($fields) {
        foreach ($fields as $field => $value) {
            if (empty($value)) {
                $this->errors[] = ucfirst($field) . " is required.";
            }
        }
    }

    // Validate new password strength
    public function validateNewPassword($password) {
        if (strlen($password) < 8) {
            $this->errors[] = "Password must be at least 8 characters long.";
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $this->errors[] = "Password must contain at least one uppercase letter.";
        }

        if (!preg_match('/[a-z]/', $password)) {
            $this->errors[] = "Password must contain at least one lowercase letter.";
        }

        if (!preg_match('/\d/', $password)) {
            $this->errors[] = "Password must contain at least one number.";
        }

        if (!preg_match('/[^\w]/', $password)) {
            $this->errors[] = "Password must contain at least one special character.";
        }
    }

    // Validate email format and check for existing emails (excluding current user email)
    public function validateEmail($email, $pdo, $currentUserId = null) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Invalid email format.";
            return;
        }

        $sql = "SELECT id FROM Users WHERE email = :email";
        $params = ['email' => $email];
        if ($currentUserId !== null) {
            $sql .= " AND id != :currentUserId";
            $params['currentUserId'] = $currentUserId;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        if ($stmt->rowCount() > 0) {
            $this->errors[] = "Email is already registered.";
        }
    }

    // Validate profile picture upload (extension, size, dimensions)
    public function validateProfilePic($profile_pic) {
        if (!empty($profile_pic['name'])) {
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            $file_ext = strtolower(pathinfo($profile_pic['name'], PATHINFO_EXTENSION));

            if (!in_array($file_ext, $allowed_types)) {
                $this->errors[] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
            }

            if ($profile_pic['size'] > 2 * 1024 * 1024) { // 2MB limit
                $this->errors[] = "Profile picture must be less than 2MB.";
            }

            list($width, $height) = getimagesize($profile_pic['tmp_name']);
            if ($width > 1000 || $height > 1000) {
                $this->errors[] = "Profile picture dimensions should not exceed 1000x1000 pixels.";
            }
        }
    }

    // Validate passwords and confirm they match
    public function validatePassword($password, $confirm_password) {
        if ($password !== $confirm_password) {
            $this->errors[] = "Passwords do not match!";
        }
        if (strlen($password) < 8) {
            $this->errors[] = "Password must be at least 8 characters long!";
        }
        if (!preg_match("/[A-Z]/", $password)) {
            $this->errors[] = "Password must contain at least one uppercase letter!";
        }
        if (!preg_match("/[0-9]/", $password)) {
            $this->errors[] = "Password must contain at least one number!";
        }
    }
// In your Validation.php file
public function validatePasswordStrength($password)
{
    // Check if password meets the regex criteria
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $this->errors[] = "Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.";
    }
}

    // Check if there are no errors
    public function isValid() {
        return empty($this->errors);
    }

    // Get all validation errors
    public function getErrors() {
        return $this->errors;
    }
}
?>
