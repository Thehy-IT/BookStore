<?php
session_start();
include 'dbconnect.php';

// Thiết lập header để trả về JSON
header('Content-Type: application/json');

// Hàm để trả về lỗi và dừng script
function return_error($message)
{
    echo json_encode(['success' => false, 'message' => $message]);
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

if ($new_quantity === false || $new_quantity < 0) {
    return_error('Số lượng không hợp lệ.');
}

// 3. Lấy thông tin sản phẩm (giá, số lượng tồn kho)
$product_stmt = $con->prepare("SELECT Price, Available FROM products WHERE PID = ?");
$product_stmt->bind_param("s", $product_id);
$product_stmt->execute();
$product_res = $product_stmt->get_result();
if ($product_res->num_rows == 0) {
    return_error('Sản phẩm không tồn tại.');
}
$product_data = $product_res->fetch_assoc();
$price = $product_data['Price'];
$available = $product_data['Available'];

if ($new_quantity > $available) {
    return_error("Số lượng tồn kho không đủ (chỉ còn {$available} sản phẩm).");
}

// 4. Cập nhật hoặc xóa khỏi cơ sở dữ liệu
if ($new_quantity > 0) {
    // Cập nhật số lượng
    $stmt = $con->prepare("UPDATE cart SET Quantity = ? WHERE UserID = ? AND ProductID = ?");
    $stmt->bind_param("iis", $new_quantity, $user_id, $product_id);
    if (!$stmt->execute()) {
        return_error('Lỗi khi cập nhật cơ sở dữ liệu.');
    }
    $stmt->close();
} else {
    // Xóa sản phẩm nếu số lượng là 0
    $stmt = $con->prepare("DELETE FROM cart WHERE UserID = ? AND ProductID = ?");
    $stmt->bind_param("is", $user_id, $product_id);
    if (!$stmt->execute()) {
        return_error('Lỗi khi xóa sản phẩm khỏi giỏ hàng.');
    }
    $stmt->close();
}


// 5. Lấy lại tổng số tiền và tổng số sản phẩm mới để trả về cho client
$sql = "SELECT SUM(c.Quantity * p.Price) as total_amount, SUM(c.Quantity) as total_items, p.Price
        FROM cart c
        JOIN products p ON c.ProductID = p.PID
        WHERE c.UserID = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

$new_total_amount = $result['total_amount'] ?? 0;
$new_total_items = $result['total_items'] ?? 0;

// 6. Trả về kết quả thành công
echo json_encode([
    'success' => true,
    'new_total_amount' => $new_total_amount,
    'new_total_items' => (int)$new_total_items,
    'new_item_subtotal' => $price * $new_quantity // Thêm tổng phụ của item vừa cập nhật
]);
