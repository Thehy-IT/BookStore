<?php
session_start();
include 'dbconnect.php';

// Thiết lập header để trả về JSON
header('Content-Type: application/json');

// Hàm để trả về kết quả và dừng script
function return_json_response($success, $message, $data = [])
{
    $response = ['success' => $success, 'message' => $message];
    if (!empty($data)) {
        $response = array_merge($response, $data);
    }
    echo json_encode($response);
    exit();
}

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    return_json_response(false, 'Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng.', ['type' => 'login_required']);
}
$user_id = $_SESSION['user_id'];

// 2. Kiểm tra dữ liệu đầu vào
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['product_id']) || !isset($_POST['quantity'])) {
    return_json_response(false, 'Yêu cầu không hợp lệ.', ['type' => 'error']);
}

$product_id = $_POST['product_id'];
$quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);

if ($quantity <= 0) {
    return_json_response(false, 'Số lượng không hợp lệ.', ['type' => 'error']);
}

// 3. Xử lý logic thêm vào giỏ hàng (tương tự cart.php)
$check_stmt = $con->prepare("SELECT Quantity FROM cart WHERE UserID = ? AND ProductID = ?");
$check_stmt->bind_param("is", $user_id, $product_id);
$check_stmt->execute();
$cart_result = $check_stmt->get_result();

if ($cart_result->num_rows > 0) {
    $row = $cart_result->fetch_assoc();
    $new_quantity = $row['Quantity'] + $quantity;
    $update_stmt = $con->prepare("UPDATE cart SET Quantity = ? WHERE UserID = ? AND ProductID = ?");
    $update_stmt->bind_param("iis", $new_quantity, $user_id, $product_id);
    $update_stmt->execute();
} else {
    $insert_stmt = $con->prepare("INSERT INTO cart (UserID, ProductID, Quantity) VALUES (?, ?, ?)");
    $insert_stmt->bind_param("isi", $user_id, $product_id, $quantity);
    $insert_stmt->execute();
}

// 4. Lấy tổng số lượng mới trong giỏ hàng để cập nhật header
$stmt_cart_count = $con->prepare("SELECT SUM(Quantity) as total_items FROM cart WHERE UserID = ?");
$stmt_cart_count->bind_param("i", $user_id);
$stmt_cart_count->execute();
$total_items = $stmt_cart_count->get_result()->fetch_assoc()['total_items'] ?? 0;

// 5. Trả về kết quả thành công
return_json_response(true, 'Đã thêm sản phẩm vào giỏ hàng!', ['new_total_items' => (int)$total_items]);
?>