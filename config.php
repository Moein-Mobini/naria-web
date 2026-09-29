<?php
// NARIA Shop Configuration

// مسیر دیتابیس — داخل پوشه پروژه (سازگار با ویندوز و لینوکس)
define('DATA_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR);
define('DB_PATH', DATA_DIR . 'naria.db');
define('JSON_DB', DATA_DIR . 'store.json'); // جایگزین در صورت نبود SQLite
define('UPLOAD_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR);
define('RECEIPT_DIR', UPLOAD_DIR . 'receipts' . DIRECTORY_SEPARATOR);

define('SITE_NAME', 'NARIA');
define('SITE_URL', '');
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'naria2026'); // در محیط واقعی حتماً تغییر دهید
define('CURRENCY', 'تومان');

// ساخت پوشه‌های لازم
if (!is_dir(DATA_DIR)) {
    mkdir(DATA_DIR, 0755, true);
}
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}
if (!is_dir(RECEIPT_DIR)) {
    mkdir(RECEIPT_DIR, 0755, true);
}
// Receipts are served only through admin/download-receipt.php.
$receiptHtaccess = RECEIPT_DIR . '.htaccess';
if (!file_exists($receiptHtaccess)) {
    file_put_contents($receiptHtaccess, "Deny from all\n");
}

// فایل محافظت از پوشه data
$htaccess = DATA_DIR . '.htaccess';
if (!file_exists($htaccess)) {
    file_put_contents($htaccess, "Deny from all\n");
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Tehran');
?>
