<!-- ===== MAIN CONTENT ===== -->
<?php
// Xác định tiêu đề trang dựa vào URL hiện tại
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = "Hệ thống quản lý sinh viên";

if (strpos($current_page, 'student') !== false) {
    $page_title = "Quản lý sinh viên";
} elseif (strpos($current_page, 'class') !== false) {
    $page_title = "Quản lý lớp học";
} elseif (strpos($current_page, 'faculty') !== false) {
    $page_title = "Quản lý khoa";
}
?>

<!-- Header -->
<header class="sticky top-0 z-20 bg-white shadow-md">
  <div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center h-16">
      <!-- Left side: Title with proper spacing for toggle button on mobile -->
      <div class="flex items-center">
        <div class="w-16 md:w-0"></div> <!-- Spacing for toggle button on mobile -->
        <h2 class="text-xl md:text-2xl font-semibold text-blue-700 truncate">
          <?php echo htmlspecialchars($page_title); ?>
        </h2>
      </div>

      <!-- Right side: User info and logout -->
      <div class="flex items-center gap-4 px-4">
        <div class="flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1a4 4 0 004 4 4 4 0 004-4V6a4 4 0 00-4-4zM3 18a7 7 0 1114 0H3z" clip-rule="evenodd" />
          </svg>
          <span class="text-sm font-medium hidden md:block">
            <?php
              if (isset($_SESSION['fullname'])) {
                  echo htmlspecialchars($_SESSION['fullname']);
              } else {
                  echo 'Khách';
              }
            ?>
          </span>
        </div>
        <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md no-underline">
          <span class="md:inline hidden">Đăng xuất</span>
          <span class="md:hidden inline">Thoát</span>
        </a>
      </div>
  </header>

<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 py-6 md:px-6 space-y-6">