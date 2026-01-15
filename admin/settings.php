<?php
// admin/settings.php
require_once '../includes/functions.php'; // Nhớ include file này
include 'includes/header.php';

if (isset($_POST['save_settings'])) {
    update_option('site_title', $_POST['site_title']);
    update_option('site_description', $_POST['site_description']);
    update_option('footer_text', $_POST['footer_text']);
    echo "<div class='alert alert-success'>Đã lưu cài đặt!</div>";
}
?>

<h2><i class="fa-solid fa-cog"></i> Cài đặt tổng quan</h2>
<form method="post" class="card p-4 mt-3 shadow-sm">
    <div class="mb-3">
        <label class="form-label fw-bold">Tên Website (Site Title)</label>
        <input type="text" name="site_title" class="form-control" 
               value="<?php echo htmlspecialchars(get_option('site_title')); ?>">
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Khẩu hiệu (Tagline)</label>
        <input type="text" name="site_description" class="form-control" 
               value="<?php echo htmlspecialchars(get_option('site_description')); ?>">
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Chân trang (Footer Text)</label>
        <input type="text" name="footer_text" class="form-control" 
               value="<?php echo htmlspecialchars(get_option('footer_text')); ?>">
    </div>

    <button type="submit" name="save_settings" class="btn btn-primary">Lưu thay đổi</button>
</form>

<?php include 'includes/footer.php'; ?>
