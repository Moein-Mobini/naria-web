<?php
require_once __DIR__ . '/../config.php';

function requireAdmin() {
    if (empty($_SESSION['admin_logged_in'])) {
        header('Location: index.php');
        exit;
    }
}

function isAdmin() {
    return !empty($_SESSION['admin_logged_in']);
}
?>
