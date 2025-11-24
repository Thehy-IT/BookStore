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
        $title = mysqli_real_escape_string($con, $_POST['title']);
        $author = mysqli_real_escape_string($con, $_POST['author']);
        $price = (float)$_POST['price'];
        $description = mysqli_real_escape_string($con, $_POST['description']);
        $image = mysqli_real_escape_string($con, $_POST['image']);

        $sql = "INSERT INTO products (Title, Author, Price, Description, Image) VALUES ('$title', '$author', '$price', '$description', '$image')";

        if (mysqli_query($con, $sql)) {
            set_message("Thêm sản phẩm thành công!", "success");
        } else {
            set_message("Lỗi: " . mysqli_error($con), "danger");
        }
        break;

    case 'edit':
        $pid = (int)$_POST['pid'];
        $title = mysqli_real_escape_string($con, $_POST['title']);
        $author = mysqli_real_escape_string($con, $_POST['author']);
        $price = (float)$_POST['price'];
        $description = mysqli_real_escape_string($con, $_POST['description']);
        $image = mysqli_real_escape_string($con, $_POST['image']);

        $sql = "UPDATE products SET 
                    Title = '$title', 
                    Author = '$author', 
                    Price = '$price', 
                    Description = '$description', 
                    Image = '$image' 
                WHERE PID = $pid";

        if (mysqli_query($con, $sql)) {
            set_message("Cập nhật sản phẩm thành công!", "success");
        } else {
            set_message("Lỗi: " . mysqli_error($con), "danger");
        }
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

header("Location: manage_products.php");
exit();
