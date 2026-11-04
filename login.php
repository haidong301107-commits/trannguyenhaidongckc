<?php
require_once __DIR__ . '/includes/init.php';

$old_mssv = '';

// Nếu đã đăng nhập thì chuyển về trang chủ
if (isset($_SESSION['sinhvien'])) {
  header('Location: ' . INDEX_URL);
  exit();
}

// Xử lý đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $mssv = trim($_POST['mssv'] ?? '');
  $password = $_POST['password'] ?? '';
  $old_mssv = htmlspecialchars($mssv);

  if ($mssv !== '' && $password !== '') {
    $sinh_vien = DB::selectOne(
      "SELECT mssv, hoten, password, trangthai FROM sinh_vien WHERE mssv = :mssv",
      ['mssv' => $mssv]
    );

    if ($sinh_vien) {
      // Kiểm tra trạng thái sinh viên
      if ($sinh_vien['trangthai'] === 'Đã nghỉ') {
        $_SESSION['error'] = 'Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ phòng đào tạo.';
      } else {
        $stored = $sinh_vien['password'];
        $ok = false;

        if ($stored !== '' && password_verify($password, $stored)) {
          $ok = true;
          // Nâng cấp hash nếu cần
          if (password_needs_rehash($stored, PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            DB::update(
              'sinh_vien',
              ['password' => $newHash],
              'mssv = :mssv',
              ['mssv' => $mssv]
            );
          }
        }

        if ($ok) {
          $_SESSION['user_role'] = 'sinhvien';
          $_SESSION['sinhvien'] = $mssv;
          $_SESSION['fullname'] = $sinh_vien['hoten'];
          unset($_SESSION['error']);
          header('Location: ' . INDEX_URL);
          exit();
        } else {
          $_SESSION['error'] = 'Mã số sinh viên hoặc mật khẩu không đúng!' . $mssv . $password;
        }
      }
    } else {
      $_SESSION['error'] = 'Mã số sinh viên hoặc mật khẩu không đúng!';
    }
  } else {
    $_SESSION['error'] = 'Vui lòng nhập đầy đủ mã số sinh viên và mật khẩu.';
  }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng nhập sinh viên | Hệ thống quản lý sinh viên</title>
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- ICON TRANG WEB -->
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🎓</text></svg>">
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

  <!-- CARD LOGIN -->
  <div class="bg-white shadow-xl rounded-2xl w-full max-w-md p-8 space-y-6">

    <!-- HEADER -->
    <div class="text-center">
      <div class="flex justify-center mb-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1a4 4 0 004 4 4 4 0 004-4V6a4 4 0 00-4-4zM3 18a7 7 0 1114 0H3z" clip-rule="evenodd" />
        </svg>
      </div>
      <h1 class="text-2xl font-semibold text-blue-700">Đăng nhập sinh viên</h1>
      <p class="text-gray-500 text-sm mt-1">Hệ thống quản lý thông tin sinh viên</p>
    </div>

    <!-- FORM LOGIN -->
    <form action="login.php" method="POST" class="space-y-5">
      <div>
        <label class="block text-sm font-medium text-gray-700">Mã số sinh viên</label>
        <input type="text" name="mssv" required
          class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
          placeholder="Nhập mã số sinh viên" value="<?php echo $old_mssv; ?>">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Mật khẩu</label>
        <input type="password" name="password" required
          class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
          placeholder="Nhập mật khẩu">
      </div>

      <?php if (isset($_SESSION['success'])): ?>
        <div class="text-sm text-center text-green-600">
          <?php echo htmlspecialchars($_SESSION['success']);
          unset($_SESSION['success']); ?>
        </div>
      <?php endif; ?>

      <?php if (isset($_SESSION['error'])): ?>
        <div class="text-sm text-center text-red-600">
          <?php echo htmlspecialchars($_SESSION['error']);
          unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>

      <button type="submit"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold transition duration-200">
        Đăng nhập
      </button>
    </form>

    <!-- LIÊN KẾT PHỤ -->
    <div class="text-center text-sm text-gray-600">
      <p>Bạn quên mật khẩu? <a href="#" class="text-blue-600 hover:underline">Liên hệ quản trị viên</a></p>
    </div>

    <!-- FOOTER -->
    <div class="text-center text-gray-400 text-xs mt-4">
      © 2025 - Hệ thống quản lý sinh viên
    </div>

  </div>
</body>

</html>