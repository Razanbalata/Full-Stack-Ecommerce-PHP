<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// حل مشكلة المسار الديناميكي عبر تحديد المجلد الرئيسي للمشروع (php-ecommerce-project)
$base_dir = dirname(__DIR__); 
$db_path = $base_dir . '/includes/db.php';

if (file_exists($db_path)) {
    require_once $db_path;
}

$cartCount = 0;

// التحقق من وجود الجلسة والاتصال قبل تشغيل الاستعلام
if (isset($_SESSION['user_id']) && isset($conn) && $conn instanceof mysqli) {
    $uid = (int)$_SESSION['user_id'];

    // استعلام مباشر وسريع لحساب إجمالي الكميات في السلة
    $query = "SELECT SUM(quantity) AS total FROM cart_items WHERE user_id = $uid";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $cartCount = isset($row['total']) ? (int)$row['total'] : 0;
    }
}

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EliteShop | متجرك الإلكتروني المتميز</title>

    <link rel="stylesheet" href="/php-ecommerce-project/assets/css/style.css">

    <style>
        header {
            background-color: #0c2340;
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

        .menu-toggle-btn {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }

        @media (max-width: 668px) {
            .menu-toggle-btn {
                display: block;
            }

            nav {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background-color: #0c2340;
                z-index: 999;
            }

            nav.open-dropdown {
                display: block;
            }

            nav ul {
                flex-direction: column;
                padding: 15px;
                list-style: none;
            }

            nav ul li {
                margin: 10px 0;
            }

            nav ul li a {
                color: white;
                text-decoration: none;
            }
        }

        @media (min-width: 669px) {
            nav ul {
                display: flex;
                gap: 25px;
                list-style: none;
                margin: 0;
                padding: 0;
            }

            nav ul li a {
                color: white;
                text-decoration: none;
                font-size: 15px;
            }
        }
    </style>
</head>

<body>

    <header>

        <button class="menu-toggle-btn" onclick="toggleDropdown()">☰</button>

        <div class="logo">
            <a href="/php-ecommerce-project/index.php">EliteShop</a>
        </div>

        <?php include __DIR__ . '/navbar.php'; ?>

        <div class="cart-icon">
            <a href="/php-ecommerce-project/pages/cart.php">
                🛒
                <span id="cart-count">
                    <?= $cartCount ?>
                </span>
            </a>
        </div>

    </header>

    <script>
        function toggleDropdown() {
            document.querySelector('nav').classList.toggle('open-dropdown');
        }
    </script>
</body>
</html>
