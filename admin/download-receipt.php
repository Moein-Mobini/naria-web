<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/auth.php';
requireAdmin();
$id=(int)($_GET['id']??0); $order=db_getOrder($id);
if(!$order || empty($order['receipt_file'])) { http_response_code(404); exit('فیش یافت نشد.'); }
$file=RECEIPT_DIR.basename($order['receipt_file']);
if(!is_file($file)) { http_response_code(404); exit('فایل یافت نشد.'); }
$mime=(new finfo(FILEINFO_MIME_TYPE))->file($file);
$allowed=['image/jpeg','image/png','image/webp']; if(!in_array($mime,$allowed,true)){http_response_code(403);exit('فرمت غیرمجاز.');}
header('Content-Type: '.$mime); header('Content-Disposition: inline; filename="receipt-'.$id.'.'.pathinfo($file,PATHINFO_EXTENSION).'"'); header('X-Content-Type-Options: nosniff'); readfile($file);
