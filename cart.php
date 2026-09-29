<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

$cart = $_SESSION['cart'] ?? [];
$total = 0;
foreach ($cart as $item) $total += $item['price'] * $item['qty'];

$pageTitle = 'سبد خرید | NARIA';
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <div class="eyebrow">CART</div>
    <h1>سبد خرید</h1>
  </div>
</div>

<section style="padding-top:20px">
  <div class="container">
    <?php if (empty($cart)): ?>
      <div class="empty-state">
        <div class="icon">🛒</div>
        <p>سبد خرید شما خالی است.</p>
        <a href="shop.php" class="btn">رفتن به فروشگاه</a>
      </div>
    <?php else: ?>
      <div class="table-wrap">
        <table class="cart-table">
          <thead>
            <tr>
              <th>محصول</th>
              <th>قیمت واحد</th>
              <th>تعداد</th>
              <th>جمع</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cart as $item): ?>
            <tr>
              <td><?= htmlspecialchars($item['name']) ?></td>
              <td><?= formatPrice($item['price']) ?></td>
              <td>
                <input type="number" class="qty-input" min="1" value="<?= $item['qty'] ?>"
                       onchange="updateCartQty(<?= $item['id'] ?>, this.value)">
              </td>
              <td><?= formatPrice($item['price'] * $item['qty']) ?></td>
              <td>
                <button class="btn sm danger" onclick="removeFromCart(<?= $item['id'] ?>)">حذف</button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="cart-summary" style="max-width:400px;margin-right:auto">
        <div class="row"><span>جمع کل</span><strong style="color:var(--gold2)"><?= formatPrice($total) ?></strong></div>
        <div style="margin-top:20px;display:flex;gap:10px;flex-wrap:wrap">
          <a href="checkout.php" class="btn">ادامه و ثبت سفارش</a>
          <a href="shop.php" class="btn ghost">ادامه خرید</a>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
