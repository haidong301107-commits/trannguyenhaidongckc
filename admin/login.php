
<?php
require_once __DIR__ . '/../includes/init.php';

// Nếu đã đăng nhập quản trị viên
if (isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit;
}

$old_username = '';

// Xử lý biểu mẫu đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $old_username = $username;

    if ($username === '' || $password === '') {
        $_SESSION['err_admin'] =
            'Vui lòng nhập tên đăng nhập và mật khẩu!';
    } else {
        try {
            $user = DB::selectOne(
                'SELECT * FROM quan_tri_vien
                 WHERE username = :u LIMIT 1',
                ['u' => $username]
            );

            $stored = $user['password'] ?? '';

            if (
                $user &&
                $stored !== '' &&
                password_verify($password, $stored)
            ) {
                // Cập nhật thời gian đăng nhập trước khi tạo session
                DB::execute(
                    'UPDATE quan_tri_vien
                     SET last_login = NOW()
                     WHERE username = :u',
                    ['u' => $username]
                );

                // Tạo session mới sau khi xác thực thành công
                session_regenerate_id(true);

                $_SESSION['user_role'] = 'admin';
                $_SESSION['admin'] = $username;
                $_SESSION['fullname'] = $user['hoten'] ?? '';

                unset($_SESSION['err_admin']);

                header('Location: index.php');
                exit;
            }

            $_SESSION['err_admin'] =
                'Tên đăng nhập hoặc mật khẩu không đúng!';

        } catch (Throwable $e) {
            error_log(
                'Lỗi đăng nhập admin: ' . $e->getMessage()
            );

            $_SESSION['err_admin'] =
                'Đã xảy ra lỗi hệ thống. Vui lòng thử lại sau!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Đăng nhập quản trị viên | Quản lý sinh viên</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="icon"
        href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🎓</text></svg>">
</head>

<body class="bg-gradient-to-br from-slate-900 to-blue-800
             flex items-center justify-center min-h-screen p-4">

    <div class="bg-white shadow-2xl rounded-2xl
                w-full max-w-md p-8 space-y-6">

        <!-- Tiêu đề -->
        <div class="text-center">
            <div class="text-5xl mb-4">🔐</div>

            <h1 class="text-2xl font-bold text-blue-700">
                Quản trị hệ thống
            </h1>

            <p class="text-gray-500 text-sm mt-2">
                Hệ thống quản lý thông tin sinh viên
            </p>
        </div>

        <!-- Thông báo lỗi -->
        <?php if (!empty($_SESSION['err_admin'])): ?>
            <div class="bg-red-50 border border-red-200
                        text-red-700 text-sm rounded-lg p-3"
                 role="alert">
                <?= htmlspecialchars(
                    $_SESSION['err_admin'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </div>

            <?php unset($_SESSION['err_admin']); ?>
        <?php endif; ?>

        <!-- Form đăng nhập -->
        <form action="login.php" method="POST" class="space-y-5">

            <div>
                <label for="username"
                       class="block text-sm font-medium text-gray-700">
                    Tên đăng nhập
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    required
                    autocomplete="username"
                    value="<?= htmlspecialchars(
                        $old_username,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    placeholder="Nhập tên đăng nhập"
                    class="mt-1 w-full px-4 py-3 border border-gray-300
                           rounded-lg focus:ring-2 focus:ring-blue-500
                           focus:outline-none"
                >
            </div>

            <div>
                <label for="password"
                       class="block text-sm font-medium text-gray-700">
                    Mật khẩu
                </label>

                <div class="relative mt-1">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Nhập mật khẩu"
                        class="w-full px-4 py-3 pr-16 border border-gray-300
                               rounded-lg focus:ring-2 focus:ring-blue-500
                               focus:outline-none"
                    >

                    <button
                        type="button"
                        id="togglePassword"
                        class="absolute inset-y-0 right-3
                               text-sm text-blue-600 font-medium"
                    >
                        Hiện
                    </button>
                </div>
            </div>

            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700
                       text-white py-3 rounded-lg font-semibold
                       transition duration-200"
            >
                Đăng nhập
            </button>
        </form>

        <!-- Liên hệ -->
        <div class="text-center text-sm text-gray-500">
            Quên mật khẩu?
            <span class="text-blue-600">
                Liên hệ quản trị hệ thống
            </span>
        </div>

        <!-- Chân trang -->
        <div class="text-center text-gray-400 text-xs">
            &copy; <?= date('Y') ?> - Hệ thống quản lý sinh viên
        </div>

    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const toggleButton = document.getElementById('togglePassword');

        toggleButton.addEventListener('click', function () {
            const showPassword = passwordInput.type === 'password';

            passwordInput.type = showPassword ? 'text' : 'password';
            toggleButton.textContent = showPassword ? 'Ẩn' : 'Hiện';
        });
    </script>

</body>
</html>
