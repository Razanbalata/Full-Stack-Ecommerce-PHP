<?php
// 1. استدعاء الهيدر المشترك في البداية
include 'includes/header.php';
?>

<section class="hero" style="background: linear-gradient(135deg, #1e293b, #0f172a); color: white; padding: 90px 5%; text-align: center; border-bottom: 4px solid #f59e0b;">
    <h1 style="font-size: 38px; margin-bottom: 15px; font-weight: 800;">اكتشف عالم التقنية المتكامل</h1>
    <p style="font-size: 18px; color: #94a3b8; margin-bottom: 35px;">خيارك الأول لأحدث الأجهزة والإلكترونيات بأسعار تنافسية وجودة مضمونة.</p>
    <a href="products.php" style="background-color: #f59e0b; color: #1e293b; padding: 14px 35px; text-decoration: none; font-weight: bold; border-radius: 5px; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3); transition: 0.3s;">تسوق الآن ←</a>
</section>

<div style="max-width: 1200px; margin: 40px auto 20px auto; padding: 0 20px;">
    <input type="text" id="searchBar" placeholder="ابحث عن المنتجات المميزة هنا..." style="width: 100%; padding: 14px; border: 2px solid #cbd5e1; border-radius: 6px; font-size: 16px; outline: none; transition: border-color 0.3s;">
</div>

<main style="max-width: 1200px; margin: 0 auto; padding: 20px; min-height: 400px;">
    <h2 style="margin-bottom: 30px; border-right: 5px solid #f59e0b; padding-right: 12px; font-size: 24px;">منتجات مختارة لكِ</h2>

    <div class="products-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
        
        <div class="product-card" data-name="سماعات لاسلكية برو" style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center; display: flex; flex-direction: column; justify-content: space-between; border: 1px solid #e2e8f0;">
            <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&q=80" alt="سماعة برو" style="max-width: 100%; border-radius: 6px; margin-bottom: 15px;">
            <h3 style="font-size: 18px; margin-bottom: 8px; color: #1e293b;">سماعات لاسلكية Pro</h3>
            <p style="color: #f59e0b; font-weight: bold; font-size: 22px; margin-bottom: 15px;">149 ₪</p>
            <div style="display: flex; gap: 10px;">
                <a href="product-detail.php?id=1" style="flex: 1; background-color: #e2e8f0; color: #1e293b; text-decoration: none; padding: 10px; border-radius: 5px; font-weight: bold; font-size: 14px; line-height: 20px;">التفاصيل</a>
                <button onclick="addToCart(1, 'سماعات لاسلكية Pro', 149)" style="flex: 2; background-color: #1e293b; color: white; border: none; padding: 10px; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 14px;">أضف للسلة</button>
            </div>
        </div>

        <div class="product-card" data-name="ساعة ذكية سبورت" style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center; display: flex; flex-direction: column; justify-content: space-between; border: 1px solid #e2e8f0;">
            <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80" alt="ساعة سبورت" style="max-width: 100%; border-radius: 6px; margin-bottom: 15px;">
            <h3 style="font-size: 18px; margin-bottom: 8px; color: #1e293b;">ساعة ذكية Sport</h3>
            <p style="color: #f59e0b; font-weight: bold; font-size: 22px; margin-bottom: 15px;">249 ₪</p>
            <div style="display: flex; gap: 10px;">
                <a href="product-detail.php?id=2" style="flex: 1; background-color: #e2e8f0; color: #1e293b; text-decoration: none; padding: 10px; border-radius: 5px; font-weight: bold; font-size: 14px; line-height: 20px;">التفاصيل</a>
                <button onclick="addToCart(2, 'ساعة ذكية Sport', 249)" style="flex: 2; background-color: #1e293b; color: white; border: none; padding: 10px; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 14px;">أضف للسلة</button>
            </div>
        </div>

    </div>
</main>

<script>
// تصفية حية للمنتجات المميزة بالبحث السريع (Client-side)
document.getElementById('searchBar').addEventListener('input', function(e) {
    let query = e.target.value.toLowerCase();
    let items = document.querySelectorAll('.product-card');

    items.forEach(item => {
        let name = item.getAttribute('data-name').toLowerCase();
        item.style.display = name.includes(query) ? 'flex' : 'none';
    });
});

// دالة إضافة العنصر للـ LocalStorage وحفظ البيانات
function addToCart(id, name, price) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    let existing = cart.find(item => item.id === id);

    if (existing) {
        existing.quantity += 1;
    } else {
        cart.push({ id: id, name: name, price: price, quantity: 1 });
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount(); // دالة تحديث العداد الموجودة بالفوتر لشاشتكِ فورياً
    alert(`تمت إضافة "${name}" إلى سلة المشتريات!`);
}
</script>

<?php
// 3. استدعاء الفوتر المشترك في النهاية
include 'includes/footer.php';
?>