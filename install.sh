#!/bin/bash

# --- 1. THU THẬP THÔNG TIN ---
clear
echo "=========================================="
echo "             CÀI ĐẶT BiCMS                "
echo "=========================================="

# Buộc người dùng nhập thông tin từ bàn phím (/dev/tty)
exec < /dev/tty

read -p " Tên Database (Mặc định: bicms_db): " DB_NAME
DB_NAME=${DB_NAME:-bicms_db}
read -p " User MySQL mới (Mặc định: bicms_user): " DB_USER
DB_USER=${DB_USER:-bicms_user}
read -p " Pass MySQL mới (Mặc định: 123456): " DB_PASS
DB_PASS=${DB_PASS:-123456}
read -p " Tài khoản Admin CMS: " ADMIN_USER
ADMIN_USER=${ADMIN_USER:-admin}
read -p " Mật khẩu Admin CMS: " ADMIN_PASS
ADMIN_PASS=${ADMIN_PASS:-admin123}

# --- 2. CÀI ĐẶT MÔI TRƯỜNG ---
echo " Đang cài đặt Apache, MySQL, PHP..."
sudo apt update -y > /dev/null
sudo apt install apache2 mysql-server php php-mysql git -y > /dev/null

# --- 3. TẢI CODE ---
cd /var/www/html
sudo rm -rf bicms
sudo git clone https://github.com/thanhpham2k6/bicms.git bicms
cd bicms

# --- 4. KHỞI TẠO DATABASE (SỬ DỤNG BIẾN ĐÃ XỬ LÝ) ---
echo " Đang tạo Database: $DB_NAME..."

# Tạo DB và User
sudo mysql -u root <<EOF
CREATE DATABASE IF NOT EXISTS $DB_NAME;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
EOF

# Tạo bảng (Truyền biến DB_NAME vào lệnh)
sudo mysql -u root "$DB_NAME" <<EOF
CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(50), password VARCHAR(255), email VARCHAR(100));
CREATE TABLE IF NOT EXISTS categories (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255), slug VARCHAR(255));
CREATE TABLE IF NOT EXISTS posts (id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255), slug VARCHAR(255), content TEXT, image VARCHAR(255), user_id INT, category_id INT);
CREATE TABLE IF NOT EXISTS options (option_name VARCHAR(100) PRIMARY KEY, option_value TEXT);
EOF

# --- 5. TẠO ADMIN ---
HASH_PASS=$(php -r "echo password_hash('$ADMIN_PASS', PASSWORD_DEFAULT);")
sudo mysql -u root "$DB_NAME" -e "INSERT INTO users (username, password) VALUES ('$ADMIN_USER', '$HASH_PASS');"
sudo mysql -u root "$DB_NAME" -e "INSERT INTO options (option_name, option_value) VALUES ('site_title', 'BiCMS'), ('admin_email', 'admin@example.com');"

# --- 6. GHI CONFIG ---
sudo tee includes/db.php > /dev/null <<EOF
<?php
\$host = 'localhost';
\$dbname = '$DB_NAME';
\$username = '$DB_USER';
\$password = '$DB_PASS';
try {
    \$pdo = new PDO("mysql:host=\$host;dbname=\$dbname;charset=utf8", \$username, \$password);
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException \$e) {
    die("Lỗi kết nối: " . \$e->getMessage());
}
?>
EOF

sudo mkdir -p uploads
sudo chown -R www-data:www-data /var/www/html/bicms
sudo chmod -R 775 /var/www/html/bicms

echo " Xong! Truy cập: http://$(hostname -I | awk '{print $1}')/bicms"
