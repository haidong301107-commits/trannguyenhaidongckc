<?php
require_once __DIR__ . '/includes/init.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['sinhvien'])) {
    header('Location: ' . INDEX_URL . 'login.php');
    exit();
}

// Xử lý đổi mật khẩu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = trim($_POST['current_password'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    
    $errors = [];
    
    // Validate form
    if (empty($current_password)) {
        $errors[] = 'Vui lòng nhập mật khẩu hiện tại';
    }
    if (empty($new_password)) {
        $errors[] = 'Vui lòng nhập mật khẩu mới';
    } elseif (strlen($new_password) < 6) {
        $errors[] = 'Mật khẩu mới phải có ít nhất 6 ký tự';
    }
    if ($new_password !== $confirm_password) {
        $errors[] = 'Xác nhận mật khẩu không khớp';
    }
    if ($new_password === $current_password && !empty($new_password)) {
        $errors[] = 'Mật khẩu mới không được trùng với mật khẩu hiện tại';
    }

    if (empty($errors)) {
        // Kiểm tra mật khẩu hiện tại
        $mssv = $_SESSION['sinhvien'];
        $sinh_vien = DB::selectOne(
            "SELECT password FROM sinh_vien WHERE mssv = :mssv",
            ['mssv' => $mssv]
        );

        if (!$sinh_vien || !password_verify($current_password, $sinh_vien['password'])) {
            $_SESSION['error'] = 'Mật khẩu hiện tại không chính xác';
        } else {
            // Cập nhật mật khẩu mới
            try {
                DB::getPdo()->prepare(
                    "UPDATE sinh_vien SET password = :password WHERE mssv = :mssv"
                )->execute([
                    'password' => password_hash($new_password, PASSWORD_DEFAULT),
                    'mssv' => $mssv
                ]);

                // Lưu thông báo thành công vào session
                $_SESSION['success'] = 'Đổi mật khẩu thành công! Vui lòng đăng nhập lại với mật khẩu mới.';
                
                // Xóa các session liên quan đến đăng nhập
                unset($_SESSION['user_role']);
                unset($_SESSION['sinhvien']);
                unset($_SESSION['fullname']);
                
                // Chuyển hướng về trang đăng nhập
                header('Location: ' . INDEX_URL . 'login.php');
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật mật khẩu. Vui lòng thử lại!';
            }
        }
    } else {
        $_SESSION['error'] = implode('<br>', $errors);
    }
}

include('includes/sidebar.php');
include('includes/header.php');

// Flash Messages
if (!empty($_SESSION['success'])) {
    echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 max-w-xl mx-auto">'
        . htmlspecialchars($_SESSION['success']) . '</div>';
    unset($_SESSION['success']);
}
if (!empty($_SESSION['error'])) {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 max-w-xl mx-auto">'
        . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>

<div class="max-w-xl mx-auto">
    <div class="bg-white shadow rounded-xl p-6">
        <h3 class="text-xl font-semibold text-gray-800 border-b pb-2 mb-6">
            🔑 Đổi mật khẩu
        </h3>

        <form action="change_password.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Mật khẩu hiện tại
                </label>
                <div class="relative">
                    <input type="password" 
                           name="current_password"
                           class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                           required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Mật khẩu mới
                </label>
                <div class="relative">
                    <input type="password" 
                           name="new_password"
                           class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                           minlength="6"
                           required>
                </div>
                <p class="mt-1 text-sm text-gray-500">Mật khẩu phải có ít nhất 6 ký tự</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Xác nhận mật khẩu mới
                </label>
                <div class="relative">
                    <input type="password" 
                           name="confirm_password"
                           class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"
                           required>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="index.php" 
                   class="px-5 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400">
                    Hủy
                </a>
                <button type="submit" 
                        class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Đổi mật khẩu
                </button>
            </div>
        </form>
    </div>
</div>

<?php include('includes/footer.php'); ?>