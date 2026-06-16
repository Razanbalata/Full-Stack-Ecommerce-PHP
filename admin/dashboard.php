<?php
// admin/dashboard.php — لوحة تحكم الإدارة الشاملة
session_start();
require_once __DIR__ . '/../includes/db.php';

// الحماية الأقصى: التحقق من أن المستخدم أدمن
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /php-ecommerce-project/pages/products.php');
    exit;
}

// 1. جلب الإحصائيات (Admin Dashboard Metrics)
$totalProducts = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
$totalOrders = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
$totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$unreadMessages = $conn->query("SELECT COUNT(*) FROM contacts WHERE is_read = 0")->fetch_row()[0];

// 2. معالجة الإجراءات (تغيير صلاحية مستخدم أو حذفه / قراءة الرسائل)
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if ($action === 'toggle_role' && isset($_GET['user_id'])) {
        $uId = (int)$_GET['user_id'];
        $conn->query("UPDATE users SET role = IF(role='admin', 'customer', 'admin') WHERE user_id = $uId AND user_id != {$_SESSION['user_id']}");
        header('Location: dashboard.php?tab=users'); exit;
    }
    
    if ($action === 'delete_user' && isset($_GET['user_id'])) {
        $uId = (int)$_GET['user_id'];
        // التحقق من عدم وجود طلبات مرتبطة قبل الحذف حسب الوثيقة
        $checkOrders = $conn->query("SELECT COUNT(*) FROM orders WHERE user_id = $uId")->fetch_row()[0];
        if ($checkOrders == 0) {
            $conn->query("DELETE FROM users WHERE user_id = $uId");
            header('Location: dashboard.php?tab=users&msg=success'); exit;
        } else {
            header('Location: dashboard.php?tab=users&msg=has_orders'); exit;
        }
    }

    if ($action === 'mark_read' && isset($_GET['msg_id'])) {
        $mId = (int)$_GET['msg_id'];
        $conn->query("UPDATE contacts SET is_read = 1 WHERE message_id = $mId");
        header('Location: dashboard.php?tab=messages'); exit;
    }
}

$activeTab = $_GET['tab'] ?? 'overview';
$pageTitle = 'لوحة التحكم — EliteShop Admin';
require_once __DIR__ . '/../includes/header.php';
?>

<main style="max-width: 1200px; margin: 40px auto; padding: 0 20px; font-family: system-ui, sans-serif; direction: rtl; text-align: right;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px;">
        <h1 style="color: #0f172a; margin: 0; font-weight: 800;">🛠️ لوحة الإدارة والتحكم للموقع</h1>
        <a href="/php-ecommerce-project/pages/products.php" style="background: #ef4444; color: white; text-decoration: none; padding: 10px 20px; border-radius: 10px; font-size: 14px; font-weight: bold;">خروج للمتجر ←</a>
    </div>

    <div style="display: flex; gap: 10px; margin-bottom: 30px;">
        <a href="?tab=overview" style="padding: 12px 20px; border-radius: 10px; text-decoration: none; font-weight: bold; font-size: 14px; <?= $activeTab === 'overview' ? 'background:#0f172a; color:white;' : 'background:#e2e8f0; color:#334155;' ?>">📊 النظرة العامة</a>
        <a href="admin_products.php" style="padding: 12px 20px; border-radius: 10px; text-decoration: none; font-weight: bold; font-size: 14px; background:#e2e8f0; color:#334155;">📦 إدارة المنتجات والطلبات</a>
        <a href="?tab=users" style="padding: 12px 20px; border-radius: 10px; text-decoration: none; font-weight: bold; font-size: 14px; <?= $activeTab === 'users' ? 'background:#0f172a; color:white;' : 'background:#e2e8f0; color:#334155;' ?>">👥 المستخدمين</a>
        <a href="?tab=messages" style="padding: 12px 20px; border-radius: 10px; text-decoration: none; font-weight: bold; font-size: 14px; <?= $activeTab === 'messages' ? 'background:#0f172a; color:white;' : 'background:#e2e8f0; color:#334155;' ?>">📩 رسائل الزبائن (<?= $unreadMessages ?>)</a>
    </div>

    <?php if ($activeTab === 'overview'): ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 40px;">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 24px; border-radius: 16px;">
                <span style="color: #64748b; font-size: 14px; display:block; margin-bottom: 5px;">إجمالي المنتجات</span>
                <strong style="font-size: 28px; color:#0f172a;"><?= $totalProducts ?> منتج</strong>
            </div>
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 24px; border-radius: 16px;">
                <span style="color: #64748b; font-size: 14px; display:block; margin-bottom: 5px;">إجمالي الطلبات المستلمة</span>
                <strong style="font-size: 28px; color:#10b981;"><?= $totalOrders ?> طلب</strong>
            </div>
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 24px; border-radius: 16px;">
                <span style="color: #64748b; font-size: 14px; display:block; margin-bottom: 5px;">المسجلين بالمنصة</span>
                <strong style="font-size: 28px; color:#3b82f6;"><?= $totalUsers ?> مستخدم</strong>
            </div>
            <div style="background: #fffbeb; border: 1px solid #fde68a; padding: 24px; border-radius: 16px;">
                <span style="color: #b45309; font-size: 14px; display:block; margin-bottom: 5px;">رسائل غير مقروءة</span>
                <strong style="font-size: 28px; color:#d97706;"><?= $unreadMessages ?> رسالة</strong>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($activeTab === 'users'): ?>
        <?php if(isset($_GET['msg']) && $_GET['msg'] === 'has_orders'): ?>
            <div style="background:#fef2f2; color:#dc2626; padding:12px; border-radius:10px; margin-bottom:15px; font-size:14px; font-weight:bold;">⚠️ لا يمكن حذف هذا المستخدم نظراً لوجود طلبات شراء مسجلة باسمه في النظام!</div>
        <?php endif; ?>
        <table style="width:100%; border-collapse:collapse; background:white; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden;">
            <thead>
                <tr style="background:#0f172a; color:white;">
                    <th style="padding:12px;">المعرف</th><th style="padding:12px;">الاسم الكامل</th><th style="padding:12px;">البريد الإلكتروني</th><th style="padding:12px;">الصلاحية الحالية</th><th style="padding:12px; text-align:left;">إجراءات الإدارة</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
                while($u = $users->fetch_assoc()): ?>
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:12px;">#<?= $u['user_id'] ?></td>
                        <td style="padding:12px; font-weight:600;"><?= htmlspecialchars($u['full_name']) ?></td>
                        <td style="padding:12px;"><?= htmlspecialchars($u['email']) ?></td>
                        <td style="padding:12px;"><span style="padding:4px 10px; border-radius:20px; font-size:12px; font-weight:bold; <?= $u['role'] === 'admin' ? 'background:#f0fdf4; color:#16a34a;' : 'background:#f1f5f9; color:#475569;' ?>"><?= $u['role'] ?></span></td>
                        <td style="padding:12px; text-align:left;">
                            <a href="?action=toggle_role&user_id=<?= $u['user_id'] ?>" style="color:#3b82f6; text-decoration:none; font-size:13px; margin-left:15px; font-weight:bold;">🔄 تبديل الصلاحية</a>
                            <a href="?action=delete_user&user_id=<?= $u['user_id'] ?>" onclick="return confirm('هل أنت متأكد من حذف حساب هذا المستخدم نهائياً؟')" style="color:#ef4444; text-decoration:none; font-size:13px; font-weight:bold;">❌ حذف</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if ($activeTab === 'messages'): ?>
        <div style="display:flex; flex-direction:column; gap:15px;">
            <?php 
            $messages = $conn->query("SELECT * FROM contacts ORDER BY submitted_at DESC");
            if($messages->num_rows == 0) echo "<p style='color:#64748b;'>لا توجد رسائل واردة حتى الآن.</p>";
            while($m = $messages->fetch_assoc()): ?>
                <div style="background:white; border:1px solid #e2e8f0; padding:20px; border-radius:14px; <?= !$m['is_read'] ? 'border-right:5px solid #3b82f6;' : '' ?>">
                    <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                        <strong><?= htmlspecialchars($m['name']) ?> (<span style="color:#64748b; font-weight:normal;"><?= htmlspecialchars($m['email']) ?></span>)</strong>
                        <span style="font-size:12px; color:#94a3b8;"><?= $m['submitted_at'] ?></span>
                    </div>
                    <div style="font-size:14px; color:#475569; margin-bottom:12px;"><strong>الموضوع:</strong> <?= htmlspecialchars($m['subject']) ?></div>
                    <p style="background:#f8fafc; padding:12px; border-radius:8px; margin:0; font-size:14px; color:#334155; line-height:1.6;"><?= nl2br(htmlspecialchars($m['message'])) ?></p>
                    <?php if(!$m['is_read']): ?>
                        <div style="text-align:left; margin-top:10px;"><a href="?action=mark_read&msg_id=<?= $m['message_id'] ?>" style="background:#3b82f6; color:white; text-decoration:none; padding:6px 14px; border-radius:8px; font-size:12px; font-weight:bold;">✓ تعليم كمقروء</a></div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>

</main>