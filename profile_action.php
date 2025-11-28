<?php
session_start();
include "dbconnect.php";

// 1. Kiểm tra đăng nhập và phương thức POST
if (!isset($_SESSION['user_id']) || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

if ($action == 'update_info') {
    // --- XỬ LÝ CẬP NHẬT THÔNG TIN CÁ NHÂN ---
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phonenumber = trim($_POST['phonenumber']);
    $address = trim($_POST['address']);

    // Validate
    if (empty($fullname) || empty($email)) {
        $_SESSION['flash_message'] = "Họ tên và Email không được để trống.";
        $_SESSION['flash_type'] = "error";
        header("Location: profile.php");
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash_message'] = "Định dạng email không hợp lệ.";
        $_SESSION['flash_type'] = "error";
        header("Location: profile.php");
        exit();
    }

    // Kiểm tra xem email mới có bị trùng với người dùng khác không
    $stmt_check = $con->prepare("SELECT UserID FROM users WHERE Email = ? AND UserID != ?");
    $stmt_check->bind_param("si", $email, $user_id);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        $_SESSION['flash_message'] = "Email này đã được sử dụng bởi một tài khoản khác.";
        $_SESSION['flash_type'] = "error";
        header("Location: profile.php");
        exit();
    }

    // Cập nhật vào CSDL
    $stmt = $con->prepare("UPDATE users SET FullName = ?, Email = ?, PhoneNumber = ?, Address = ? WHERE UserID = ?");
    $stmt->bind_param("ssssi", $fullname, $email, $phonenumber, $address, $user_id); // Thêm 's' cho PhoneNumber và Address

    if ($stmt->execute()) {
        $_SESSION['flash_message'] = "Cập nhật thông tin cá nhân thành công!";
        $_SESSION['flash_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Đã có lỗi xảy ra. Vui lòng thử lại.";
        $_SESSION['flash_type'] = "error";
    }

} elseif ($action == 'update_password') {
    // --- XỬ LÝ ĐỔI MẬT KHẨU ---
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // Validate
    if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
        $_SESSION['flash_message'] = "Vui lòng điền đầy đủ các trường mật khẩu.";
        $_SESSION['flash_type'] = "error";
        header("Location: profile.php");
        exit();
    }
    if (strlen($new_password) < 6) {
        $_SESSION['flash_message'] = "Mật khẩu mới phải có ít nhất 6 ký tự.";
        $_SESSION['flash_type'] = "error";
        header("Location: profile.php");
        exit();
    }
    if ($new_password !== $confirm_new_password) {
        $_SESSION['flash_message'] = "Mật khẩu mới và mật khẩu xác nhận không khớp.";
        $_SESSION['flash_type'] = "error";
        header("Location: profile.php");
        exit();
    }

    // Kiểm tra mật khẩu hiện tại
    $stmt_pass = $con->prepare("SELECT Password FROM users WHERE UserID = ?");
    $stmt_pass->bind_param("i", $user_id);
    $stmt_pass->execute();
    $user = $stmt_pass->get_result()->fetch_assoc();

    if ($user && password_verify($current_password, $user['Password'])) {
        // Mật khẩu hiện tại đúng -> Cập nhật mật khẩu mới
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt_update = $con->prepare("UPDATE users SET Password = ? WHERE UserID = ?");
        $stmt_update->bind_param("si", $hashed_new_password, $user_id);
        $stmt_update->execute();
        $_SESSION['flash_message'] = "Đổi mật khẩu thành công!";
        $_SESSION['flash_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Mật khẩu hiện tại không chính xác.";
        $_SESSION['flash_type'] = "error";
    }
}

header("Location: profile.php");
exit();