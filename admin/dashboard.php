<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/auth.php';
requireAdmin();

$stats = db_stats();
$recentOrders = array_slice(db_getOrders(), 0, 8);

$adminTitle = 'داشبورد';
$adminActive = 'dashboard';
include __DIR__ . '/layout.php';
?>

<div class="admin-header">
  <h1>داشبورد</h1>
</div>

<div class="stats">
  <div class="stat-card">
    <div class="num"><?= $stats['products'] ?></div>
    <div class="label">محصولات</div>
  </div>
  <div class="stat-card">
    <div class="num"><?= $stats['orders'] ?></div>
    <div class="label">کل سفارش‌ها</div>
  </div>
  <div class="stat-card">
    <div class="num"><?= $stats['pending'] ?></div>
    <div class="label">در انتظار تأیید</div>
  </div>
  <div class="stat-card">
    <div class="num" style="font-size:20px"><?= number_format($stats['revenue']) ?></div>
    <div class="label">فروش تأییدشده (تومان)</div>
  </div>
</div>

<div class="admin-header" style="margin-top:10px">
  <h2 style="font-size:20px;margin:0">آخرین سفارش‌ها</h2>
  <a href="orders.php" class="btn sm">همه سفارش‌ها</a>
</div>

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
      </tr>
    </thead>
    <tbody>
      <?php if (empty($recentOrders)): ?>
        <tr><td colspan="6" style="text-align:center;color:var(--muted)">هنوز سفارشی ثبت نشده</td></tr>
      <?php else: ?>
        <?php foreach ($recentOrders as $o): ?>
        <tr>
          <td><?= $o['id'] ?></td>
          <td><?= htmlspecialchars($o['customer_name']) ?></td>
          <td><?= htmlspecialchars($o['phone']) ?></td>
          <td><?= number_format($o['total']) ?></td>
          <td>
            <?php
            $badge = 'badge-pending'; $label = 'در انتظار';
            if ($o['status'] === 'confirmed') { $badge = 'badge-confirmed'; $label = 'تأیید شده'; }
            elseif ($o['status'] === 'shipped') { $badge = 'badge-shipped'; $label = 'ارسال شده'; }
            elseif ($o['status'] === 'cancelled') { $badge = 'badge-cancelled'; $label = 'لغو شده'; }
            ?>
            <span class="badge <?= $badge ?>"><?= $label ?></span>
          </td>
          <td><?= date('Y/m/d H:i', strtotime($o['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</main></div></body></html>
