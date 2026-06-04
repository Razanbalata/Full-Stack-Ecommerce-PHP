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

<style>
    .cart-layout-container {
        display: flex;
        flex-direction: row-reverse;
        gap: 30px;
        align-items: flex-start;
    }
    .cart-items-section {
        flex: 1.7;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .cart-summary-section {
        flex: 1;
        position: sticky;
        top: 20px;
        background: #ffffff;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
        border: 1px solid #e2e8f0;
    }
    .product-card-row {
        background: #ffffff;
        border-radius: 20px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.01);
    }
    .qty-input-field {
        width: 55px; 
        padding: 8px; 
        border: 1px solid #cbd5e1; 
        border-radius: 10px; 
        text-align: center; 
        font-size: 14px; 
        font-weight: 600; 
        outline: none;
        background: #f8fafc;
    }
    .btn-delete-item {
        background: #fef2f2;
        color: #ef4444;
        border: 1px solid #fee2e2;
        padding: 8px 14px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-delete-item:hover {
        background: #ef4444;
        color: #ffffff;
    }
    @media (max-width: 900px) {
        .cart-layout-container {
            flex-direction: column;
        }
        .product-card-row {
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<div style="background-color: #f8fafc; min-height: 85vh; padding: 50px 20px; font-family: system-ui, -apple-system, sans-serif; direction: rtl; box-sizing: border-box;">
    <main style="max-width: 1200px; margin: 0 auto;">

        <?php if (!empty($checkoutSuccess)): ?>
        <div style="text-align: center; padding: 50px 20px; background: #ffffff; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); max-width: 500px; margin: 40px auto; border: 1px solid #e2e8f0;">
            <div style="font-size: 55px; margin-bottom: 15px;">🎉</div>
            <h2 style="color: #10b981; font-weight: bold; margin-bottom: 12px; font-size: 24px;">تم تقديم طلبك بنجاح!</h2>
            <p style="color: #64748b; font-size: 15px; margin-bottom: 25px;">رقم طلبك المميز هو: <strong style="color: #0f172a;">#<?= $orderId ?></strong></p>
            <a href="/php-ecommerce-project/pages/products.php" style="background-color: #0f172a; color: white; padding: 12px 35px; text-decoration: none; border-radius: 25px; font-weight: bold; display: inline-block; font-size: 14px;">تسوق مجدداً ←</a>
        </div>

        <?php elseif (empty($items)): ?>
        <div style="text-align: center; padding: 60px 20px; background: #ffffff; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); max-width: 550px; margin: 40px auto; border: 1px dashed #cbd5e1;">
            <div style="font-size: 55px; margin-bottom: 15px;">🛒</div>
            <h2 style="color: #475569; font-weight: bold; margin-bottom: 10px; font-size: 22px;">سلة المشتريات فارغة</h2>
            <p style="color: #94a3b8; font-size: 14px; margin-bottom: 25px;">سلتك لا تحتوي على أي منتجات في الوقت الحالي.</p>
            <a href="/php-ecommerce-project/pages/products.php" style="background-color: #0f172a; color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; display: inline-block; font-size: 14px;">تصفح المنتجات الآن</a>
        </div>

        <?php else: ?>
        
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 30px;">
            <span style="font-size: 26px;">🛒</span>
            <h2 style="font-size: 24px; color: #0f172a; font-weight: 800; margin: 0;">حقيبة المشتريات (<?= count($items) ?>)</h2>
        </div>

        <div class="cart-layout-container">
            
            <div class="cart-summary-section">
                <h3 style="font-size: 18px; color: #0f172a; margin-top: 0; margin-bottom: 20px; font-weight: bold; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">ملخص الطلب</h3>
                
                <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px; font-size: 14px; color: #475569;">
                    <div style="display: flex; justify-content: space-between;">
                        <span>المجموع الفرعي:</span>
                        <span style="font-weight: 600; color: #0f172a;">₪ <?= number_format($subtotal, 0) ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>الضريبة المضافة (16%):</span>
                        <span style="font-weight: 600; color: #0f172a;">₪ <?= number_format($subtotal * 0.16, 0) ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>تكلفة الشحن:</span>
                        <span style="color: #10b981; font-weight: bold;">مجاني</span>
                    </div>
                    <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 8px 0;">
                    <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: bold; color: #0f172a;">
                        <span>المجموع الكلي:</span>
                        <span style="color: #0f172a;">₪ <?= number_format($finalTotal, 0) ?></span>
                    </div>
                </div>

                <form method="POST" style="margin: 0;">
                    <input type="hidden" name="action" value="checkout">
                    
                    <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 25px;">
                        <label style="font-size: 13px; color: #334155; font-weight: 700;">📍 عنوان التوصيل الفعلي *</label>
                        <input type="text" name="shipping_address" 
                               placeholder="المدينة، اسم الشارع، رقم البناية" 
                               required
                               style="width: 100%; padding: 12px 14px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 14px; outline: none; box-sizing: border-box; background: #f8fafc;">
                    </div>

                    <button type="submit" style="width: 100%; background-color: #10b981; color: white; border: none; padding: 14px; border-radius: 14px; font-weight: bold; font-size: 15px; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15); transition: background-color 0.2s;">
                        تأكيد وإرسال الطلب 👍
                    </button>
                </form>
            </div>

            <div class="cart-items-section">
                <?php foreach ($items as $item): ?>
                <div class="product-card-row">
                    
                    <div style="display: flex; align-items: center; gap: 15px; flex: 2; text-align: right;">
                        <img src="/php-ecommerce-project/<?= htmlspecialchars($item['image_url'] ?? '') ?>"
                             alt="" width="75" height="75"
                             onerror="this.src='/php-ecommerce-project/assets/images/products/placeholder.jpg'"
                             style="object-fit: cover; border-radius: 14px; border: 1px solid #e2e8f0; flex-shrink: 0;">
                        <div>
                            <h4 style="margin: 0 0 6px 0; font-size: 15px; color: #0f172a; font-weight: bold; line-height: 1.4;"><?= htmlspecialchars($item['name']) ?></h4>
                            <span style="font-size: 14px; color: #64748b; font-weight: 500;">سعر الوحدة: ₪ <?= number_format($item['price'], 0) ?></span>
                        </div>
                    </div>

                    <div style="flex: 1; display: flex; justify-content: center; align-items: center;">
                        <form method="POST" style="margin: 0;">
                            <input type="hidden" name="action"  value="update">
                            <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 13px; color: #64748b;">الكمية:</span>
                                <input type="number" name="quantity"
                                       value="<?= $item['quantity'] ?>"
                                       min="1" max="<?= $item['stock_quantity'] ?>"
                                       onchange="this.form.submit()"
                                       class="qty-input-field">
                            </div>
                        </form>
                    </div>

                    <div style="flex: 1; display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                        <span style="font-size: 16px; font-weight: 700; color: #0f172a;">₪ <?= number_format($item['subtotal'], 0) ?></span>
                        <form method="POST" style="margin: 0;">
                            <input type="hidden" name="action"  value="remove">
                            <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                            <button type="submit" class="btn-delete-item">
                                🗑️ حذف
                            </button>
                        </form>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>

        </div>
        <?php endif; ?>

    </main>
</div>

<?php require_once '../includes/footer.php'; ?>