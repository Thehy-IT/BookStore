<?php
session_start();
include "dbconnect.php";

// 1. Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    // Nếu chưa, đặt thông báo lỗi và chuyển hướng
    $_SESSION['flash_message'] = "Bạn cần đăng nhập để để lại đánh giá.";
    $_SESSION['flash_type'] = "warning";
    // Chuyển hướng về trang trước đó (nếu có) hoặc trang chủ
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
    exit();
}

// 2. Kiểm tra xem có phải là phương thức POST không
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 3. Lấy dữ liệu từ form
    $product_id = $_POST['product_id'];
    $user_id = $_SESSION['user_id'];
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);

    // 4. Xác thực dữ liệu
    if (empty($product_id) || $rating < 1 || $rating > 5) {
        $_SESSION['flash_message'] = "Dữ liệu không hợp lệ. Vui lòng thử lại.";
        $_SESSION['flash_type'] = "error";
        header('Location: description.php?ID=' . $product_id);
        exit();
    }

    // 5. Chèn vào cơ sở dữ liệu bằng Prepared Statement
    $stmt = $con->prepare("INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siis", $product_id, $user_id, $rating, $comment);

    if ($stmt->execute()) {
        $_SESSION['flash_message'] = "Cảm ơn bạn đã đánh giá sản phẩm!";
        $_SESSION['flash_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Đã có lỗi xảy ra. Vui lòng thử lại.";
        $_SESSION['flash_type'] = "error";
    }
    $stmt->close();

    // 6. Chuyển hướng người dùng trở lại trang sản phẩm
    header('Location: description.php?ID=' . $product_id);
    exit();
}
?>