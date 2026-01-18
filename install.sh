#!/bin/bash

# --- 1. THU THẬP THÔNG TIN ---
clear
echo "=========================================="
echo "   🚀 CÀI ĐẶT BiCMS TỪ GITHUB (FULL)    "
echo "=========================================="

# A. CẤU HÌNH DATABASE (Sẽ tự tạo mới)
echo "--- [1] THÔNG TIN DATABASE MỚI ---"
read -p "👉 Tên Database muốn tạo (Mặc định: bicms_db): " DB_NAME
DB_NAME=${DB_NAME:-bicms_db}

read -p "👉 Tên User MySQL muốn tạo (Mặc định: bicms_user): " DB_USER
DB_USER=${DB_USER:-bicms_user}

read -p "👉 Mật khẩu cho User MySQL này (Mặc định: 123456): " DB_PASS
DB_PASS=${DB_PASS:-123456}

# B. CẤU HÌNH TÀI KHOẢN ADMIN CMS
echo -e "\n--- [2] TẠO TÀI KHOẢN ADMIN CMS ---"
read -p "👉 Tên đăng nhập Admin (Mặc định: admin): " ADMIN_USER
ADMIN_USER=${ADMIN_USER:-admin}

read -p "👉 Mật khẩu Admin (Mặc định: admin123): " ADMIN_PASS
ADMIN_PASS=${ADMIN_PASS:-admin123}


# --- 2. CÀI ĐẶT MÔI TRƯỜNG & GIT ---
echo -e "\n📦 Dang cap nhat va cai dat Apache, MySQL, PHP, Git..."
sudo apt update -y > /dev/null 2>&1
sudo apt install apache2 mysql-server php php-mysql php-curl php-gd php-mbstring php-xml php-zip unzip git -y > /dev/null 2>&1


# --- 3. TẢI SOURCE CODE TỪ GITHUB (QUAN TRỌNG NHẤT) ---
echo "⬇️ Dang tai ma nguon tu GitHub..."
cd /var/www/html

# Xóa thư mục cũ nếu có để tránh lỗi
if [ -d "bicms" ]; then
    sudo rm -rf bicms
fi

# Clone code về
sudo git clone https://github.com/thanhpham2k6/bicms.git
cd bicms

# Kiểm tra xem tải được chưa
if [ ! -f "index.php" ]; then
    echo "❌ Lỗi: Không tải được mã nguồn từ GitHub. Kiểm tra lại mạng!"
    exit 1
fi


# --- 4. THIẾT LẬP MYSQL (TẠO USER & DB) ---
echo "🗄️ Dang cau hinh Database..."

# Tạo Database
sudo mysql -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME};"

# Tạo User MySQL mới và cấp quyền (Fix lỗi 'làm gì có user')
sudo mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
sudo mysql -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"


# --- 5. TẠO CẤU TRÚC BẢNG ---
echo "📝 Dang tao cac bang du lieu..."

sudo mysql ${DB_NAME} -e "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);"

sudo mysql ${DB_NAME} -e "CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL
);"

sudo mysql ${DB_NAME} -e "CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255),
    user_id INT,
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);"

sudo mysql ${DB_NAME} -e "CREATE TABLE IF NOT EXISTS options (
    option_name VARCHAR(100) PRIMARY KEY,
    option_value TEXT
);"


# --- 6. TẠO DỮ LIỆU MẪU ---
echo "👤 Dang them du lieu mau..."

# Hash pass admin
HASH_PASS=$(php -r "echo password_hash('$ADMIN_PASS', PASSWORD_DEFAULT);")

# Thêm Admin vào DB
sudo mysql ${DB_NAME} -e "DELETE FROM users WHERE username='$ADMIN_USER';"
sudo mysql ${DB_NAME} -e "INSERT INTO users (username, password, email) VALUES ('$ADMIN_USER', '$HASH_PASS', 'admin@example.com');"

# Thêm cấu hình mẫu
sudo mysql ${DB_NAME} -e "INSERT IGNORE INTO categories (name, slug) VALUES ('Tin tức', 'tin-tuc'), ('Lập trình', 'lap-trinh');"
sudo mysql ${DB_NAME} -e "INSERT IGNORE INTO options (option_name, option_value) VALUES ('site_title', 'BiCMS'), ('site_description', 'Một dự án CMS siêu nhẹ');"


# --- 7. GHI FILE CẤU HÌNH (Kết nối Code với DB vừa tạo) ---
echo "⚙️ Dang ghi file config..."

# Ghi đè file includes/db.php
cat > includes/db.php <<EOF
<?php
\$host = 'localhost';
\$dbname = '$DB_NAME';
\$username = '$DB_USER';
\$password = '$DB_PASS';

try {
    \$pdo = new PDO("mysql:host=\$host;dbname=\$dbname;charset=utf8", \$username, \$password);
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException \$e) {
    die("Lỗi kết nối Database: " . \$e->getMessage());
}
?>
EOF


# --- 8. PHÂN QUYỀN & HOÀN TẤT ---
# Tạo folder uploads nếu trong git chưa có (hoặc git chỉ lưu folder rỗng)
mkdir -p uploads
sudo chown -R www-data:www-data /var/www/html/bicms
sudo chmod -R 775 /var/www/html/bicms

echo "=========================================="
echo "✅ CÀI ĐẶT THÀNH CÔNG!"
echo "👉 Truy cập: http://$(hostname -I | awk '{print $1}')/bicms"
echo "👉 Admin CMS: $ADMIN_USER / $ADMIN_PASS"
echo "👉 MySQL Info: User '$DB_USER' - Pass '$DB_PASS'"
echo "=========================================="
