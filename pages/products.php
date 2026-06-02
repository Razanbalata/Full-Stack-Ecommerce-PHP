<?php
// 1. استدعاء الهيدر الموحد
include '../includes/header.php';
include '../includes/db.php';

$query = "SELECT * FROM products";
$products = mysqli_query($conn, $query);
var_dump($products);
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

        <?php while ($product = mysqli_fetch_assoc($products)): ?>
            <div class="product-item" data-category="<?php echo $product['category_id']; ?>">
                <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>">

                <h3><?php echo $product['name']; ?></h3>

                <p><?php echo $product['description']; ?></p>

                <p><?php echo $product['price']; ?> ₪</p>

                <div>
                    <a href="product-detail.php?id=<?php echo $product['product_id']; ?>">التفاصيل</a>

                    <button onclick="addToCart(<?php echo $product['product_id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>)">
                        أضف للسلة
                    </button>
                </div>
            </div>
        <?php endwhile; ?>

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
// 3. استدعاء الفوتر الموحد
include '../includes/footer.php';
?>