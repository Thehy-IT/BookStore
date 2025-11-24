<?php
session_start();
include 'dbconnect.php';

header('Content-Type: application/json');

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Cập nhật trạng thái is_read = 1 cho tất cả thông báo của người dùng
$stmt = $con->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update notifications.']);
}

$stmt->close();
