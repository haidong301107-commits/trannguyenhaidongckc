<?php
require_once __DIR__ . '/includes/init.php';

// Kiểm tra xem có đăng nhập chưa
if (!isset($_SESSION['sinhvien'])) {
    header('Location: ' . INDEX_URL . 'login.php');
    exit();
}

// Xóa các session liên quan đến sinh viên
unset($_SESSION['sinhvien']);

// Xóa toàn bộ session nếu không còn session nào khác cần giữ lại
session_destroy();

// Xóa cookie session
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Chuyển hướng về trang đăng nhập
header('Location: ' . INDEX_URL . 'login.php');
exit();