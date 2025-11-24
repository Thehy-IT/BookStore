<?php
session_start();
include 'dbconnect.php';

// Thiết lập header để trả về JSON
header('Content-Type: application/json');

// Hàm để trả về kết quả và dừng script
function return_json_response($success, $message, $type = 'info')
{
    echo json_encode(['success' => $success, 'message' => $message, 'type' => $type]);
    exit();
}

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    return_json_response(false, 'Bạn cần đăng nhập để thêm sản phẩm vào danh sách yêu thích.', 'login_required');
}
$user_id = $_SESSION['user_id'];

// 2. Kiểm tra dữ liệu đầu vào
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['product_id'])) {
    return_json_response(false, 'Yêu cầu không hợp lệ.', 'error');
}

$product_id = $_POST['product_id'];

// 3. Xử lý logic thêm vào wishlist

// Kiểm tra xem sản phẩm đã có trong wishlist chưa
$check_stmt = $con->prepare("SELECT * FROM wishlist WHERE UserID = ? AND ProductID = ?");
$check_stmt->bind_param("is", $user_id, $product_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    // Nếu đã có, thông báo cho người dùng
    return_json_response(false, 'Sách này đã có trong danh sách yêu thích của bạn.', 'info');
} else {
    // Nếu chưa có, thêm vào
    $insert_stmt = $con->prepare("INSERT INTO wishlist (UserID, ProductID) VALUES (?, ?)");
    $insert_stmt->bind_param("is", $user_id, $product_id);
    if ($insert_stmt->execute()) {
        return_json_response(true, 'Đã thêm vào danh sách yêu thích thành công!', 'success');
    } else {
        return_json_response(false, 'Đã có lỗi xảy ra, vui lòng thử lại.', 'error');
    }
}

$check_stmt->close();
?>