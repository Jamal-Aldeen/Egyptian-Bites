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

    // Find user by ID
    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Find user by email
    public function findByEmail($email)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create a new user
    public function create($full_name, $email, $password, $role, $verificationToken = null)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO Users (full_name, email, password, role, verification_token) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $email, $hashedPassword, $role, $verificationToken]);
        return $this->pdo->lastInsertId();
    }

    public function changePassword($userId, $newPassword)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE Users SET password = :password WHERE id = :user_id");
            $result = $stmt->execute([
                'password' => $newPassword,
                'user_id' => $userId
            ]);
            if ($result) {
                error_log("Password updated successfully for user ID: $userId");
            } else {
                error_log("Failed to update password for user ID: $userId");
            }
            return $result;
        } catch (Exception $e) {
            error_log("Error updating password: " . $e->getMessage());
            return false;
        }
    }
    
    
    // Verify email
    public function verifyEmail($token)
    {
        $stmt = $this->pdo->prepare("UPDATE Users SET verified = TRUE, verification_token = NULL WHERE verification_token = ?");
        return $stmt->execute([$token]);
    }

    // Get addresses for a user
    public function getAddresses($userId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM UserAddresses WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add a new address for the user
    public function addAddress($userId, $label, $addressLine1, $addressLine2, $city)
    {
        $stmt = $this->pdo->prepare("INSERT INTO UserAddresses (user_id, label, address_line1, address_line2, city) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$userId, $label, $addressLine1, $addressLine2, $city]);
    }

    // Delete an address
    public function deleteAddress($addressId, $userId)
    {
        $stmt = $this->pdo->prepare("DELETE FROM UserAddresses WHERE id = ? AND user_id = ?");
        return $stmt->execute([$addressId, $userId]);
    }

    // Update user profile information
    public function updateProfile($id, $fullName = null, $email = null, $profilePicture = null)
    {
        $sql = "UPDATE Users SET";
        $params = [];
    
        if ($fullName !== null) {
            $sql .= " full_name = :full_name,";
            $params['full_name'] = $fullName;
        }
    
        if ($email !== null) {
            $sql .= " email = :email,";
            $params['email'] = $email;
        }
    
        if ($profilePicture !== null) {
            $sql .= " profile_picture = :profile_picture,";
            $params['profile_picture'] = $profilePicture;
        }
    
        $sql = rtrim($sql, ',');
    
        $sql .= " WHERE id = :id";
        $params['id'] = $id;
    
        try {
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute($params)) {
                return true;
            } else {
                error_log("Update failed: " . implode(", ", $stmt->errorInfo()));
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error updating profile: " . $e->getMessage());
            return false;
        }
    }
    
    // Update user profile picture with validation
    
public function updateProfilePicture($userId, $profilePictureName)
{
    $query = "UPDATE users SET profile_picture = :profile_picture WHERE id = :user_id";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(':profile_picture', $profilePictureName);
    $stmt->bindParam(':user_id', $userId);

    return $stmt->execute();
}
public function handleProfilePicture($file, $userId)
{
    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '/../../public/uploads/';
        $uploadFile = $uploadDir . basename($file['name']);

        // Check if upload directory exists, if not, create it
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move the uploaded file to the desired directory
        if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
            // Update profile picture in database
            return $this->updateProfilePicture($userId, $uploadFile);
        } else {
            throw new Exception("Failed to move uploaded file.");
        }
    } else {
        throw new Exception("No file uploaded or there was an error with the upload.");
    }
}




    // Get all users
    public function getAllUsers()
    {
        $stmt = $this->pdo->query("SELECT * FROM Users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update role of a user
    public function updateRole($userId, $newRole)
    {
        $stmt = $this->pdo->prepare("UPDATE Users SET role = ? WHERE id = ?");
        return $stmt->execute([$newRole, $userId]);
    }

    // Delete a user
    public function delete($userId)
    {
        $stmt = $this->pdo->prepare("DELETE FROM Users WHERE id = ?");
        return $stmt->execute([$userId]);
    }
}
