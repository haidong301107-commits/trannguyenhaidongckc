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
    $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
    header('Location: student_add.php');
    exit();
}

// Tạo mật khẩu mặc định (mã hóa)
$default_password = password_hash($mssv, PASSWORD_DEFAULT);

try {
    $sql = "INSERT INTO sinh_vien (mssv, hoten, ngaysinh, gioitinh, malop, makhoa, email, sdt, diachi, password, trangthai)
            VALUES (:mssv, :hoten, :ngaysinh, :gioitinh, :malop, :makhoa, :email, :sdt, :diachi, :password, :trangthai)";
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
        'password' => $default_password,
        'trangthai' => $trangthai
    ]);
    $_SESSION['success'] = 'Thêm sinh viên thành công: ' . htmlspecialchars($hoten) . ' (MSSV: ' . htmlspecialchars($mssv) . ')';
    header('Location: ' . INDEX_URL . 'admin/student_list.php');
    exit();
} catch (PDOException $e) {
    $_SESSION['error'] = 'Thêm sinh viên thất bại: ' . htmlspecialchars($hoten) . ' (MSSV: ' . htmlspecialchars($mssv) . '). Lỗi: ' . $e->getMessage();
    header('Location: ' . INDEX_URL . 'admin/student_add.php');
    exit();
}