<?php
include 'includes/header.php';

if (!isset($_GET['id'])) { header("Location: users.php"); exit; }
$id = $_GET['id'];

// Lấy thông tin hiện tại
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $new_pass = $_POST['password'];

    if (!empty($new_pass)) {
        // Nếu có nhập pass mới thì cập nhật cả pass
        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET email = ?, password = ? WHERE id = ?";
        $params = [$email, $hashed_pass, $id];
    } else {
        // Nếu không nhập pass thì chỉ sửa email
        $sql = "UPDATE users SET email = ? WHERE id = ?";
        $params = [$email, $id];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo "<script>alert('Cập nhật thành công!'); window.location.href='users.php';</script>";
}
?>

<h2>Sửa thông tin: <?php echo htmlspecialchars($user['username']); ?></h2>
<form method="POST" class="card p-4 shadow-sm" style="max-width: 500px;">
    <div class="mb-3">
        <label>Username (Không cho sửa)</label>
        <input type="text" class="form-control" value="<?php echo $user['username']; ?>" disabled>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?php echo $user['email']; ?>">
    </div>
    <div class="mb-3">
        <label>Mật khẩu mới</label>
        <input type="password" name="password" class="form-control" placeholder="Để trống nếu không muốn đổi pass">
    </div>
    <button type="submit" class="btn btn-primary">Cập nhật</button>
    <a href="users.php" class="btn btn-secondary">Hủy</a>
</form>

<?php include 'includes/footer.php'; ?>
