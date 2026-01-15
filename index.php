<?php
// 1. Kết nối Database và Hàm chức năng
require_once 'includes/db.php';
require_once 'includes/functions.php';

// 2. CÂU LỆNH SQL QUAN TRỌNG:
// Dùng LEFT JOIN để lấy thêm cột 'name' từ bảng categories và đặt tên giả là 'cat_name'
// Logic Tìm kiếm
$keyword = isset($_GET['s']) ? $_GET['s'] : '';

$sql = "SELECT posts.*, categories.name AS cat_name 
        FROM posts 
        LEFT JOIN categories ON posts.category_id = categories.id";

if ($keyword) {
    // Nếu có từ khóa -> Tìm trong Tiêu đề hoặc Nội dung
    $sql .= " WHERE posts.title LIKE :keyword OR posts.content LIKE :keyword";
}

$sql .= " ORDER BY posts.created_at DESC";

// Chuẩn bị thực thi
$stmt = $pdo->prepare($sql);

if ($keyword) {
    $stmt->execute(['keyword' => "%$keyword%"]); // Thêm dấu % để tìm tương đối
} else {
    $stmt->execute();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo get_option('site_title'); ?> - Trang chủ</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        /* Hero Section Styling */
        .hero-section { 
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://source.unsplash.com/1600x900/?technology,code');
            background-size: cover;
            background-position: center;
            color: white; 
            padding: 80px 0; 
            margin-bottom: 40px; 
        }
        /* Card Styling */
        .post-card { border: none; border-radius: 10px; overflow: hidden; transition: transform 0.2s; background: #fff; margin-bottom: 30px; box-shadow: 0 2px 15px rgba(0,0,0,0.05); }
        .post-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .img-thumb { height: 220px; object-fit: cover; width: 100%; }
        /* Category Badge */
        .cat-badge { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fa-solid fa-cube text-primary"></i> <?php echo get_option('site_title'); ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Giới thiệu</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-primary text-white btn-sm px-4 ms-3 rounded-pill" href="admin/login.php">Đăng nhập</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3"><?php echo get_option('site_title'); ?></h1>
            <p class="lead mb-4"><?php echo get_option('site_description'); ?></p>
            <a href="#content" class="btn btn-outline-light rounded-pill px-4">Khám phá ngay</a>
        </div>
    </header>

    <div class="container" id="content">
        <div class="row">
            <div class="col-md-8">
                <?php if ($stmt->rowCount() > 0): ?>
                    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="card post-card">
                            <?php if (!empty($row['image'])): ?>
                                <a href="post.php?id=<?php echo $row['id']; ?>">
                                    <img src="<?php echo $row['image']; ?>" class="card-img-top img-thumb" alt="Post Image">
                                </a>
                            <?php endif; ?>
                            
                            <div class="card-body p-4">
                                <h2 class="card-title h4 fw-bold mb-3">
                                    <a href="post.php?id=<?php echo $row['id']; ?>" class="text-decoration-none text-dark hover-primary">
                                        <?php echo htmlspecialchars($row['title']); ?>
                                    </a>
                                </h2>

                                <div class="mb-3 d-flex align-items-center text-muted small">
                                    <div class="me-3">
                                        <i class="fa-regular fa-calendar me-1"></i> 
                                        <?php echo date("d/m/Y", strtotime($row['created_at'])); ?>
                                    </div>
                                    <div>
                                        <span class="badge bg-light text-secondary border cat-badge">
                                            <i class="fa-solid fa-folder me-1"></i> 
                                            <?php echo htmlspecialchars($row['cat_name'] ?? 'Chưa phân loại'); ?>
                                        </span>
                                    </div>
                                </div>

                                <p class="card-text text-secondary">
                                    <?php 
                                        $plain_text = strip_tags($row['content']);
                                        echo mb_substr($plain_text, 0, 160) . "..."; 
                                    ?>
                                </p>
                                <a href="post.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary btn-sm rounded-pill mt-2">
                                    Đọc tiếp <i class="fa-solid fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="100" class="mb-3 opacity-50">
                        <h4 class="text-muted">Chưa có bài viết nào!</h4>
                        <p>Hãy vào trang Admin để viết bài đầu tiên nhé.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-3">Tìm kiếm</h5>
                        <form action="" method="GET">
                            <div class="input-group">
                                <input type="text" class="form-control" name="s" placeholder="Nhập từ khóa...">
                                <button class="btn btn-dark" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-3">Về chúng tôi</h5>
                        <p class="text-muted mb-0">Website được xây dựng trên nền tảng <strong>BiCMS</strong> - Hệ quản trị nội dung mã nguồn mở, tối giản, hiệu suất cao dành cho Developer.</p>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-3">Chuyên mục</h5>
                        <ul class="list-group list-group-flush">
                            <?php
                            // Lấy danh sách chuyên mục cho Sidebar
                            $sidebar_cats = $pdo->query("SELECT * FROM categories");
                            while($cat = $sidebar_cats->fetch()):
                            ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <a href="#" class="text-decoration-none text-secondary">
                                    <i class="fa-solid fa-angle-right me-2 text-primary"></i> 
                                    <?php echo $cat['name']; ?>
                                </a>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0 small text-white-50"><?php echo get_option('footer_text'); ?></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
