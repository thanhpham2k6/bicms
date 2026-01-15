<?php
require_once '../includes/db.php';
include 'includes/header.php';

// 1. Kiểm tra ID
if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = $_GET['id'];

// 2. Xử lý khi bấm nút Cập nhật
if (isset($_POST['update_post'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $cat_id = $_POST['category_id']; // Lấy ID chuyên mục mới
    
    // Update SQL: Thêm category_id vào câu lệnh
    $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, category_id = ? WHERE id = ?");
    
    if ($stmt->execute([$title, $content, $cat_id, $id])) {
        echo "<div class='alert alert-success'>
                <i class='fa-solid fa-check'></i> Cập nhật thành công! 
                <a href='index.php'>Về danh sách</a>
              </div>";
    }
}

// 3. Lấy dữ liệu bài viết hiện tại
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) { die("Bài viết không tồn tại!"); }
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="fa-solid fa-pen-to-square"></i> Chỉnh sửa bài viết</h2>
</div>

<form method="post">
    <div class="row">
        <div class="col-md-9">
            <div class="card shadow-sm p-3 mb-3">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tiêu đề bài viết</label>
                    <input type="text" name="title" class="form-control" 
                           value="<?php echo htmlspecialchars($post['title']); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Nội dung</label>
                    <textarea name="content" id="editor"><?php echo $post['content']; ?></textarea>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3 mb-3">
                <label class="form-label fw-bold">Hành động</label>
                <hr class="my-2">
                <button type="submit" name="update_post" class="btn btn-primary w-100">
                    <i class="fa-solid fa-save"></i> Lưu thay đổi
                </button>
                <a href="index.php" class="btn btn-secondary w-100 mt-2">Hủy bỏ</a>
            </div>

            <div class="card shadow-sm p-3">
                <label class="form-label fw-bold">Chuyên mục</label>
                <select name="category_id" class="form-select" size="5">
                    <?php
                    $cats = $pdo->query("SELECT * FROM categories");
                    while ($c = $cats->fetch()) {
                        // Logic: Nếu ID chuyên mục trùng với ID đang lưu trong bài viết -> Thì thêm chữ 'selected'
                        $selected = ($c['id'] == $post['category_id']) ? 'selected' : '';
                        echo "<option value='{$c['id']}' $selected>{$c['name']}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>
</form>

<?php include 'includes/footer.php'; ?>
