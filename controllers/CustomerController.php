<?php
// session_start(); // استدعاء الجلسة مرة واحدة في بداية الملف
include_once $_SERVER['DOCUMENT_ROOT'] . '/models/Order.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';

class CustomerController {
    private $pdo;
    private $orderModel;

    public function __construct() {
        // Use the constants to create the PDO connection
        $this->pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
            DB_USER,
            DB_PASSWORD
        );

        // Check if the connection is successful
        if (!$this->pdo) {
            die("Could not connect to the database");
        }

        $this->orderModel = new Order($this->pdo);  // Pass the PDO instance to the Order model
    }

    // عرض تاريخ الطلبات
    public function orderHistory() {
        if (!isset($_SESSION['user_id'])) {
            echo "User is not logged in!";
            exit();
        }

        $customer_id = $_SESSION['user_id'];

        // الحصول على جميع الطلبات الخاصة بالعميل
        $orders = $this->orderModel->getByCustomer($customer_id);

        // عرض الطلبات إذا كانت موجودة
        if ($orders) {
            include '../views/customer/order-history.php';  // تضمين صفحة عرض الطلبات
        } else {
            echo "No orders found for this user.";
        }
    }

    // إعادة طلب
    public function reorder($order_id) {
        if (!isset($_SESSION['user_id'])) {
            echo "User is not logged in!";
            exit();
        }
    
        $customer_id = $_SESSION['user_id'];
    
        // الحصول على تفاصيل الطلب الأصلي
        $stmt = $this->orderModel->getByOrderId($order_id); // تأكد من أن هذه الدالة جلبت البيانات بشكل صحيح
        $originalOrder = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($originalOrder) {
            // التحقق إذا كان الحقل items يحتوي على بيانات JSON
            if (isset($originalOrder['items']) && !empty($originalOrder['items']) && is_array(json_decode($originalOrder['items'], true))) {
                // تحويل JSON إلى مصفوفة
                $items = json_decode($originalOrder['items'], true);
    
                // إعداد الطلب الجديد بناءً على البيانات الأصلية
                $this->orderModel->customer_id = $customer_id;
                $this->orderModel->items = $items;
                $this->orderModel->total = $originalOrder['total'];
    
                // إنشاء الطلب الجديد
                if ($this->orderModel->create()) {
                    header('Location: /customer/order-history');
                    exit();
                } else {
                    echo "Failed to reorder the items.";
                }
            } else {
                echo "Invalid order items.";  // عرض رسالة توضح أن البيانات غير صالحة
            }
        } else {
            echo "Order not found.";
        }
    }
    
// داخل CustomerController.php
public function getOrderDetails($order_id) {
    $stmt = $this->orderModel->getByOrderId($order_id);
    $orderDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($orderDetails) {
        // إذا كان الحقل items موجود وتحتوي على بيانات، نقوم بفك تشفيرها إلى مصفوفة
        if (isset($orderDetails['items']) && !empty($orderDetails['items']) && is_array(json_decode($orderDetails['items'], true))) {
            $orderDetails['items'] = json_decode($orderDetails['items'], true);
        } else {
            $orderDetails['items'] = []; // إذا لم تحتوي على بيانات أو كانت غير صحيحة
        }
    }

    return $orderDetails;
}



}
?>
