<?php
require_once __DIR__ . '/../includes/init.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ' . INDEX_URL . 'admin/login.php');
    exit();
}

$search = trim($_GET['search'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 5;
$offset = ($page - 1) * $per_page;


$where = [];
$params = [];

if ($search !== '') {
    $where[] = "(k.makhoa LIKE :search_makhoa OR k.tenkhoa LIKE :search_tenkhoa)";
    $params['search_makhoa'] = "%$search%";
    $params['search_tenkhoa'] = "%$search%";
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$total = DB::selectOne(
    "
    SELECT COUNT(*) as count 
    FROM khoa k 
    $whereClause",
    $params
)['count'];

$khoas = DB::select(
    "
    SELECT k.*, COUNT(l.malop) as solop
    FROM khoa k
    LEFT JOIN lop l ON k.makhoa = l.makhoa
    $whereClause
    GROUP BY k.makhoa 
    ORDER BY k.tenkhoa
    LIMIT :offset, :limit",
    array_merge($params, ['offset' => $offset, 'limit' => $per_page])
);

$total_pages = ceil($total / $per_page);

include('../includes/sidebar.php');
include('../includes/header.php');

if (!empty($_SESSION['success'])) {
    echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 max-w-7xl mx-auto">' . htmlspecialchars($_SESSION['success']) . '</div>';
    unset($_SESSION['success']);
}

if (!empty($_SESSION['error'])) {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 max-w-7xl mx-auto">' . htmlspecialchars($_SESSION['error']) . '</div>';
    unset($_SESSION['error']);
}
?>


<div class="max-w-7xl mx-auto px-4">
    <div class="bg-white rounded-xl shadow p-4 mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                <form method="GET" action="" class="flex items-center gap-2">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                        placeholder="Tìm theo mã hoặc tên khoa..."
                        class="px-3 py-2 border rounded-md w-64 focus:ring-2 focus:ring-blue-400 outline-none text-sm" />
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm">
                        🔍 Tìm kiếm
                    </button>
                </form>

                <a href="faculty_add.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm inline-flex items-center">
                    ➕ Thêm khoa
                </a>
            </div>

            <div class="text-gray-500 text-sm">
                Tổng số khoa: <span class="font-semibold text-gray-800"><?php echo $total; ?></span>
            </div>
        </div>
    </div>
</div>
<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-blue-600 text-white">
            <tr>
                <th class="py-3 px-4 text-center" style="width: 60px">STT</th>
                <th class="py-3 px-4 text-left">Mã khoa</th>
                <th class="py-3 px-4 text-left">Tên khoa</th>
                <th class="py-3 px-4 text-left">Số lượng lớp</th>
                <th class="py-3 px-4 text-left">Ghi chú</th>
                <th class="py-3 px-4 text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($khoas as $index => $khoa): ?>
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4 text-center text-gray-600"><?php echo $offset + $index + 1; ?></td>
                    <td class="py-3 px-4"><?php echo htmlspecialchars($khoa['makhoa']); ?></td>
                    <td class="py-3 px-4"><?php echo htmlspecialchars($khoa['tenkhoa']); ?></td>
                    <td class="py-3 px-4"><?php echo $khoa['solop']; ?></td>
                    <td class="py-3 px-4"><?php echo htmlspecialchars($khoa['mota'] ?? ''); ?></td>
                    <td class="py-3 px-4 text-center space-x-2">
                        <a href="faculty_edit.php?makhoa=<?php echo urlencode($khoa['makhoa']); ?>"
                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-md text-xs inline-block">
                            ✏️ Sửa
                        </a>
                        <a href="faculty_delete.php?makhoa=<?php echo urlencode($khoa['makhoa']); ?>"
                            onclick="return confirm('Bạn có chắc muốn xóa khoa \'<?php echo addslashes($khoa['tenkhoa']); ?>\'? Điều này sẽ xóa luôn các lớp trong khoa.');"
                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-xs inline-block">
                            🗑️ Xóa
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (count($khoas) === 0): ?>
                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-500">
                        Không tìm thấy khoa nào <?php echo $search ? "phù hợp với từ khóa '$search'" : "" ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

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
        $range = 2;
        if ($page > $range + 1): ?>
            <a href="?page=1<?php echo $search ? '&search=' . urlencode($search) : ''; ?>"
               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">1</a>
            <?php if ($page > $range + 2): ?>
                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>
            <?php endif;
        endif;

        for ($i = max(1, $page - $range); $i <= min($total_pages, $page + $range); $i++): ?>
            <a href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"
               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?php echo $i === $page ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-50'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor;

        if ($page < $total_pages - $range): ?>
            <?php if ($page < $total_pages - $range - 1): ?>
                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>
            <?php endif; ?>
            <a href="?page=<?php echo $total_pages; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"
               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"><?php echo $total_pages; ?></a>
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