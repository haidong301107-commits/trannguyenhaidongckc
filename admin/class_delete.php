<?php
require_once __DIR__ . '/../includes/init.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ' . INDEX_URL . 'admin/login.php');
    exit();
}

$malop = trim($_GET['malop'] ?? '');
if ($malop === '') {
    $_SESSION['error'] = 'Không tìm thấy mã lớp.';
    header('Location: ' . INDEX_URL . 'admin/class_list.php');
    exit();
}

$lop = DB::selectOne('
    SELECT l.*, k.tenkhoa, COUNT(sv.mssv) as sosv 
    FROM lop l 
    LEFT JOIN khoa k ON l.makhoa = k.makhoa 
    LEFT JOIN sinh_vien sv ON l.malop = sv.malop 
    WHERE l.malop = :malop 
    GROUP BY l.malop', 
    ['malop' => $malop]
);

if (!$lop) {
    $_SESSION['error'] = 'Không tìm thấy lớp cần xóa.';
    header('Location: ' . INDEX_URL . 'admin/class_list.php');
    exit();
}

try {
    DB::beginTransaction();

    DB::update('sinh_vien', 
        ['malop' => null],
        'malop = :malop',
        ['malop' => $malop]
    );

    DB::delete('lop', 'malop = :malop', ['malop' => $malop]);

    DB::commit();

    $_SESSION['success'] = sprintf(
        'Đã xóa thành công lớp %s và gỡ %d sinh viên khỏi lớp!',
        $lop['tenlop'],
        $lop['sosv']
    );
} catch (Exception $e) {
    DB::rollBack();
    $_SESSION['error'] = 'Có lỗi xảy ra khi xóa lớp. Vui lòng thử lại.';
}

header('Location: ' . INDEX_URL . 'admin/class_list.php');
exit();