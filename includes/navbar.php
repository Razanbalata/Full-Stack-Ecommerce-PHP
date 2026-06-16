<nav>
    <ul class="flex space-x-4 rtl">
        <li><a href="/php-ecommerce-project/index.php">الرئيسية</a></li>
        <li><a href="/php-ecommerce-project/pages/products.php">المنتجات</a></li>
        <li><a href="/php-ecommerce-project/pages/contact.php">اتصل بنا</a></li>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <li><a href="/php-ecommerce-project/admin/dashboard.php" style="color: #f59e0b; font-weight: bold;">لوحة التحكم (Admin)</a></li>
            <?php else: ?>
                <li><a href="/php-ecommerce-project/pages/cart.php">سلة التسوق</a></li>
                <li><a href="/php-ecommerce-project/pages/profile.php">الملف الشخصي</a></li>
            <?php endif; ?>
            <li><a href="/php-ecommerce-project/auth/logout.php" style="color: #ef4444;">خروج</a></li>
        <?php else: ?>
            <li><a href="/php-ecommerce-project/auth/login.php">تسجيل الدخول</a></li>
            <li><a href="/php-ecommerce-project/auth/register.php">إنشاء حساب</a></li>
        <?php endif; ?>
    </ul>
</nav>