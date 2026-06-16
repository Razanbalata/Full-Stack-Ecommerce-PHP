<?php
// pages/order-details.php — عرض تفاصيل فاتورة محددة
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$pageTitle = 'تفاصيل الفاتورة — EliteShop';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/navbar.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /php-ecommerce-project/auth/login.php'); exit;
}

$orderId = (int)($_GET['id'] ?? 0);
$userId = (int)$_SESSION['user_id'];

// جلب تفاصيل الطلب الرئيسي للتأكد أنه يخص المستخدم الحالي (أمان وخصوصية)
$orderStmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$orderStmt->bind_param('ii', $orderId, $userId);
$orderStmt->execute();
$order = $orderStmt->get_result()->fetch_assoc();

if (!$order) {
    echo "<div style='text-align:center; margin:50px; color:red;'>⚠️ الطلب غير موجود أو لا تملك صلاحية عرضه.</div>";
    require_once '../includes/footer.php'; exit;
}

// جلب العناصر داخل هذه الفاتورة مع أسماء المنتجات وصورها من جدول order_items وجدول products
$itemsStmt = $conn->prepare("
    SELECT oi.*, p.name, p.image_url 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.product_id 
    WHERE oi.order_id = ?
");
$itemsStmt->bind_param('i', $orderId);
$itemsStmt->execute();
$itemsResult = $itemsStmt->get_result();
?>

<main style="max-width: 800px; margin: 50px auto; padding: 0 20px; font-family: system-ui, sans-serif; direction: rtl; text-align: right;">
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 24px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.01);">
        <h2 style="margin-top:0; color:#0f172a;">📄 تفاصيل الفاتورة رقم #<?= $order['order_id'] ?></h2>
        <p style="color:#64748b; font-size:14px;">تاريخ الطلب: <?= $order['order_date'] ?> | حالة الطلب: <strong style="color:#f59e0b;"><?= $order['status'] ?></strong></p>
        <p style="background:#f8fafc; padding:12px; border-radius:8px; font-size:14px; color:#475569;">📍 <strong>عنوان الشحن لهذه الفاتورة:</strong> <?= htmlspecialchars($order['shipping_address']) ?></p>
        
        <table style="width:100%; border-collapse:collapse; margin-top:20px;">
            <thead>
                <tr style="background:#f1f5f9; color:#475569; text-align:right;">
                    <th style="padding:12px;">المنتج</th><th style="padding:12px;">السعر المفرد</th><th style="padding:12px;">الكمية</th><th style="padding:12px;">الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                <?php while($item = $itemsResult->fetch_assoc()): ?>
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:12px; font-weight:600;"><?= htmlspecialchars($item['name']) ?></td>
                        <td style="padding:12px;"><?= $item['unit_price'] ?> ₪</td>
                        <td style="padding:12px;"><?= $item['quantity'] ?></td>
                        <td style="padding:12px; font-weight:bold; color:#10b981;"><?= ($item['unit_price'] * $item['quantity']) ?> ₪</td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <div style="text-align:left; margin-top:25px; font-size:18px; font-weight:bold; color:#0f172a;">
            المجموع الكلي المدفوع: <span style="color:#10b981; font-size:22px;"><?= $order['total_amount'] ?> ₪</span>
        </div>
        <div style="margin-top:20px;"><a href="profile.php" style="color:#3b82f6; text-decoration:none; font-weight:bold; font-size:14px;">← العودة للملف الشخصي</a></div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>