#!/bin/bash

# Cấu hình Repo
REPO_URL="https://github.com/thanhpham2k6/bicms.git"

GREEN='\033[0;32m'
CYAN='\033[0;36m'
NC='\033[0m'

echo -e "${CYAN}===========================================${NC}"
echo -e "${CYAN}      BiCMS AUTO INSTALLER (NO-SQL FILE)   ${NC}"
echo -e "${CYAN}===========================================${NC}"

# 1. NHẬP CẤU HÌNH
echo -e "${GREEN}👉 Bước 1: Cấu hình hệ thống${NC}"
read -p "Tên thư mục web (Mặc định: bicms): " INSTALL_FOLDER
INSTALL_FOLDER=${INSTALL_FOLDER:-bicms}
TARGET_DIR="/var/www/html/$INSTALL_FOLDER"

read -p "Tên Database (Mặc định: bicms_db): " DB_NAME
DB_NAME=${DB_NAME:-bicms_db}

read -p "User Database (Mặc định: bicms_user): " DB_USER
DB_USER=${DB_USER:-bicms_user}

read -sp "Mật khẩu Database (Để trống sẽ tự sinh): " DB_PASS
if [ -z "$DB_PASS" ]; then DB_PASS=$(openssl rand -base64 12); fi
echo ""

echo -e "\n-> Đang cài vào: $TARGET_DIR"

# 2. CÀI LAMP STACK & GIT
echo -e "${GREEN}👉 Bước 2: Cài đặt Web Server...${NC}"
sudo apt update -q
sudo apt install -y apache2 mysql-server php php-mysql php-pdo php-mbstring git -q

# 3. TẢI CODE
echo -e "${GREEN}👉 Bước 3: Tải Source Code...${NC}"
if [ -d "$TARGET_DIR" ]; then sudo rm -rf "$TARGET_DIR"; fi
sudo git clone "$REPO_URL" "$TARGET_DIR"

# 4. TẠO DATABASE & BẢNG (MAGIC STEP)
echo -e "${GREEN}👉 Bước 4: Tự động tạo cấu trúc bảng (Schema)...${NC}"

# 4.1 Tạo DB và User
sudo mysql -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\`;"
sudo mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
sudo mysql -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

# 4.2 Tạo bảng USERS
sudo mysql -u root "$DB_NAME" <<EOF
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
EOF

# 4.3 Tạo bảng CATEGORIES
sudo mysql -u root "$DB_NAME" <<EOF
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
EOF

# 4.4 Tạo bảng POSTS (Có cột SLUG)
sudo mysql -u root "$DB_NAME" <<EOF
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255),
    content TEXT,
    image VARCHAR(255),
    category_id INT,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
EOF

# 4.5 Tạo Admin mặc định (Pass: 123456)
# Dùng PHP để Hash password cho chuẩn
ADMIN_PASS_HASH=$(php -r "echo password_hash('123456', PASSWORD_DEFAULT);")
sudo mysql -u root "$DB_NAME" -e "INSERT INTO users (username, password, email) VALUES ('admin', '$ADMIN_PASS_HASH', 'admin@example.com');"
sudo mysql -u root "$DB_NAME" -e "INSERT INTO categories (name) VALUES ('Tin công nghệ'), ('Đời sống');"

echo "✅ Đã tạo xong bảng và tài khoản Admin."

# 5. TẠO FILE KẾT NỐI PHP
echo -e "${GREEN}👉 Bước 5: Tạo file cấu hình db.php...${NC}"
mkdir -p "$TARGET_DIR/includes"

cat <<EOF | sudo tee "$TARGET_DIR/includes/db.php" > /dev/null
<?php
\$host = 'localhost';
\$db   = '$DB_NAME';
\$user = '$DB_USER';
\$pass = '$DB_PASS';
\$charset = 'utf8mb4';

\$dsn = "mysql:host=\$host;dbname=\$db;charset=\$charset";
\$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    \$pdo = new PDO(\$dsn, \$user, \$pass, \$options);
} catch (\PDOException \$e) {
    throw new \PDOException(\$e->getMessage(), (int)\$e->getCode());
}
?>
EOF

# 6. PHÂN QUYỀN & HOÀN TẤT
echo -e "${GREEN}👉 Bước 6: Dọn dẹp & Kích hoạt...${NC}"
sudo chown -R www-data:www-data "$TARGET_DIR"
sudo chmod -R 755 "$TARGET_DIR"
sudo a2enmod rewrite
sudo service apache2 restart

echo -e "${CYAN}===========================================${NC}"
echo -e "${GREEN}🎉 CÀI ĐẶT THÀNH CÔNG!${NC}"
echo -e "👉 Website: http://localhost/$INSTALL_FOLDER"
echo -e "👉 Admin Login: admin / 123456"
echo -e "👉 DB Pass: $DB_PASS"
echo -e "${CYAN}===========================================${NC}"
