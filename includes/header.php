<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechZone | متجرك الإلكتروني المتميز</title>
    
    <link rel="stylesheet" href="/php-ecommerce-project/assets/css/style.css">
</head>

<body>
    <header>
        <div class="logo">
            <a href="/php-ecommerce-project/index.php">TechZone</a>
        </div>

        <?php
        // التضمين باستخدام __DIR__ ممتاز لأنه يجلب المسار الفيزيائي الحقيقي على السيرفر
        include __DIR__ . '/navbar.php';
        ?>

        <div class="cart-icon">
            <a href="/php-ecommerce-project/pages/cart.php">
                🛒 <span id="cart-count">0</span>
            </a>
        </div>
    </header>