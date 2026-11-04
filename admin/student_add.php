<?php
require_once __DIR__ . '/../includes/init.php';

// Kiểm tra xem có phải admin không
if (!isset($_SESSION['admin'])) {
    header('Location: ' . INDEX_URL . 'admin/login.php');
    exit();
}

// Lấy danh sách khoa
$khoas = DB::select('SELECT makhoa, tenkhoa FROM khoa ORDER BY tenkhoa');
// Lấy danh sách lớp
$lops = DB::select('SELECT malop, tenlop, makhoa FROM lop ORDER BY tenlop');

include('../includes/sidebar.php');
include('../includes/header.php');
?>
<?php
// Hiển thị thông báo (nếu có) khi quay lại form
if (!empty($_SESSION['success'])) {
    echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 max-w-3xl mx-auto">'.htmlspecialchars($_SESSION['success']).'</div>';
    unset($_SESSION['success']);
}
if (!empty($_SESSION['error'])) {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 max-w-3xl mx-auto">'.htmlspecialchars($_SESSION['error']).'</div>';
    unset($_SESSION['error']);
}

?>
<div class="bg-white shadow rounded-xl p-6 max-w-3xl mx-auto">
    <h3 class="text-xl font-semibold mb-4 text-gray-700 border-b pb-2">📋 Thông tin sinh viên</h3>

    <form action="student_add_process.php" method="POST" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mã số sinh viên <span class="text-red-500">*</span></label>
                <input type="text" name="mssv" required class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên <span class="text-red-500">*</span></label>
                <input type="text" name="hoten" required class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ngày sinh</label>
                <input type="date" name="ngaysinh" class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Giới tính</label>
                <select name="gioitinh" class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
                    <option value="Nam">Nam</option>
                    <option value="Nữ">Nữ</option>
                    <option value="Khác">Khác</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Khoa</label>
                <select id="makhoa" name="makhoa" class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
                    <option value="">-- Chọn khoa --</option>
                    <?php foreach ($khoas as $khoa): ?>
                        <option value="<?= htmlspecialchars($khoa['makhoa']) ?>">
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
                        <option value="<?= htmlspecialchars($lop['malop']) ?>" data-makhoa="<?= htmlspecialchars($lop['makhoa']) ?>">
                            <?= htmlspecialchars($lop['tenlop']) ?> (<?= htmlspecialchars($lop['malop']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                <input type="text" name="sdt" class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                <input type="text" name="diachi" class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
            <select name="trangthai" class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
                <option value="Đang học">Đang học</option>
                <option value="Tốt nghiệp">Tốt nghiệp</option>
                <option value="Bảo lưu">Bảo lưu</option>
                <option value="Đã nghỉ">Đã nghỉ</option>
            </select>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <a href="student_list.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm">Hủy</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">Thêm sinh viên</button>
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
            // Nếu chưa chọn khoa, chỉ hiện option đầu
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