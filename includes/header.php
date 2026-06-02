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
    <title>EliteShop | متجرك الإلكتروني المتميز</title>
    
    <link rel="stylesheet" href="/php-ecommerce-project/assets/css/style.css">

    <!-- تنسيقات التجاوب والـ Dropdown للشاشات الصغيرة -->
    <style>
        header {
            background-color: #0c2340; /* اللون الكحلي الفاخر المعتمد */
            padding: 15px 4%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 1000;
        }

        header .logo a {
            color: #ffffff;
            text-decoration: none;
            font-size: 24px;
            font-weight: bold;
        }

        header .cart-icon a {
            color: #ffffff;
            text-decoration: none;
            font-size: 22px;
            position: relative;
        }

        header #cart-count {
            background-color: #f59e0b;
            color: white;
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 50%;
            position: absolute;
            top: -8px;
            right: -10px;
        }

        /* زر القائمة المنسدلة للشاشات الصغيرة (مخفي افتراضياً) */
        .menu-toggle-btn {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            outline: none;
        }

        /* الشاشات الصغيرة (الجوال) */
        @media (max-width: 668px) {
            .menu-toggle-btn {
                display: block; /* إظهار زر التحكم بالمنسدلة */
            }

            nav {
                display: none; /* إخفاء القائمة الأساسية لتتحول إلى دروب داون */
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background-color: #0c2340;
                box-shadow: 0 8px 16px rgba(0,0,0,0.15);
                border-top: 1px solid rgba(255,255,255,0.1);
                z-index: 999;
            }

            /* كلاس يضيفه الجافاسكريبت لإظهار الدروب داون */
            nav.open-dropdown {
                display: block;
            }

            nav ul {
                flex-direction: column;
                padding: 15px 20px;
                margin: 0;
                list-style: none;
            }

            nav ul li {
                margin: 10px 0;
            }

            nav ul li a {
                display: block;
                color: #ffffff;
                text-decoration: none;
                font-size: 16px;
                padding-bottom: 8px;
                border-bottom: 1px solid rgba(255,255,255,0.05);
            }
        }

        /* الشاشات الكبيرة (الافتراضي المتناسق مع الصورة) */
        @media (min-width: 669px) {
            nav ul {
                display: flex;
                gap: 25px;
                list-style: none;
                margin: 0;
                padding: 0;
            }
            nav ul li a {
                color: #ffffff;
                text-decoration: none;
                font-size: 15px;
            }
        }
    </style>
</head>

<body>
    <header>
        <!-- زر الهمبرغر للتحكم بالدروب داون في الجوال -->
        <button class="menu-toggle-btn" onclick="toggleDropdown()">☰</button>

        <div class="logo">
            <a href="/php-ecommerce-project/index.php">EliteShop</a>
        </div>

        <?php
        // تضمين القائمة كما هي
        include __DIR__ . '/navbar.php';
        ?>

        <div class="cart-icon">
            <a href="/php-ecommerce-project/pages/cart.php">
                🛒 <span id="cart-count">5</span>
            </a>
        </div>
    </header>

    <!-- كود جافاسكريبت بسيط جداً لتشغيل فتح وإغلاق الدروب داون -->
    <script>
        function toggleDropdown() {
            var navbar = document.querySelector('nav');
            navbar.classList.toggle('open-dropdown');
        }
    </script>