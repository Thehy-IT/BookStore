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
        $title = $_POST['title'];
        $author = $_POST['author'];
        $image_url = $_POST['image_url'];
        $content = $_POST['content'];

        $stmt = $con->prepare("INSERT INTO news (title, author, image_url, content) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $author, $image_url, $content);

        if ($stmt->execute()) {
            set_message("Thêm bài viết thành công!", "success");
        } else {
            set_message("Lỗi: " . $stmt->error, "danger");
        }
        $stmt->close();
        break;

    case 'edit':
        $id = (int)$_POST['id'];
        $title = $_POST['title'];
        $author = $_POST['author'];
        $image_url = $_POST['image_url'];
        $content = $_POST['content'];

        // Trong ví dụ này, chúng ta cập nhật tất cả các trường.
        // Nếu một trường nào đó trống, nó sẽ ghi đè giá trị cũ thành rỗng.
        $stmt = $con->prepare("UPDATE news SET title = ?, author = ?, image_url = ?, content = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $title, $author, $image_url, $content, $id);

        if ($stmt->execute()) {
            set_message("Cập nhật bài viết thành công!", "success");
        } else {
            set_message("Lỗi: " . $stmt->error, "danger");
        }
        $stmt->close();
        break;

    case 'delete':
        $id_to_delete = (int)$_GET['id'];

        $stmt = $con->prepare("DELETE FROM news WHERE id = ?");
        $stmt->bind_param("i", $id_to_delete);

        if ($stmt->execute()) {
            set_message("Xóa bài viết thành công!", "success");
        } else {
            set_message("Lỗi: " . $stmt->error, "danger");
        }
        $stmt->close();
        break;
    
    default:
        set_message("Hành động không hợp lệ.", "warning");
        break;
}

header("Location: manage_news.php");
exit();