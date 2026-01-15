<?php
require_once '../includes/db.php';
// Include Header
include 'includes/header.php';

// Lấy danh sách bài viết
$stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Quản lý bài viết</h2>
    <a href="add_post.php" class="btn btn-primary">Viết bài mới</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Tiêu đề</th>
                    <th>Ngày đăng</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td>
                        <a href="edit_post.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">
    <i class="fa-solid fa-pen"></i> Sửa
</a>
                        <a href="delete_post.php?id=<?php echo $row['id']; ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Bạn chắc chắn muốn xóa bài này?');">
                           <i class="fa-solid fa-trash"></i> Xóa
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
