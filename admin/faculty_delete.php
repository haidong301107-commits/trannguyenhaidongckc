<?php
require_once __DIR__ . '/../includes/init.php';


if (!isset($_SESSION['admin'])) {
    header('Location: ' . INDEX_URL . 'admin/login.php');
    exit();
}


$makhoa = trim($_GET['makhoa'] ?? '');
if ($makhoa === '') {
    $_SESSION['error'] = 'Không tìm thấy mã khoa.';
    header('Location: ' . INDEX_URL . 'admin/faculty_list.php');
    exit();
}

$khoa = DB::selectOne('
    SELECT k.*, COUNT(l.malop) as solop 
    FROM khoa k 
    LEFT JOIN lop l ON k.makhoa = l.makhoa 
    WHERE k.makhoa = :makhoa 
    GROUP BY k.makhoa', 
    ['makhoa' => $makhoa]
);

if (!$khoa) {
    $_SESSION['error'] = 'Không tìm thấy khoa cần xóa.';
    header('Location: ' . INDEX_URL . 'admin/faculty_list.php');
    exit();
}

try {

    DB::beginTransaction();

    DB::delete('lop', 'makhoa = :makhoa', ['makhoa' => $makhoa]);

    DB::delete('khoa', 'makhoa = :makhoa', ['makhoa' => $makhoa]);

    DB::commit();

    $_SESSION['success'] = sprintf(
        'Đã xóa thành công khoa %s và %d lớp thuộc khoa!',
        $khoa['tenkhoa'],
        $khoa['solop']
    );
} catch (Exception $e) {
    DB::rollBack();
    $_SESSION['error'] = 'Có lỗi xảy ra khi xóa khoa. Vui lòng thử lại.';
}

header('Location: ' . INDEX_URL . 'admin/faculty_list.php');
exit();