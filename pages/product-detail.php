<?php
// pages/product-detail.php - تفاصيل المنتج
require_once __DIR__ . '/../includes/db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: products.php');
    exit;
}

// جلب المنتج
$stmt = $conn->prepare(
    "SELECT p.*, c.name AS category_name
     FROM products p
     LEFT JOIN categories c ON p.category_id = c.category_id
     WHERE p.product_id = ?"
);
$stmt->bind_param('i', $id);
$stmt->execute();
$p = $stmt->get_result()->fetch_assoc();

if (!$p) {
    header('Location: products.php');
    exit;
}

// منتجات مشابهة
$relStmt = $conn->prepare(
    "SELECT * FROM products
     WHERE category_id = ? AND product_id != ?
     LIMIT 3"
);
$relStmt->bind_param('ii', $p['category_id'], $id);
$relStmt->execute();
$related = $relStmt->get_result();

$pageTitle = htmlspecialchars($p['name']) . ' — EliteShop';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<main class="main-container" style="max-width: 1200px; margin: 0 auto; padding: 40px 20px; font-family: system-ui, -apple-system, sans-serif; direction: rtl; min-height: 80vh;">
    
    <div class="product-detail" style="display: flex; flex-wrap: wrap; gap: 40px; background: #ffffff; padding: 30px; border-radius: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; align-items: center; margin-bottom: 50px;">

        <div class="detail-image" style="flex: 1; min-width: 300px; max-width: 500px; height: 450px; border-radius: 16px; overflow: hidden; background-color: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center;">
            <img src="/php-ecommerce-project/<?= htmlspecialchars($p['image_url'] ?? 'assets/images/products/placeholder.jpg') ?>"
                 alt="<?= htmlspecialchars($p['name']) ?>"
                 onerror="this.src='/php-ecommerce-project/assets/images/products/placeholder.jpg'"
                 style="width: 100%; height: 100%; object-fit: cover;">
        </div>

        <div class="detail-info" style="flex: 1.2; min-width: 300px; display: flex; flex-direction: column; align-items: flex-start; text-align: right;">
            
            <span class="category-tag" style="background-color: #f1f5f9; color: #475569; padding: 5px 14px; border-radius: 20px; font-size: 13px; font-weight: 500; margin-bottom: 15px;">
                <?= htmlspecialchars($p['category_name'] ?? 'عام') ?>
            </span>
            
            <h1 style="font-size: 28px; color: #0f172a; margin: 0 0 15px 0; font-weight: bold; line-height: 1.3;"><?= htmlspecialchars($p['name']) ?></h1>
            
            <p class="detail-price" style="color: #f59e0b; font-size: 26px; font-weight: bold; margin: 0 0 20px 0;">
                <span style="font-size: 18px; margin-left: 3px;">₪</span><?= number_format($p['price'], 0) ?>
            </p>
            
            <hr style="width: 100%; border: 0; border-top: 1px solid #e2e8f0; margin-bottom: 20px;">
            
            <p class="detail-desc" style="font-size: 15px; color: #475569; line-height: 1.7; margin: 0 0 25px 0; white-space: pre-line; text-align: justify;">
                <?= nl2br(htmlspecialchars($p['description'])) ?>
            </p>
            
            <div style="margin-bottom: 30px;">
                <?php if ($p['stock_quantity'] > 0): ?>
                    <span style="background-color: #ecfdf5; color: #059669; padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: bold; display: inline-block;">
                        ✅ متوفر في المخزن (<?= $p['stock_quantity'] ?> قطعة متبقية)
                    </span>
                <?php else: ?>
                    <span style="background-color: #fef2f2; color: #dc2626; padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: bold; display: inline-block;">
                        ❌ نفذت الكمية من المخزن حالياً
                    </span>
                <?php endif; ?>
            </div>

            <?php if ($p['stock_quantity'] > 0): ?>
            <form action="/php-ecommerce-project/api/add-to-cart.php" method="POST" class="add-form" style="display: flex; align-items: center; gap: 15px; width: 100%; max-width: 400px; background-color: #f8fafc; padding: 15px; border-radius: 16px; border: 1px solid #e2e8f0;">
                <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                <input type="hidden" name="redirect" value="/php-ecommerce-project/pages/product-detail.php?id=<?= $p['product_id'] ?>">

                <div style="display: flex; align-items: center; gap: 8px;">
                    <label for="qty" style="font-size: 14px; color: #475569; font-weight: bold; white-space: nowrap;">الكمية:</label>
                    <input type="number" id="qty" name="quantity" value="1" min="1" max="<?= $p['stock_quantity'] ?>" class="qty-input" style="width: 60px; padding: 8px; border: 1px solid #cbd5e1; border-radius: 10px; text-align: center; font-size: 14px; font-weight: bold; outline: none;">
                </div>

                <button type="submit" class="btn-hero" style="flex: 1; background-color: #0f172a; color: white; border: none; padding: 12px 20px; border-radius: 25px; font-weight: bold; cursor: pointer; font-size: 14px; display: flex; justify-content: center; align-items: center; gap: 8px; transition: background-color 0.2s;">
                    🛒 أضف إلى السلة
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($related->num_rows > 0): ?>
    <section class="related-section" style="margin-top: 60px;">
        <h2 style="font-size: 22px; color: #0f172a; margin-bottom: 30px; font-weight: bold; text-align: center; position: relative;">✨ منتجات مشابهة قد تعجبك</h2>
        
        <div class="products-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 30px; justify-content: center;">
            <?php while ($r = $related->fetch_assoc()): ?>
            <div class="product-card" style="background: #ffffff; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.04); overflow: hidden; display: flex; flex-direction: column; text-align: center; border: 1px solid #f1f5f9; transition: transform 0.3s;">
                
                <div style="width: 100%; height: 180px; overflow: hidden; background-color: #f8fafc;">
                    <img src="/php-ecommerce-project/<?= htmlspecialchars($r['image_url'] ?? 'assets/images/products/placeholder.jpg') ?>"
                         alt="<?= htmlspecialchars($r['name']) ?>"
                         onerror="this.src='/php-ecommerce-project/assets/images/products/placeholder.jpg'"
                         style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                
                <div class="card-body" style="padding: 15px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; align-items: center; gap: 10px;">
                    <h3 style="font-size: 16px; color: #0f172a; margin: 0; font-weight: bold; line-height: 1.4; height: 44px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;"><?= htmlspecialchars($r['name']) ?></h3>
                    
                    <p class="price" style="color: #f59e0b; font-size: 16px; font-weight: bold; margin: 0;">
                        <span>₪</span><?= number_format($r['price'], 0) ?>
                    </p>
                    
                    <a href="product-detail.php?id=<?= $r['product_id'] ?>" class="btn-outline" style="width: 80%; text-align: center; background-color: #0f172a; color: white; text-decoration: none; padding: 8px 15px; border-radius: 20px; font-size: 13px; font-weight: bold; transition: background-color 0.2s;">عرض التفاصيل</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </section>
    <?php endif; ?>
</main>

<?php require_once '../includes/footer.php'; ?>