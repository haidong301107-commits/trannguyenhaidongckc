<?php
require_once __DIR__ . '/../includes/init.php';

// Kiểm tra xem có phải admin không
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

// Xóa session admin
unset($_SESSION['admin']);

// Nếu không còn session nào khác cần giữ lại, có thể xóa toàn bộ session
session_destroy();

// Clear cookie session
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Chuyển hướng về trang login
header('Location: login.php');
exit();