<?php
include_once '../models/Order.php';
include_once '../config/db.php';

class StaffController {
    private $pdo;

    public function __construct() {
        $this->pdo = $GLOBALS['pdo']; // استخدام الـ PDO من db.php مباشرة
    }

    // عرض جميع الطلبات مع الصفحات
    public function manageOrders() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
            header("Location: /login");
            exit();
        }

        // التحقق من الصفحة الحالية
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max($page, 1); // التأكد من أن الصفحة لا تكون أقل من 1

        $limit = 10;
        $offset = ($page - 1) * $limit;

        // استعلام لاسترجاع الطلبات
        $stmt = $this->pdo->prepare("SELECT o.*, u.name AS customer_name 
        FROM Orders o 
        LEFT JOIN Users u ON o.user_id = u.id 
        LIMIT :limit OFFSET :offset");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // حساب إجمالي الطلبات والصفحات
        $totalOrders = $this->pdo->query("SELECT COUNT(*) FROM Orders")->fetchColumn();
        $totalPages = ceil($totalOrders / $limit);
        $page = min($page, $totalPages); // التأكد من أن الصفحة لا تتجاوز الإجمالي

        // إرسال البيانات إلى العرض
        include '../views/staff/order-management.php';
    }
}
?>
