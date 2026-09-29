<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/auth.php';

if (isAdmin()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';
    if ($user === ADMIN_USER && $pass === ADMIN_PASS) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    }
    $error = 'نام کاربری یا رمز عبور اشتباه است.';
}
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ورود ادمین | NARIA</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <div class="login-box">
    <div class="brand" style="font-size:24px;margin-bottom:8px">NARIA</div>
    <p style="color:var(--muted);margin-bottom:28px">پنل مدیریت فروشگاه</p>
    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
      <div class="form-group" style="text-align:right">
        <label>نام کاربری</label>
        <input type="text" name="username" required autofocus>
      </div>
      <div class="form-group" style="text-align:right">
        <label>رمز عبور</label>
        <input type="password" name="password" required>
      </div>
      <button type="submit" class="btn" style="width:100%">ورود</button>
    </form>
    <p style="margin-top:20px;font-size:12px;color:var(--muted)"><a href="../index.php">← بازگشت به سایت</a></p>
  </div>
</body>
</html>
