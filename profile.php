<?php
require_once __DIR__ . '/includes/init.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['sinhvien'])) {
    header('Location: ' . INDEX_URL . 'login.php');
    exit();
}

// Lấy thông tin sinh viên
$mssv = $_SESSION['sinhvien'];
$sinh_vien = DB::selectOne(
    "SELECT sv.*, l.tenlop, k.tenkhoa 
    FROM sinh_vien sv 
    LEFT JOIN lop l ON sv.malop = l.malop 
    LEFT JOIN khoa k ON sv.makhoa = k.makhoa 
    WHERE sv.mssv = :mssv",
    ['mssv' => $mssv]
);

if (!$sinh_vien) {
    $_SESSION['error'] = 'Không tìm thấy thông tin sinh viên!';
    header('Location: ' . INDEX_URL . 'logout.php');
    exit();
}

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ngaysinh = trim($_POST['ngaysinh'] ?? '');
    $gioitinh = trim($_POST['gioitinh'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $sdt = trim($_POST['sdt'] ?? '');
    $diachi = trim($_POST['diachi'] ?? '');

    $errors = [];

    // Validate email
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ';
    }

    // Validate số điện thoại
    if ($sdt !== '' && !preg_match('/^[0-9]{10,11}$/', $sdt)) {
        $errors[] = 'Số điện thoại không hợp lệ (phải có 10-11 chữ số)';
    }

    // Validate ngày sinh
    if ($ngaysinh !== '') {
        $date = DateTime::createFromFormat('Y-m-d', $ngaysinh);
        if (!$date || $date->format('Y-m-d') !== $ngaysinh) {
            $errors[] = 'Ngày sinh không hợp lệ';
        }
    }

    // Validate giới tính
    if (!in_array($gioitinh, ['Nam', 'Nữ', 'Khác'])) {
        $errors[] = 'Giới tính không hợp lệ';
    }

    if (empty($errors)) {
        try {
            $sql = "UPDATE sinh_vien SET 
                    ngaysinh = :ngaysinh,
                    gioitinh = :gioitinh,
                    email = :email,
                    sdt = :sdt,
                    diachi = :diachi
                    WHERE mssv = :mssv";

            DB::getPdo()->prepare($sql)->execute([
                'ngaysinh' => $ngaysinh ?: null,
                'gioitinh' => $gioitinh,
                'email' => $email ?: null,
                'sdt' => $sdt ?: null,
                'diachi' => $diachi ?: null,
                'mssv' => $mssv
            ]);

            $_SESSION['success'] = 'Cập nhật thông tin thành công!';
            header('Location: index.php');
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật thông tin. Vui lòng thử lại!';
        }
    } else {
        $_SESSION['error'] = implode('<br>', $errors);
    }
}

include('includes/sidebar.php');
include('includes/header.php');

// Flash Messages
if (!empty($_SESSION['success'])) {
    echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 max-w-3xl mx-auto">'
        . htmlspecialchars($_SESSION['success']) . '</div>';
    unset($_SESSION['success']);
}
if (!empty($_SESSION['error'])) {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 max-w-3xl mx-auto">'
        . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>

<div class="max-w-3xl mx-auto">
    <!-- Form cập nhật thông tin -->
    <div class="bg-white shadow rounded-xl p-6">
        <h3 class="text-xl font-semibold text-gray-800 border-b pb-2 mb-6">
            ✏️ Cập nhật thông tin cá nhân
        </h3>

        <form action="profile.php" method="POST" class="space-y-6">
            <!-- Thông tin không được sửa -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg mb-4">
                <div>
                    <p class="text-sm text-gray-600">Mã số sinh viên</p>
                    <p class="font-medium"><?php echo htmlspecialchars($sinh_vien['mssv']); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Họ và tên</p>
                    <p class="font-medium"><?php echo htmlspecialchars($sinh_vien['hoten']); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Lớp</p>
                    <p class="font-medium"><?php echo htmlspecialchars($sinh_vien['tenlop'] ?? '(Chưa phân lớp)'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Khoa</p>
                    <p class="font-medium"><?php echo htmlspecialchars($sinh_vien['tenkhoa'] ?? '(Chưa phân khoa)'); ?></p>
                </div>
            </div>

            <!-- Thông tin được phép cập nhật -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày sinh</label>
                    <input type="date" name="ngaysinh" 
                           value="<?php echo $sinh_vien['ngaysinh'] ?? ''; ?>"
                           class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Giới tính</label>
                    <select name="gioitinh" 
                            class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="Nam" <?php echo $sinh_vien['gioitinh'] === 'Nam' ? 'selected' : ''; ?>>Nam</option>
                        <option value="Nữ" <?php echo $sinh_vien['gioitinh'] === 'Nữ' ? 'selected' : ''; ?>>Nữ</option>
                        <option value="Khác" <?php echo $sinh_vien['gioitinh'] === 'Khác' ? 'selected' : ''; ?>>Khác</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" 
                           value="<?php echo htmlspecialchars($sinh_vien['email'] ?? ''); ?>"
                           placeholder="example@email.com"
                           class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                    <input type="tel" name="sdt" 
                           value="<?php echo htmlspecialchars($sinh_vien['sdt'] ?? ''); ?>"
                           placeholder="0xxxxxxxxx"
                           class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                    <input type="text" name="diachi" 
                           value="<?php echo htmlspecialchars($sinh_vien['diachi'] ?? ''); ?>"
                           placeholder="Nhập địa chỉ của bạn"
                           class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="index.php" 
                   class="px-5 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400">
                    Hủy
                </a>
                <button type="submit" 
                        class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>

<?php include('includes/footer.php'); ?>