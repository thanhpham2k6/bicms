<?php
// Bắt buộc khởi động session để kiểm tra đăng nhập
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nếu chưa đăng nhập thì đá về trang login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiCMS Admin Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
    
    <script>
        tinymce.init({
            selector: 'textarea#editor', // Áp dụng cho thẻ <textarea id="editor">
            height: 500,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 16px; }'
        });
    </script>

    <style>
        body { background-color: #f4f6f9; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        .sidebar {
            min-width: 250px;
            max-width: 250px;
            min-height: 100vh;
            background: #212529; /* Màu đen sang trọng */
            color: #fff;
            transition: all 0.3s;
        }
        .sidebar .sidebar-header { padding: 20px; background: #1a1d20; }
        .sidebar ul.components { padding: 20px 0; border-bottom: 1px solid #4b545c; }
        .sidebar ul p { color: #fff; padding: 10px; }
        .sidebar ul li a {
            padding: 15px 20px;
            font-size: 1.1em;
            display: block;
            color: #c2c7d0;
            text-decoration: none;
        }
        .sidebar ul li a:hover { color: #fff; background: #343a40; border-left: 4px solid #0d6efd; }
        .sidebar ul li.active > a { color: #fff; background: #343a40; }
        .content { width: 100%; padding: 20px; }
    </style>
</head>
<body>

<div class="wrapper">
    <nav class="sidebar">
        <div class="sidebar-header">
            <h3><i class="fa-solid fa-cube"></i> BiCMS</h3>
        </div>

        <ul class="list-unstyled components">
            <li>
                <a href="index.php"><i class="fa-solid fa-gauge-high"></i> Tổng quan</a>
            </li>
            <li>
                <a href="add_post.php"><i class="fa-solid fa-pen-nib"></i> Viết bài mới</a>
            </li>
            <li>
                <a href="../index.php" target="_blank"><i class="fa-solid fa-globe"></i> Xem Website</a>
	    </li>
	    <li><a href="categories.php"><i class="fa-solid fa-folder"></i> Chuyên mục</a>
	    </li>
		<li class="nav-item">
    <a class="nav-link" href="users.php"><i class="fa-solid fa-users"></i> Thành viên</a>
</li>	    

	<li>
                <a href="logout.php" class="text-danger"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
            </li>
        </ul>
    </nav>

    <div class="content">
