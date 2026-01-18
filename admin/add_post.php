<?php
// BẬT HIỂN THỊ LỖI (Để debug trang trắng)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/db.php';

// Kiểm tra file functions.php có tồn tại không
if (!file_exists('../includes/functions.php')) {
    die("<div style='color:red; padding:20px;'>❌ Lỗi: Thiếu file includes/functions.php. Hãy tạo file này trước!</div>");
}
require_once '../includes/functions.php';
include 'includes/header.php';

// Lấy danh sách chuyên mục
try {
    $cats = $pdo->query("SELECT * FROM categories")->fetchAll();
} catch (Exception $e) {
    die("<div class='alert alert-danger'>Lỗi bảng Categories: " . $e->getMessage() . "<br>Hãy chạy lệnh tạo bảng categories trong Terminal!</div>");
}

$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category_id = $_POST['category_id'];
    $user_id = $_SESSION['user_id'];
    
    // Check hàm slug
    if (!function_exists('create_slug')) {
        die("<div class='alert alert-danger'>❌ Lỗi: Hàm create_slug() chưa được định nghĩa. Kiểm tra lại nội dung file functions.php!</div>");
    }

    // --- XỬ LÝ UPLOAD ẢNH ---
    $image_path = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_name = "post_" . time() . "." . $ext;
            $destination = "../uploads/" . $new_name;
            
            // Tạo thư mục nếu chưa có
            if (!is_dir('../uploads')) {
                mkdir('../uploads', 0775, true);
                chmod('../uploads', 0775); // Set quyền
            }

            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $image_path = "uploads/" . $new_name;
            } else {
                $error_msg = "Lỗi quyền thư mục: Không ghi được file vào uploads.";
            }
        } else {
            $error_msg = "Chỉ chấp nhận file ảnh (JPG, PNG, GIF).";
        }
    }

    // LƯU DB
    if (empty($error_msg)) {
        try {
            $slug = create_slug($title);
            $sql = "INSERT INTO posts (title, slug, content, category_id, user_id, image) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$title, $slug, $content, $category_id, $user_id, $image_path])) {
                // Thành công
                echo "<script>alert('✅ Đăng bài thành công!'); window.location.href='index.php';</script>";
                exit;
            } else {
                $err = $stmt->errorInfo();
                $error_msg = "Lỗi SQL: " . $err[2];
            }
        } catch (Exception $e) {
            $error_msg = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}
?>

<div class="container mt-4">
    <h2><i class="fa-solid fa-pen-nib"></i> Viết bài mới</h2>
    
    <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label class="form-label fw-bold">Tiêu đề</label>
            <input type="text" name="title" class="form-control" required>
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
            <label class="form-label fw-bold">Nội dung</label>
            <textarea name="content" class="form-control" rows="10"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Đăng bài</button>
    </form>
</div>

<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>CKEDITOR.replace('content');</script>

<?php include 'includes/footer.php'; ?>
