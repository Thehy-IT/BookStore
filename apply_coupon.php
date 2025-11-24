<?php
session_start();
include 'dbconnect.php';

header('Content-Type: application/json');

// Hàm trả về response JSON
function json_response($success, $message, $data = [])
{
    $response = ['success' => $success, 'message' => $message];
    if (!empty($data)) {
        $response = array_merge($response, $data);
    }
    echo json_encode($response);
    exit();
}

// 1. Kiểm tra đăng nhập và phương thức request
if (!isset($_SESSION['user_id'])) {
    json_response(false, 'Bạn cần đăng nhập để sử dụng mã giảm giá.');
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['coupon_code'])) {
    json_response(false, 'Yêu cầu không hợp lệ.');
}

$user_id = $_SESSION['user_id'];
$coupon_code = trim($_POST['coupon_code']);

// 2. Lấy tổng giá trị giỏ hàng hiện tại
$sql_cart = "SELECT SUM(p.Price * c.Quantity) as subtotal 
             FROM cart c 
             JOIN products p ON c.ProductID = p.PID 
             WHERE c.UserID = ?";
$stmt_cart = $con->prepare($sql_cart);
$stmt_cart->bind_param("i", $user_id);
$stmt_cart->execute();
$cart_result = $stmt_cart->get_result()->fetch_assoc();
$subtotal = $cart_result['subtotal'] ?? 0;

if ($subtotal <= 0) {
    json_response(false, 'Giỏ hàng của bạn đang trống.');
}

// 3. Tìm mã giảm giá trong CSDL
$sql_coupon = "SELECT * FROM coupons WHERE code = ? AND (expiry_date IS NULL OR expiry_date >= CURDATE())";
$stmt_coupon = $con->prepare($sql_coupon);
$stmt_coupon->bind_param("s", $coupon_code);
$stmt_coupon->execute();
$coupon_result = $stmt_coupon->get_result();

if ($coupon_result->num_rows == 0) {
    json_response(false, 'Mã giảm giá không hợp lệ hoặc đã hết hạn.');
}

$coupon = $coupon_result->fetch_assoc();

// 4. Xác thực các điều kiện của mã
if ($subtotal < $coupon['min_spend']) {
    json_response(false, 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($coupon['min_spend']) . 'đ để áp dụng mã này.');
}

if ($coupon['usage_count'] >= $coupon['usage_limit']) {
    json_response(false, 'Mã giảm giá này đã hết lượt sử dụng.');
}

// 5. Tính toán số tiền giảm giá
$discount_amount = 0;
if ($coupon['type'] == 'percentage') {
    $discount_amount = ($subtotal * $coupon['value']) / 100;
} else { // fixed
    $discount_amount = $coupon['value'];
}

// Đảm bảo tiền giảm không lớn hơn tổng tiền
$discount_amount = min($discount_amount, $subtotal);

$new_total = $subtotal - $discount_amount;

// 6. Lưu vào session để sử dụng khi đặt hàng
$_SESSION['coupon_code'] = $coupon_code;
$_SESSION['discount_amount'] = $discount_amount;

// 7. Trả về kết quả thành công
json_response(true, 'Áp dụng mã giảm giá thành công!', [
    'discount_amount' => $discount_amount,
    'new_total' => $new_total
]);
