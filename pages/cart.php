<?php
// pages/cart.php — سلة المشتريات المحدثة طبقاً للتصميم المطلوب
$pageTitle = 'سلة المشتريات — EliteShop';
require_once '../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_once '../includes/navbar.php';

// التحقق من تسجيل الدخول لحماية السلة
if (!isset($_SESSION['user_id'])) {
    header('Location: /php-ecommerce-project/auth/login.php?msg=login_required');
    exit;
}

$userId = (int)$_SESSION['user_id'];

// ── معالجة تحديث الكمية وإجراءات السلة ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'update') {
        $cartId  = (int)$_POST['cart_id'];
        $newQty  = max(1, (int)$_POST['quantity']);
        $stmt = $conn->prepare(
            "UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND user_id = ?"
        );
        $stmt->bind_param('iii', $newQty, $cartId, $userId);
        $stmt->execute();
    }

    if ($_POST['action'] === 'remove') {
        $cartId = (int)$_POST['cart_id'];
        $stmt = $conn->prepare(
            "DELETE FROM cart_items WHERE cart_id = ? AND user_id = ?"
        );
        $stmt->bind_param('ii', $cartId, $userId);
        $stmt->execute();
    }

    if ($_POST['action'] === 'checkout') {
        $address = trim($_POST['shipping_address'] ?? '');
        if (!empty($address)) {
            // حساب المجموع الكلي الفعلي شامل الضريبة 16% لإدخاله في الطلب
            $totalRes = $conn->query(
                "SELECT SUM(p.price * ci.quantity) AS total
                 FROM cart_items ci
                 JOIN products p ON ci.product_id = p.product_id
                 WHERE ci.user_id = $userId"
            );
            $subtotal = (float)($totalRes->fetch_assoc()['total'] ?? 0);
            $totalWithTax = $subtotal * 1.16; // المجموع شامل الضريبة

            if ($subtotal > 0) {
                // إدخال الأوردر الرئيسي
                $oStmt = $conn->prepare(
                    "INSERT INTO orders (user_id, total_amount, shipping_address)
                     VALUES (?, ?, ?)"
                );
                $oStmt->bind_param('ids', $userId, $totalWithTax, $address);
                $oStmt->execute();
                $orderId = $conn->insert_id;

                // إدخال تفاصيل عناصر الأوردر
                $items = $conn->query(
                    "SELECT ci.product_id, ci.quantity, p.price
                     FROM cart_items ci
                     JOIN products p ON ci.product_id = p.product_id
                     WHERE ci.user_id = $userId"
                );
                $iStmt = $conn->prepare(
                    "INSERT INTO order_items (order_id, product_id, quantity, unit_price)
                     VALUES (?, ?, ?, ?)"
                );
                while ($item = $items->fetch_assoc()) {
                    $iStmt->bind_param('iiid',
                        $orderId, $item['product_id'],
                        $item['quantity'], $item['price']
                    );
                    $iStmt->execute();
                }

                // إفراغ محتويات السلة للمستخدم بعد الشراء بنجاح
                $conn->query("DELETE FROM cart_items WHERE user_id = $userId");
                $checkoutSuccess = true;
            }
        }
    }

    if (!isset($checkoutSuccess)) {
        header('Location: cart.php');
        exit;
    }
}

// ── جلب وعرض عناصر السلة الحالية للمستخدم ─────────────────────────────────
$cartItems = $conn->query(
    "SELECT ci.cart_id, ci.quantity,
            p.product_id, p.name, p.price, p.image_url, p.stock_quantity
     FROM cart_items ci
     JOIN products p ON ci.product_id = p.product_id
     WHERE ci.user_id = $userId"
);

$subtotal = 0;
$items = [];
while ($row = $cartItems->fetch_assoc()) {
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $subtotal += $row['subtotal'];
    $items[] = $row;
}
// حساب المجموع الكلي الفعلي النهائي مع الضريبة (16%)
$finalTotal = $subtotal * 1.16;
?>

<!-- الخلفية الرمادية الفاتحة المحيطة بالبطاقة بالكامل -->
<div style="background-color: #f0f4f8; min-height: 85vh; padding: 40px 20px; font-family: system-ui, -apple-system, sans-serif; direction: rtl; box-sizing: border-box;">
    <main style="max-width: 1100px; margin: 0 auto;">

        <?php if (!empty($checkoutSuccess)): ?>
        <!-- واجهة التنبيه بنجاح إتمام الطلب -->
        <div style="text-align: center; padding: 50px 20px; background: #ffffff; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); max-width: 500px; margin: 40px auto; border: 1px solid #e2e8f0;">
            <div style="font-size: 55px; margin-bottom: 15px;">🎉</div>
            <h2 style="color: #10b981; font-weight: bold; margin-bottom: 12px; font-size: 24px;">تم تقديم طلبك بنجاح!</h2>
            <p style="color: #64748b; font-size: 15px; margin-bottom: 25px;">رقم طلبك المميز هو: <strong style="color: #0f172a;">#<?= $orderId ?></strong></p>
            <a href="/php-ecommerce-project/pages/products.php" style="background-color: #0c2340; color: white; padding: 12px 35px; text-decoration: none; border-radius: 25px; font-weight: bold; display: inline-block; font-size: 14px;">تسوق مجدداً ←</a>
        </div>

        <?php elseif (empty($items)): ?>
        <!-- واجهة السلة فارغة -->
        <div style="text-align: center; padding: 60px 20px; background: #ffffff; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); max-width: 550px; margin: 40px auto; border: 1px dashed #cbd5e1;">
            <div style="font-size: 55px; margin-bottom: 15px;">🛒</div>
            <h2 style="color: #475569; font-weight: bold; margin-bottom: 10px; font-size: 22px;">سلة المشتريات فارغة</h2>
            <p style="color: #94a3b8; font-size: 14px; margin-bottom: 25px;">سلتك لا تحتوي على أي منتجات في الوقت الحالي.</p>
            <a href="/php-ecommerce-project/pages/products.php" style="background-color: #0c2340; color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; display: inline-block; font-size: 14px;">تصفح المنتجات الآن</a>
        </div>

        <?php else: ?>
        
        <!-- البطاقة البيضاء النظيفة الموحدة (طبق الأصل من الصورة المعطاة) -->
        <div style="background: #ffffff; border-radius: 28px; padding: 40px 50px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); border: 1px solid #eef2f6;">
            
            <!-- الهيدر الداخلي للبطاقة -->
            <div style="display: flex; align-items: center; justify-content: flex-start; gap: 10px; margin-bottom: 30px;">
                <span style="font-size: 24px;">🛒</span>
                <h2 style="font-size: 22px; color: #000000; font-weight: bold; margin: 0;">سلة المشتريات</h2>
            </div>
            
            <!-- جدول المنتجات العريض المتناسق -->
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: center; vertical-align: middle;">
                    <thead>
                        <tr style="border-bottom: 1px solid #f1f5f9; color: #475569; font-size: 15px; font-weight: bold;">
                            <th style="padding: 15px 10px; text-align: right; font-weight: bold; width: 12%;">المنتج</th>
                            <th style="padding: 15px 10px; text-align: right; font-weight: bold; width: 40%;">الاسم</th>
                            <th style="padding: 15px 10px; font-weight: bold; width: 12%;">السعر</th>
                            <th style="padding: 15px 10px; font-weight: bold; width: 12%;">الكمية</th>
                            <th style="padding: 15px 10px; font-weight: bold; width: 12%;">الإجمالي</th>
                            <th style="padding: 15px 10px; font-weight: bold; width: 12%;"></th>
                        </tr>
                    </thead>
                    <tbody style="color: #1e293b; font-size: 15px;">
                        <?php foreach ($items as $item): ?>
                        <tr style="border-bottom: 1px solid #f8fafc;">
                            <!-- صورة المنتج المدورة المتناسقة -->
                            <td style="padding: 20px 10px; text-align: right;">
                                <img src="/php-ecommerce-project/<?= htmlspecialchars($item['image_url'] ?? '') ?>"
                                     alt="" width="65" height="65"
                                     onerror="this.src='/php-ecommerce-project/assets/images/products/placeholder.jpg'"
                                     style="object-fit: cover; border-radius: 14px; border: 1px solid #f1f5f9;">
                            </td>
                            <!-- اسم المنتج (محاذي لليمين بجانب الصورة) -->
                            <td style="padding: 20px 10px; text-align: right; font-weight: 500; color: #0f172a;">
                                <?= htmlspecialchars($item['name']) ?>
                            </td>
                            <!-- السعر الفرعي -->
                            <td style="padding: 20px 10px; color: #334155; font-weight: 500;">
                                ₪ <?= number_format($item['price'], 0) ?>
                            </td>
                            <!-- حقل إدخال رقم الكمية بتصميم الحواف الناعمة النظيفة -->
                            <td style="padding: 20px 10px;">
                                <form method="POST" style="display: inline-block;">
                                    <input type="hidden" name="action"  value="update">
                                    <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                    <input type="number" name="quantity"
                                           value="<?= $item['quantity'] ?>"
                                           min="1" max="<?= $item['stock_quantity'] ?>"
                                           onchange="this.form.submit()"
                                           style="width: 65px; padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 10px; text-align: center; font-size: 14px; font-weight: 500; outline: none; background: #ffffff;">
                                </form>
                            </td>
                            <!-- إجمالي السعر للمنتج الحالي -->
                            <td style="padding: 20px 10px; font-weight: 600; color: #0f172a;">
                                ₪ <?= number_format($item['subtotal'], 0) ?>
                            </td>
                            <!-- زر الحذف الأحمر الصريح المطابق تماماً للصورة المعروضة -->
                            <td style="padding: 20px 10px;">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action"  value="remove">
                                    <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                    <button type="submit" style="background-color: #ef4444; color: white; border: none; padding: 6px 16px; border-radius: 8px; font-size: 13px; font-weight: bold; cursor: pointer; transition: background-color 0.2s;">
                                        🗑️ حذف
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- خط فاصل ناعم سفلي -->
            <div style="border-bottom: 1px solid #edf2f7; margin-top: 20px; margin-bottom: 30px;"></div>

            <!-- الفوتر السفلي للبطاقة: يحتوي على المجموع الكلي الاستراتيجي المائل لليمين، وزر الشراء المائل لليسار -->
            <form method="POST" style="margin: 0;">
                <input type="hidden" name="action" value="checkout">
                
                <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: 20px;">
                    
                    <!-- الكتلة اليمينية: المجموع الكلي + مدخل العنوان المدمج بذكاء -->
                    <div style="display: flex; flex-direction: column; gap: 15px; width: 100%; max-width: 450px;">
                        <div style="font-size: 20px; font-weight: bold; color: #000000; display: flex; align-items: center; gap: 5px;">
                            <span>المجموع الكلي:</span>
                            <span style="color: #000000; font-size: 22px;">₪ <?= number_format($finalTotal, 0) ?></span>
                            <span style="font-size: 12px; color: #94a3b8; font-weight: normal; margin-right: 5px;">(شامل الضريبة 16% وشحن مجاني)</span>
                        </div>
                        
                        <!-- إدخال عنوان الشحن بطريقة مدمجة وأنيقة لحل مشكلة الإرسال -->
                        <div style="display: flex; flex-direction: column; gap: 5px;">
                            <label style="font-size: 13px; color: #475569; font-weight: bold;">عنوان التوصيل للتثبيت والطلب *</label>
                            <input type="text" name="shipping_address" 
                                   placeholder="مثال: رام الله، شارع الإرسال، عمارة الأمل" 
                                   required
                                   style="width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 14px; outline: none; box-sizing: border-box; text-align: right;">
                        </div>
                    </div>

                    <!-- الكتلة اليسارية: زر إتمام الشراء الأخضر اللامع والملفت المطابق للصورة تماماً -->
                    <div style="display: flex; align-items: flex-end; justify-content: flex-end; min-height: 80px;">
                        <button type="submit" style="background-color: #10b981; color: white; border: none; padding: 12px 45px; border-radius: 30px; font-weight: bold; font-size: 15px; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#059669'" onmouseout="this.style.backgroundColor='#10b981'">
                            إتمام الشراء
                        </button>
                    </div>

                </div>
            </form>

        </div>
        <?php endif; ?>

    </main>
</div>

<?php require_once '../includes/footer.php'; ?>