<?php
require_once __DIR__ . '/../includes/init.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ' . INDEX_URL . 'admin/login.php');
    exit();
}

include('../includes/sidebar.php');
include('../includes/header.php');
?>

<?php
if (!empty($_SESSION['error'])) {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 max-w-3xl mx-auto">'.htmlspecialchars($_SESSION['error']).'</div>';
    unset($_SESSION['error']);
}
?>

<div class="bg-white shadow rounded-xl p-6 max-w-3xl mx-auto">
    <h3 class="text-xl font-semibold mb-4 text-gray-700 border-b pb-2">➕ Thêm khoa mới</h3>

    <form action="faculty_add_process.php" method="POST" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mã khoa <span class="text-red-500">*</span></label>
                <input type="text" name="makhoa" required maxlength="20"
                       class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên khoa <span class="text-red-500">*</span></label>
                <input type="text" name="tenkhoa" required maxlength="100"
                       class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                <textarea name="mota" rows="3"
                          class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none"
                          placeholder="Mô tả thêm về khoa (không bắt buộc)"></textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <a href="faculty_list.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm">Hủy</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">Thêm khoa</button>
        </div>
    </form>
</div>

<?php include('../includes/footer.php'); ?>