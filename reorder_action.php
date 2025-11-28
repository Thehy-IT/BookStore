<?php
session_start();
include 'dbconnect.php';

// 1. Kiểm tra đăng nhập và các tham số cần thiết
if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = (int) $_GET['order_id'];

// 2. Xác thực đơn hàng thuộc về người dùng hiện tại
$verify_stmt = $con->prepare("SELECT order_id FROM orders WHERE order_id = ? AND user_id = ?");
$verify_stmt->bind_param("ii", $order_id, $user_id);
$verify_stmt->execute();
$verify_result = $verify_stmt->get_result();

if ($verify_result->num_rows == 0) {
    // Đơn hàng không tồn tại hoặc không thuộc về người dùng này
    $_SESSION['flash_message'] = "Đơn hàng không hợp lệ.";
    $_SESSION['flash_type'] = "error";
    header("Location: order_tracking.php");
    exit();
}
$verify_stmt->close();

// 3. Lấy tất cả sản phẩm từ đơn hàng cũ
$items_stmt = $con->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();

$items_to_add = [];
while ($item = $items_result->fetch_assoc()) {
    $items_to_add[] = $item;
}
$items_stmt->close();

if (empty($items_to_add)) {
    $_SESSION['flash_message'] = "Đơn hàng này không có sản phẩm nào để mua lại.";
    $_SESSION['flash_type'] = "warning";
    header("Location: order_tracking.php?id=" . $order_id);
    exit();
}

// 4. Thêm sản phẩm vào giỏ hàng hiện tại
foreach ($items_to_add as $item) {
    $product_id = $item['product_id'];
    $quantity = $item['quantity'];

    // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
    $check_cart_stmt = $con->prepare("SELECT Quantity FROM cart WHERE UserID = ? AND ProductID = ?");
    $check_cart_stmt->bind_param("is", $user_id, $product_id);
    $check_cart_stmt->execute();
    $cart_result = $check_cart_stmt->get_result();

    if ($cart_result->num_rows > 0) {
        // Nếu có, cập nhật số lượng
        $update_cart_stmt = $con->prepare("UPDATE cart SET Quantity = Quantity + ? WHERE UserID = ? AND ProductID = ?");
        $update_cart_stmt->bind_param("iis", $quantity, $user_id, $product_id);
        $update_cart_stmt->execute();
    } else {
        // Nếu chưa, thêm mới
        $insert_cart_stmt = $con->prepare("INSERT INTO cart (UserID, ProductID, Quantity) VALUES (?, ?, ?)");
        $insert_cart_stmt->bind_param("isi", $user_id, $product_id, $quantity);
        $insert_cart_stmt->execute();
    }
}

// 5. Đặt thông báo và chuyển hướng đến giỏ hàng
$_SESSION['flash_message'] = "Đã thêm tất cả sản phẩm từ đơn hàng cũ vào giỏ hàng của bạn.";
$_SESSION['flash_type'] = "success";
header("Location: cart.php");
exit();
?>