<?php
require_once __DIR__ . '/../config/db.php';

class Menu {
    private $pdo;

    public function __construct($pdo = null) {
        global $conn;
        $this->pdo = $pdo ?: (isset($GLOBALS['conn']) ? $GLOBALS['conn'] : null);
    }

    public function getMenuItems() {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM Menu");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching menu: " . $e->getMessage());
            return false;
        }
    }
}
?>