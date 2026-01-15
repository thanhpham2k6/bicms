<?php
// Khởi động session
session_start();

// Xóa sạch các biến session
session_unset();

// Hủy session
session_destroy();

// Chuyển hướng về trang đăng nhập
header("Location: login.php");
exit;
?>
