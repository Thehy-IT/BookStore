<?php
session_start();
include 'dbconnect.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    // Đặt thông báo và chuyển hướng về trang đăng nhập nếu cần
    $_SESSION['flash_message'] = "Bạn cần đăng nhập để thực hiện hành động này.";
    $_SESSION['flash_type'] = "warning";
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// 2. Lấy hành động từ URL
$action = $_GET['action'] ?? '';
$product_id = $_GET['id'] ?? null;

switch ($action) {
    case 'remove':
        if ($product_id) {
            $stmt = $con->prepare("DELETE FROM wishlist WHERE UserID = ? AND ProductID = ?");
            $stmt->bind_param("is", $user_id, $product_id);
            $stmt->execute();
            $_SESSION['flash_message'] = "Đã xóa sản phẩm khỏi danh sách yêu thích.";
            $_SESSION['flash_type'] = "success";
        }
        break;

    case 'clear_all':
        $stmt = $con->prepare("DELETE FROM wishlist WHERE UserID = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $_SESSION['flash_message'] = "Đã xóa tất cả sản phẩm khỏi danh sách yêu thích.";
        $_SESSION['flash_type'] = "success";
        break;

    case 'add_to_cart':
        if ($product_id) {
            $quantity = 1; // Mặc định thêm 1
            $check_stmt = $con->prepare("SELECT Quantity FROM cart WHERE UserID = ? AND ProductID = ?");
            $check_stmt->bind_param("is", $user_id, $product_id);
            $check_stmt->execute();
            $result = $check_stmt->get_result();

            if ($result->num_rows > 0) {
                $update_stmt = $con->prepare("UPDATE cart SET Quantity = Quantity + ? WHERE UserID = ? AND ProductID = ?");
                $update_stmt->bind_param("iis", $quantity, $user_id, $product_id);
                $update_stmt->execute();
            } else {
                $insert_stmt = $con->prepare("INSERT INTO cart (UserID, ProductID, Quantity) VALUES (?, ?, ?)");
                $insert_stmt->bind_param("isi", $user_id, $product_id, $quantity);
                $insert_stmt->execute();
            }
            $_SESSION['flash_message'] = "Đã thêm sản phẩm vào giỏ hàng.";
            $_SESSION['flash_type'] = "success";
        }
        break;

    case 'add_all_to_cart':
        $wishlist_stmt = $con->prepare("SELECT ProductID FROM wishlist WHERE UserID = ?");
        $wishlist_stmt->bind_param("i", $user_id);
        $wishlist_stmt->execute();
        $wishlist_items = $wishlist_stmt->get_result();

        while ($item = $wishlist_items->fetch_assoc()) {
            $pid = $item['ProductID'];
            $check_cart_stmt = $con->prepare("SELECT Quantity FROM cart WHERE UserID = ? AND ProductID = ?");
            $check_cart_stmt->bind_param("is", $user_id, $pid);
            $check_cart_stmt->execute();
            if ($check_cart_stmt->get_result()->num_rows > 0) {
                $update_cart_stmt = $con->prepare("UPDATE cart SET Quantity = Quantity + 1 WHERE UserID = ? AND ProductID = ?");
                $update_cart_stmt->bind_param("is", $user_id, $pid);
                $update_cart_stmt->execute();
            } else {
                $insert_cart_stmt = $con->prepare("INSERT INTO cart (UserID, ProductID, Quantity) VALUES (?, ?, 1)");
                $insert_cart_stmt->bind_param("is", $user_id, $pid);
                $insert_cart_stmt->execute();
            }
        }
        $_SESSION['flash_message'] = "Đã thêm tất cả sản phẩm yêu thích vào giỏ hàng.";
        $_SESSION['flash_type'] = "success";
        break;
}

// 3. Chuyển hướng người dùng trở lại trang wishlist
header("Location: wishlist.php");
exit();
