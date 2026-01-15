<?php
// includes/functions.php
require_once 'db.php';

function get_option($name) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT option_value FROM options WHERE option_name = ?");
    $stmt->execute([$name]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['option_value'] : '';
}

function update_option($name, $value) {
    global $pdo;
    // Kiểm tra xem đã có chưa
    $check = $pdo->prepare("SELECT option_name FROM options WHERE option_name = ?");
    $check->execute([$name]);
    
    if ($check->rowCount() > 0) {
        $stmt = $pdo->prepare("UPDATE options SET option_value = ? WHERE option_name = ?");
        $stmt->execute([$value, $name]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO options (option_name, option_value) VALUES (?, ?)");
        $stmt->execute([$name, $value]);
    }
}
?>
