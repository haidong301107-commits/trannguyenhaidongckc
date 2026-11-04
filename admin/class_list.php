<?php
require_once __DIR__ . '/../includes/init.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ' . INDEX_URL . 'admin/login.php');
    exit();
}

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

$search = trim($_GET['search'] ?? '');
$makhoa = trim($_GET['makhoa'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 5;
$offset = ($page - 1) * $per_page;

$where = [];
$params = [];

if ($search !== '') {

    $where[] = "(l.malop LIKE :search_malop OR l.tenlop LIKE :search_tenlop OR k.tenkhoa LIKE :search_tenkhoa)";
    $params['search_malop'] = "%$search%";
    $params['search_tenlop'] = "%$search%";
    $params['search_tenkhoa'] = "%$search%";
}

if ($makhoa !== '') {
    $where[] = 'l.makhoa = :makhoa';
    $params['makhoa'] = $makhoa;
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$sqlTotal = 'SELECT COUNT(DISTINCT l.malop) as count FROM lop l LEFT JOIN khoa k ON l.makhoa = k.makhoa ' . $whereClause;
$paramsTotal = [];
if (isset($params['search_malop'])) $paramsTotal['search_malop'] = $params['search_malop'];
if (isset($params['search_tenlop'])) $paramsTotal['search_tenlop'] = $params['search_tenlop'];
if (isset($params['search_tenkhoa'])) $paramsTotal['search_tenkhoa'] = $params['search_tenkhoa'];
if (isset($params['makhoa'])) $paramsTotal['makhoa'] = $params['makhoa'];
$totalRow = DB::selectOne($sqlTotal, $paramsTotal);
$total = $totalRow ? (int) $totalRow['count'] : 0;

$sqlLops = 'SELECT l.malop, l.tenlop, l.makhoa, k.tenkhoa, COUNT(sv.mssv) as sosv FROM lop l '
    . 'LEFT JOIN khoa k ON l.makhoa = k.makhoa '
    . 'LEFT JOIN sinh_vien sv ON l.malop = sv.malop '
    . $whereClause
    . ' GROUP BY l.malop '
    . 'ORDER BY l.malop DESC '
    . ' LIMIT :offset, :limit';

$lops = DB::select($sqlLops, array_merge($params, ['offset' => $offset, 'limit' => $per_page]));

// Danh sách khoa cho filter
$khoas = DB::select('SELECT makhoa, tenkhoa FROM khoa ORDER BY tenkhoa');

$total_pages = max(1, (int) ceil($total / $per_page));
?>

<div class="max-w-7xl mx-auto px-4">
    <div class="bg-white rounded-xl shadow p-4 mb-4">
        <form method="GET" action="" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Tìm theo mã lớp, tên lớp..." 
                       class="w-full sm:w-80 px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-400 outline-none text-sm" />

                <select name="makhoa" class="px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-400 outline-none text-sm">
                    <option value="">Tất cả khoa</option>
                    <?php foreach ($khoas as $khoa): ?>
                        <option value="<?php echo htmlspecialchars($khoa['makhoa']); ?>" 
                                <?php echo $khoa['makhoa'] === $makhoa ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($khoa['tenkhoa']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm">
                    🔍 Tìm kiếm
                </button>

                <a href="class_add.php" class="ml-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm inline-flex items-center">
                    ➕ Thêm lớp
                </a>
            </div>

            <div class="mt-2 sm:mt-0 text-sm text-gray-600">
                <div>Tổng số lớp: <span class="font-semibold text-gray-800"><?php echo $total; ?></span></div>
            </div>
        </form>
    </div>
</div>
<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-blue-600 text-white">
            <tr>
                <th class="py-3 px-4 text-center" style="width: 60px">STT</th>
                <th class="py-3 px-4 text-left">Mã lớp</th>
                <th class="py-3 px-4 text-left">Tên lớp</th>
                <th class="py-3 px-4 text-left">Khoa</th>
                <th class="py-3 px-4 text-left">Sĩ số</th>
                <th class="py-3 px-4 text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($lops as $index => $lop): ?>
            <tr class="hover:bg-gray-50">
                <td class="py-3 px-4 text-center text-gray-600"><?php echo $offset + $index + 1; ?></td>
                <td class="py-3 px-4"><?php echo htmlspecialchars($lop['malop']); ?></td>
                <td class="py-3 px-4"><?php echo htmlspecialchars($lop['tenlop']); ?></td>
                <td class="py-3 px-4"><?php echo htmlspecialchars($lop['tenkhoa']); ?></td>
                <td class="py-3 px-4"><?php echo (int) $lop['sosv']; ?></td>
                <td class="py-3 px-4 text-center space-x-2">
                    <a href="class_edit.php?malop=<?php echo urlencode($lop['malop']); ?>" 
                       class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-md text-xs inline-block">
                       ✏️ Sửa
                    </a>
                    <a href="class_delete.php?malop=<?php echo urlencode($lop['malop']); ?>" 
                       onclick="return confirm('Bạn có chắc muốn xóa lớp \'<?php echo addslashes($lop['tenlop']); ?>\'?');"
                       class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-xs inline-block">
                       🗑️ Xóa
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (count($lops) === 0): ?>
            <tr>
                <td colspan="6" class="py-8 text-center text-gray-500">
                    Không tìm thấy lớp nào <?php 
                    echo $search ? "phù hợp với từ khóa '" . htmlspecialchars($search) . "'" : "";
                    echo $makhoa ? ($search ? " và " : "") . "thuộc khoa đã chọn" : "";
                    ?>
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
        <a href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $makhoa ? '&makhoa=' . urlencode($makhoa) : ''; ?>" 
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
            <a href="?page=1<?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $makhoa ? '&makhoa=' . urlencode($makhoa) : ''; ?>"
               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">1</a>
            <?php if ($page > $range + 2): ?>
                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>
            <?php endif;
        endif;

        for ($i = max(1, $page - $range); $i <= min($total_pages, $page + $range); $i++): ?>
            <a href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $makhoa ? '&makhoa=' . urlencode($makhoa) : ''; ?>"
               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?php echo $i === $page ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-50'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor;

        if ($page < $total_pages - $range): ?>
            <?php if ($page < $total_pages - $range - 1): ?>
                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>
            <?php endif; ?>
            <a href="?page=<?php echo $total_pages; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $makhoa ? '&makhoa=' . urlencode($makhoa) : ''; ?>"
               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"><?php echo $total_pages; ?></a>
        <?php endif; ?>

        <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $makhoa ? '&makhoa=' . urlencode($makhoa) : ''; ?>"
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