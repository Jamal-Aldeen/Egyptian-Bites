<?php

class Validation {
    
    private $errors = [];

    public function checkEmptyFields($fields) {
        foreach ($fields as $field => $value) {
            if (empty($value)) {
                $this->errors[] = ucfirst($field) . " is required.";
            }
        }
    }

    public function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Invalid email format.";
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

    public function validateProfilePic($profile_pic) {
        if (!empty($profile_pic['name'])) {
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array(strtolower(pathinfo($profile_pic['name'], PATHINFO_EXTENSION)), $allowed_types)) {
                $this->errors[] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
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
