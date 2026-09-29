<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

$id = (int)($_GET['id'] ?? 0);
$p = db_getProduct($id);

if (!$p || empty($p['active'])) {
    header('Location: shop.php');
    exit;
}

$pageTitle = htmlspecialchars($p['name']) . ' | NARIA';
include __DIR__ . '/includes/header.php';
?>

<div class="page-header" style="padding-bottom:20px">
  <div class="container">
    <a href="shop.php" style="color:var(--gold2);font-size:14px">← بازگشت به فروشگاه</a>
  </div>
</div>

<section style="padding-top:0">
  <div class="container">
    <div class="feature" style="align-items:start">
      <div class="visual" style="min-height:420px">
        <?php if (!empty($p['image']) && file_exists(__DIR__ . '/assets/uploads/' . $p['image'])): ?>
          <img src="assets/uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
        <?php else: ?>
          <div style="font-size:100px;color:var(--gold2);opacity:.4">◈</div>
        <?php endif; ?>
      </div>
      <div class="copy">
        <div class="eyebrow"><?= htmlspecialchars($p['category']) ?></div>
        <h2 style="font-size:36px"><?= htmlspecialchars($p['name']) ?></h2>
        <div class="goldline"></div>
        <div class="price" style="font-size:24px;margin-bottom:16px"><?= formatPrice($p['price']) ?></div>
        <p><?= nl2br(htmlspecialchars($p['description'] ?? '')) ?></p>
        <p style="color:var(--muted);font-size:14px">موجودی: <?= (int)$p['stock'] ?> عدد</p>
        <?php if ((int)$p['stock'] > 0): ?>
          <button class="btn" onclick="addToCart(<?= (int)$p['id'] ?>, '<?= htmlspecialchars(addslashes($p['name'])) ?>', <?= (float)$p['price'] ?>)">افزودن به سبد خرید</button>
        <?php else: ?>
          <span class="btn" style="opacity:.5;cursor:default">ناموجود</span>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
