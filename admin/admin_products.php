<?php
// admin/admin_products.php — إدارة المنتجات والطلبات والعمليات عليها (CRUD)
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /php-ecommerce-project/pages/products.php');
    exit;
}

// 1. إضافة منتج جديد
if (isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock_quantity'];
    $catId = (int)$_POST['category_id'];
    $imgUrl = trim($_POST['image_url']);

    $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock_quantity, category_id, image_url) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssdiis', $name, $desc, $price, $stock, $catId, $imgUrl);
    $stmt->execute();
    header('Location: admin_products.php?success=1'); exit;
}

// 2. تحديث حالة الطلب
if (isset($_POST['update_status'])) {
    $orderId = (int)$_POST['order_id'];
    $newStatus = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->bind_param('si', $newStatus, $orderId);
    $stmt->execute();
    header('Location: admin_products.php?success=2'); exit;
}

// 3. حذف منتج
if (isset($_GET['delete_product'])) {
    $pId = (int)$_GET['delete_product'];
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param('i', $pId);
    $stmt->execute();
    header('Location: admin_products.php?success=3'); exit;
}

$pageTitle = 'المخازن والطلبات — EliteShop Admin';
require_once __DIR__ . '/../includes/header.php';
?>

<main style="max-width: 1200px; margin: 40px auto; padding: 0 20px; font-family: system-ui, sans-serif; direction: rtl; text-align: right;">
    
    <div style="margin-bottom: 25px;"><a href="dashboard.php" style="text-decoration:none; font-weight:bold; color:#3b82f6;">← العودة للوحة الرئيسية</a></div>

    <div style="background:#f8fafc; border:1px solid #e2e8f0; padding:25px; border-radius:16px; margin-bottom:40px;">
        <h3 style="margin-top:0; color:#0f172a;">➕ إضافة منتج جديد للمتجر</h3>
        <form method="POST" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:15px;">
            <input type="text" name="name" placeholder="اسم المنتج" required style="padding:10px; border-radius:8px; border:1px solid #cbd5e1;">
            <input type="number" step="0.01" name="price" placeholder="السعر (₪)" required style="padding:10px; border-radius:8px; border:1px solid #cbd5e1;">
            <input type="number" name="stock_quantity" placeholder="الكمية بالمخزن" required style="padding:10px; border-radius:8px; border:1px solid #cbd5e1;">
            <input type="text" name="image_url" placeholder="مسار الصورة (مثال: assets/images/products/item.jpg)" style="padding:10px; border-radius:8px; border:1px solid #cbd5e1;">
            <select name="category_id" style="padding:10px; border-radius:8px; border:1px solid #cbd5e1;">
                <?php 
                $cats = $conn->query("SELECT * FROM categories");
                while($c = $cats->fetch_assoc()) echo "<option value='{$c['category_id']}'>{$c['name']}</option>";
                ?>
            </select>
            <textarea name="description" placeholder="وصف وتفاصيل المنتج الفنية..." style="grid-column:1/-1; padding:10px; border-radius:8px; border:1px solid #cbd5e1; font-family:inherit;"></textarea>
            <button type="submit" name="add_product" style="grid-column:1/-1; background:#0f172a; color:white; border:none; padding:12px; border-radius:8px; font-weight:bold; cursor:pointer;">حفظ وإدراج المنتج الجديد الفوري 🚀</button>
        </form>
    </div>

    <h3 style="color:#0f172a; margin-bottom:15px;">📦 قائمة المنتجات الحالية بالمخزن</h3>
    <table style="width:100%; border-collapse:collapse; background:white; border:1px solid #e2e8f0; margin-bottom:5px;">
        <thead>
            <tr style="background:#0f172a; color:white;">
                <th style="padding:10px;">ID</th><th style="padding:10px;">الاسم</th><th style="padding:10px;">السعر</th><th style="padding:10px;">المخزون المتوفر</th><th style="padding:10px; text-align:left;">إجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $prods = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
            while($p = $prods->fetch_assoc()): ?>
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:10px;">#<?= $p['product_id'] ?></td>
                    <td style="padding:10px; font-weight:600;"><?= htmlspecialchars($p['name']) ?></td>
                    <td style="padding:10px; color:#10b981; font-weight:bold;"><?= $p['price'] ?> ₪</td>
                    <td style="padding:10px; font-weight:bold; color:<?= $p['stock_quantity'] < 5 ? '#ef4444':'#475569' ?>;"><?= $p['stock_quantity'] ?> وحدة</td>
                    <td style="padding:10px; text-align:left;"><a href="?delete_product=<?= $p['product_id'] ?>" onclick="return confirm('هل تريد حذف هذا المنتج من المتجر؟')" style="color:#ef4444; text-decoration:none; font-weight:bold; font-size:13px;">❌ حذف المنتج</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h3 style="color:#0f172a; margin-top:5px; margin-bottom:15px;">🛒 طلبات وفواتير الزبائن الحالية</h3>
    <table style="width:100%; border-collapse:collapse; background:white; border:1px solid #e2e8f0;">
        <thead>
            <tr style="background:#1e293b; color:white;">
                <th style="padding:10px;">رقم الطلب</th><th style="padding:10px;">اسم العميل</th><th style="padding:10px;">المبلغ الإجمالي</th><th style="padding:10px;">حالة الطلب الحالية</th><th style="padding:10px; text-align:left;">تعديل الحالة وفحص الفاتورة</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $orders = $conn->query("SELECT o.*, u.full_name FROM orders o JOIN users u ON o.user_id = u.user_id ORDER BY o.order_date DESC");
            while($o = $orders->fetch_assoc()): ?>
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:10px; font-weight:bold;">#<?= $o['order_id'] ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($o['full_name']) ?></td>
                    <td style="padding:10px; font-weight:bold; color:#10b981;"><?= $o['total_amount'] ?> ₪</td>
                    <td style="padding:10px;"><strong style="color:#f59e0b;"><?= $o['status'] ?></strong></td>
                    <td style="padding:10px; text-align:left;">
                        <form method="POST" style="display:inline-flex; gap:5px; align-items:center;">
                            <input type="hidden" name="order_id" value="<?= $o['order_id'] ?>">
                            <select name="status" style="padding:5px; border-radius:6px;">
                                <option value="pending" <?= $o['status']=='pending'?'selected':'' ?>>قيد الانتظار</option>
                                <option value="paid" <?= $o['status']=='paid'?'selected':'' ?>>تم الدفع</option>
                                <option value="shipped" <?= $o['status']=='shipped'?'selected':'' ?>>تم الشحن</option>
                                <option value="delivered" <?= $o['status']=='delivered'?'selected':'' ?>>تم التوصيل</option>
                                <option value="cancelled" <?= $o['status']=='cancelled'?'selected':'' ?>>ملغي</option>
                            </select>
                            <button type="submit" name="update_status" style="background:#0f172a; color:white; border:none; padding:6px 12px; border-radius:6px; font-size:12px; cursor:pointer;">تحديث الحالة</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</main>