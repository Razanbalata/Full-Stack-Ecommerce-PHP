<?php
// pages/checkout.php — نسخة متوافقة تماماً مع الـ SQL Dump الخاص بـ ecommerce_db
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'إتمام الطلب — EliteShop';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/navbar.php';

// حماية الصفحة: يجب تسجيل الدخول للوصول هنا
if (!isset($_SESSION['user_id'])) {
    header('Location: /php-ecommerce-project/auth/login.php?msg=login_required');
    exit;
}

$userId = (int)$_SESSION['user_id'];
$error = '';

// 1. جلب عناصر السلة وحساب الإجمالي من جدول cart_items
$sql = "SELECT cr.*, p.price, p.stock_quantity, p.name FROM cart_items cr 
        JOIN products p ON cr.product_id = p.product_id 
        WHERE cr.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$cartItems = $stmt->get_result();

if ($cartItems->num_rows === 0) {
    header('Location: /php-ecommerce-project/pages/cart.php');
    exit;
}

$totalAmount = 0;
$itemsArray = [];

while ($item = $cartItems->fetch_assoc()) {
    // التحقق من المخزن
    if ($item['quantity'] > $item['stock_quantity']) {
        header('Location: /php-ecommerce-project/pages/cart.php?error=out_of_stock');
        exit;
    }
    $totalAmount += $item['price'] * $item['quantity'];
    $itemsArray[] = $item;
}

// 2. معالجة إرسال الفورم وتثبيت الطلب في قاعدة البيانات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shippingAddress = trim($_POST['shipping_address'] ?? '');
    $phone           = trim($_POST['phone'] ?? ''); // نستقبله، وسنقوم بتحديثه في جدول المستخدمين للحفاظ عليه مستقبلاً

    if (empty($shippingAddress) || empty($phone)) {
        $error = 'يرجى ملء الحقول الإلزامية (عنوان الشحن ورقم الهاتف).';
    } else {
        // بدء المعاملة التراكمية بأمان
        $conn->begin_transaction();

        try {
            // تحديث رقم الهاتف في جدول المستخدمين (لأن جدول orders لا يحتوي على حقل هاتف)
            $updateUserSql = "UPDATE users SET phone = ? WHERE user_id = ?";
            $updateUserStmt = $conn->prepare($updateUserSql);
            $updateUserStmt->bind_param('si', $phone, $userId);
            $updateUserStmt->execute();

            // أ. إدخال الطلب الرئيسي في جدول orders متوافق 100% مع الـ Dump (الحالة تلقائياً pending)
            $orderSql = "INSERT INTO orders (user_id, total_amount, status, shipping_address, order_date) 
                         VALUES (?, ?, 'pending', ?, NOW())";
            $orderStmt = $conn->prepare($orderSql);
            if (!$orderStmt) { throw new Exception($conn->error); }
            
            $orderStmt->bind_param('ids', $userId, $totalAmount, $shippingAddress);
            $orderStmt->execute();
            $orderId = $conn->insert_id; 

            // ب. نقل المنتجات لجدول order_items (الأعمدة: order_id, product_id, quantity, unit_price)
            $addItemSql = "INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)";
            $addItemStmt = $conn->prepare($addItemSql);
            if (!$addItemStmt) { throw new Exception($conn->error); }

            // تجهيز استعلام تحديث المخزن في جدول products
            $updateStockSql = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?";
            $updateStockStmt = $conn->prepare($updateStockSql);

            foreach ($itemsArray as $item) {
                // إدراج تفاصيل المنتج
                $addItemStmt->bind_param('iiid', $orderId, $item['product_id'], $item['quantity'], $item['price']);
                $addItemStmt->execute();

                // خصم الكمية من مخزن المنتج
                $updateStockStmt->bind_param('ii', $item['quantity'], $item['product_id']);
                $updateStockStmt->execute();
            }

            // ج. تفريغ السلة تماماً للمستخدم بعد الشراء
            $clearCartStmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
            $clearCartStmt->bind_param('i', $userId);
            $clearCartStmt->execute();

            // تثبيت الحفظ النهائي في قاعدة البيانات
            $conn->commit();

            // تحويل العميل لصفحة النجاح وتمرير المعرف
            $_SESSION['last_order_id'] = $orderId;
            header('Location: /php-ecommerce-project/pages/order-success.php');
            exit;

        } catch (Exception $e) {
            $conn->rollback();
            $error = 'فشلت معالجة الطلب التقنية: ' . $e->getMessage();
        }
    }
}
?>

<style>
    .checkout-grid { display: flex; flex-wrap: wrap; gap: 40px; align-items: flex-start; }
    .checkout-form-block { flex: 1.5; min-width: 320px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 24px; padding: 40px; }
    .checkout-summary-block { flex: 1; min-width: 300px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 24px; padding: 30px; position: sticky; top: 20px; }
    .checkout-input { width: 100%; padding: 14px 18px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 14px; outline: none; box-sizing: border-box; text-align: right; margin-bottom: 20px; }
    .checkout-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.08); }
    .btn-place-order { background-color: #0f172a; color: #ffffff; border: none; padding: 16px 30px; border-radius: 14px; font-weight: bold; font-size: 16px; cursor: pointer; width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.3s ease; }
    .btn-place-order:hover { background-color: #10b981; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.2); }
    .mini-item-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px dashed #f1f5f9; font-size: 13px; }
</style>

<main class="main-container" style="max-width: 1200px; margin: 0 auto; padding: 50px 20px; font-family: system-ui, -apple-system, sans-serif; direction: rtl; min-height: 80vh;">

    <div style="margin-bottom: 35px; text-align: right;">
        <h1 style="font-size: 32px; color: #0f172a; font-weight: 800; margin-bottom: 8px;">💳 إتمام الشراء والشحن</h1>
        <div style="width: 50px; height: 4px; background-color: #3b82f6; border-radius: 2px;"></div>
    </div>

    <?php if ($error): ?>
        <div style="background-color: #fef2f2; color: #dc2626; padding: 14px 20px; border-radius: 12px; font-size: 14px; font-weight: 500; margin-bottom: 25px; border: 1px solid #fecaca;">
            ⚠️ <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="checkout-grid">
        
        <div class="checkout-form-block">
            <div style="margin-bottom: 25px;">
                <h2 style="font-size: 20px; color: #0f172a; margin: 0 0 6px 0; font-weight: 700;">🏠 تفاصيل الشحن والتوصيل</h2>
                <p style="font-size: 13px; color: #64748b; margin: 0;">الرجاء تزويدنا بالمعلومات الدقيقة لضمان وصول طلبك بأسرع وقت ممكن.</p>
            </div>

            <form method="POST">
                <label style="display: block; font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 8px;">عنوان الشحن بالكامل <span style="color: #ef4444;">*</span></label>
                <input type="text" name="shipping_address" required 
                       placeholder="مثال: غزة، الرمال، شارع الشهداء، عمارة رقم 5" 
                       value="<?= htmlspecialchars($_POST['shipping_address'] ?? '') ?>"
                       class="checkout-input">

                <label style="display: block; font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 8px;">رقم الهاتف للتواصل <span style="color: #ef4444;">*</span></label>
                <input type="tel" name="phone" required 
                       placeholder="مثال: 059XXXXXXXX" 
                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                       class="checkout-input" style="direction: ltr; text-align: right;">

                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 12px; margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 20px;">💵</span>
                    <p style="margin: 0; font-size: 13px; color: #475569; font-weight: 500; line-height: 1.4;">
                        <strong>طريقة الدفع الحالية:</strong> الدفع عند الاستلام نقداً للمندوب فور التسليم.
                    </p>
                </div>

                <button type="submit" class="btn-place-order">
                    <span>تأكيد وإرسال الطلب الآن</span> 🚀
                </button>
            </form>
        </div>

        <div class="checkout-summary-block">
            <h2 style="font-size: 17px; color: #0f172a; font-weight: 800; margin: 0 0 15px 0; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">📦 مراجعة المنتجات</h2>
            
            <div style="max-height: 240px; overflow-y: auto; margin-bottom: 20px; padding-left: 5px;">
                <?php foreach ($itemsArray as $p): ?>
                    <div class="mini-item-row">
                        <span style="color: #334155; font-weight: 600; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <?= htmlspecialchars($p['name']) ?> <span style="color: #94a3b8; font-weight: normal;">(x<?= $p['quantity'] ?>)</span>
                        </span>
                        <span style="color: #475569; font-weight: 600;"><?= number_format($p['price'] * $p['quantity'], 2) ?> ₪</span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="width: 100%; height: 1px; background-color: #e2e8f0; margin-bottom: 15px;"></div>

            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <span style="font-size: 15px; font-weight: 700; color: #0f172a;">المبلغ الإجمالي المستحق:</span>
                <span style="font-size: 22px; font-weight: 800; color: #10b981;"><?= number_format($totalAmount, 2) ?> ₪</span>
            </div>
        </div>

    </div>
</main>

<?php require_once '../includes/footer.php'; ?>