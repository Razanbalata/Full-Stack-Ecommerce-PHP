<?php
// pages/my-orders.php — سجل طلبات المستخدم
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'سجل طلباتي — EliteShop';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/navbar.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /php-ecommerce-project/auth/login.php?msg=login_required');
    exit;
}

$userId = (int)$_SESSION['user_id'];

// جلب الطلبات الخاصة بالمستخدم الحالي مرتبة من الأحدث للأقدم
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$ordersResult = $stmt->get_result();
?>

<main class="main-container" style="max-width: 1000px; margin: 50px auto; padding: 0 20px; font-family: system-ui, -apple-system, sans-serif; direction: rtl; min-height: 75vh;">
    
    <div style="margin-bottom: 35px; text-align: right;">
        <h1 style="font-size: 32px; color: #0f172a; font-weight: 800; margin-bottom: 8px;">📦 سجل طلباتي السابق</h1>
        <div style="width: 50px; height: 4px; background-color: #3b82f6; border-radius: 2px;"></div>
    </div>

    <?php if ($ordersResult->num_rows === 0): ?>
        <div style="text-align: center; padding: 60px 20px; background: #ffffff; border-radius: 24px; border: 1px solid #e2e8f0;">
            <p style="font-size: 16px; color: #64748b; margin-bottom: 20px;">لم تقم بإجراء أي طلبات حتى الآن.</p>
            <a href="/php-ecommerce-project/pages/products.php" style="background: #0f172a; color: white; text-decoration: none; padding: 12px 24px; border-radius: 12px; font-weight: bold; font-size: 14px;">ابدأ التسوق الآن</a>
        </div>
    <?php else: ?>
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 24px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.01);">
            <table style="width: 100%; border-collapse: collapse; text-align: right;">
                <thead>
                    <tr style="background-color: #0f172a; color: #ffffff;">
                        <th style="padding: 16px 20px; font-size: 14px;">رقم الطلب</th>
                        <th style="padding: 16px 20px; font-size: 14px;">التاريخ</th>
                        <th style="padding: 16px 20px; font-size: 14px;">الإجمالي الكلي</th>
                        <th style="padding: 16px 20px; font-size: 14px;">الحالة</th>
                        <th style="padding: 16px 20px; font-size: 14px; text-align: left;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $ordersResult->fetch_assoc()): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 16px 20px; font-weight: bold; color: #0f172a;">#<?= $order['order_id'] ?></td>
                            <td style="padding: 16px 20px; color: #475569; font-size: 13px;"><?= date('Y-m-d H:i', strtotime($order['order_date'])) ?></td>
                            <td style="padding: 16px 20px; font-weight: 700; color: #10b981;"><?= number_format($order['total_amount'], 2) ?> ₪</td>
                            <td style="padding: 16px 20px;">
                                <?php if ($order['status'] === 'pending'): ?>
                                    <span style="background: #fffbeb; color: #b45309; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid #fde68a;">قيد الانتظار ⏳</span>
                                <?php elseif ($order['status'] === 'paid'): ?>
                                    <span style="background: #f0fdf4; color: #16a34a; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid #bbf7d0;">تم الدفع ✓</span>
                                <?php else: ?>
                                    <span style="background: #f1f5f9; color: #475569; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;"><?= htmlspecialchars($order['status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 16px 20px; text-align: left;">
                                <a href="/php-ecommerce-project/pages/order-details.php?id=<?= $order['order_id'] ?>" style="color: #3b82f6; text-decoration: none; font-size: 13px; font-weight: bold;">عرض التفاصيل ←</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<?php require_once '../includes/footer.php'; ?>