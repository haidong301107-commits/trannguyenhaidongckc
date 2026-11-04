<?php
require_once __DIR__ . '/../includes/init.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ' . INDEX_URL . 'admin/login.php');
    exit();
}

// Lấy dữ liệu POST
$mssv = trim($_POST['mssv'] ?? '');
$hoten = trim($_POST['hoten'] ?? '');
$ngaysinh = $_POST['ngaysinh'] ?? null;
$gioitinh = $_POST['gioitinh'] ?? 'Nam';
$malop = $_POST['malop'] ?? '';
$makhoa = $_POST['makhoa'] ?? '';
$email = trim($_POST['email'] ?? '');
$sdt = trim($_POST['sdt'] ?? '');
$diachi = trim($_POST['diachi'] ?? '');
$trangthai = $_POST['trangthai'] ?? 'Đang học';

// Validate cơ bản
if ($mssv === '' || $hoten === '' || $malop === '' || $makhoa === '') {
    $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc cho sinh viên: '.htmlspecialchars($hoten).' (MSSV: '.htmlspecialchars($mssv).')';
    header('Location: ' . INDEX_URL . 'admin/student_list.php');
    exit();
}

try {
    // Kiểm tra sinh viên tồn tại
    $student = DB::selectOne('SELECT mssv, hoten FROM sinh_vien WHERE mssv = :mssv', ['mssv' => $mssv]);
    if (!$student) {
        $_SESSION['error'] = 'Không tìm thấy sinh viên với MSSV: ' . htmlspecialchars($mssv);
        header('Location: ' . INDEX_URL . 'admin/student_list.php');
        exit();
    }

    $sql = "UPDATE sinh_vien SET 
            hoten = :hoten,
            ngaysinh = :ngaysinh,
            gioitinh = :gioitinh,
            malop = :malop,
            makhoa = :makhoa,
            email = :email,
            sdt = :sdt,
            diachi = :diachi,
            trangthai = :trangthai
            WHERE mssv = :mssv";
            
    DB::getPdo()->prepare($sql)->execute([
        'mssv' => $mssv,
        'hoten' => $hoten,
        'ngaysinh' => $ngaysinh ?: null,
        'gioitinh' => $gioitinh,
        'malop' => $malop,
        'makhoa' => $makhoa,
        'email' => $email,
        'sdt' => $sdt,
        'diachi' => $diachi,
        'trangthai' => $trangthai
    ]);

    $_SESSION['success'] = 'Cập nhật thông tin sinh viên thành công: '.htmlspecialchars($hoten).' (MSSV: '.htmlspecialchars($mssv).')';
    header('Location: ' . INDEX_URL . 'admin/student_list.php');
    exit();
} catch (PDOException $e) {
    $_SESSION['error'] = 'Lỗi khi cập nhật thông tin sinh viên: '.htmlspecialchars($hoten).' (MSSV: '.htmlspecialchars($mssv).'). Lỗi: ' . $e->getMessage();
    header('Location: ' . INDEX_URL . 'admin/student_list.php');
    exit();
}