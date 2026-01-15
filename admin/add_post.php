<?php
// --- PHẦN 1: PHP XỬ LÝ LOGIC (NẰM TRÊN CÙNG) ---
require_once '../includes/db.php';
require_once '../includes/functions.php';
include 'includes/header.php';

// Lấy danh sách chuyên mục để hiện vào ô chọn
$cats = $pdo->query("SELECT * FROM categories")->fetchAll();

// Khi người dùng bấm nút "Đăng bài"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category_id = $_POST['category_id'];
    $user_id = $_SESSION['user_id'];
    
    // 1. XỬ LÝ UPLOAD ẢNH
    $image_path = ""; // Mặc định là rỗng

    // Kiểm tra xem có file ảnh gửi lên không
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            // Đặt tên file theo thời gian để không bị trùng
            $new_name = "post_" . time() . "." . $ext;
            $destination = "../uploads/" . $new_name;
            
            // Di chuyển file vào thư mục uploads
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $image_path = "uploads/" . $new_name;
            } else {
                echo "<script>alert('Lỗi: Không lưu được ảnh. Kiểm tra quyền thư mục uploads!');</script>";
            }
        } else {
            echo "<script>alert('Chỉ chấp nhận file ảnh (JPG, PNG, GIF)!');</script>";
        }
    }

    // 2. TẠO SLUG VÀ LƯU VÀO DATABASE
    $slug = create_slug($title);
    
    $sql = "INSERT INTO posts (title, slug, content, category_id, user_id, image) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$title, $slug, $content, $category_id, $user_id, $image_path])) {
        echo "<script>alert('Đăng bài thành công!'); window.location.href='index.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Lỗi Database: Không lưu được bài viết.</div>";
    }
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fa-solid fa-pen-nib"></i> Viết bài mới</h2>
    </div>

    <form method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
        
        <div class="mb-3">
            <label class="form-label fw-bold">Tiêu đề bài viết</label>
            <input type="text" name="title" class="form-control" placeholder="Ví dụ: Hướng dẫn cài Linux..." required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Chuyên mục</label>
            <select name="category_id" class="form-select">
                <?php foreach($cats as $c): ?>
                    <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Ảnh đại diện</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Nội dung chi tiết</label>
            <textarea name="content" class="form-control" rows="10" required></textarea>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success"><i class="fa-solid fa-paper-plane"></i> Đăng bài ngay</button>
            <a href="index.php" class="btn btn-secondary">Hủy bỏ</a>
        </div>
    </form>
    </div>

<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
    // Biến thẻ textarea thành trình soạn thảo xịn
    CKEDITOR.replace( 'content' );
</script>

<?php include 'includes/footer.php'; ?>
