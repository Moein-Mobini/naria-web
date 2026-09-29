<?php
require_once __DIR__ . '/../config.php';
unset($_SESSION['admin_logged_in']);
header('Location: index.php');
exit;
?>
