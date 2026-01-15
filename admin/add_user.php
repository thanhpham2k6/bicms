<?php
require_once '../includes/db.php';
include 'includes/header.php';

$error = '';

// XỬ LÝ KHI BẤM NÚT LƯU
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Lấy dữ liệu từ Form (Dùng trim để cắt khoảng trắng thừa)
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // 2. Kiểm tra dữ liệu đầu vào
    if (empty($username) || empty($password)) {
        $error = "Vui lòng nhập Username và Mật khẩu!";
    } else {
        try {
            // 3. Kiểm tra xem Username đã tồn tại chưa
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt_check->execute([$username]);
            
            if ($stmt_check->fetchColumn() > 0) {
                $error = "Tên đăng nhập '$username' đã có người dùng. Vui lòng chọn tên khác.";
            } else {
                // 4. Nếu chưa có -> Thêm mới
                // Mã hóa mật khẩu
                $hashed_pass = password_hash($password, PASSWORD_DEFAULT);

                // Câu lệnh INSERT chuẩn
                $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                
                if ($stmt->execute([$username, $email, $hashed_pass])) {
                    // Thành công -> Quay về trang danh sách
                    echo "<script>window.location.href='users.php?msg=success';</script>";
                    exit;
                } else {
                    $error = "Lỗi khi lưu vào Database (Execute failed).";
                }
            }
        } catch (PDOException $e) {
            // IN LỖI CHI TIẾT ĐỂ BRO BIẾT ĐƯỜNG SỬA
            $error = "Lỗi SQL: " . $e->getMessage();
        }
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-user-plus"></i> Thêm thành viên mới</h5>
                </div>
                <div class="card-body">
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fa-solid fa-triangle-exclamation"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" placeholder="Ví dụ: admin2" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="user@example.com">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="users.php" class="btn btn-secondary">Quay lại</a>
                            <button type="submit" class="btn btn-success">Lưu Người Dùng</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
