<?php
// pages/profile.php — صفحة الملف الشخصي وسجل الطلبات للمستخدم
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'ملفي الشخصي — EliteShop';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/navbar.php';

// حماية الصفحة: يجب أن يكون المستخدم مسجلاً للدخول
if (!isset($_SESSION['user_id'])) {
    header('Location: /php-ecommerce-project/auth/login.php?msg=login_required');
    exit;
}

$userId = (int)$_SESSION['user_id'];
$successMsg = '';
$errorMsg = '';

// 1. معالجة تحديث البيانات الشخصية (الاسم، الهاتف، العنوان) عند إرسال الفورم
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fullName = trim($_POST['full_name']);
    $phone    = trim($_POST['phone']);
    $address  = trim($_POST['address']);

    if (empty($fullName)) {
        $errorMsg = 'الاسم الكامل مطلوب ولا يمكن تركه فارغاً.';
    } else {
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, address = ? WHERE user_id = ?");
        $stmt->bind_param('sssi', $fullName, $phone, $address, $userId);
        
        if ($stmt->execute()) {
            $_SESSION['full_name'] = $fullName; // تحديث الاسم في الجلسة ليظهر فوراً في النيفبار
            $successMsg = 'تم تحديث بيانات ملفكِ الشخصي بنجاح! ✨';
        } else {
            $errorMsg = 'حدث خطأ أثناء التحديث، يرجى المحاولة مرة أخرى.';
        }
    }
}

// 2. جلب بيانات المستخدم الحالية الحية من قاعدة البيانات
$userStmt = $conn->prepare("SELECT full_name, email, phone, address FROM users WHERE user_id = ?");
$userStmt->bind_param('i', $userId);
$userStmt->execute();
$user = $userStmt->get_result()->fetch_assoc();

// 3. جلب سجل الطلبات الخاص بهذا العميل (Order History)
$ordersStmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$ordersStmt->bind_param('i', $userId);
$ordersStmt->execute();
$ordersResult = $ordersStmt->get_result();
?>

<main class="main-container" style="max-width: 1100px; margin: 50px auto; padding: 0 20px; font-family: system-ui, -apple-system, sans-serif; direction: rtl; text-align: right; min-height: 80vh;">
    
    <div style="margin-bottom: 40px;">
        <h1 style="font-size: 32px; color: #0f172a; font-weight: 800; margin-bottom: 8px;">👤 حسابي وملفي الشخصي</h1>
        <div style="width: 60px; height: 4px; background-color: #3b82f6; border-radius: 2px;"></div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; align-items: start;">
        
        <section style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 24px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.01);">
            <h2 style="font-size: 20px; color: #0f172a; margin-top: 0; margin-bottom: 20px; font-weight: 700; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">📋 تعديل البيانات</h2>
            
            <?php if ($successMsg): ?>
                <div style="background-color: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 12px; border-radius: 12px; font-size: 14px; margin-bottom: 20px; font-weight: 500;">
                    <?= htmlspecialchars($successMsg) ?>
                </div>
            <?php endif; ?>

            <?php if ($errorMsg): ?>
                <div style="background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 12px; border-radius: 12px; font-size: 14px; margin-bottom: 20px; font-weight: 500;">
                    <?= htmlspecialchars($errorMsg) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">الاسم الكامل</label>
                    <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 14px; box-sizing: border-box; outline: none;">
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #94a3b8; margin-bottom: 6px;">البريد الإلكتروني (لا يمكن تعديله)</label>
                    <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; background: #f8fafc; color: #64748b; border-radius: 12px; font-size: 14px; box-sizing: border-box; direction: ltr; text-align: right;">
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">رقم الهاتف</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 14px; box-sizing: border-box; outline: none;">
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">عنوان الشحن الافتراضي</label>
                    <textarea name="address" rows="3" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 14px; box-sizing: border-box; outline: none; resize: none; font-family: inherit;"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                </div>

                <button type="submit" name="update_profile" style="width: 100%; background: #0f172a; color: white; border: none; padding: 14px; border-radius: 12px; font-weight: bold; font-size: 14px; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#3b82f6'" onmouseout="this.style.background='#0f172a'">💾 حفظ التغييرات الجديدة</button>
            </form>
        </section>

        <section style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 24px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.01);">
            <h2 style="font-size: 20px; color: #0f172a; margin-top: 0; margin-bottom: 20px; font-weight: 700; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">📦 سجل طلباتي السابق</h2>
            
            <?php if ($ordersResult->num_rows === 0): ?>
                <div style="text-align: center; padding: 40px 20px; color: #64748b;">
                    <p style="font-size: 15px; margin: 0;">لم تقومي بإجراء أي طلبات شراء حتى الآن.</p>
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="border-bottom: 2px solid #edf2f7; text-align: right; color: #475569;">
                                <th style="padding: 12px 8px;">رقم الطلب</th>
                                <th style="padding: 12px 8px;">التاريخ</th>
                                <th style="padding: 12px 8px;">المجموع الكلي</th>
                                <th style="padding: 12px 8px;">الحالة</th>
                                <th style="padding: 12px 8px; text-align: left;">الفاتورة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = $ordersResult->fetch_assoc()): ?>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 14px 8px; font-weight: bold; color: #0f172a;">#<?= $order['order_id'] ?></td>
                                    <td style="padding: 14px 8px; color: #64748b; font-size: 13px;"><?= date('Y-m-d', strtotime($order['order_date'])) ?></td>
                                    <td style="padding: 14px 8px; font-weight: 700; color: #10b981;"><?= number_format($order['total_amount'], 2) ?> ₪</td>
                                    <td style="padding: 14px 8px;">
                                        <?php if ($order['status'] === 'pending'): ?>
                                            <span style="background: #fffbeb; color: #b45309; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">قيد الانتظار</span>
                                        <?php elseif ($order['status'] === 'paid'): ?>
                                            <span style="background: #f0fdf4; color: #16a34a; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">تم الدفع ✓</span>
                                        <?php else: ?>
                                            <span style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;"><?= htmlspecialchars($order['status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 14px 8px; text-align: left;">
                                        <a href="/php-ecommerce-project/pages/order-details.php?id=<?= $order['order_id'] ?>" style="color: #3b82f6; text-decoration: none; font-weight: bold; font-size: 13px;">عرض تفاصيل الفاتورة ←</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

    </div>
</main>

<?php require_once '../includes/footer.php'; ?>