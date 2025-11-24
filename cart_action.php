<?php
session_start();
include 'dbconnect.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_message'] = "Bạn cần đăng nhập để thực hiện hành động này.";
    $_SESSION['flash_type'] = "warning";
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// 2. Lấy hành động từ URL
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'clear_all':
        $stmt = $con->prepare("DELETE FROM cart WHERE UserID = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $_SESSION['flash_message'] = "Đã xóa tất cả sản phẩm khỏi giỏ hàng.";
        $_SESSION['flash_type'] = "success";
        break;
}

// 3. Chuyển hướng người dùng trở lại trang giỏ hàng
header("Location: cart.php");
exit();
