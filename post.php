<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

// 1. Kiểm tra xem có ID bài viết không
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php"); // Không có ID thì đá về trang chủ
    exit;
}

$id = $_GET['id'];

// 2. Lấy dữ liệu bài viết
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

// Nếu không tìm thấy bài (do nhập ID linh tinh)
if (!$post) {
    die("Bài viết không tồn tại! <a href='index.php'>Quay lại</a>");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - <?php echo get_option('site_title'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .post-content img { max-width: 100%; height: auto; border-radius: 5px; } /* Ảnh trong bài tự co giãn */
        .featured-image { width: 100%; max-height: 400px; object-fit: cover; border-radius: 8px; margin-bottom: 20px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="fa-solid fa-cube"></i> <?php echo get_option('site_title'); ?></a>
            <div class="ms-auto">
                <a href="index.php" class="btn btn-outline-light btn-sm">Quay lại trang chủ</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <article class="bg-white p-4 shadow-sm rounded">
                    
                    <h1 class="fw-bold mb-3"><?php echo htmlspecialchars($post['title']); ?></h1>
                    
                    <div class="text-muted mb-4 small">
                        <i class="fa-regular fa-calendar"></i> Đăng ngày: <?php echo date("d/m/Y H:i", strtotime($post['created_at'])); ?>
                    </div>

                    <?php if (!empty($post['image'])): ?>
                        <img src="<?php echo $post['image']; ?>" class="featured-image shadow-sm" alt="Ảnh đại diện">
                    <?php endif; ?>

                    <div class="post-content lh-lg mt-3">
                        <?php echo $post['content']; ?>
                    </div>

                </article>

                <div class="mt-4 text-center">
                    <a href="index.php" class="btn btn-primary"><i class="fa-solid fa-arrow-left"></i> Xem các bài khác</a>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3">
        <p class="mb-0"><?php echo get_option('footer_text'); ?></p>
    </footer>

</body>
</html>
