<?php
require_once __DIR__ . '/config.php'; require_once __DIR__ . '/includes/db.php';
$pending=$_SESSION['pending_checkout']??null; if(!$pending){header('Location: checkout.php');exit;}
$pageTitle='درگاه پرداخت | NARIA'; include __DIR__.'/includes/header.php';
?>
<div class="page-header"><div class="container"><div class="eyebrow">ONLINE GATEWAY</div><h1>درگاه پرداخت</h1></div></div>
<section style="padding-top:20px"><div class="container" style="max-width:620px">
<div class="gateway-box"><div class="gateway-logo">NARIA</div><h2>درگاه پرداخت نمایشی</h2><p>مبلغ قابل پرداخت</p><div class="gateway-price"><?= formatPrice($pending['total']) ?></div><div class="alert alert-info">این صفحه شبیه‌سازی درگاه است و هیچ تراکنش واقعی انجام نمی‌دهد.</div>
<form method="post" action="payment-gateway-result.php"><button class="btn success" type="submit">پرداخت موفق (دمو)</button><a href="payment-method.php" class="btn ghost">انصراف</a></form></div></div></section>
<?php include __DIR__.'/includes/footer.php'; ?>
