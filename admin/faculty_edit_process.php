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
    header('Location: ' . INDEX_URL . 'admin/faculty_edit.php?makhoa=' . urlencode($makhoa));
    exit();
}

if (strlen($tenkhoa) > 100) {
    $_SESSION['error'] = 'Tên khoa không được vượt quá 100 ký tự.';
    header('Location: ' . INDEX_URL . 'admin/faculty_edit.php?makhoa=' . urlencode($makhoa));
    exit();
}

$existing = DB::selectOne('SELECT tenkhoa FROM khoa WHERE makhoa = :makhoa', ['makhoa' => $makhoa]);
if (!$existing) {
    $_SESSION['error'] = 'Không tìm thấy khoa cần cập nhật.';
    header('Location: ' . INDEX_URL . 'admin/faculty_list.php');
    exit();
}

try {
    $sql = "UPDATE khoa SET tenkhoa = :tenkhoa, mota = :mota WHERE makhoa = :makhoa";
    DB::getPdo()->prepare($sql)->execute([
        'makhoa' => $makhoa,
        'tenkhoa' => $tenkhoa,
        'mota' => $mota ?: null
    ]);

    $_SESSION['success'] = "Đã cập nhật thông tin khoa '$tenkhoa' thành công!";
    header('Location: ' . INDEX_URL . 'admin/faculty_list.php');
    exit();
} catch (Exception $e) {
    $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật thông tin khoa. Vui lòng thử lại.';
    header('Location: ' . INDEX_URL . 'admin/faculty_edit.php?makhoa=' . urlencode($makhoa));
    exit();
}