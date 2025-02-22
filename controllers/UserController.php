<?php
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $userModel;

    public function __construct() {
        global $pdo;
        $this->userModel = new User();
    }

    public function getAllUsers() {
        return $this->userModel->getAllUsers();
    }

    public function updateUserRole($userId, $newRole) {
        return $this->userModel->updateRole($userId, $newRole);
    }

    public function deleteUser($userId) {
        return $this->userModel->delete($userId);
    }
    
}
?>