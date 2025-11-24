<?php
session_start();
include 'dbconnect.php';

// Thiết lập header để trả về JSON
header('Content-Type: application/json');

// Hàm để trả về lỗi và dừng script
function return_error($message, $old_quantity = null)
{
    echo json_encode(['success' => false, 'message' => $message, 'old_quantity' => $old_quantity]);
    exit();
}

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    return_error('Bạn cần đăng nhập để thực hiện hành động này.');
}
$user_id = $_SESSION['user_id'];

// 2. Kiểm tra dữ liệu đầu vào
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['product_id']) || !isset($_POST['quantity'])) {
    return_error('Yêu cầu không hợp lệ.');
}

$product_id = $_POST['product_id'];
$new_quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);

if ($new_quantity === false || $new_quantity < 1) {
    return_error('Số lượng không hợp lệ.');
}

// 3. Cập nhật cơ sở dữ liệu
$stmt = $con->prepare("UPDATE cart SET Quantity = ? WHERE UserID = ? AND ProductID = ?");
$stmt->bind_param("iis", $new_quantity, $user_id, $product_id);

if (!$stmt->execute()) {
    return_error('Lỗi khi cập nhật cơ sở dữ liệu.');
}

$stmt->close();

// 4. Lấy lại tổng số tiền và tổng số sản phẩm mới để trả về cho client
$sql = "SELECT SUM(c.Quantity * p.Price) as total_amount, SUM(c.Quantity) as total_items
        FROM cart c
        JOIN products p ON c.ProductID = p.PID
        WHERE c.UserID = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

$new_total_amount = $result['total_amount'] ?? 0;
$new_total_items = $result['total_items'] ?? 0;

// 5. Trả về kết quả thành công
echo json_encode([
    'success' => true,
    'new_total_amount' => $new_total_amount,
    'new_total_items' => $new_total_items
]);
