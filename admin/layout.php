<?php
// Call with $adminTitle and $adminActive before include
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($adminTitle ?? 'پنل ادمین') ?> | NARIA</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="admin-layout">
  <aside class="admin-sidebar">
    <a class="brand" href="dashboard.php">NARIA</a>
    <nav class="admin-nav">
      <a href="dashboard.php" class="<?= ($adminActive ?? '') === 'dashboard' ? 'active' : '' ?>">داشبورد</a>
      <a href="products.php" class="<?= ($adminActive ?? '') === 'products' ? 'active' : '' ?>">محصولات</a>
      <a href="orders.php" class="<?= ($adminActive ?? '') === 'orders' ? 'active' : '' ?>">سفارش‌ها</a>
      <a href="support.php" class="<?= ($adminActive ?? '') === 'support' ? 'active' : '' ?>">پشتیبانی</a>
      <a href="../index.php" target="_blank">مشاهده سایت ↗</a>
      <a href="logout.php" style="color:#e74c3c;margin-top:20px">خروج</a>
    </nav>
  </aside>
  <main class="admin-main">
