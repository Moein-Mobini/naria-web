<?php
require_once __DIR__.'/config.php'; require_once __DIR__.'/includes/db.php';
$error=''; $done=false; $ticketId=0;
if($_SERVER['REQUEST_METHOD']==='POST'){
  $name=trim($_POST['name']??''); $phone=trim($_POST['phone']??''); $subject=trim($_POST['subject']??''); $message=trim($_POST['message']??'');
  if($name===''||$phone===''||$subject===''||$message==='') $error='همه فیلدهای الزامی را تکمیل کنید.';
  else { $ticketId=db_createSupportTicket($name,$phone,$subject,$message); $done=true; }
}
$pageTitle='پشتیبانی | NARIA'; include __DIR__.'/includes/header.php';
?>
<div class="page-header"><div class="container"><div class="eyebrow">NARIA SUPPORT</div><h1>پشتیبانی</h1></div></div>
<section style="padding-top:20px"><div class="container support-grid">
<div class="cart-summary support-card"><div class="eyebrow">CONTACT SUPPORT</div><h2>درخواست خود را ارسال کنید</h2><p class="muted-text">پیام شما ثبت می‌شود و از پنل مدیریت قابل پیگیری است.</p><?php if($done): ?><div class="alert alert-success">درخواست شما با شماره <strong>#<?= $ticketId ?></strong> ثبت شد.</div><a class="btn" href="index.php">بازگشت به سایت</a><?php else: ?><?php if($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?><form method="post"><div class="form-group"><label>نام *</label><input name="name" required value="<?= htmlspecialchars($_POST['name']??'') ?>"></div><div class="form-group"><label>شماره تماس *</label><input name="phone" required value="<?= htmlspecialchars($_POST['phone']??'') ?>"></div><div class="form-group"><label>موضوع *</label><input name="subject" required value="<?= htmlspecialchars($_POST['subject']??'') ?>"></div><div class="form-group"><label>پیام *</label><textarea name="message" rows="6" required><?= htmlspecialchars($_POST['message']??'') ?></textarea></div><button class="btn" type="submit">ارسال درخواست</button></form><?php endif; ?></div>
<div class="support-side"><div class="support-info"><span>◉</span><div><strong>پاسخ‌گویی</strong><p>درخواست‌های ثبت‌شده از پنل مدیریت قابل مشاهده و پیگیری هستند.</p></div></div><div class="support-info"><span>▣</span><div><strong>اطلاعات سفارش</strong><p>برای پیگیری سفارش، شماره سفارش و شماره تماس خود را در پیام بنویسید.</p></div></div><div class="support-info"><span>↯</span><div><strong>پرداخت</strong><p>در صورت مشکل در پرداخت، موضوع و شماره پیگیری را ارسال کنید.</p></div></div></div>
</div></section>
<?php include __DIR__.'/includes/footer.php'; ?>
