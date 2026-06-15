<?php
// 1. استدعاء الهيدر المشترك وقاعدة البيانات في البداية
include 'includes/header.php';
//require_once 'includes/db.php';

// جلب آخر 6 منتجات مميزة مضافة ديناميكياً
$featured = $conn->query(
    "SELECT * FROM products ORDER BY created_at DESC LIMIT 6"
);
?>

<style>
    .modern-hero {
        background: radial-gradient(circle at top right, #1e293b, #0f172a);
        color: white;
        padding: 120px 20px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .modern-hero::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 50px;
        background: linear-gradient(to top, #f8fafc, transparent);
        pointer-events: none;
    }
    .hero-btn {
        background: #f59e0b;
        color: white;
        padding: 14px 35px;
        text-decoration: none;
        font-weight: 600;
        border-radius: 50px;
        display: inline-block;
        font-size: 16px;
        box-shadow: 0 10px 20px rgba(245, 158, 11, 0.2);
        transition: all 0.3s ease;
    }
    .hero-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 25px rgba(245, 158, 11, 0.3);
        background: #d97706;
    }
    .search-container input:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.1) !important;
    }
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 30px;
    }
    .product-card {
        background: #ffffff;
        border-radius: 24px;
        border: 1px solid #f1f5f9;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
        border-color: #e2e8f0;
    }
    .img-wrapper {
        width: 100%;
        height: 240px;
        overflow: hidden;
        position: relative;
        background: #f8fafc;
    }
    .img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 15px;
        transition: transform 0.5s ease;
    }
    .product-card:hover .img-wrapper img {
        transform: scale(1.06);
    }
    .btn-submit {
        width: 100%;
        background-color: #0f172a;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 14px;
        font-weight: 600;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    .btn-submit:hover {
        background-color: #3b82f6;
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.25);
    }
</style>

<section class="modern-hero">
    <div style="max-width: 800px; margin: 0 auto;">
        <h1 style="font-size: 42px; margin-bottom: 20px; font-weight: 800; line-height: 1.3;">تسوق أحدث الأجهزة والإلكترونيات</h1>
        <p style="font-size: 18px; color: #94a3b8; margin-bottom: 35px; font-weight: 300;">عروض حصرية وجودة مضمونة - توصيل سريع وأسعار تنافسية</p>
        <a href="pages/products.php" class="hero-btn">تسوق الآن ←</a>
    </div>
</section>

<?php if (isset($_GET['success']) && $_GET['success'] === 'added'): ?>
    <div style="max-width: 1200px; margin: 20px auto; padding: 0 20px;">
        <div style="background-color: #ecfdf5; color: #059669; padding: 14px 20px; border-radius: 12px; font-size: 14px; font-weight: 500; border: 1px solid #a7f3d0; text-align: right; direction: rtl;">
            ✅ تم إضافة المنتج إلى حقيبة المشتريات بنجاح!
        </div>
    </div>
<?php endif; ?>

<div class="search-container" style="max-width: 700px; margin: -30px auto 40px auto; padding: 0 20px; position: relative; z-index: 10;">
    <input type="text" id="searchBar" placeholder="🔍 ابحث عن المنتجات المميزة هنا..." style="width: 100%; padding: 16px 25px; border: 1px solid #e2e8f0; border-radius: 50px; font-size: 16px; outline: none; box-shadow: 0 15px 30px rgba(15, 23, 42, 0.06); text-align: right; background: #ffffff; transition: all 0.3s;">
</div>

<main style="max-width: 1200px; margin: 0 auto; padding: 20px 20px 80px 20px; min-height: 400px;">
    
    <div style="text-align: center; margin-bottom: 50px;">
        <h2 style="font-size: 28px; color: #0f172a; font-weight: 800; margin-bottom: 10px;">⭐ منتجات مميزة</h2>
        <div style="width: 60px; height: 4px; background: #f59e0b; margin: 0 auto; border-radius: 2px;"></div>
    </div>

    <div class="products-grid">
        
        <?php while ($p = $featured->fetch_assoc()): ?>
            <div class="product-card" data-name="<?= strtolower(htmlspecialchars($p['name'])) ?>">
                
                <div class="img-wrapper">
                    <img src="/php-ecommerce-project/<?= htmlspecialchars($p['image_url']) ?>"
                         alt="<?= htmlspecialchars($p['name']) ?>"
                         onerror="this.src='/php-ecommerce-project/assets/images/products/placeholder.jpg'">
                </div>
                
                <div class="card-body" style="padding: 24px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between;">
                    
                    <div style="text-align: center;">
                        <h3 style="font-size: 18px; color: #1e293b; margin: 0 0 12px 0; font-weight: 700; line-height: 1.4; min-height: 50px; display: flex; align-items: center; justify-content: center;"><?= htmlspecialchars($p['name']) ?></h3>
                        
                        <p class="price" style="color: #f59e0b; font-size: 22px; font-weight: 800; margin-bottom: 20px; display: inline-block; direction: rtl;">
                            <?= number_format($p['price'], 0) ?> <span style="font-size: 14px; font-weight: 600; color: #64748b; margin-right: 2px;">₪</span>
                        </p>
                    </div>
                    
                    <div class="card-actions" style="width: 100%; display: flex; flex-direction: column; gap: 12px; align-items: center;">
                        
                        <form action="/php-ecommerce-project/api/add-to-cart.php" method="POST" style="width: 100%;">
                            <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                            <input type="hidden" name="quantity" value="1">
                            <input type="hidden" name="redirect" value="/php-ecommerce-project/index.php">
                            <button type="submit" class="btn-submit">أضف إلى السلة</button>
                        </form>

                        <a href="/php-ecommerce-project/pages/product-detail.php?id=<?= $p['product_id'] ?>"
                           style="color: #64748b; text-decoration: none; font-size: 14px; font-weight: 500; transition: color 0.2s;" 
                           onmouseover="this.style.color='#3b82f6'" onmouseout="this.style.color='#64748b'">عرض التفاصيل ⚡</a>
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
</script>

<?php
// 3. استدعاء الفوتر المشترك في النهاية
include 'includes/footer.php';
?>