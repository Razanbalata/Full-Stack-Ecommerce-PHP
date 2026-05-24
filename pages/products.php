<?php
// 1. استدعاء الهيدر الموحد
include '../includes/header.php';

// مصفوفة المنتجات الديناميكية - يمكنكِ التعديل عليها أو إضافة منتجات جديدة هنا بسهولة
$products = [
    [
        'id' => 1,
        'name' => 'سماعات لاسلكية Pro',
        'category' => 'electronics',
        'price' => 149,
        'desc' => 'سماعة محيطية عازلة للصوت مع بطارية تدوم 40 ساعة.',
        'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&q=80'
    ],
    [
        'id' => 2,
        'name' => 'ساعة ذكية Sport',
        'category' => 'electronics',
        'price' => 249,
        'desc' => 'مقاومة للماء، وتدعم قياس نبضات القلب والأنشطة الرياضية.',
        'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80'
    ],
    [
        'id' => 3,
        'name' => 'قاعدة شحن لاسلكية 3 في 1',
        'category' => 'accessories',
        'price' => 99,
        'desc' => 'اشحن هاتفك وساعتك وسماعتك في نفس الوقت وبسرعة فائقة.',
        'image' => 'https://images.unsplash.com/photo-1611532736597-de2d4265fba3?w=500&q=80'
    ],
    [
        'id' => 4,
        'name' => 'حقيبة أجهزة لوحية مقاومة للماء',
        'category' => 'accessories',
        'price' => 55,
        'desc' => 'حماية كاملة لجهازك مع جيوب إضافية لترتيب الأسلاك والشواحن.',
        'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=500&q=80'
    ]
];
?>

<main style="max-width: 1200px; margin: 40px auto; padding: 0 20px; min-height: 500px; direction: rtl;">
    
    <h1 style="text-align: center; margin-bottom: 10px; color: #1e293b; font-weight: bold;">تصفح جميع المنتجات</h1>
    <p style="text-align: center; color: #64748b; margin-bottom: 40px;">اكتشف أحدث الأجهزة والملحقات التقنية بأفضل الأسعار</p>

    <div style="text-align: center; margin-bottom: 40px; display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
        <button onclick="filterCategory('all')" class="filter-btn active" style="background-color: #f59e0b; color: #1e293b; border: none; padding: 10px 25px; font-weight: bold; border-radius: 20px; cursor: pointer; transition: 0.3s;">الكل</button>
        <button onclick="filterCategory('electronics')" class="filter-btn" style="background-color: #e2e8f0; color: #1e293b; border: none; padding: 10px 25px; font-weight: bold; border-radius: 20px; cursor: pointer; transition: 0.3s;">أجهزة إلكترونية</button>
        <button onclick="filterCategory('accessories')" class="filter-btn" style="background-color: #e2e8f0; color: #1e293b; border: none; padding: 10px 25px; font-weight: bold; border-radius: 20px; cursor: pointer; transition: 0.3s;">إكسسوارات</button>
    </div>

    <div class="products-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
        
        <?php 
        foreach ($products as $product): 
        ?>
            <div class="product-item" data-category="<?php echo $product['category']; ?>" style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center; display: flex; flex-direction: column; height: fit-content; border: 1px solid #e2e8f0;">
                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" style="max-width: 100%; border-radius: 6px; margin-bottom: 15px; height: 200px; object-fit: cover;">
                
                <h3 style="font-size: 18px; margin-bottom: 8px; color: #1e293b; font-weight: bold;"><?php echo $product['name']; ?></h3>
                <p style="color: #64748b; font-size: 14px; margin-bottom: 15px; flex-grow: 1;"><?php echo $product['desc']; ?></p>
                <p style="color: #f59e0b; font-weight: bold; font-size: 22px; margin-bottom: 15px;"><?php echo $product['price']; ?> ₪</p>
                
                <div style="display: flex; gap: 10px;">
                    <a href="product-detail.php?id=<?php echo $product['id']; ?>" style="flex: 1; background-color: #e2e8f0; color: #1e293b; text-decoration: none; padding: 10px; border-radius: 5px; font-weight: bold; font-size: 14px; line-height: 20px; text-align: center;">التفاصيل</a>
                    <button onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>)" style="flex: 2; background-color: #1e293b; color: white; border: none; padding: 10px; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 14px;">أضف للسلة</button>
                </div>
            </div>
        <?php 
        endforeach; 
        ?>

    </div>
</main>

<script>
// دالة التصفية حسب الفئة
function filterCategory(category) {
    let items = document.querySelectorAll('.product-item');
    
    items.forEach(item => {
        if (category === 'all') {
            item.style.display = 'flex'; 
        } else {
            if (item.getAttribute('data-category') === category) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none'; 
            }
        }
    });

    let buttons = document.querySelectorAll('.filter-btn');
    buttons.forEach(btn => {
        btn.style.backgroundColor = '#e2e8f0';
        btn.style.color = '#1e293b';
    });
    event.target.style.backgroundColor = '#f59e0b';
}

// دالة إضافة العنصر للـ LocalStorage الموحدة
function addToCart(id, name, price) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    let existing = cart.find(item => item.id === id);

    if (existing) {
        existing.quantity += 1;
    } else {
        cart.push({ id: id, name: name, price: price, quantity: 1 });
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    if (typeof updateCartCount === "function") {
        updateCartCount(); 
    }
    alert(`تمت إضافة "${name}" إلى سلة المشتريات!`);
}
</script>

<?php
// 3. استدعاء الفوتر الموحد
include '../includes/footer.php';
?>