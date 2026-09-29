<?php
if (!isset($pageTitle)) $pageTitle = 'NARIA | Tobacco & Accessories';
$cartCount = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) $cartCount += $item['qty'];
}
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <meta name="description" content="NARIA — Tobacco & Accessories | فروشگاه لوکس ناریـا">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <!-- Age Gate -->
  <div class="age" id="ageGate">
    <div class="age-box">
      <div class="eyebrow">NARIA · TOBACCO & ACCESSORIES</div>
      <h2>ورود به ناریـا</h2>
      <p>این وب‌سایت مربوط به محصولات دخانی و لوازم جانبی است. فقط در صورت داشتن سن قانونی محل زندگی خود وارد شوید.</p>
      <div class="age-actions">
        <button class="btn" onclick="enterSite()">من سن قانونی دارم</button>
        <button class="btn ghost" onclick="leaveSite()">خروج</button>
      </div>
    </div>
  </div>

  <nav class="nav">
    <a class="brand" href="index.php">NARIA</a>
    <div class="links">
      <a href="index.php">خانه</a>
      <a href="shop.php">فروشگاه</a>
      <a href="index.php#about">درباره ناریـا</a>
      <a href="support.php">پشتیبانی</a>
      <a href="index.php#contact">تماس</a>
      <button class="theme-toggle" id="themeToggle" type="button" aria-label="تغییر تم"><span class="theme-sun">☀</span><span class="theme-moon">☾</span></button>
      <a href="cart.php" class="cart-btn">🛒 سبد
        <?php if ($cartCount > 0): ?>
          <span class="cart-count" id="cartCount"><?= $cartCount ?></span>
        <?php else: ?>
          <span class="cart-count" id="cartCount" style="display:none">0</span>
        <?php endif; ?>
      </a>
    </div>
  </nav>
