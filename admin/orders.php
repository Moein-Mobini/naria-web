<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/auth.php';
requireAdmin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    db_updateOrderStatus((int)$_POST['order_id'], $_POST['status']);
    $message = 'وضعیت سفارش به‌روزرسانی شد.';
}

$viewId = isset($_GET['view']) ? (int)$_GET['view'] : 0;
$viewOrder = null;
$viewItems = [];
if ($viewId > 0) {
    $viewOrder = db_getOrder($viewId);
    if ($viewOrder) {
        $viewItems = db_getOrderItems($viewId);
    }
}

$filter = $_GET['status'] ?? '';
$allowedFilters = ['pending','confirmed','shipped','cancelled'];
if ($filter && !in_array($filter, $allowedFilters)) $filter = '';
$orders = db_getOrders($filter);

$statusLabels = [
    'pending' => 'در انتظار',
    'confirmed' => 'تأیید شده',
    'shipped' => 'ارسال شده',
    'cancelled' => 'لغو شده'
];
$statusBadges = [
    'pending' => 'badge-pending',
    'confirmed' => 'badge-confirmed',
    'shipped' => 'badge-shipped',
    'cancelled' => 'badge-cancelled'
];

$adminTitle = 'مدیریت سفارش‌ها';
$adminActive = 'orders';
include __DIR__ . '/layout.php';
?>

<div class="admin-header">
  <h1>سفارش‌ها</h1>
  <div style="display:flex;gap:8px;flex-wrap:wrap">
    <a href="orders.php" class="filter-btn <?= $filter === '' ? 'active' : '' ?>">همه</a>
    <a href="?status=pending" class="filter-btn <?= $filter === 'pending' ? 'active' : '' ?>">در انتظار</a>
    <a href="?status=confirmed" class="filter-btn <?= $filter === 'confirmed' ? 'active' : '' ?>">تأیید شده</a>
    <a href="?status=shipped" class="filter-btn <?= $filter === 'shipped' ? 'active' : '' ?>">ارسال شده</a>
    <a href="?status=cancelled" class="filter-btn <?= $filter === 'cancelled' ? 'active' : '' ?>">لغو شده</a>
  </div>
</div>

<?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>

<?php if ($viewOrder): ?>
  <div style="background:linear-gradient(145deg,#151515,#0b0b0b);border:1px solid var(--line);border-radius:18px;padding:28px;margin-bottom:30px">
    <div style="display:flex;justify-content:space-between;align-items:start;flex-wrap:wrap;gap:12px">
      <div>
        <h2 style="margin:0;color:var(--gold2)">سفارش #<?= $viewOrder['id'] ?></h2>
        <p style="color:var(--muted);margin:6px 0"><?= date('Y/m/d H:i', strtotime($viewOrder['created_at'])) ?></p>
      </div>
      <a href="orders.php<?= $filter ? '?status='.$filter : '' ?>" class="btn sm ghost">بستن</a>
    </div>
    <div class="goldline"></div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
      <div><strong style="color:var(--gold2)">مشتری:</strong> <?= htmlspecialchars($viewOrder['customer_name']) ?></div>
      <div><strong style="color:var(--gold2)">تلفن:</strong> <?= htmlspecialchars($viewOrder['phone']) ?></div>
      <div style="grid-column:1/-1"><strong style="color:var(--gold2)">آدرس:</strong> <?= htmlspecialchars($viewOrder['address'] ?: '—') ?></div>
      <?php if (!empty($viewOrder['notes'])): ?>
        <div style="grid-column:1/-1"><strong style="color:var(--gold2)">توضیحات:</strong> <?= htmlspecialchars($viewOrder['notes']) ?></div>
      <?php endif; ?>
    </div>

    <table class="admin-table" style="margin-bottom:20px">
      <thead><tr><th>محصول</th><th>تعداد</th><th>قیمت واحد</th><th>جمع</th></tr></thead>
      <tbody>
        <?php foreach ($viewItems as $it): ?>
        <tr>
          <td><?= htmlspecialchars($it['product_name']) ?></td>
          <td><?= $it['quantity'] ?></td>
          <td><?= number_format($it['price']) ?></td>
          <td><?= number_format($it['price'] * $it['quantity']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <div class="payment-admin-box"><div><strong>روش پرداخت:</strong> <?= ($viewOrder['payment_method'] ?? '') === 'card_transfer' ? 'کارت به کارت' : (($viewOrder['payment_method'] ?? '') === 'gateway' ? 'درگاه' : 'ثبت نشده') ?></div><?php if(!empty($viewOrder['payment_reference'])): ?><div><strong>شماره پیگیری:</strong> <?= htmlspecialchars($viewOrder['payment_reference']) ?></div><?php endif; ?><?php if(!empty($viewOrder['receipt_file'])): ?><div><strong>فیش واریزی:</strong> <a class="btn sm" target="_blank" href="download-receipt.php?id=<?= $viewOrder['id'] ?>">مشاهده فیش</a></div><?php endif; ?></div>
    <div style="text-align:left;font-size:18px;color:var(--gold2);font-weight:700">
      جمع کل: <?= number_format($viewOrder['total']) ?> تومان
    </div>

    <form method="post" style="margin-top:24px;display:flex;gap:12px;align-items:center;flex-wrap:wrap">
      <input type="hidden" name="order_id" value="<?= $viewOrder['id'] ?>">
      <label style="color:var(--gold2)">تغییر وضعیت:</label>
      <select name="status" class="status-select">
        <?php foreach ($statusLabels as $k => $v): ?>
          <option value="<?= $k ?>" <?= $viewOrder['status'] === $k ? 'selected' : '' ?>><?= $v ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn sm">ذخیره</button>
    </form>
  </div>
<?php endif; ?>

<div class="table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <th>#</th>
        <th>مشتری</th>
        <th>تلفن</th>
        <th>مبلغ</th>
        <th>وضعیت</th>
        <th>تاریخ</th>
        <th>عملیات</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($orders)): ?>
        <tr><td colspan="7" style="text-align:center;color:var(--muted)">سفارشی یافت نشد</td></tr>
      <?php else: ?>
        <?php foreach ($orders as $o): ?>
        <tr>
          <td><?= $o['id'] ?></td>
          <td><?= htmlspecialchars($o['customer_name']) ?></td>
          <td><?= htmlspecialchars($o['phone']) ?></td>
          <td><?= number_format($o['total']) ?></td>
          <td><span class="badge <?= $statusBadges[$o['status']] ?? 'badge-pending' ?>"><?= $statusLabels[$o['status']] ?? $o['status'] ?></span></td>
          <td><?= date('Y/m/d H:i', strtotime($o['created_at'])) ?></td>
          <td>
            <a href="?view=<?= $o['id'] ?><?= $filter ? '&status='.$filter : '' ?>" class="btn sm">جزئیات</a>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</main></div></body></html>
