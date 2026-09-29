<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

$featured = db_getProducts(true, true);
$pageTitle = 'NARIA | Tobacco & Accessories';
include __DIR__ . '/includes/header.php';
?>

<header class="hero" id="home">
  <div class="hero-bg"><img src="./assets/images/naria-1.png" alt="NARIA Tobacco & Accessories"></div>
  <div class="hero-content">
    <div class="eyebrow">TOBACCO · HOOKAH · ACCESSORIES</div>
    <h1>NARIA</h1>
    <p>ویترین لوکس برند ناریـا؛ هویت مشکی و طلایی، تجربه خرید مجلل برای محصولات دخانی و اکسسوری.</p>
    <a class="btn" href="shop.php">مشاهده فروشگاه ↓</a>
    <a class="btn ghost" href="#about">درباره ما</a>
  </div>
</header>

<section id="categories">
  <div class="container">
    <div class="section-title">
      <small>NARIA COLLECTION</small>
      <h2>دسته‌بندی محصولات</h2>
      <p>انتخاب کنید و وارد دنیای ناریـا شوید.</p>
    </div>
    <div class="grid">
      <a href="shop.php?cat=<?= urlencode('تنباکو') ?>" class="card">
        <div class="icon">◈</div><h3>تنباکو</h3><p>انواع محصولات تنباکو و ترکیبات منتخب</p>
      </a>
      <a href="shop.php?cat=<?= urlencode('سیگار') ?>" class="card">
        <div class="icon">◇</div><h3>سیگار</h3><p>کالکشن و بسته‌بندی‌های برند</p>
      </a>
      <a href="shop.php?cat=<?= urlencode('هوکا') ?>" class="card">
        <div class="icon">♧</div><h3>هوکا</h3><p>تجهیزات و اکسسوری‌های قلیان</p>
      </a>
      <a href="shop.php?cat=<?= urlencode('ویپ و پاد') ?>" class="card">
        <div class="icon">▣</div><h3>ویپ و پاد</h3><p>لوازم جانبی و محصولات مرتبط</p>
      </a>
    </div>
  </div>
</section>

<section id="about">
  <div class="container feature">
    <div class="visual">
<!--        <img src="./assets/images/naria-2.png" alt="NARIA Tobacco & Accessories">-->
      <div style="font-size:80px;color:var(--gold2);opacity:.5">NARIA</div>
    </div>
    <div class="copy">
      <div class="eyebrow">THE NARIA EXPERIENCE</div>
      <h2>لوکس، تاریک، متمایز.</h2>
      <div class="goldline"></div>
      <p>ناریـا با تمرکز روی کیفیت، بسته‌بندی لوکس و تجربه خرید خاص، مجموعه‌ای از بهترین محصولات تنباکو، هوکا و اکسسوری را ارائه می‌دهد.</p>
      <p>فروشگاه آنلاین آماده دریافت سفارش شماست. محصولات را انتخاب کنید و سفارش خود را ثبت نمایید.</p>
      <a class="btn" href="shop.php">ورود به فروشگاه</a>
    </div>
  </div>
</section>

<?php if (count($featured) > 0): ?>
<section>
  <div class="container">
    <div class="section-title">
      <small>FEATURED</small>
      <h2>ویترین ناریـا</h2>
      <p>محصولات منتخب و ویژه</p>
    </div>
    <div class="products">
      <?php foreach ($featured as $p): ?>
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
          <p><?= htmlspecialchars(function_exists('mb_substr') ? mb_substr($p['description'] ?? '', 0, 60) : substr($p['description'] ?? '', 0, 60)) ?>...</p>
          <div class="price"><?= formatPrice($p['price']) ?></div>
          <div class="product-actions">
            <button class="btn sm" onclick="addToCart(<?= (int)$p['id'] ?>, '<?= htmlspecialchars(addslashes($p['name'])) ?>', <?= (float)$p['price'] ?>)">افزودن به سبد</button>
            <a class="btn sm ghost" href="product.php?id=<?= (int)$p['id'] ?>">جزئیات</a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section id="contact">
  <div class="container">
    <div class="banner">
      <div class="inner">
        <div class="eyebrow">NARIA STORE</div>
        <h2>به دنیای ناریـا خوش آمدید.</h2>
        <p>برای ثبت سفارش از فروشگاه استفاده کنید. در صورت نیاز به هماهنگی، از طریق اینستاگرام با ما در ارتباط باشید.</p>
        <a class="btn" href="shop.php">خرید از فروشگاه</a>
        <a class="btn ghost" href="https://instagram.com/" target="_blank" rel="noopener">Instagram ↗</a>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
