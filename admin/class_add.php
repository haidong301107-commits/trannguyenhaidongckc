<?php
require_once __DIR__ . '/../includes/init.php';

// Kiểm tra xem có phải admin không
if (!isset($_SESSION['admin'])) {
    header('Location: ' . INDEX_URL . 'admin/login.php');
    exit();
}

// Lấy danh sách khoa
$khoas = DB::select('SELECT makhoa, tenkhoa FROM khoa ORDER BY tenkhoa');

include('../includes/sidebar.php');
include('../includes/header.php');
?>

<?php
// Hiển thị thông báo (nếu có) khi quay lại form
if (!empty($_SESSION['error'])) {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 max-w-3xl mx-auto">'.htmlspecialchars($_SESSION['error']).'</div>';
    unset($_SESSION['error']);
}
?>

<div class="bg-white shadow rounded-xl p-6 max-w-3xl mx-auto">
    <h3 class="text-xl font-semibold mb-4 text-gray-700 border-b pb-2">➕ Thêm lớp mới</h3>

    <form action="class_add_process.php" method="POST" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mã lớp <span class="text-red-500">*</span></label>
                <input type="text" name="malop" required maxlength="10"
                       class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên lớp <span class="text-red-500">*</span></label>
                <input type="text" name="tenlop" required maxlength="50"
                       class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Khoa <span class="text-red-500">*</span></label>
                <select name="makhoa" required class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
                    <option value="">-- Chọn khoa --</option>
                    <?php foreach ($khoas as $khoa): ?>
                        <option value="<?= htmlspecialchars($khoa['makhoa']) ?>">
                            <?= htmlspecialchars($khoa['tenkhoa']) ?> (<?= htmlspecialchars($khoa['makhoa']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <a href="class_list.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm">Hủy</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">Thêm lớp</button>
        </div>
    </form>
</div>

<?php include('../includes/footer.php'); ?>