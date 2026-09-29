<?php
require_once __DIR__ . '/config.php'; require_once __DIR__ . '/includes/db.php';
$pending=$_SESSION['pending_checkout']??null; if(!$pending){header('Location: checkout.php');exit;}
$error=''; $orderId=0;
try { $orderId=db_createOrder($pending['customer'],$pending['cart'],$pending['total']); db_updateOrderPayment($orderId,'gateway','DEMO-'.date('YmdHis')); $_SESSION['cart']=[]; unset($_SESSION['pending_checkout']); }
catch(Exception $e){$error='خطا در ثبت سفارش.';}
$pageTitle='نتیجه پرداخت | NARIA'; include __DIR__.'/includes/header.php';
?>
<div class="page-header"><div class="container"><div class="eyebrow">PAYMENT RESULT</div><h1>نتیجه پرداخت</h1></div></div>
<section style="padding-top:20px"><div class="container" style="max-width:620px;text-align:center">
<?php if($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php else: ?><div class="alert alert-success"><strong>پرداخت با موفقیت شبیه‌سازی شد.</strong><br>شماره سفارش: <strong>#<?= $orderId ?></strong><br>این تراکنش واقعی نیست و فقط برای نمایش قالب است.</div><?php endif; ?>
<a href="shop.php" class="btn">بازگشت به فروشگاه</a></div></section>
<?php include __DIR__.'/includes/footer.php'; ?>
