<?php
require_once __DIR__ . '/includes/init.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['sinhvien'])) {
    header('Location: ' . INDEX_URL . 'login.php');
    exit();
}

// Xử lý phân trang
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Nếu có id thông báo, hiển thị chi tiết
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $thong_bao = DB::selectOne(
        "SELECT * FROM thong_bao WHERE id = :id",
        ['id' => $id]
    );

    if (!$thong_bao) {
        $_SESSION['error'] = 'Không tìm thấy thông báo!';
        header('Location: notifications.php');
        exit();
    }
} else {
    // Lấy tổng số thông báo
    $total = DB::selectOne(
        "SELECT COUNT(*) as count FROM thong_bao"
    )['count'];

    // Lấy danh sách thông báo theo trang
    $thong_bao_list = DB::select(
        "SELECT * FROM thong_bao 
        ORDER BY created_at DESC 
        LIMIT :offset, :limit",
        ['offset' => $offset, 'limit' => $per_page]
    );

    $total_pages = ceil($total / $per_page);
}

include('includes/sidebar.php');
include('includes/header.php');

// Hiển thị thông báo
if (!empty($_SESSION['success'])) {
    echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 max-w-7xl mx-auto">' 
        . htmlspecialchars($_SESSION['success']) . '</div>';
    unset($_SESSION['success']);
}
if (!empty($_SESSION['error'])) {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 max-w-7xl mx-auto">' 
        . htmlspecialchars($_SESSION['error']) . '</div>';
    unset($_SESSION['error']);
}

// Hiển thị chi tiết thông báo nếu có id
if (isset($_GET['id']) && isset($thong_bao)): ?>
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow p-6">
            <!-- Tiêu đề và ngày tạo -->
            <div class="border-b pb-4 mb-4">
                <h1 class="text-2xl font-semibold text-gray-800 mb-2">
                    <?php echo htmlspecialchars($thong_bao['tieude']); ?>
                </h1>
                <p class="text-sm text-gray-500">
                    Đăng ngày: <?php echo date('d/m/Y H:i', strtotime($thong_bao['created_at'])); ?>
                </p>
            </div>

            <!-- Nội dung thông báo -->
            <div class="prose max-w-none">
                <?php echo nl2br(htmlspecialchars($thong_bao['noidung'])); ?>
            </div>

            <!-- Nút quay lại -->
            <div class="mt-6 border-t pt-4">
                <a href="notifications.php" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Quay lại danh sách thông báo
                </a>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Danh sách thông báo -->
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    📢 Tất cả thông báo
                </h2>

                <?php if (count($thong_bao_list) > 0): ?>
                    <div class="space-y-4">
                        <?php foreach ($thong_bao_list as $tb): ?>
                            <div class="border-b pb-4">
                                <h3 class="font-medium mb-1">
                                    <a href="?id=<?php echo $tb['id']; ?>" 
                                       class="text-blue-600 hover:text-blue-800">
                                        <?php echo htmlspecialchars($tb['tieude']); ?>
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-500">
                                    <?php echo date('d/m/Y H:i', strtotime($tb['created_at'])); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Phân trang -->
                    <?php if ($total_pages > 1): ?>
                        <div class="mt-6 flex justify-center gap-2">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?>" 
                                   class="px-3 py-1 bg-white text-blue-600 border rounded hover:bg-blue-50">
                                    ← Trước
                                </a>
                            <?php endif; ?>

                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                <a href="?page=<?php echo $i; ?>" 
                                   class="px-3 py-1 <?php echo $i === $page ? 'bg-blue-600 text-white' : 'bg-white text-blue-600 hover:bg-blue-50'; ?> border rounded">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <a href="?page=<?php echo $page + 1; ?>" 
                                   class="px-3 py-1 bg-white text-blue-600 border rounded hover:bg-blue-50">
                                    Tiếp →
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        Chưa có thông báo nào.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include('includes/footer.php'); ?>