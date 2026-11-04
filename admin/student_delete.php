<?php
require_once __DIR__ . '/../includes/init.php';

// Chỉ admin mới được phép
if (!isset($_SESSION['admin'])) {
    header('Location: ' . INDEX_URL . 'admin/login.php');
    exit();
}

$mssv = trim($_GET['mssv'] ?? '');
if ($mssv === '') {
    $_SESSION['error'] = 'Mã số sinh viên không hợp lệ.';
    header('Location: student_list.php');
    exit();
}

try {
    // Lấy thông tin sinh viên để hiển thị trong thông báo
    $student = DB::selectOne('SELECT mssv, hoten FROM sinh_vien WHERE mssv = :mssv', ['mssv' => $mssv]);
    if (!$student) {
        $_SESSION['error'] = 'Không tìm thấy sinh viên với MSSV: ' . htmlspecialchars($mssv);
        header('Location: student_list.php');
        exit();
    }

    // Thực hiện xóa
    $stmt = DB::getPdo()->prepare('DELETE FROM sinh_vien WHERE mssv = :mssv');
    $stmt->execute(['mssv' => $mssv]);

    $_SESSION['success'] = 'Đã xóa sinh viên: '.htmlspecialchars($student['hoten']).' (MSSV: '.htmlspecialchars($student['mssv']).')';
    header('Location: student_list.php');
    exit();
} catch (PDOException $e) {
    $_SESSION['error'] = 'Lỗi khi xóa sinh viên: '.htmlspecialchars($student['hoten'] ?? $mssv).' (MSSV: '.htmlspecialchars($mssv).'). Lỗi: ' . $e->getMessage();
    header('Location: student_list.php');
    exit();
}