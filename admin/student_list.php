<?php
require_once __DIR__ . '/../includes/init.php';

// Kiểm tra xem có phải admin không
if (!isset($_SESSION['admin'])) {
  header('Location: ' . INDEX_URL . 'admin/login.php');
  exit();
}

include('../includes/sidebar.php');
include('../includes/header.php');
?>

<?php
// Hiển thị thông báo thành công/thất bại từ các thao tác (thêm, sửa, xóa)
if (!empty($_SESSION['success'])) {
    echo '<div class="max-w-7xl mx-auto px-4 mb-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                ' . htmlspecialchars($_SESSION['success']) . '
            </div>
          </div>';
    unset($_SESSION['success']);
}
if (!empty($_SESSION['error'])) {
    echo '<div class="max-w-7xl mx-auto px-4 mb-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                ' . htmlspecialchars($_SESSION['error']) . '
            </div>
          </div>';
    unset($_SESSION['error']);
}

// Xử lý các tham số tìm kiếm và phân trang
$search = trim($_GET['search'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 5;
$offset = ($page - 1) * $per_page;

// Chuẩn bị câu query
$where = [];
$params = [];

if ($search !== '') {
    $where[] = "(sv.mssv LIKE :search OR sv.hoten LIKE :search OR l.tenlop LIKE :search OR k.tenkhoa LIKE :search)";
    $params['search'] = "%$search%";
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Query lấy tổng số sinh viên
$count_query = "
    SELECT COUNT(DISTINCT sv.mssv) as total 
    FROM sinh_vien sv
    LEFT JOIN lop l ON sv.malop = l.malop
    LEFT JOIN khoa k ON l.makhoa = k.makhoa
    $whereClause
";
$total = DB::selectOne($count_query, $params)['total'];
$total_pages = ceil($total / $per_page);
?>
<!-- THANH TÌM KIẾM VÀ THÊM MỚI -->
<div class="bg-white rounded-xl shadow p-4">
    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
        <form method="GET" class="flex items-center gap-2 flex-1">
            <div class="flex-1">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Tìm mã hoặc tên sinh viên..." 
                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-400 outline-none text-sm" />
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm inline-flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
                Tìm kiếm
            </button>
        </form>
        
        <div class="flex items-center gap-4">
            <div class="text-gray-500 text-sm whitespace-nowrap">
                Tổng sinh viên: <span class="font-semibold text-gray-800"><?php echo number_format($total); ?></span>
            </div>
            <a href="student_add.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm inline-flex items-center gap-2 whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Thêm sinh viên
            </a>
        </div>
    </div>
</div>

<?php
// Query lấy danh sách sinh viên với phân trang
$query = "
    SELECT 
        sv.mssv,
        sv.hoten,
        sv.email,
        sv.sdt,
        sv.gioitinh,
        sv.diachi,
        sv.trangthai,
        l.tenlop,
        k.tenkhoa
    FROM sinh_vien sv
    LEFT JOIN lop l ON sv.malop = l.malop
    LEFT JOIN khoa k ON l.makhoa = k.makhoa
    $whereClause
    LIMIT :limit OFFSET :offset
";

$params['offset'] = $offset;
$params['limit'] = $per_page;
$students = DB::select($query, $params);
?>

<!-- BẢNG DANH SÁCH SINH VIÊN -->
<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-blue-600 text-white">
            <tr>
                <th class="py-3 px-4 text-center" style="width: 60px">STT</th>
                <th class="py-3 px-4 text-left">Mã SV</th>
                <th class="py-3 px-4 text-left">Họ và tên</th>
                <th class="py-3 px-4 text-left">Lớp</th>
                <th class="py-3 px-4 text-left">Khoa</th>
                <th class="py-3 px-4 text-left">Email</th>
                <th class="py-3 px-4 text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php if (empty($students)): ?>
            <tr>
                <td colspan="7" class="py-4 px-4 text-center text-gray-500">
                    Không tìm thấy sinh viên nào
                    <?php if ($search !== ''): ?>
                        phù hợp với từ khóa "<?php echo htmlspecialchars($search); ?>"
                    <?php endif; ?>
                </td>
            </tr>
            <?php else: ?>
                <?php foreach ($students as $index => $student): ?>
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4 text-center text-gray-600">
                        <?php echo $offset + $index + 1; ?>
                    </td>
                    <td class="py-3 px-4"><?php echo htmlspecialchars($student['mssv']); ?></td>
                    <td class="py-3 px-4"><?php echo htmlspecialchars($student['hoten']); ?></td>
                    <td class="py-3 px-4"><?php echo htmlspecialchars($student['tenlop']); ?></td>
                    <td class="py-3 px-4"><?php echo htmlspecialchars($student['tenkhoa']); ?></td>
                    <td class="py-3 px-4"><?php echo htmlspecialchars($student['email']); ?></td>
                    <td class="py-3 px-4 text-center space-x-2">
                        <a href="student_edit.php?mssv=<?php echo urlencode($student['mssv']); ?>" 
                           class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-md text-xs inline-flex items-center gap-1">
                           <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                            Sửa
                        </a>
                        <a href="student_delete.php?mssv=<?php echo urlencode($student['mssv']); ?>" 
                           onclick="return confirm('Bạn có chắc chắn muốn xóa sinh viên này?')"
                           class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-xs inline-flex items-center gap-1">
                           <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Xóa
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- PHÂN TRANG -->
<?php if ($total_pages > 1): ?>
<div class="mt-6 flex justify-center">
    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Phân trang">
        <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
           class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
            <span class="sr-only">Trang trước</span>
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
        </a>
        <?php endif; ?>

        <?php
        // Logic hiển thị phân trang
        $range = 2; // Số trang hiển thị trước và sau trang hiện tại
        
        // Luôn hiển thị trang đầu
        if ($page > $range + 1): ?>
            <a href="?page=1<?php echo $search ? '&search=' . urlencode($search) : ''; ?>"
               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                1
            </a>
            <?php if ($page > $range + 2): ?>
                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                    ...
                </span>
            <?php endif;
        endif;

        // Hiển thị các trang xung quanh trang hiện tại
        for ($i = max(1, $page - $range); $i <= min($total_pages, $page + $range); $i++): ?>
            <a href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"
               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?php echo $i === $page ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-50'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor;

        // Luôn hiển thị trang cuối
        if ($page < $total_pages - $range): ?>
            <?php if ($page < $total_pages - $range - 1): ?>
                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                    ...
                </span>
            <?php endif; ?>
            <a href="?page=<?php echo $total_pages; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"
               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                <?php echo $total_pages; ?>
            </a>
        <?php endif; ?>

        <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"
           class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
            <span class="sr-only">Trang sau</span>
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
        </a>
        <?php endif; ?>
    </nav>
</div>
<?php endif; ?>

<?php include('../includes/footer.php'); ?>