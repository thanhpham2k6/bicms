<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php'; // Để lấy tên web

$error = '';
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Sai tên đăng nhập hoặc mật khẩu!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 40px;
        }
        .login-header { text-align: center; margin-bottom: 30px; }
        .login-header i { font-size: 50px; color: #764ba2; margin-bottom: 10px; }
        .form-control:focus { box-shadow: none; border-color: #764ba2; }
        .btn-login {
            background: #764ba2; border: none; padding: 12px;
            font-size: 16px; font-weight: bold; width: 100%;
            transition: 0.3s;
        }
        .btn-login:hover { background: #5b3a7d; }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <i class="fa-solid fa-cube"></i>
            <h3>BiCMS Admin</h3>
            <p class="text-muted">Chào mừng quay trở lại!</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger text-center p-2 mb-3 small">
                <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="floatingInput" name="username" placeholder="Username" required>
                <label for="floatingInput">Tên đăng nhập</label>
            </div>
            <div class="form-floating mb-4">
                <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password" required>
                <label for="floatingPassword">Mật khẩu</label>
            </div>
            
            <button type="submit" name="login" class="btn btn-primary btn-login text-white rounded-pill">
                Đăng nhập ngay <i class="fa-solid fa-arrow-right ms-2"></i>
            </button>
            
            <div class="text-center mt-4">
                <a href="../index.php" class="text-decoration-none text-secondary small">
                    <i class="fa-solid fa-house"></i> Quay về trang chủ
                </a>
            </div>
        </form>
    </div>

</body>
</html>
