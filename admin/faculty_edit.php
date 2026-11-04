<?php
require_once __DIR__ . '/../includes/init.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ' . INDEX_URL . 'admin/login.php');
    exit();
}

$makhoa = trim($_GET['makhoa'] ?? '');
if ($makhoa === '') {
    $_SESSION['error'] = 'Không tìm thấy mã khoa.';
    header('Location: ' . INDEX_URL . 'admin/faculty_list.php');
    exit();
}

$khoa = DB::selectOne('
    SELECT k.*, COUNT(l.malop) as solop 
    FROM khoa k 
    LEFT JOIN lop l ON k.makhoa = l.makhoa 
    WHERE k.makhoa = :makhoa 
    GROUP BY k.makhoa', 
    ['makhoa' => $makhoa]
);

if (!$khoa) {
    $_SESSION['error'] = 'Không tìm thấy khoa với mã: ' . htmlspecialchars($makhoa);
    header('Location: ' . INDEX_URL . 'admin/faculty_list.php');
    exit();
}

include('../includes/sidebar.php');
include('../includes/header.php');
?>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 max-w-3xl mx-auto">
        <?php echo htmlspecialchars($_SESSION['error']); ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="bg-white shadow rounded-xl p-6 max-w-3xl mx-auto">
    <h3 class="text-xl font-semibold mb-4 text-gray-700 border-b pb-2">📝 Cập nhật thông tin khoa</h3>

    <form action="faculty_edit_process.php" method="POST" class="space-y-4">
        <input type="hidden" name="makhoa" value="<?php echo htmlspecialchars($khoa['makhoa']); ?>">
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mã khoa</label>
                <div class="w-full border rounded-md px-3 py-2 bg-gray-100 text-gray-600">
                    <?php echo htmlspecialchars($khoa['makhoa']); ?>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên khoa <span class="text-red-500">*</span></label>
                <input type="text" name="tenkhoa" required maxlength="100"
                       value="<?php echo htmlspecialchars($khoa['tenkhoa']); ?>"
                       class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none"
                       placeholder="Tối đa 100 ký tự">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                <textarea name="mota" rows="3"
                          class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none"
                          placeholder="Mô tả thêm về khoa (không bắt buộc)"><?php echo htmlspecialchars($khoa['mota'] ?? ''); ?></textarea>
            </div>

            <div class="sm:col-span-2">
                <p class="text-sm text-gray-600">
                    Khoa này hiện có <strong><?php echo $khoa['solop']; ?></strong> lớp.
                </p>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <a href="faculty_list.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm">Hủy</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">Lưu thay đổi</button>
        </div>
    </form>
</div>

<?php include('../includes/footer.php'); ?>