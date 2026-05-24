<?php
// 1. استدعاء الهيدر الموحد
include '../includes/header.php';

// محاكاة مصفوفة المنتجات (بيانات ثابتة مؤقتاً لتسهيل العرض قبل ربط قاعدة البيانات)
$products = [
    1 => [
        'name' => 'سماعة لاسلكية Pro Max',
        'price' => 149,
        'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&q=80',
        'desc' => 'جودة استوديو، عزل ضوضاء نشط، بطارية تدوم 40 ساعة، بلوتوث 5.3 وتصميم مريح للأذن مع شاحن سريع وعمر بطارية طويل جداً لتستمتع بالاستماع طوال اليوم بدون انقطاع.'
    ],
    2 => [
        'name' => 'ساعة ذكية Sport',
        'price' => 249,
        'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80',
        'desc' => 'مقاومة للماء، وتدعم قياس نبضات القلب والأنشطة الرياضية المختلفة مع شاشة عالية الدقة.'
    ],
    3 => [
        'name' => 'لابتوب UltraBook',
        'price' => 3999,
        'image' => 'https://images.unsplash.com/photo-1611532736597-de2d4265fba3?w=500&q=80',
        'desc' => 'أداء خارق مع معالج حديث، ذاكرة عشوائية واسعة، وهارد سريع جداً للمصممين والمبرمجين.'
    ],
    4 => [
        'name' => 'شاحن سريع 65W',
        'price' => 79,
        'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=500&q=80',
        'desc' => 'يحتوي على منافذ متعددة لشحن هاتفك وحاسوبك المحمول في نفس الوقت بأقصى سرعة وأمان.'
    ]
];

// جلب معرف المنتج من الرابط الإلكتروني، وإذا لم يتوفر نعرض المنتج الأول تلقائياً
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// التحقق من وجود المنتج داخل المصفوفة، إذا لم يوجد نختار المنتج رقم 1 كبديل آمن
if (!array_key_exists($product_id, $products)) {
    $product_id = 1;
}

$current_product = $products[$product_id];
?>

<main style="max-width: 1100px; margin: 60px auto; padding: 0 20px; min-height: 500px;direction:ltr;">
    
    <div style="background: white; border-radius: 12px; padding: 40px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; gap: 40px; flex-wrap: wrap; align-items: center; border: 1px solid #e2e8f0;">
        
        <div style="flex: 1; min-width: 300px; direction:rtl;">
            <h1 style="font-size: 28px; color: #1e293b; margin-bottom: 15px; font-weight: bold;"><?php echo $current_product['name']; ?></h1>
            
            <p style="color: #64748b; font-size: 16px; line-height: 1.8; margin-bottom: 25px;">
                <?php echo $current_product['desc']; ?>
            </p>
            
            <p style="color: #f59e0b; font-weight: bold; font-size: 32px; margin-bottom: 30px;">
                <?php echo $current_product['price']; ?> ₪
            </p>

            <div style="margin-bottom: 25px; display: flex; align-items: center; gap: 15px;">
                <label for="quantity" style="font-weight: bold; color: #1e293b; font-size: 16px;">الكمية:</label>
                <input type="number" id="quantity" value="1" min="1" max="10" style="width: 70px; padding: 10px; border: 2px solid #cbd5e1; border-radius: 6px; font-size: 16px; text-align: center; font-weight: bold; outline: none;">
            </div>

            <button onclick="addDetailedProductToCart(<?php echo $product_id; ?>, '<?php echo $current_product['name']; ?>', <?php echo $current_product['price']; ?>)" style="background-color: #1e293b; color: white; border: none; padding: 14px 35px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 16px; display: flex; align-items: center; gap: 10px; transition: 0.3s;">
                🛒 أضف إلى السلة
            </button>
        </div>

        <div style="flex: 1; min-width: 300px; text-align: center;">
            <img src="<?php echo $current_product['image']; ?>" alt="<?php echo $current_product['name']; ?>" style="max-width: 100%; max-height: 400px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
        </div>

    </div>
</main>

<script>
// دالة الإضافة المخصصة لصفحة تفاصيل المنتج لتدعم محدد الكمية الديناميكي
function addDetailedProductToCart(id, name, price) {
    // 1. قراءة الكمية التي حددها المستخدم وتحويلها لرقم صحيح
    let qtyInput = document.getElementById('quantity');
    let quantityToAdd = parseInt(qtyInput.value) || 1;

    // التأكد من أن الكمية المدخلة منطقية وليست صفراً أو سالباً
    if (quantityToAdd <= 0) {
        alert('يرجى اختيار كمية صحيحة!');
        return;
    }

    // 2. جلب السلة الحالية من ذاكرة المتصفح المحلية
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // 3. البحث في السلة إذا كان المنتج مضافاً سابقاً
    let existing = cart.find(item => item.id === id);

    if (existing) {
        // إذا كان موجوداً، نزيد على كميته السابقة المقدار الجديد الذي اختاره المستخدم
        existing.quantity += quantityToAdd;
    } else {
        // إذا كان منتجاً جديداً تماماً، نضيفه بكافة بياناته مع الكمية المحددة
        cart.push({ id: id, name: name, price: price, quantity: quantityToAdd });
    }

    // 4. حفظ التحديثات في الـ LocalStorage من جديد
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // 5. تحديث العداد العلوي الموجود بالفوتر فوراً
    updateCartCount();
    
    // رسالة تأكيد للمستخدم موضحاً بها الكمية
    alert(`تمت إضافة ${quantityToAdd} من "${name}" إلى سلة المشتريات بنجاح!`);
}
</script>

<?php
// 3. استدعاء الفوتر الموحد
include '../includes/footer.php';
?>