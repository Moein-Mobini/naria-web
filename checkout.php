<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) { header('Location: cart.php'); exit; }
$total = 0;
foreach ($cart as $item) $total += $item['price'] * $item['qty'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['customer_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    if ($name === '' || $phone === '') {
        $error = 'نام و شماره تماس الزامی است.';
    } else {
        $_SESSION['pending_checkout'] = [
            'customer' => ['name'=>$name,'phone'=>$phone,'address'=>$address,'notes'=>$notes],
            'cart' => $cart,
            'total' => $total,
        ];
        header('Location: payment-method.php'); exit;
    }
}
$pageTitle = 'ثبت سفارش | NARIA';
include __DIR__ . '/includes/header.php';
?>
<div class="page-header"><div class="container"><div class="eyebrow">CHECKOUT</div><h1>ثبت سفارش</h1></div></div>
<section style="padding-top:20px"><div class="container" style="max-width:720px">
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<div class="cart-summary" style="margin-bottom:28px">
<h3 style="margin-top:0;color:var(--gold2)">خلاصه سفارش</h3>
<?php foreach ($cart as $item): ?><div class="row"><span><?= htmlspecialchars($item['name']) ?> × <?= $item['qty'] ?></span><span><?= formatPrice($item['price']*$item['qty']) ?></span></div><?php endforeach; ?>
<div class="goldline"></div><div class="row"><strong>جمع کل</strong><strong style="color:var(--gold2)"><?= formatPrice($total) ?></strong></div>
</div>
<form method="post">
<div class="form-group"><label>نام و نام خانوادگی *</label><input type="text" name="customer_name" required value="<?= htmlspecialchars($_POST['customer_name'] ?? '') ?>"></div>
<div class="form-group"><label>شماره تماس *</label><input type="tel" name="phone" required placeholder="09xxxxxxxxx" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"></div>
<div class="form-group"><label>آدرس</label><textarea name="address" rows="3" placeholder="آدرس کامل برای ارسال"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea></div>
<div class="form-group"><label>توضیحات (اختیاری)</label><textarea name="notes" rows="2"><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea></div>
<button type="submit" class="btn">انتخاب روش پرداخت</button><a href="cart.php" class="btn ghost">بازگشت به سبد</a>
</form></div></section>
<?php include __DIR__ . '/includes/footer.php'; ?>
