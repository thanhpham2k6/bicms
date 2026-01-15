<?php
// 1. KẾT NỐI DATABASE (Quan trọng nhất)
require_once '../includes/db.php';
include 'includes/header.php';

// 2. XỬ LÝ LẤY DỮ LIỆU
$users = [];
$error_msg = "";

try {
    // Lấy tất cả user, user mới nhất lên đầu
    $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_msg = "Lỗi không lấy được danh sách: " . $e->getMessage();
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fa-solid fa-users"></i> Quản lý Thành viên</h2>
        <a href="add_user.php" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Thêm mới
        </a>
    </div>

    <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-check-circle"></i> Thao tác thành công!
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">ID</th>
                        <th width="30%">Username</th>
                        <th width="35%">Email</th>
                        <th width="30%" class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-box-open fa-3x mb-3"></i><br>
                                Chưa có thành viên nào. Hãy thêm mới!
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?php echo $u['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($u['username']); ?></strong>
                                <?php if($u['id'] == $_SESSION['user_id']) echo '<span class="badge bg-success ms-1">Là bạn</span>'; ?>
                            </td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td class="text-end">
                                <a href="edit_user.php?id=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-pen"></i> Sửa
                                </a>
                                
                                <?php if($u['id'] != $_SESSION['user_id']): ?>
                                    <a href="delete_user.php?id=<?php echo $u['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Bạn chắc chắn muốn xóa user: <?php echo $u['username']; ?>?');">
                                        <i class="fa-solid fa-trash"></i> Xóa
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
