<?php
// 1. استدعاء الهيدر المشترك في البداية
include 'includes/header.php';
require_once 'includes/db.php';

$featured = $conn->query(
    "SELECT * FROM products ORDER BY created_at DESC LIMIT 6"
);
?>

<!-- قسم الـ Hero متوافق تماماً مع الصورة -->
<section class="hero" style="background: linear-gradient(to bottom, #0f172a, #1e293b); color: white; padding: 100px 5px 80px 5px; text-align: center;">
    <h1 style="font-size: 36px; margin-bottom: 15px; font-weight: bold; letter-spacing: 0.5px;">تسوق أحدث الأجهزة والإلكترونيات</h1>
    <p style="font-size: 16px; color: #cbd5e1; margin-bottom: 30px;">عروض حصرية وجودة مضمونة - توصيل سريع وأسعار تنافسية</p>
    <a href="products.php" style="background-color: #f59e0b; color: #ffffff; padding: 10px 30px; text-decoration: none; font-weight: bold; border-radius: 25px; display: inline-block; font-size: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: 0.3s;">تسوق الآن ←</a>
</section>

<!-- شريط البحث السريع -->
<div style="max-width: 1200px; margin: 30px auto 10px auto; padding: 0 20px;">
    <input type="text" id="searchBar" placeholder="ابحث عن المنتجات المميزة هنا..." style="width: 100%; padding: 12px 20px; border: 1px solid #e2e8f0; border-radius: 25px; font-size: 15px; outline: none; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-align: right;">
</div>

<!-- القسم الرئيسي للمنتجات المميزة -->
<main style="max-width: 1200px; margin: 0 auto; padding: 40px 20px; min-height: 400px; background-color: #f8fafc;">
    
    <!-- العنوان موسط مع الإيموجي مثل الصورة -->
    <h2 style="text-align: center; margin-bottom: 40px; font-size: 26px; color: #0f172a; font-weight: bold;">⭐ منتجات مميزة</h2>

    <!-- شبكة عرض المنتجات المتجاوبة -->
    <div class="products-grid" style="display: grid; grid-template-columns: repeat(4, minmax(200px, 1fr)); gap: 10px; justify-content: center;">
        
        <?php while ($p = $featured->fetch_assoc()): ?>
            <div class="product-card" data-name="<?= strtolower(htmlspecialchars($p['name'])) ?>" style="background: #ffffff; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); overflow: hidden; display: flex; flex-direction: column; text-align: center; transition: transform 0.3s; padding-bottom: 20px;">
                
                <!-- صورة المنتج بحواف دائرية علوية -->
                <div style="width: 100%; height: 200px; overflow: hidden;">
                    <img src="/php-ecommerce-project/<?= htmlspecialchars($p['image_url']) ?>"
                         alt="<?= htmlspecialchars($p['name']) ?>"
                         onerror="this.src='/php-ecommerce-project/assets/images/products/placeholder.jpg'"
                         style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                
                <!-- تفاصيل المنتج -->
                <div class="card-body" style="padding: 20px 15px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; align-items: center;">
                    
                    <h3 style="font-size: 18px; color: #1e293b; margin: 0 0 10px 0; font-weight: bold;"><?= htmlspecialchars($p['name']) ?></h3>
                    
                    <!-- السعر باللون البرتقالي المتناسق مع الصورة -->
                    <p class="price" style="color: #f59e0b; font-size: 18px; font-weight: bold; margin-bottom: 15px;">
                        <span style="font-size: 14px; margin-left: 3px;">₪</span><?= number_format($p['price'], 0) ?>
                    </p>
                    
                    <!-- أزرار التحكم والطلب -->
                    <div class="card-actions" style="width: 100%; display: flex; flex-direction: column; gap: 8px; align-items: center;">
                        
                        <form action="/php-ecommerce-project/api/add-to-cart.php" method="POST" style="width: 85%;">
                            <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                            <input type="hidden" name="quantity" value="1">
                            <input type="hidden" name="redirect" value="/php-ecommerce-project/">
                            <button type="submit" style="width: 100%; background-color: #0f172a; color: white; border: none; padding: 10px 20px; border-radius: 20px; font-weight: bold; cursor: pointer; font-size: 14px; transition: background 0.2s;">أضف إلى السلة</button>
                        </form>

                        <a href="/php-ecommerce-project/pages/product-detail.php?id=<?= $p['product_id'] ?>"
                           style="color: #64748b; text-decoration: none; font-size: 13px; margin-top: 5px; transition: color 0.2s;" 
                           onmouseover="this.style.color='#0f172a'" onmouseout="this.style.color='#64748b'">عرض التفاصيل</a>
                    </div>

                </div>
            </div>
        <?php endwhile; ?>

    </div>
</main>

<script>
    // تصفية حية للمنتجات المميزة بالبحث السريع (Client-side)
    document.getElementById('searchBar').addEventListener('input', function(e) {
        let query = e.target.value.toLowerCase();
        let items = document.querySelectorAll('.product-card');

        items.forEach(item => {
            let name = item.getAttribute('data-name').toLowerCase();
            if (name.includes(query)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // دالة إضافة العنصر للـ LocalStorage وحفظ البيانات
    function addToCart(id, name, price) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        let existing = cart.find(item => item.id === id);

        if (existing) {
            existing.quantity += 1;
        } else {
            cart.push({
                id: id,
                name: name,
                price: price,
                quantity: 1
            });
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        if (typeof updateCartCount === "function") {
            updateCartCount(); 
        }
        alert(`تمت إضافة "${name}" إلى سلة المشتريات!`);
    }
</script>

<?php
// 3. استدعاء الفوتر المشترك في النهاية
include 'includes/footer.php';
?>