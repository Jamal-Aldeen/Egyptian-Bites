<?php
require_once __DIR__ . '/../config/db.php';

class User
{
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        error_log("Database connection established: " . ($this->pdo ? "Yes" : "No"));
    }

    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Explicitly specify fetch mode
    }
    // Find user by email
    public function findByEmail($email)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    // Create a new user
    public function create($full_name, $email, $password, $role, $verificationToken = null)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO Users (full_name, email, password, role, verification_token) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $email, $hashedPassword, $role, $verificationToken]);
        return $this->pdo->lastInsertId();
    }

    // Change password
    public function changePassword($id, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("UPDATE Users SET password = ? WHERE id = ?");
        return $stmt->execute([$hashedPassword, $id]);
    }

    // Verify email
    public function verifyEmail($token)
    {
        $stmt = $this->pdo->prepare("UPDATE Users SET verified = TRUE, verification_token = NULL WHERE verification_token = ?");
        return $stmt->execute([$token]);
    }

    // Add these new methods for address management
    public function getAddresses($userId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM UserAddresses WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function addAddress($userId, $label, $addressLine1, $addressLine2, $city)
    {
        $stmt = $this->pdo->prepare("INSERT INTO UserAddresses 
            (user_id, label, address_line1, address_line2, city)
            VALUES (?, ?, ?, ?, ?)");

        return $stmt->execute([
            $userId,
            $label,
            $addressLine1,
            $addressLine2,
            $city
        ]);
    }

    public function deleteAddress($addressId, $userId)
    {
        $stmt = $this->pdo->prepare("DELETE FROM UserAddresses WHERE id = ? AND user_id = ?");
        return $stmt->execute([$addressId, $userId]);
    }

    // UpdateProfile method
    public function updateProfile($id, $fullName, $email, $profilePicture = null)
    {
        $sql = "UPDATE Users SET full_name = ?, email = ?";
        $params = [$fullName, $email];

        if ($profilePicture) {
            $sql .= ", profile_picture = ?";
            $params[] = $profilePicture;
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        // Return the updated user data
        return $this->findById($id);
    }
}
