<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
$pending = $_SESSION['pending_checkout'] ?? null;
if (!$pending || empty($pending['cart'])) { header('Location: checkout.php'); exit; }
$pageTitle='روش پرداخت | NARIA'; include __DIR__.'/includes/header.php';
?>
<div class="page-header"><div class="container"><div class="eyebrow">PAYMENT</div><h1>انتخاب روش پرداخت</h1></div></div>
<section style="padding-top:20px"><div class="container" style="max-width:760px">
<div class="cart-summary"><div class="row"><span>نام مشتری</span><strong><?= htmlspecialchars($pending['customer']['name']) ?></strong></div><div class="row"><span>مبلغ قابل پرداخت</span><strong style="color:var(--gold2)"><?= formatPrice($pending['total']) ?></strong></div></div>
<div class="payment-options">
<a class="payment-option" href="payment-card.php"><div class="payment-icon">▣</div><div><h3>کارت به کارت</h3><p>نمایش اطلاعات حساب و ثبت رسید پرداخت</p></div><span>←</span></a>
<a class="payment-option" href="payment-gateway.php"><div class="payment-icon">↯</div><div><h3>پرداخت از طریق درگاه</h3><p>ورود به صفحه درگاه پرداخت و پرداخت آنلاین</p></div><span>←</span></a>
</div></div></section>
<?php include __DIR__.'/includes/footer.php'; ?>
