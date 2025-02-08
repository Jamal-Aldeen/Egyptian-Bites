<?php
require_once __DIR__ . '/../config/db.php';

class User {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    // Find user by ID
    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Find user by email
    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    // Create a new user
    public function create($username, $email, $password, $role, $verificationToken = null) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO Users (username, email, password, role, verification_token) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $email, $hashedPassword, $role, $verificationToken]);
        return $this->pdo->lastInsertId();
    }

    // Update user profile
    public function updateProfile($id, $username, $email, $profilePicture = null) {
        $sql = "UPDATE Users SET username = ?, email = ?";
        $params = [$username, $email];

        if ($profilePicture) {
            $sql .= ", profile_picture = ?";
            $params[] = $profilePicture;
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    // Change password
    public function changePassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("UPDATE Users SET password = ? WHERE id = ?");
        return $stmt->execute([$hashedPassword, $id]);
    }

    // Verify email
    public function verifyEmail($token) {
        $stmt = $this->pdo->prepare("UPDATE Users SET verified = TRUE, verification_token = NULL WHERE verification_token = ?");
        return $stmt->execute([$token]);
    }
}
?>