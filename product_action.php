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

// Hàm xử lý upload ảnh
function handle_image_upload($file_input_name, $current_image = '')
{
    $upload_dir = 'img/books/';
    // Tạo thư mục nếu chưa tồn tại
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Kiểm tra xem có file mới được tải lên không
    if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES[$file_input_name];
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        // Tạo tên file duy nhất để tránh trùng lặp
        $new_filename = uniqid('book_', true) . '.' . $file_extension;
        $target_path = $upload_dir . $new_filename;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            // Xóa ảnh cũ nếu có và không phải là ảnh mặc định
            if (!empty($current_image) && file_exists($current_image) && strpos($current_image, 'placeholder') === false) {
                unlink($current_image);
            }
            return $target_path; // Trả về đường dẫn file mới
        } else {
            // Lỗi khi di chuyển file
            return false;
        }
    }
    // Nếu không có file mới, giữ lại ảnh cũ
    return $current_image;
}

switch ($action) {
    case 'add':
        $image_path = handle_image_upload('image_file');
        if ($image_path === false) {
            set_message("Lỗi khi tải ảnh lên.", "danger");
            header("Location: manage_products.php");
            exit();
        }

        $stmt = $con->prepare("INSERT INTO products (Title, Author, Price, Description, Image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "ssdss",
            $_POST['title'],
            $_POST['author'],
            $_POST['price'],
            $_POST['description'],
            $image_path
        );

        if ($stmt->execute()) {
            set_message("Thêm sản phẩm thành công!", "success");
        } else {
            set_message("Lỗi: " . $stmt->error, "danger");
        }
        $stmt->close();
        break;

    case 'edit':
        $pid = (int)$_POST['pid'];
        $current_image = $_POST['current_image'];

        $image_path = handle_image_upload('image_file', $current_image);
        if ($image_path === false) {
            set_message("Lỗi khi tải ảnh mới lên.", "danger");
            header("Location: manage_products.php");
            exit();
        }

        $stmt = $con->prepare("UPDATE products SET Title = ?, Author = ?, Price = ?, Description = ?, Image = ? WHERE PID = ?");
        $stmt->bind_param(
            "ssdssi",
            $_POST['title'],
            $_POST['author'],
            $_POST['price'],
            $_POST['description'],
            $image_path,
            $pid
        );

        if ($stmt->execute()) {
            set_message("Cập nhật sản phẩm thành công!", "success");
        } else {
            set_message("Lỗi: " . $stmt->error, "danger");
        }
        $stmt->close();
        break;

    case 'delete':
        $pid = (int)$_GET['id'];
        $sql = "DELETE FROM products WHERE PID = $pid";

        if (mysqli_query($con, $sql)) {
            set_message("Xóa sản phẩm thành công!", "success");
        } else {
            set_message("Lỗi: " . mysqli_error($con), "danger");
        }
        break;

    default:
        set_message("Hành động không hợp lệ.", "warning");
        break;
}

// Xác định URL để chuyển hướng về. Mặc định là manage_products.php
$return_url = $_REQUEST['return_url'] ?? 'manage_products.php';

// Để tăng cường bảo mật, chỉ cho phép chuyển hướng đến các trang được chỉ định.
$allowed_redirects = ['manage_products.php', 'admin.php'];
if (!in_array($return_url, $allowed_redirects)) {
    $return_url = 'manage_products.php'; // Nếu không hợp lệ, chuyển về trang mặc định an toàn.
}

header("Location: " . $return_url);
exit();
