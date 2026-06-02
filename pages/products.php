<?php
$pageTitle = 'المنتجات — EliteShop';
require_once '../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_once '../includes/navbar.php';
// ── فلترة وبحث ──────────────────────────────────────────────
$search   = trim($_GET['search']   ?? '');
$catId    = (int)($_GET['category'] ?? 0);
$sort     = $_GET['sort'] ?? 'newest';

$where    = ['1=1'];
$params   = [];
$types    = '';

if ($search !== '') {
    $where[]  = '(p.name LIKE ? OR p.description LIKE ?)';
    $like     = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $types   .= 'ss';
}

if ($catId > 0) {
    $where[]  = 'p.category_id = ?';
    $params[] = $catId;
    $types   .= 'i';
}

$orderBy = match ($sort) {
    'price_asc'  => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    default      => 'p.created_at DESC',
};

$sql  = "SELECT p.*, c.name AS category_name
         FROM products p
         LEFT JOIN categories c ON p.category_id = c.category_id
         WHERE " . implode(' AND ', $where) . "
         ORDER BY $orderBy";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result();

// جلب الفئات للـ filter
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
?>

<main class="main-container" style="max-width: 1200px; margin: 0 auto; padding: 40px 20px; font-family: system-ui, -apple-system, sans-serif; direction: rtl; min-height: 80vh;">

    <h1 style="font-size: 28px; color: #0f172a; margin-bottom: 30px; font-weight: bold; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; text-align: right;">📦 جميع المنتجات</h1>

    <form method="GET" class="filters-bar" style="background-color: #ffffff; padding: 20px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03); display: flex; flex-wrap: wrap; gap: 15px; align-items: center; margin-bottom: 40px; border: 1px solid #f1f5f9;">

        <input type="text" name="search"
            value="<?= htmlspecialchars($search) ?>"
            placeholder="ابحث عن منتج..."
            class="filter-input"
            style="flex: 1; min-width: 200px; padding: 12px 20px; border: 1px solid #cbd5e1; border-radius: 25px; font-size: 14px; outline: none; transition: border-color 0.3s; text-align: right;">

        <select name="category" class="filter-select" style="min-width: 160px; padding: 12px 20px; border: 1px solid #cbd5e1; border-radius: 25px; font-size: 14px; outline: none; background-color: #fff; cursor: pointer; text-align: right;">
            <option value="0">كل الفئات</option>
            <?php while ($cat = $categories->fetch_assoc()): ?>
                <option value="<?= $cat['category_id'] ?>"
                    <?= $catId == $cat['category_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <select name="sort" class="filter-select" style="min-width: 160px; padding: 12px 20px; border: 1px solid #cbd5e1; border-radius: 25px; font-size: 14px; outline: none; background-color: #fff; cursor: pointer; text-align: right;">
            <option value="newest" <?= $sort === 'newest'     ? 'selected' : '' ?>>الأحدث</option>
            <option value="price_asc" <?= $sort === 'price_asc'  ? 'selected' : '' ?>>السعر: من الأقل</option>
            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>السعر: من الأعلى</option>
        </select>

        <button type="submit" class="btn-primary" style="background-color: #0f172a; color: white; border: none; padding: 12px 30px; border-radius: 25px; font-weight: bold; cursor: pointer; font-size: 14px; transition: background-color 0.2s;">تطبيق الفلترة</button>
    </form>

    <div class="products-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; justify-content: center;">
        <?php if ($products->num_rows === 0): ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: #64748b; background: #ffffff; border-radius: 16px; border: 1px dashed #cbd5e1;">
                <p style="font-size: 18px; margin: 0;">لا توجد منتجات تطابق خيارات البحث الحالية.</p>
            </div>
        <?php else: ?>
            <?php while ($p = $products->fetch_assoc()): ?>
                <div class="product-card" style="background: #ffffff; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.04); overflow: hidden; display: flex; flex-direction: column; text-align: center; transition: transform 0.3s, box-shadow 0.3s; border: 1px solid #f1f5f9;">

                    <div style="width: 100%; height: 220px; overflow: hidden; background-color: #f8fafc; position: relative;">
                        <img src="/php-ecommerce-project/<?= htmlspecialchars($p['image_url'] ?? 'assets/images/products/placeholder.jpg') ?>"
                            alt="<?= htmlspecialchars($p['name']) ?>"
                            onerror="this.src='/php-ecommerce-project/assets/images/products/placeholder.jpg'"
                            style="width: 100%; height: 100%; object-fit: cover;">
                    </div>

                    <div class="card-body" style="padding: 20px 20px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; align-items: center; gap: 10px;">

                        <div style="width: 100%;">
                            <span class="category-tag" style="display: inline-block; background-color: #f1f5f9; color: #475569; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; margin-bottom: 8px;"><?= htmlspecialchars($p['category_name'] ?? 'عام') ?></span>

                            <h3 style="font-size: 18px; color: #0f172a; margin: 0 0 8px 0; font-weight: bold; line-height: 1.4; height: 50px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;"><?= htmlspecialchars($p['name']) ?></h3>

                            <p class="description" style="font-size: 13px; color: #64748b; margin: 0 0 12px 0; line-height: 1.5; padding: 0 5px; height: 38px; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars(mb_substr($p['description'], 0, 50)) ?>...</p>
                        </div>

                        <div style="width: 100%;">
                            <p class="price" style="color: #f59e0b; font-size: 20px; font-weight: bold; margin: 0 0 8px 0;">
                                <span style="font-size: 14px; margin-left: 2px;">₪</span><?= number_format($p['price'], 0) ?>
                            </p>

                            <div style="margin-bottom: 15px;">
                                <?php if ($p['stock_quantity'] > 0): ?>
                                    <span style="display: inline-block; background-color: #ecfdf5; color: #059669; padding: 3px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">✅ متوفر في المخزن</span>
                                <?php else: ?>
                                    <span style="display: inline-block; background-color: #fef2f2; color: #dc2626; padding: 3px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">❌ غير متوفر حالياً</span>
                                <?php endif; ?>
                            </div>

                            <div class="card-actions" style="width: 100%; display: flex; flex-direction: column; gap: 8px; align-items: center;">
                                <?php if ($p['stock_quantity'] > 0): ?>
                                    <form action="/php-ecommerce-project/api/add-to-cart.php" method="POST" style="width: 100%;">
                                        <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <input type="hidden" name="redirect" value="/php-ecommerce-project/pages/products.php">
                                        <button type="submit" class="btn-primary" style="width: 100%; background-color: #0f172a; color: #ffffff; border: none; padding: 10px 20px; border-radius: 25px; font-weight: bold; cursor: pointer; font-size: 14px; transition: background-color 0.2s;">أضف للسلة 🛒</button>
                                    </form>
                                <?php else: ?>
                                    <button type="button" disabled style="width: 100%; background-color: #cbd5e1; color: #94a3b8; border: none; padding: 10px 20px; border-radius: 25px; font-weight: bold; cursor: not-allowed; font-size: 14px;">نفذت الكمية</button>
                                <?php endif; ?>

                                <a href="/php-ecommerce-project/pages/product-detail.php?id=<?= $p['product_id'] ?>"
                                    class="btn-outline"
                                    style="color: #64748b; text-decoration: none; font-size: 13px; font-weight: 500; transition: color 0.2s; padding: 4px 0; display: inline-block;"
                                    onmouseover="this.style.color='#0f172a'"
                                    onmouseout="this.style.color='#64748b'">عرض التفاصيل الكاملة</a>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>