<footer style="background-color: #0f172a; color: #94a3b8; text-align: center; padding: 20px; margin-top: 40px; border-top: 1px solid #334155;">
    <p>© <?php echo date("Y"); ?> TechZone </p> </footer>
</body>
</html>
<script>
// دالة لتحديث عداد السلة من الـ LocalStorage لتشتغل فورياً على كل الصفحات
function updateCartCount() {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    let totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    const badge = document.getElementById('cart-count');
    if (badge) badge.innerText = totalItems;
}

// تشغيل الدالة فور تحميل الصفحة
updateCartCount();
</script>
</body>
</html>