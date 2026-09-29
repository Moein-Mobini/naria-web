<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

$cat = isset($_GET['cat']) ? trim($_GET['cat']) : '';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

$products = db_getProducts(true, false, $cat, $q);
$categories = db_getCategories();

$pageTitle = 'فروشگاه | NARIA';
include __DIR__ . '/includes/header.php';

function shortText($t, $len = 70) {
    if (function_exists('mb_substr')) {
        $s = mb_substr($t, 0, $len);
        return mb_strlen($t) > $len ? $s . '...' : $s;
    }
    $s = substr($t, 0, $len);
    return strlen($t) > $len ? $s . '...' : $s;
}
?>

<div class="page-header">
  <div class="container">
    <div class="eyebrow">NARIA SHOP</div>
    <h1>فروشگاه ناریـا</h1>
    <p style="color:var(--muted)">تمام محصولات در یک نگاه</p>
  </div>
</div>

<section style="padding-top:20px">
  <div class="container">
    <div class="filters">
      <a href="shop.php" class="filter-btn <?= $cat === '' ? 'active' : '' ?>">همه</a>
      <?php foreach ($categories as $c): ?>
        <a href="shop.php?cat=<?= urlencode($c) ?>" class="filter-btn <?= $cat === $c ? 'active' : '' ?>"><?= htmlspecialchars($c) ?></a>
      <?php endforeach; ?>
    </div>

    <form method="get" action="shop.php" style="max-width:400px;margin:0 auto 30px;display:flex;gap:8px">
      <?php if ($cat): ?><input type="hidden" name="cat" value="<?= htmlspecialchars($cat) ?>"><?php endif; ?>
      <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="جستجوی محصول..." style="flex:1;padding:12px 16px;background:#121212;border:1px solid var(--line);border-radius:12px;color:var(--text);font-family:inherit">
      <button type="submit" class="btn sm">جستجو</button>
    </form>

    <?php if (count($products) === 0): ?>
      <div class="empty-state">
        <div class="icon">◈</div>
        <p>محصولی یافت نشد.</p>
        <a href="shop.php" class="btn sm">بازگشت به فروشگاه</a>
      </div>
    <?php else: ?>
      <div class="products">
        <?php foreach ($products as $p): ?>
        <article class="product">
          <div class="product-img">
            <?php if (!empty($p['image']) && file_exists(__DIR__ . '/assets/uploads/' . $p['image'])): ?>
              <img src="assets/uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
            <?php else: ?>
              ◈
            <?php endif; ?>
          </div>
          <div class="product-body">
            <span class="tag"><?= htmlspecialchars($p['category']) ?></span>
            <h3><?= htmlspecialchars($p['name']) ?></h3>
            <p><?= htmlspecialchars(shortText($p['description'] ?? '')) ?></p>
            <div class="price"><?= formatPrice($p['price']) ?></div>
            <?php if ((int)($p['stock'] ?? 0) <= 0): ?>
              <span style="color:var(--danger);font-size:13px">ناموجود</span>
            <?php else: ?>
              <div class="product-actions">
                <button class="btn sm" onclick="addToCart(<?= (int)$p['id'] ?>, '<?= htmlspecialchars(addslashes($p['name'])) ?>', <?= (float)$p['price'] ?>)">افزودن به سبد</button>
                <a class="btn sm ghost" href="product.php?id=<?= (int)$p['id'] ?>">جزئیات</a>
              </div>
            <?php endif; ?>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
