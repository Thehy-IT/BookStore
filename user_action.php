<?php
session_start();
include "dbconnect.php";

// CHẶN KHÔNG CHO USER THƯỜNG VÀO
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$action = $_REQUEST['action'] ?? '';

function set_message($message, $type)
{
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

switch ($action) {
    case 'add':
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        // Mã hóa mật khẩu
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $con->prepare("INSERT INTO users (UserName, Password, Role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $role);

        if ($stmt->execute()) {
            set_message("Thêm người dùng thành công!", "success");
        } else {
            set_message("Lỗi: Tên đăng nhập có thể đã tồn tại.", "danger");
        }
        $stmt->close();
        break;

    case 'edit':
        $userid = (int)$_POST['userid'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        if (!empty($password)) {
            // Nếu có nhập mật khẩu mới -> Cập nhật cả mật khẩu
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $con->prepare("UPDATE users SET UserName = ?, Password = ?, Role = ? WHERE UserID = ?");
            $stmt->bind_param("sssi", $username, $hashed_password, $role, $userid);
        } else {
            // Nếu không nhập mật khẩu -> Chỉ cập nhật username và role
            $stmt = $con->prepare("UPDATE users SET UserName = ?, Role = ? WHERE UserID = ?");
            $stmt->bind_param("ssi", $username, $role, $userid);
        }

        if ($stmt->execute()) {
            set_message("Cập nhật người dùng thành công!", "success");
        } else {
            set_message("Lỗi: " . $stmt->error, "danger");
        }
        $stmt->close();
        break;

    case 'delete':
        $userid_to_delete = (int)$_GET['id'];
        $current_admin_id = (int)$_SESSION['user_id']; // Lấy ID của admin đang đăng nhập

        // Kiểm tra nếu admin đang cố gắng tự xóa mình
        if ($userid_to_delete === $current_admin_id) {
            set_message("Bạn không thể tự xóa tài khoản của chính mình.", "warning");
        } else {
            // Nếu không, tiến hành xóa như bình thường
            $stmt = $con->prepare("DELETE FROM users WHERE UserID = ?");
            $stmt->bind_param("i", $userid_to_delete);

            if ($stmt->execute()) {
                set_message("Xóa người dùng thành công!", "success");
            } else {
                set_message("Lỗi: " . $stmt->error, "danger");
            }
            $stmt->close();
        }
        break;
}

header("Location: manage_users.php");
exit();
