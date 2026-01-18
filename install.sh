#!/bin/bash

# --- 1. THIẾT LẬP BAN ĐẦU ---
clear
echo "=========================================="
echo "   🛡️ HỆ THỐNG CÀI ĐẶT BiCMS TỰ ĐỘNG   "
echo "=========================================="

# Nhập thông tin Database
read -p "👉 Tên Database (Mặc định: bicms_db): " DB_NAME
DB_NAME=${DB_NAME:-bicms_db}
read -p "👉 User MySQL mới (Mặc định: bicms_user): " DB_USER
DB_USER=${DB_USER:-bicms_user}
read -p "👉 Pass MySQL mới (Mặc định: 123456): " DB_PASS
DB_PASS=${DB_PASS:-123456}

# Nhập thông tin Admin CMS
read -p "👉 Tài khoản Admin CMS: " ADMIN_USER
ADMIN_USER=${ADMIN_USER:-admin}
read -p "👉 Mật khẩu Admin CMS: " ADMIN_PASS
ADMIN_PASS=${ADMIN_PASS:-admin123}

# --- 2. CÀI ĐẶT MÔI TRƯỜNG ---
echo "📦 Đang cài đặt Apache, MySQL, PHP..."
sudo apt update -y > /dev/null
sudo apt install apache2 mysql-server php php-mysql git -y > /dev/null

# --- 3. XỬ LÝ THƯ MỤC VÀ TẢI CODE ---
echo "⬇️ Đang lấy mã nguồn từ GitHub..."
# Di chuyển vào thư mục web
cd /var/www/html

# Nếu thư mục bicms đã tồn tại thì xóa để clone mới
if [ -d "bicms" ]; then
    sudo rm -rf bicms
fi

# Clone code về thư mục bicms
sudo git clone https://github.com/thanhpham2k6/bicms.git bicms
cd bicms

# --- 4. CẤU HÌNH DATABASE (FIX LỖI QUYỀN TRUY CẬP) ---
echo "🗄️ Đang khởi tạo Database và User..."

# Dùng sudo mysql để chạy với quyền root hệ thống (không cần pass mysql ban đầu)
sudo mysql <<EOF
CREATE DATABASE IF NOT EXISTS ${DB_NAME};
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
EOF

# Tạo bảng dữ liệu
sudo mysql ${DB_NAME} <<EOF
CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(50), password VARCHAR(255), email VARCHAR(100));
CREATE TABLE IF NOT EXISTS categories (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255), slug VARCHAR(255));
CREATE TABLE IF NOT EXISTS posts (id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255), slug VARCHAR(255), content TEXT, image VARCHAR(255), user_id INT, category_id INT);
CREATE TABLE IF NOT EXISTS options (option_name VARCHAR(100) PRIMARY KEY, option_value TEXT);
EOF

# --- 5. TẠO TÀI KHOẢN ADMIN ---
HASH_PASS=$(php -r "echo password_hash('$ADMIN_PASS', PASSWORD_DEFAULT);")
sudo mysql ${DB_NAME} -e "INSERT INTO users (username, password) VALUES ('$ADMIN_USER', '$HASH_PASS');"
sudo mysql ${DB_NAME} -e "INSERT INTO options (option_name, option_value) VALUES ('site_title', 'BiCMS'), ('admin_email', 'admin@example.com');"

# --- 6. GHI FILE DB.PHP (TỰ ĐỘNG KHỚP VỚI THÔNG TIN VỪA NHẬP) ---
echo "⚙️ Đang kết nối mã nguồn với Database..."
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

# --- 7. PHÂN QUYỀN CUỐI CÙNG ---
sudo mkdir -p uploads
sudo chown -R www-data:www-data /var/www/html/bicms
sudo chmod -R 775 /var/www/html/bicms

echo "=========================================="
echo "✅ CÀI ĐẶT HOÀN TẤT!"
echo "👉 Truy cập: http://$(hostname -I | awk '{print $1}')/bicms"
echo "=========================================="
