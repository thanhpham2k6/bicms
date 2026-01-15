<?php
require_once '../includes/db.php';
include 'includes/header.php';

// Xử lý Thêm chuyên mục
if (isset($_POST['add_cat'])) {
    $name = $_POST['name'];
    // Tạo slug đơn giản (thay khoảng trắng bằng dấu gạch ngang)
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    
    $stmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
    $stmt->execute([$name, $slug]);
    echo "<div class='alert alert-success'>Đã thêm chuyên mục!</div>";
}

// Xử lý Xóa chuyên mục
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($id != 1) { // Không cho xóa chuyên mục mặc định (ID=1)
        // Chuyển tất cả bài viết của danh mục này về ID 1 (Uncategorized) trước khi xóa
        $pdo->prepare("UPDATE posts SET category_id = 1 WHERE category_id = ?")->execute([$id]);
        
        $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
        echo "<script>window.location.href='categories.php';</script>";
    } else {
        echo "<script>alert('Không thể xóa chuyên mục mặc định!');</script>";
    }
}
?>

<div class="row">
    <div class="col-md-4">
        <h3>Thêm Chuyên mục</h3>
        <form method="post" class="card p-3 shadow-sm">
            <div class="mb-3">
                <label>Tên chuyên mục</label>
                <input type="text" name="name" class="form-control" required>
                <small class="text-muted">Slug sẽ được tạo tự động.</small>
            </div>
            <button type="submit" name="add_cat" class="btn btn-primary">Thêm mới</button>
        </form>
    </div>

    <div class="col-md-8">
        <h3>Danh sách</h3>
        <table class="table table-bordered bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Slug</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $cats = $pdo->query("SELECT * FROM categories");
                while ($c = $cats->fetch()):
                ?>
                <tr>
                    <td><?php echo $c['id']; ?></td>
                    <td><?php echo htmlspecialchars($c['name']); ?></td>
                    <td><code><?php echo $c['slug']; ?></code></td>
                    <td>
                        <?php if($c['id'] != 1): ?>
                        <a href="categories.php?delete=<?php echo $c['id']; ?>" 
                           onclick="return confirm('Xóa chuyên mục này?')" 
                           class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
