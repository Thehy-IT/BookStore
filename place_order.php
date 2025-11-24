<?php
session_start();
include 'dbconnect.php';

// Hàm để đặt thông báo và chuyển hướng
function set_flash_and_redirect($message, $type, $location)
{
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
    header("Location: $location");
    exit();
}

// 1. Kiểm tra đăng nhập và phương thức request
if (!isset($_SESSION['user_id'])) {
    set_flash_and_redirect('Vui lòng đăng nhập để đặt hàng.', 'warning', 'cart.php');
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash_and_redirect('Yêu cầu không hợp lệ.', 'error', 'checkout.php');
}

$user_id = $_SESSION['user_id'];

// 2. Lấy thông tin từ form
$customer_name = trim($_POST['customer_name']);
$phone_number = trim($_POST['phone_number']);
$shipping_address = trim($_POST['shipping_address']);
$payment_method = $_POST['payment_method'];

if (empty($customer_name) || empty($phone_number) || empty($shipping_address)) {
    set_flash_and_redirect('Vui lòng điền đầy đủ thông tin giao hàng.', 'error', 'checkout.php');
}

// 3. Lấy thông tin giỏ hàng từ CSDL
$sql_cart = "SELECT c.ProductID, c.Quantity, p.Price 
             FROM cart c 
             JOIN products p ON c.ProductID = p.PID 
             WHERE c.UserID = ?";
$stmt_cart = $con->prepare($sql_cart);
$stmt_cart->bind_param("i", $user_id);
$stmt_cart->execute();
$cart_items_result = $stmt_cart->get_result();

if ($cart_items_result->num_rows == 0) {
    set_flash_and_redirect('Giỏ hàng của bạn trống.', 'warning', 'cart.php');
}

$cart_items = [];
$subtotal = 0;
while ($row = $cart_items_result->fetch_assoc()) {
    $cart_items[] = $row;
    $subtotal += $row['Price'] * $row['Quantity'];
}

// 4. Lấy thông tin giảm giá từ session (nếu có)
$discount_amount = $_SESSION['discount_amount'] ?? 0;
$coupon_code = $_SESSION['coupon_code'] ?? null;
$final_total = $subtotal - $discount_amount;

// Bắt đầu transaction để đảm bảo toàn vẹn dữ liệu
$con->begin_transaction();

try {
    // 5. Chèn vào bảng `orders`
    $sql_order = "INSERT INTO orders (UserID, CustomerName, PhoneNumber, ShippingAddress, PaymentMethod, Subtotal, Discount, TotalAmount, CouponCode) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_order = $con->prepare($sql_order);
    $stmt_order->bind_param("issssddds", $user_id, $customer_name, $phone_number, $shipping_address, $payment_method, $subtotal, $discount_amount, $final_total, $coupon_code);
    $stmt_order->execute();
    $order_id = $con->insert_id; // Lấy ID của đơn hàng vừa tạo

    // 6. Chèn vào bảng `order_details`
    $sql_details = "INSERT INTO order_details (OrderID, ProductID, Quantity, Price) VALUES (?, ?, ?, ?)";
    $stmt_details = $con->prepare($sql_details);
    foreach ($cart_items as $item) {
        $stmt_details->bind_param("isid", $order_id, $item['ProductID'], $item['Quantity'], $item['Price']);
        $stmt_details->execute();
    }

    // 7. Xóa giỏ hàng sau khi đã đặt hàng thành công
    $sql_delete_cart = "DELETE FROM cart WHERE UserID = ?";
    $stmt_delete = $con->prepare($sql_delete_cart);
    $stmt_delete->bind_param("i", $user_id);
    $stmt_delete->execute();

    // 8. Cập nhật số lần sử dụng coupon nếu có
    if ($coupon_code) {
        $sql_update_coupon = "UPDATE coupons SET usage_count = usage_count + 1 WHERE code = ?";
        $stmt_coupon = $con->prepare($sql_update_coupon);
        $stmt_coupon->bind_param("s", $coupon_code);
        $stmt_coupon->execute();
    }

    // Commit transaction
    $con->commit();

    // Xóa session coupon và chuyển hướng thành công
    unset($_SESSION['coupon_code'], $_SESSION['discount_amount']);
    header("Location: cart.php?action=placed");
    exit();
} catch (Exception $e) {
    // Rollback nếu có lỗi
    $con->rollback();
    set_flash_and_redirect('Đã xảy ra lỗi trong quá trình đặt hàng. Vui lòng thử lại. Lỗi: ' . $e->getMessage(), 'error', 'checkout.php');
}
