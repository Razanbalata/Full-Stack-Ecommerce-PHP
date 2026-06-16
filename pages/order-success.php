<?php
// pages/order-success.php — صفحة نجاح الطلب اللحظية
session_start();
require_once __DIR__ . '/../includes/db.php';

// جدار حماية: منع الدخول العشوائي إذا لم يكن المستخدم مسجلاً لدخوله
if (!isset($_SESSION['user_id'])) {
    header('Location: /php-ecommerce-project/auth/login.php');
    exit;
}

// جلب رقم الطلب المعالج حالياً (سواء من الـ GET أو من الجلسة لتأمين الفلو)
$order_id = intval($_GET['order_id'] ?? $_SESSION['last_order_id'] ?? 0);

// إذا لم يكن هناك رقم طلب حقيقي، يتم توجيهه تلقائياً لصفحة المنتجات
if ($order_id === 0) {
    header('Location: /php-ecommerce-project/pages/products.php');
    exit;
}

// تنظيف وتدمير متغيرات الطلب المؤقتة من الجلسة (إجراء الحماية البرمجية لمنع التكرار Idempotency)
unset($_SESSION['last_order_id']);

$pageTitle = 'تمت العملية بنجاح 🎉 — EliteShop';
require_once '../includes/header.php';
?>

<main class="success-container" style="max-width: 1200px; margin: 0 auto; padding: 60px 20px; font-family: system-ui, -apple-system, sans-serif; direction: rtl; min-height: 75vh; display: flex; align-items: center; justify-content: center;">
    
    <div class="success-card" style="width: 100%; max-width: 550px; background: #ffffff; border-radius: 24px; box-shadow: 0 15px 35px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; padding: 45px 30px; text-align: center; box-sizing: border-box;">
        
        <div class="success-icon-wrap" style="width: 90px; height: 90px; background-color: #f0fdf4; border-radius: 50%; display: flex; justify-content: center; align-items: center; margin: 0 auto 25px auto; border: 2px solid #bbf7d0;">
            <span style="font-size: 45px; color: #16a34a; line-height: 1;">✓</span>
        </div>

        <h2 style="font-size: 26px; color: #0f172a; margin: 0 0 10px 0; font-weight: bold;">تمت عملية الشراء بنجاح! 🎉</h2>
        <p style="color: #64748b; font-size: 15px; line-height: 1.6; margin-bottom: 30px;">
            شكراً لثقتك بنا والتسوق من <strong>EliteShop</strong>. تم استلام طلبك بنجاح وجاري تجهيزه للشحن الآن في المستودعات.
        </p>

        <div class="order-badge" style="background-color: #f8fafc; border: 1px solid #ebd5e1; border-radius: 14px; padding: 18px 20px; margin-bottom: 35px; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 14px; color: #475569; font-weight: bold;">رقم الطلب الخاص بك:</span>
            <span style="font-size: 16px; color: #0f172a; font-weight: 900; background: #e2e8f0; padding: 4px 12px; border-radius: 8px; letter-spacing: 1px;">
                #<?= $order_id ?>
            </span>
        </div>

        <div class="success-actions" style="display: flex; flex-direction: column; gap: 12px;">
            
            <a href="/php-ecommerce-project/pages/my-orders.php" style="background-color: #0f172a; color: white; text-decoration: none; padding: 14px; border-radius: 25px; font-weight: bold; font-size: 15px; display: flex; justify-content: center; align-items: center; gap: 8px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0f172a'">
                تتبع واستعراض طلباتك الآن ◀
            </a>
            
            <a href="/php-ecommerce-project/pages/products.php" style="background-color: transparent; color: #f59e0b; border: 2px solid #f59e0b; text-decoration: none; padding: 12px; border-radius: 25px; font-weight: bold; font-size: 15px; display: flex; justify-content: center; align-items: center; gap: 8px; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#fffbeb'" onmouseout="this.style.backgroundColor='transparent'">
                العودة لتصفح المنتجات 🛒
            </a>
            
        </div>

        <p style="margin-top: 30px; font-size: 12px; color: #94a3b8;">
            في حال وجود أي استفسار، يسعدنا تواصلك مع الدعم الفني عبر صفحة اتصل بنا.
        </p>

    </div>
</main>

<?php require_once '../includes/footer.php'; ?>