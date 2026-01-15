<?php
// 1. Kết nối & Header
require_once '../includes/db.php';
include 'includes/header.php';

// 2. Xử lý khi bấm nút Đăng bài
if (isset($_POST['add_post'])) {
    $title = $_POST['title'];
    // Lấy nội dung từ TinyMCE
    $content = $_POST['content']; 
    // Lấy ID chuyên mục từ dropdown
    $category_id = $_POST['category_id']; 

    // Câu lệnh SQL: Thêm cả cột category_id vào
    $sql = "INSERT INTO posts (title, content, category_id, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$title, $content, $category_id])) {
        echo "<div class='alert alert-success'>
                <i class='fa-solid fa-check-circle'></i> Đăng bài thành công! 
                <a href='index.php'>Về danh sách</a>
              </div>";
    } else {
        echo "<div class='alert alert-danger'>Lỗi khi đăng bài!</div>";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="fa-solid fa-pen-nib"></i> Viết bài mới</h2>
</div>

<form method="post">
    <div class="row">
        <div class="col-md-9">
            <div class="card shadow-sm p-3 mb-3">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tiêu đề bài viết</label>
                    <input type="text" name="title" class="form-control" placeholder="Nhập tiêu đề..." required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Nội dung</label>
                    <textarea name="content" id="editor"></textarea>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3 mb-3">
                <label class="form-label fw-bold">Đăng bài</label>
                <hr class="my-2">
                <button type="submit" name="add_post" class="btn btn-primary w-100">
                    <i class="fa-solid fa-paper-plane"></i> Xuất bản ngay
                </button>
            </div>

            <div class="card shadow-sm p-3">
                <label class="form-label fw-bold">Chuyên mục</label>
                <select name="category_id" class="form-select" size="5">
                    <?php
                    // Lấy danh sách chuyên mục từ Database đổ vào đây
                    $cats = $pdo->query("SELECT * FROM categories");
                    while ($c = $cats->fetch()) {
                        echo "<option value='{$c['id']}'>{$c['name']}</option>";
                    }
                    ?>
                </select>
                <div class="mt-2 text-end">
                    <a href="categories.php" class="small text-decoration-none">+ Thêm chuyên mục mới</a>
                </div>
            </div>
        </div>
    </div>
</form>

<?php include 'includes/footer.php'; ?>
