<?php
require_once '../includes/functions.php';
include 'includes/header.php';

if (isset($_POST['upload'])) {
    $target_dir = "../uploads/";
    $filename = time() . "_" . basename($_FILES["fileToUpload"]["name"]); // Đổi tên để không trùng
    $target_file = $target_dir . $filename;
    
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $url = "http://" . $_SERVER['HTTP_HOST'] . "/bicms/uploads/" . $filename;
        echo "<div class='alert alert-success'>Upload thành công! Link ảnh của bạn:<br> 
              <input type='text' class='form-control mt-2' value='$url' onclick='this.select()'></div>";
    } else {
        echo "<div class='alert alert-danger'>Lỗi upload! Kiểm tra quyền thư mục uploads.</div>";
    }
}
?>

<h2><i class="fa-solid fa-images"></i> Thư viện Media</h2>
<div class="card p-4 mt-3">
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Chọn ảnh từ máy tính</label>
            <input type="file" name="fileToUpload" class="form-control" required>
        </div>
        <button type="submit" name="upload" class="btn btn-success">Tải lên</button>
    </form>
</div>

<h4 class="mt-4">Ảnh đã tải lên</h4>
<div class="row">
    <?php
    $files = glob("../uploads/*.*");
    foreach($files as $file) {
        $url = "http://" . $_SERVER['HTTP_HOST'] . "/bicms/uploads/" . basename($file);
        echo "<div class='col-md-2 mb-3'><img src='$url' class='img-thumbnail' style='height:100px; object-fit:cover'></div>";
    }
    ?>
</div>

<?php include 'includes/footer.php'; ?>
