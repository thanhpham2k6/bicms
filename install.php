<?php
// install.php
if (file_exists('config.php')) die("Hệ thống đã cài đặt. Hãy xóa file config.php nếu muốn cài lại.");

$msg = "";
if (isset($_POST['install'])) {
    $host = $_POST['host']; $user = $_POST['user']; $pass = $_POST['pass']; $name = $_POST['name'];
    $admin_user = $_POST['admin_user']; $admin_pass = password_hash($_POST['admin_pass'], PASSWORD_BCRYPT);

    try {
        $pdo = new PDO("mysql:host=$host", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$name`");
        $pdo->exec("USE `$name`");

        // Tạo bảng Users
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL,
            password VARCHAR(255) NOT NULL
        )");

        // Tạo bảng Posts
        $pdo->exec("CREATE TABLE IF NOT EXISTS posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // Tạo Admin mặc định
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$admin_user, $admin_pass]);

        // Ghi file config
        $config_content = "<?php\ndefine('DB_HOST', '$host');\ndefine('DB_NAME', '$name');\ndefine('DB_USER', '$user');\ndefine('DB_PASS', '$pass');\n";
        if (file_put_contents('config.php', $config_content)) {
            $msg = "<p style='color:green'>Cài đặt thành công! <a href='admin/login.php'>Vào Admin ngay</a></p>";
        } else {
            $msg = "<p style='color:red'>Lỗi quyền ghi file! Hãy chmod 777 cho thư mục.</p>";
        }
    } catch (PDOException $e) {
        $msg = "<p style='color:red'>Lỗi Database: " . $e->getMessage() . "</p>";
    }
}
?>
<!DOCTYPE html><html><head><title>Install BiCMS</title></head><body>
    <h2>Cài đặt BiCMS</h2> <?php echo $msg; ?>
    <form method="post">
        <h3>Database Info</h3>
        <input type="text" name="host" placeholder="Host (localhost)" value="localhost" required><br>
        <input type="text" name="name" placeholder="DB Name" required><br>
        <input type="text" name="user" placeholder="DB Username" required><br>
        <input type="password" name="pass" placeholder="DB Password"><br>
        <h3>Admin Account</h3>
        <input type="text" name="admin_user" placeholder="Admin Username" required><br>
        <input type="password" name="admin_pass" placeholder="Admin Password" required><br><br>
        <button type="submit" name="install">Cài đặt ngay</button>
    </form>
</body></html>
