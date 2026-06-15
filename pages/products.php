<?php
$pageTitle = 'المنتجات — EliteShop';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

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

<style>
    .dynamic-input:focus, .dynamic-select:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important;
    }
    .custom-product-card {
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.01);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #f1f5f9;
    }
    .custom-product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 30px rgba(15, 23, 42, 0.06);
        border-color: #e2e8f0;
    }
    .img-zoom-container {
        width: 100%;
        height: 240px;
        overflow: hidden;
        background-color: #f8fafc;
        position: relative;
    }
    .img-zoom-container img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 15px;
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .custom-product-card:hover .img-zoom-container img {
        transform: scale(1.05);
    }
    .action-btn-main {
        width: 100%;
        background-color: #0f172a;
        color: #ffffff;
        border: none;
        padding: 12px 20px;
        border-radius: 14px;
        font-weight: 600;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .action-btn-main:hover {
        background-color: #3b82f6;
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.2);
    }
    .filter-submit-btn {
        background-color: #0f172a;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 14px;
        font-weight: 600;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    .filter-submit-btn:hover {
        background-color: #1e293b;
        transform: translateY(-1px);
    }
</style>

<main class="main-container" style="max-width: 1200px; margin: 0 auto; padding: 50px 20px; font-family: system-ui, -apple-system, sans-serif; direction: rtl; min-height: 80vh;">

    <div style="margin-bottom: 35px; text-align: right;">
        <h1 style="font-size: 32px; color: #0f172a; font-weight: 800; margin-bottom: 8px;">📦 جميع المنتجات</h1>
        <div style="width: 50px; height: 4px; background-color: #3b82f6; border-radius: 2px;"></div>
    </div>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'added'): ?>
        <div style="margin-bottom: 25px;">
            <div style="background-color: #ecfdf5; color: #059669; padding: 14px 20px; border-radius: 12px; font-size: 14px; font-weight: 500; border: 1px solid #a7f3d0; text-align: right;">
                ✅ تم إضافة المنتج بنجاح إلى السلة!
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'out_of_stock'): ?>
        <div style="margin-bottom: 25px;">
            <div style="background-color: #fef2f2; color: #dc2626; padding: 14px 20px; border-radius: 12px; font-size: 14px; font-weight: 500; border: 1px solid #fecaca; text-align: right;">
                ⚠️ عذراً، الكمية المطلوبة غير متوفرة في المخزن حالياً.
            </div>
        </div>
    <?php endif; ?>

    <form method="GET" class="filters-bar" style="background-color: #ffffff; padding: 24px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.02); display: flex; flex-wrap: wrap; gap: 16px; align-items: center; margin-bottom: 45px; border: 1px solid #e2e8f0;">

        <input type="text" name="search"
            value="<?= htmlspecialchars($search) ?>"
            placeholder="ابحث عن منتج بالاسم أو الوصف..."
            class="dynamic-input"
            style="flex: 2; min-width: 240px; padding: 12px 20px; border: 1px solid #cbd5e1; border-radius: 14px; font-size: 14px; outline: none; transition: all 0.3s; text-align: right;">

        <select name="category" class="dynamic-select" style="flex: 1; min-width: 160px; padding: 12px 20px; border: 1px solid #cbd5e1; border-radius: 14px; font-size: 14px; outline: none; background-color: #fff; cursor: pointer; text-align: right; transition: all 0.3s;">
            <option value="0">كل الفئات</option>
            <?php while ($cat = $categories->fetch_assoc()): ?>
                <option value="<?= $cat['category_id'] ?>"
                    <?= $catId == $cat['category_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <select name="sort" class="dynamic-select" style="flex: 1; min-width: 160px; padding: 12px 20px; border: 1px solid #cbd5e1; border-radius: 14px; font-size: 14px; outline: none; background-color: #fff; cursor: pointer; text-align: right; transition: all 0.3s;">
            <option value="newest" <?= $sort === 'newest'     ? 'selected' : '' ?>>📅 الأحدث</option>
            <option value="price_asc" <?= $sort === 'price_asc'  ? 'selected' : '' ?>>📈 السعر: من الأقل</option>
            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>📉 السعر: من الأعلى</option>
        </select>

        <button type="submit" class="filter-submit-btn">تطبيق الفلترة</button>
    </form>

    <div class="products-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; justify-content: center;">
        <?php if ($products->num_rows === 0): ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 80px 20px; color: #64748b; background: #ffffff; border-radius: 24px; border: 2px dashed #cbd5e1;">
                <p style="font-size: 18px; margin: 0; font-weight: 500;">عذراً، لا توجد منتجات تطابق خيارات البحث الحالية.</p>
            </div>
        <?php else: ?>
            <?php while ($p = $products->fetch_assoc()): ?>
                <div class="custom-product-card">

                    <div class="img-zoom-container">
                        <img src="/php-ecommerce-project/<?= htmlspecialchars($p['image_url'] ?? 'assets/images/products/placeholder.jpg') ?>"
                            alt="<?= htmlspecialchars($p['name']) ?>"
                            onerror="this.src='/php-ecommerce-project/assets/images/products/placeholder.jpg'">
                    </div>

                    <div class="card-body" style="padding: 24px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; gap: 15px;">

                        <div style="width: 100%; text-align: right;">
                            <span class="category-tag" style="display: inline-block; background-color: #f1f5f9; color: #64748b; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; margin-bottom: 10px; border: 1px solid #e2e8f0;"><?= htmlspecialchars($p['category_name'] ?? 'عام') ?></span>

                            <h3 style="font-size: 18px; color: #0f172a; margin: 0 0 10px 0; font-weight: 700; line-height: 1.5; height: 54px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;"><?= htmlspecialchars($p['name']) ?></h3>

                            <p class="description" style="font-size: 13px; color: #64748b; margin: 0; line-height: 1.6; height: 42px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;"><?= htmlspecialchars(mb_substr($p['description'], 0, 65)) ?>...</p>
                        </div>

                        <div style="width: 100%;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 10px;">
                                <p class="price" style="color: #f59e0b; font-size: 22px; font-weight: 800; margin: 0; direction: rtl;">
                                    <?= number_format($p['price'], 0) ?> <span style="font-size: 14px; font-weight: 600; color: #64748b; margin-right: 2px;">₪</span>
                                </p>

                                <div>
                                    <?php if ($p['stock_quantity'] > 0): ?>
                                        <span style="display: inline-block; background-color: #f0fdf4; color: #16a34a; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 700; border: 1px solid #bbf7d0;">✓ متوفر</span>
                                    <?php else: ?>
                                        <span style="display: inline-block; background-color: #fef2f2; color: #dc2626; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 700; border: 1px solid #fecaca;">✕ غير متوفر</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="card-actions" style="width: 100%; display: flex; flex-direction: column; gap: 10px; align-items: center;">
                                <?php if ($p['stock_quantity'] > 0): ?>
                                    <form action="/php-ecommerce-project/api/add-to-cart.php" method="POST" style="width: 100%;">
                                        <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <input type="hidden" name="redirect" value="/php-ecommerce-project/pages/products.php">
                                        <button type="submit" class="action-btn-main">
                                            <span>أضف للسلة</span>
                                            <span>🛒</span>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button type="button" disabled style="width: 100%; background-color: #f1f5f9; color: #94a3b8; border: 1px dashed #cbd5e1; padding: 12px 20px; border-radius: 14px; font-weight: bold; cursor: not-allowed; font-size: 14px;">نفذت الكمية 🚫</button>
                                <?php endif; ?>

                                <a href="/php-ecommerce-project/pages/product-detail.php?id=<?= $p['product_id'] ?>"
                                    class="btn-outline"
                                    style="color: #64748b; text-decoration: none; font-size: 13px; font-weight: 600; transition: color 0.2s; padding: 6px 0; display: inline-block; width: 100%; text-align: center;"
                                    onmouseover="this.style.color='#3b82f6'"
                                    onmouseout="this.style.color='#64748b'">عرض التفاصيل الكاملة ←</a>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>