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

<style>
    .premium-detail-container {
        display: flex; 
        flex-wrap: wrap; 
        gap: 50px; 
        background: #ffffff; 
        padding: 40px; 
        border-radius: 32px; 
        box-shadow: 0 15px 40px rgba(15, 23, 42, 0.03); 
        border: 1px solid #f1f5f9; 
        align-items: flex-start; 
        margin-bottom: 60px;
    }
    .premium-image-box {
        flex: 1; 
        min-width: 320px; 
        max-width: 550px; 
        height: 500px; 
        border-radius: 24px; 
        overflow: hidden; 
        background-color: #f8fafc; 
        border: 1px solid #f1f5f9; 
        display: flex; 
        align-items: center; 
        justify-content: center;
        position: relative;
    }
    .premium-image-box img {
        width: 100%; 
        height: 100%; 
        object-fit: contain;
        padding: 20px;
        transition: transform 0.5s ease;
    }
    .premium-image-box:hover img {
        transform: scale(1.04);
    }
    .qty-btn {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        width: 35px;
        height: 35px;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.2s;
    }
    .qty-btn:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }
    .btn-add-to-cart {
        flex: 1; 
        background-color: #0f172a; 
        color: white; 
        border: none; 
        padding: 16px 25px; 
        border-radius: 16px; 
        font-weight: 600; 
        cursor: pointer; 
        font-size: 15px; 
        display: flex; 
        justify-content: center; 
        align-items: center; 
        gap: 8px; 
        transition: all 0.3s ease;
    }
    .btn-add-to-cart:hover {
        background-color: #3b82f6;
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.25);
        transform: translateY(-2px);
    }
    .related-card {
        background: #ffffff; 
        border-radius: 24px; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.01); 
        overflow: hidden; 
        display: flex; 
        flex-direction: column; 
        text-align: center; 
        border: 1px solid #f1f5f9; 
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .related-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 15px 30px rgba(15, 23, 42, 0.05);
        border-color: #e2e8f0;
    }
    .related-img-wrapper {
        width: 100%; 
        height: 200px; 
        overflow: hidden; 
        background-color: #f8fafc;
    }
    .related-img-wrapper img {
        width: 100%; 
        height: 100%; 
        object-fit: contain;
        padding: 15px;
        transition: transform 0.4s;
    }
    .related-card:hover .related-img-wrapper img {
        transform: scale(1.05);
    }
</style>

<main class="main-container" style="max-width: 1200px; margin: 0 auto; padding: 40px 20px; font-family: system-ui, -apple-system, sans-serif; direction: rtl; min-height: 80vh;">
    
    <div class="premium-detail-container">

        <div class="premium-image-box">
            <img src="/php-ecommerce-project/<?= htmlspecialchars($p['image_url'] ?? 'assets/images/products/placeholder.jpg') ?>"
                 alt="<?= htmlspecialchars($p['name']) ?>"
                 onerror="this.src='/php-ecommerce-project/assets/images/products/placeholder.jpg'">
        </div>

        <div class="detail-info" style="flex: 1.2; min-width: 320px; display: flex; flex-direction: column; align-items: flex-start; text-align: right;">
            
            <span class="category-tag" style="background-color: #eff6ff; color: #1d4ed8; padding: 6px 16px; border-radius: 30px; font-size: 12px; font-weight: 700; margin-bottom: 15px; border: 1px solid #bfdbfe;">
                📂 <?= htmlspecialchars($p['category_name'] ?? 'عام') ?>
            </span>
            
            <h1 style="font-size: 32px; color: #0f172a; margin: 0 0 15px 0; font-weight: 800; line-height: 1.4; letter-spacing: -0.5px;"><?= htmlspecialchars($p['name']) ?></h1>
            
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                <p class="detail-price" style="color: #f59e0b; font-size: 30px; font-weight: 800; margin: 0; direction: rtl;">
                    <?= number_format($p['price'], 0) ?> <span style="font-size: 18px; font-weight: 600; color: #64748b; margin-right: 2px;">₪</span>
                </p>
            </div>
            
            <div style="width: 100%; height: 1px; background: linear-gradient(to left, #e2e8f0, transparent); margin-bottom: 25px;"></div>
            
            <h3 style="font-size: 16px; color: #0f172a; margin-bottom: 10px; font-weight: 700;">الوصف والمواصفات:</h3>
            <p class="detail-desc" style="font-size: 15px; color: #475569; line-height: 1.8; margin: 0 0 30px 0; white-space: pre-line; text-align: justify;">
                <?= nl2br(htmlspecialchars($p['description'])) ?>
            </p>
            
            <div style="margin-bottom: 30px;">
                <?php if ($p['stock_quantity'] > 0): ?>
                    <span style="background-color: #f0fdf4; color: #16a34a; padding: 6px 16px; border-radius: 30px; font-size: 13px; font-weight: 700; display: inline-block; border: 1px solid #bbf7d0;">
                        ✓ متوفر في المخزن (جُلب منه <?= $p['stock_quantity'] ?> قطع فقط)
                    </span>
                <?php else: ?>
                    <span style="background-color: #fef2f2; color: #dc2626; padding: 6px 16px; border-radius: 30px; font-size: 13px; font-weight: 700; display: inline-block; border: 1px solid #fecaca;">
                        ✕ غير متوفر في المخزن حالياً
                    </span>
                <?php endif; ?>
            </div>

            <?php if ($p['stock_quantity'] > 0): ?>
            <form action="/php-ecommerce-project/api/add-to-cart.php" method="POST" class="add-form" style="display: flex; align-items: center; gap: 20px; width: 100%; max-width: 460px; background-color: #f8fafc; padding: 18px; border-radius: 20px; border: 1px solid #e2e8f0;">
                <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                <input type="hidden" name="redirect" value="/php-ecommerce-project/pages/product-detail.php?id=<?= $p['product_id'] ?>">

                <div style="display: flex; align-items: center; gap: 10px;">
                    <label for="qty" style="font-size: 14px; color: #1e293b; font-weight: 700; white-space: nowrap;">الكمية:</label>
                    <div style="display: flex; align-items: center; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 10px; padding: 2px 6px;">
                        <input type="number" id="qty" name="quantity" value="1" min="1" max="<?= $p['stock_quantity'] ?>" class="qty-input" style="width: 50px; border: none; text-align: center; font-size: 15px; font-weight: 700; outline: none; background: transparent;">
                    </div>
                </div>

                <button type="submit" class="btn-add-to-cart">
                    <span>أضف إلى سلة المشتريات</span>
                    <span>🛒</span>
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($related->num_rows > 0): ?>
    <section class="related-section" style="margin-top: 80px;">
        <div style="text-align: center; margin-bottom: 40px;">
            <h2 style="font-size: 24px; color: #0f172a; font-weight: 800; margin-bottom: 8px;">✨ قطع مميزة نقترحها لك</h2>
            <div style="width: 60px; height: 4px; background-color: #f59e0b; margin: 0 auto; border-radius: 2px;"></div>
        </div>
        
        <div class="products-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; justify-content: center;">
            <?php while ($r = $related->fetch_assoc()): ?>
            <div class="related-card">
                
                <div class="related-img-wrapper">
                    <img src="/php-ecommerce-project/<?= htmlspecialchars($r['image_url'] ?? 'assets/images/products/placeholder.jpg') ?>"
                         alt="<?= htmlspecialchars($r['name']) ?>"
                         onerror="this.src='/php-ecommerce-project/assets/images/products/placeholder.jpg'">
                </div>
                
                <div class="card-body" style="padding: 24px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; align-items: center; gap: 15px;">
                    <h3 style="font-size: 16px; color: #1e293b; margin: 0; font-weight: 700; line-height: 1.5; height: 48px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; text-align: center;"><?= htmlspecialchars($r['name']) ?></h3>
                    
                    <p class="price" style="color: #f59e0b; font-size: 18px; font-weight: 800; margin: 0; direction: rtl;">
                        <?= number_format($r['price'], 0) ?> <span style="font-size: 13px; font-weight: 600; color: #64748b; margin-right: 2px;">₪</span>
                    </p>
                    
                    <a href="product-detail.php?id=<?= $r['product_id'] ?>" style="width: 100%; text-align: center; background-color: #f1f5f9; color: #1e293b; text-decoration: none; padding: 12px 20px; border-radius: 12px; font-size: 13px; font-weight: 700; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#0f172a'; this.style.color='#ffffff';" onmouseout="this.style.backgroundColor='#f1f5f9'; this.style.color='#1e293b';">معاينة تفاصيل المنتج</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </section>
    <?php endif; ?>
</main>

<?php require_once '../includes/footer.php'; ?>