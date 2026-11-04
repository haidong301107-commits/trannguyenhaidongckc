<?php
require_once __DIR__ . '/../includes/init.php';

// Kiểm tra xem có phải admin không
if (!isset($_SESSION['admin'])) {
    header('Location: ' . INDEX_URL . 'admin/login.php');
    exit();
}

$malop = trim($_GET['malop'] ?? '');
if ($malop === '') {
    $_SESSION['error'] = 'Không tìm thấy mã lớp.';
    header('Location: ' . INDEX_URL . 'admin/class_list.php');
    exit();
}

// Lấy thông tin lớp hiện tại
$lop = DB::selectOne('
    SELECT l.malop, l.tenlop, l.makhoa, k.tenkhoa, COUNT(sv.mssv) as sosv 
    FROM lop l 
    LEFT JOIN khoa k ON l.makhoa = k.makhoa 
    LEFT JOIN sinh_vien sv ON l.malop = sv.malop 
    WHERE l.malop = :malop 
    GROUP BY l.malop', 
    ['malop' => $malop]
);

if (!$lop) {
    $_SESSION['error'] = 'Không tìm thấy lớp với mã: ' . htmlspecialchars($malop);
    header('Location: ' . INDEX_URL . 'admin/class_list.php');
    exit();
}

// Lấy danh sách khoa
$khoas = DB::select('SELECT makhoa, tenkhoa FROM khoa ORDER BY tenkhoa');

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
    <h3 class="text-xl font-semibold mb-4 text-gray-700 border-b pb-2">📝 Cập nhật thông tin lớp</h3>

    <form action="class_edit_process.php" method="POST" class="space-y-4">
        <input type="hidden" name="malop" value="<?php echo htmlspecialchars($lop['malop']); ?>">
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mã lớp</label>
                <div class="w-full border rounded-md px-3 py-2 bg-gray-100 text-gray-600">
                    <?php echo htmlspecialchars($lop['malop']); ?>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên lớp</label>
                <input type="text" name="tenlop" required maxlength="50"
                       value="<?php echo htmlspecialchars($lop['tenlop']); ?>"
                       class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Khoa</label>
                <select name="makhoa" required class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
                    <option value="">-- Chọn khoa --</option>
                    <?php foreach ($khoas as $khoa): ?>
                        <option value="<?= htmlspecialchars($khoa['makhoa']) ?>" 
                                <?php echo $lop['makhoa'] === $khoa['makhoa'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($khoa['tenkhoa']) ?> (<?= htmlspecialchars($khoa['makhoa']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="sm:col-span-2">
                <p class="text-sm text-gray-600">
                    Lớp này hiện có <strong><?php echo $lop['sosv']; ?></strong> sinh viên.
                </p>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <a href="class_list.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm">Hủy</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">Lưu thay đổi</button>
        </div>
    </form>
</div>

<?php include('../includes/footer.php'); ?>