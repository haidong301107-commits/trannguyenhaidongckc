<?php
require_once __DIR__ . '/../includes/init.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ' . INDEX_URL . 'admin/login.php');
    exit();
}

$makhoa = trim($_POST['makhoa'] ?? '');
$tenkhoa = trim($_POST['tenkhoa'] ?? '');
$mota = trim($_POST['mota'] ?? '');


if (empty($makhoa) || empty($tenkhoa)) {
    $_SESSION['error'] = 'Vui lòng điền đầy đủ mã khoa và tên khoa.';
    header('Location: ' . INDEX_URL . 'admin/faculty_add.php');
    exit();
}

if (strlen($makhoa) > 20) {
    $_SESSION['error'] = 'Mã khoa không được vượt quá 20 ký tự.';
    header('Location: ' . INDEX_URL . 'admin/faculty_add.php');
    exit();
}

if (strlen($tenkhoa) > 100) {
    $_SESSION['error'] = 'Tên khoa không được vượt quá 100 ký tự.';
    header('Location: ' . INDEX_URL . 'admin/faculty_add.php');
    exit();
}

$existing = DB::selectOne('SELECT makhoa FROM khoa WHERE makhoa = :makhoa', ['makhoa' => $makhoa]);
if ($existing) {
    $_SESSION['error'] = 'Mã khoa đã tồn tại trong hệ thống.';
    header('Location: ' . INDEX_URL . 'admin/faculty_add.php');
    exit();
}

try {
    $sql = "INSERT INTO khoa (makhoa, tenkhoa, mota) VALUES (:makhoa, :tenkhoa, :mota)";
    DB::getPdo()->prepare($sql)->execute([
        'makhoa' => $makhoa,
        'tenkhoa' => $tenkhoa,
        'mota' => $mota ?: null
    ]);

    $_SESSION['success'] = "Đã thêm khoa '$tenkhoa' thành công!";
    header('Location: ' . INDEX_URL . 'admin/faculty_list.php');
    exit();
} catch (Exception $e) {
    $_SESSION['error'] = 'Có lỗi xảy ra khi thêm khoa. Vui lòng thử lại.';
    header('Location: ' . INDEX_URL . 'admin/faculty_add.php');
    exit();
}