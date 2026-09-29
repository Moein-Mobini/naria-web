<?php
require_once __DIR__ . '/config.php'; require_once __DIR__ . '/includes/db.php';
$pending=$_SESSION['pending_checkout']??null; if(!$pending){header('Location: checkout.php');exit;}
$error=''; $done=false; $orderId=0;
if($_SERVER['REQUEST_METHOD']==='POST'){
  $ref=trim($_POST['reference']??'');
  $file=$_FILES['receipt']??null;
  if($ref==='') $error='شماره پیگیری را وارد کنید.';
  elseif(!$file || $file['error']!==UPLOAD_ERR_OK) $error='تصویر فیش واریزی را انتخاب کنید.';
  else {
    $allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
    $mime=(new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if(!isset($allowed[$mime])) $error='فرمت فیش باید JPG، PNG یا WEBP باشد.';
    elseif($file['size'] > 5*1024*1024) $error='حجم فیش نباید بیشتر از ۵ مگابایت باشد.';
    else {
      try {
        $orderId=db_createOrder($pending['customer'],$pending['cart'],$pending['total']);
        $name='receipt_'.$orderId.'_'.bin2hex(random_bytes(8)).'.'.$allowed[$mime];
        if(!move_uploaded_file($file['tmp_name'], RECEIPT_DIR.$name)) throw new Exception('upload');
        db_updateOrderPayment($orderId,'card_transfer',$ref,$name);
        $_SESSION['cart']=[]; unset($_SESSION['pending_checkout']); $done=true;
      } catch(Exception $e){ if(isset($name) && file_exists(RECEIPT_DIR.$name)) @unlink(RECEIPT_DIR.$name); $error='خطا در ثبت سفارش و فیش.'; }
    }
  }
}
$pageTitle='کارت به کارت | NARIA'; include __DIR__.'/includes/header.php';
?>
<div class="page-header"><div class="container"><div class="eyebrow">CARD TRANSFER</div><h1>کارت به کارت</h1></div></div>
<section style="padding-top:20px"><div class="container" style="max-width:620px">
<?php if($done): ?><div class="alert alert-success"><strong>سفارش ثبت شد.</strong><br>شماره سفارش: <strong>#<?= $orderId ?></strong><br>پرداخت پس از بررسی رسید تأیید می‌شود.</div><a href="shop.php" class="btn">بازگشت به فروشگاه</a>
<?php else: ?>
<?php if($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<div class="cart-summary"><h3 style="margin-top:0;color:var(--gold2)">اطلاعات پرداخت نمایشی</h3><div class="row"><span>مبلغ</span><strong><?= formatPrice($pending['total']) ?></strong></div><div class="bank-box"><div>نام صاحب حساب: NARIA SHOP</div><div>شماره کارت: <strong>0000 0000 0000 0000</strong></div><small>این اطلاعات صرفاً برای دموی قالب است.</small></div></div>
<form method="post" enctype="multipart/form-data"><div class="form-group"><label>شماره پیگیری / رسید *</label><input name="reference" required placeholder="مثلاً 123456789"></div><div class="form-group"><label>تصویر فیش واریزی *</label><input type="file" name="receipt" accept="image/jpeg,image/png,image/webp" required><small class="file-hint">حداکثر ۵ مگابایت — JPG، PNG یا WEBP</small></div><button class="btn" type="submit">ثبت رسید و تکمیل سفارش</button><a href="payment-method.php" class="btn ghost">بازگشت</a></form>
<?php endif; ?></div></section>
<?php include __DIR__.'/includes/footer.php'; ?>
