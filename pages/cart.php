<?php
// استدعاء الهيدر الموحد
include '../includes/header.php';
?>

<main style="max-width: 1100px; margin: 40px auto; padding: 0 20px; min-height: 600px;">
    
    <div style="background: white; border-radius: 12px; padding: 40px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
        
        <h2 style="color: #1e293b; margin-bottom: 30px; display: flex; align-items: center; gap: 10px;">
            🛒 سلة المشتريات
        </h2>

        <div id="cart-container">
            </div>

    </div>
</main>

<script>
// دالة لعرض محتويات السلة من الـ LocalStorage
function displayCart() {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const container = document.getElementById('cart-container');

    // إذا كانت السلة فارغة
    if (cart.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 40px; color: #64748b;">
                <p style="font-size: 18px; margin-bottom: 20px;">سلة المشتريات فارغة حالياً.</p>
                <a href="products.php" style="background-color: #1e293b; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-weight: bold;">تصفح المنتجات الآن</a>
            </div>
        `;
        return;
    }

    // بناء الهيكل الأساسي للجدول متوافقاً مع لقطة الشاشة الخاصة بكِ
    let html = `
        <table style="width: 100%; border-collapse: collapse; text-align: right; margin-bottom: 30px;">
            <thead>
                <tr style="border-bottom: 2px solid #cbd5e1; color: #64748b; font-size: 15px;">
                    <th style="padding: 12px; text-align: center;">المنتج</th>
                    <th style="padding: 12px;">الاسم</th>
                    <th style="padding: 12px;">السعر</th>
                    <th style="padding: 12px; text-align: center;">الكمية</th>
                    <th style="padding: 12px;">الإجمالي</th>
                    <th style="padding: 12px; text-align: center;">إجراء</th>
                </tr>
            </thead>
            <tbody>
    `;

    let totalAmount = 0;

    // المرور على عناصر السلة وعرضها داخل الأسطر
    cart.forEach((item, index) => {
        let subtotal = item.price * item.quantity;
        totalAmount += subtotal;

        // وضعنا صورة افتراضية أو يمكنكِ ربطها بروابط صوركِ المخصصة
        let placeholderImage = 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=80&q=80';
        if(item.id == 2) placeholderImage = 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=80&q=80';
        if(item.id == 3) placeholderImage = 'https://images.unsplash.com/photo-1611532736597-de2d4265fba3?w=80&q=80';
        if(item.id == 4) placeholderImage = 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=80&q=80';

        html += `
            <tr style="border-bottom: 1px solid #e2e8f0; color: #1e293b; font-size: 15px;">
                <td style="padding: 15px; text-align: center;">
                    <img src="${placeholderImage}" style="width: 50px; hieght: 50px; border-radius: 6px; object-fit: cover;">
                </td>
                <td style="padding: 15px; font-weight: bold;">${item.name}</td>
                <td style="padding: 15px; color: #64748b;">₪ ${item.price}</td>
                <td style="padding: 15px; text-align: center;">
                    <input type="number" value="${item.quantity}" min="1" onchange="updateQuantity(${item.id}, this.value)" style="width: 60px; padding: 6px; border: 1px solid #cbd5e1; border-radius: 6px; text-align: center; font-weight: bold;">
                </td>
                <td style="padding: 15px; font-weight: bold; color: #1e293b;">₪ ${subtotal}</td>
                <td style="padding: 15px; text-align: center;">
                    <button onclick="removeFromCart(${item.id})" style="background-color: #ef4444; color: white; border: none; padding: 6px 14px; border-radius: 5px; cursor: pointer; font-size: 13px; font-weight: bold;">حذف</button>
                </td>
            </tr>
        `;
    });

    // إغلاق جدول المنتجات وإضافة السطر السفلي الخاص بالمجموع الكلي وزر إتمام الشراء
    html += `
            </tbody>
        </table>
        
        <div style="direction:ltr;display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-top: 20px;">
            <div style="font-size: 20px; font-weight: bold; color: #1e293b;">
                المجموع الكلي: <span style="color: #1e293b; font-size: 24px;">₪ ${totalAmount}</span>
            </div>
            <button onclick="checkoutSimulation()" style="background-color: #10b981; color: white; border: none; padding: 12px 30px; border-radius: 6px; font-weight: bold; font-size: 16px; cursor: pointer; transition: 0.3s;">
                إتمام الشراء
            </button>
        </div>
    `;

    container.innerHTML = html;
}

// دالة لتعديل الكمية مباشرة من الجدول
function updateQuantity(id, newQty) {
    let quantity = parseInt(newQty) || 1;
    if (quantity <= 0) return;

    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    let item = cart.find(item => item.id === id);
    
    if (item) {
        item.quantity = quantity;
        localStorage.setItem('cart', JSON.stringify(cart));
        displayCart();      // إعادة رسم الجدول بالقيم الجديدة
        updateCartCount();  // تحديث عداد الهيدر العلوي
    }
}

// دالة لحذف منتج تماماً من السلة
function removeFromCart(id) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart = cart.filter(item => item.id !== id);
    
    localStorage.setItem('cart', JSON.stringify(cart));
    displayCart();
    updateCartCount();
}

// محاكاة زر إتمام الشراء لتفريغ السلة عند تأكيد الطلب
function checkoutSimulation() {
    alert('شكراً لك! تم استلام طلبكِ بنجاح (محاكاة لإتمام العملية).');
    localStorage.removeItem('cart');
    displayCart();
    updateCartCount();
}

// تشغيل عرض السلة فور تحميل الصفحة
document.addEventListener('DOMContentLoaded', displayCart);
</script>

<?php
// استدعاء الفوتر الموحد
include '../includes/footer.php';
?>