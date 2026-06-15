<?php
// api/add-to-cart.php — معالج إضافة للسلة
session_start();
require_once __DIR__ . '/../includes/db.php';

$redirect = $_POST['redirect'] ?? '/php-ecommerce-project/pages/cart.php';

// يجب تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header('Location: /php-ecommerce-project/auth/login.php?msg=login_required');
    exit;
}

$userId    = (int)$_SESSION['user_id'];
$productId = (int)($_POST['product_id'] ?? 0);
$quantity  = max(1, (int)($_POST['quantity'] ?? 1));

if ($productId <= 0) {
    header("Location: $redirect");
    exit;
}

// تحقق إن المنتج موجود وعنده مخزون
$check = $conn->prepare("SELECT stock_quantity FROM products WHERE product_id = ?");
$check->bind_param('i', $productId);
$check->execute();
$product = $check->get_result()->fetch_assoc();

if (!$product || $product['stock_quantity'] < $quantity) {
    header("Location: $redirect&error=out_of_stock");
    exit;
}

// INSERT أو UPDATE الكمية لو موجود
$stmt = $conn->prepare("
    INSERT INTO cart_items (user_id, product_id, quantity)
    VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE quantity = quantity + ?
");
$stmt->bind_param('iiii', $userId, $productId, $quantity, $quantity);
$stmt->execute();

$redirect .= (strpos($redirect, '?') !== false) ? '&' : '?';
header("Location: " . $redirect . "success=added");

exit;
