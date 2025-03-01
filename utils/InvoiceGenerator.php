<!-- <?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/PHPMailer/PHPMailerAutoload.php'; // التضمين التلقائي لمكتبة PHPMailer

class InvoiceGenerator {
    public static function generateInvoice($user_id, $order_id, $amount, $transaction_id) {
        global $pdo;

        // استرجاع بيانات الطلب من قاعدة البيانات بناءً على رقم الطلب
        $stmt = $pdo->prepare("SELECT o.id, o.amount, u.email, u.full_name FROM Orders o JOIN Users u ON o.user_id = u.id WHERE o.id = ? AND o.user_id = ?");
        $stmt->execute([$order_id, $user_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        // التأكد من وجود الطلب في قاعدة البيانات
        if (!$order) {
            throw new Exception("Order not found.");
        }

        // إنشاء الفاتورة
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        $pdf->Cell(190, 10, 'Invoice', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);

        $pdf->Cell(100, 10, "Invoice No: INV$order_id", 0, 1);
        $pdf->Cell(100, 10, "Date: " . date('Y-m-d'), 0, 1);
        $pdf->Cell(100, 10, "Customer: " . $order['full_name'], 0, 1);
        $pdf->Cell(100, 10, "Email: " . $order['email'], 0, 1);
        $pdf->Cell(100, 10, "Total: $" . number_format($amount, 2), 0, 1);

        // تحديد مكان تخزين الفاتورة
        $filePath = __DIR__ . "/../invoices/invoice_$order_id.pdf";
        $pdf->Output('F', $filePath);

        return $filePath;
    }
}
?> -->
