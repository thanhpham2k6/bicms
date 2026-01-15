<?php
session_start();
require_once '../includes/db.php';

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$id]);
}
header("Location: index.php");
exit;
?>
