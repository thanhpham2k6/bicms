<?php
session_start();
require_once '../includes/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 1. Chặn tự sát (Không được xóa chính mình)
    if ($id == $_SESSION['user_id']) {
        echo "<script>alert('Không thể tự xóa chính mình!'); window.location.href='users.php';</script>";
        exit;
    }

    // 2. Xóa
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: users.php?msg=deleted");
}
?>
