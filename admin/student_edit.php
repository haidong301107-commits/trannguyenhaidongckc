<?php
require_once __DIR__ . '/../includes/init.php';

// Kiểm tra xem có phải admin không
if (!isset($_SESSION['admin'])) {
    header('Location: ' . INDEX_URL . 'admin/login.php');
    exit();
}

$mssv = trim($_GET['mssv'] ?? '');
if ($mssv === '') {
    $_SESSION['error'] = 'Không tìm thấy mã số sinh viên.';
    header('Location: ' . INDEX_URL . 'admin/student_list.php');
    exit();
}

// Lấy thông tin sinh viên hiện tại
$student = DB::selectOne('
    SELECT sv.*, l.tenlop, k.tenkhoa 
    FROM sinh_vien sv 
    LEFT JOIN lop l ON sv.malop = l.malop 
    LEFT JOIN khoa k ON sv.makhoa = k.makhoa 
    WHERE sv.mssv = :mssv', 
    ['mssv' => $mssv]
);

if (!$student) {
    $_SESSION['error'] = 'Không tìm thấy sinh viên với MSSV: ' . htmlspecialchars($mssv);
    header('Location: ' . INDEX_URL . 'admin/student_list.php');
    exit();
}

// Lấy danh sách khoa
$khoas = DB::select('SELECT makhoa, tenkhoa FROM khoa ORDER BY tenkhoa');
// Lấy danh sách lớp
$lops = DB::select('SELECT malop, tenlop, makhoa FROM lop ORDER BY tenlop');

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
    <h3 class="text-xl font-semibold mb-4 text-gray-700 border-b pb-2">📝 Cập nhật thông tin sinh viên</h3>

    <form action="student_edit_process.php" method="POST" class="space-y-4">
        <input type="hidden" name="mssv" value="<?php echo htmlspecialchars($student['mssv']); ?>">
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mã số sinh viên</label>
                <div class="w-full border rounded-md px-3 py-2 bg-gray-100 text-gray-600">
                    <?php echo htmlspecialchars($student['mssv']); ?>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên</label>
                <input type="text" name="hoten" required value="<?php echo htmlspecialchars($student['hoten']); ?>"
                       class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ngày sinh</label>
                <input type="date" name="ngaysinh" value="<?php echo htmlspecialchars($student['ngaysinh'] ?? ''); ?>"
                       class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Giới tính</label>
                <select name="gioitinh" class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
                    <option value="Nam" <?php echo $student['gioitinh'] === 'Nam' ? 'selected' : ''; ?>>Nam</option>
                    <option value="Nữ" <?php echo $student['gioitinh'] === 'Nữ' ? 'selected' : ''; ?>>Nữ</option>
                    <option value="Khác" <?php echo $student['gioitinh'] === 'Khác' ? 'selected' : ''; ?>>Khác</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Khoa</label>
                <select id="makhoa" name="makhoa" class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
                    <option value="">-- Chọn khoa --</option>
                    <?php foreach ($khoas as $khoa): ?>
                        <option value="<?= htmlspecialchars($khoa['makhoa']) ?>" 
                                <?php echo $student['makhoa'] === $khoa['makhoa'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($khoa['tenkhoa']) ?> (<?= htmlspecialchars($khoa['makhoa']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Lớp</label>
                <select id="malop" name="malop" class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
                    <option value="">-- Chọn lớp --</option>
                    <?php foreach ($lops as $lop): ?>
                        <option value="<?= htmlspecialchars($lop['malop']) ?>" 
                                data-makhoa="<?= htmlspecialchars($lop['makhoa']) ?>"
                                <?php echo $student['malop'] === $lop['malop'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($lop['tenlop']) ?> (<?= htmlspecialchars($lop['malop']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($student['email'] ?? ''); ?>"
                       class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                <input type="text" name="sdt" value="<?php echo htmlspecialchars($student['sdt'] ?? ''); ?>"
                       class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                <input type="text" name="diachi" value="<?php echo htmlspecialchars($student['diachi'] ?? ''); ?>"
                       class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
            <select name="trangthai" class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
                <option value="Đang học" <?php echo $student['trangthai'] === 'Đang học' ? 'selected' : ''; ?>>Đang học</option>
                <option value="Tốt nghiệp" <?php echo $student['trangthai'] === 'Tốt nghiệp' ? 'selected' : ''; ?>>Tốt nghiệp</option>
                <option value="Bảo lưu" <?php echo $student['trangthai'] === 'Bảo lưu' ? 'selected' : ''; ?>>Bảo lưu</option>
                <option value="Đã nghỉ" <?php echo $student['trangthai'] === 'Đã nghỉ' ? 'selected' : ''; ?>>Đã nghỉ</option>
            </select>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <a href="student_list.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm">Hủy</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">Lưu thay đổi</button>
        </div>
    </form>
</div>

<script>
// Lọc lớp theo khoa
document.addEventListener('DOMContentLoaded', function() {
    const makhoaSelect = document.getElementById('makhoa');
    const malopSelect = document.getElementById('malop');
    const allOptions = Array.from(malopSelect.options);

    makhoaSelect.addEventListener('change', function() {
        const makhoa = this.value;
        // Xóa hết options (trừ option đầu)
        malopSelect.innerHTML = '';
        malopSelect.appendChild(allOptions[0].cloneNode(true));
        if (!makhoa) {
            return;
        }
        allOptions.forEach(function(opt, idx) {
            if (idx === 0) return;
            if (opt.getAttribute('data-makhoa') === makhoa) {
                malopSelect.appendChild(opt.cloneNode(true));
            }
        });
    });
});
</script>

<?php include('../includes/footer.php'); ?>