<?php
session_start();
include "dbconnect.php";

// 1. KIỂM TRA ĐIỀU KIỆN
if (!isset($_SESSION['user_id']) || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// 2. LẤY DỮ LIỆU CÁC SẢN PHẨM ĐÃ CHỌN TỪ GIỎ HÀNG VÀ TÍNH TỔNG TIỀN
$selected_products = $_SESSION['selected_products_for_order'] ?? [];

if (empty($selected_products)) {
    // Nếu không có sản phẩm nào trong session, có thể người dùng truy cập trực tiếp
    $_SESSION['flash_message'] = "Không có sản phẩm nào để đặt hàng.";
    $_SESSION['flash_type'] = "error";
    header("Location: cart.php");
    exit();
}

$placeholders = implode(',', array_fill(0, count($selected_products), '?'));
$sql = "SELECT p.PID, p.Price, c.Quantity 
        FROM cart c 
        JOIN products p ON c.ProductID = p.PID 
        WHERE c.UserID = ? AND p.PID IN ($placeholders)";
$types = 'i' . str_repeat('s', count($selected_products));
$params = array_merge([$user_id], $selected_products);
$stmt_cart = $con->prepare($sql);
$stmt_cart->bind_param($types, ...$params);
$stmt_cart->execute();
$result_cart = $stmt_cart->get_result();

$cart_items = [];
$total_amount_calculated = 0;

if ($result_cart->num_rows === 0) {
    // Nếu giỏ hàng trống, không thể đặt hàng
    $_SESSION['flash_message'] = 'Giỏ hàng của bạn đang trống hoặc sản phẩm đã chọn không hợp lệ.';
    $_SESSION['flash_type'] = 'error';
    header("Location: cart.php");
    exit();
}

while ($row = $result_cart->fetch_assoc()) {
    $cart_items[] = $row;
    $total_amount_calculated += $row['Price'] * $row['Quantity'];
}
$stmt_cart->close();

// 3. LẤY DỮ LIỆU TỪ FORM (ĐẢM BẢO TÊN INPUT KHỚP VỚI checkout.php)
$customer_name = trim($_POST['customer_name']);
$phone_number = trim($_POST['phone_number']);
$payment_method = $_POST['payment_method'];

// Ghép địa chỉ lại thành một chuỗi duy nhất để lưu vào DB
$shipping_address = trim($_POST['shipping_address']) .
    ', ' . trim($_POST['ward']) .
    ', ' . trim($_POST['district']) .
    ', ' . trim($_POST['province']) .
    ', ' . trim($_POST['country']);

$final_total_amount = $total_amount_calculated;
// (Nếu có coupon, cần áp dụng và cập nhật biến này)

// --- BẮT ĐẦU TRANSACTION ĐỂ ĐẢM BẢO TÍNH TOÀN VẸN DỮ LIỆU ---
$con->begin_transaction();
$success = true;

try {
    // 4. INSERT VÀO BẢNG orders
    $sql_order = "INSERT INTO orders (user_id, customer_name, shipping_address, phone_number, total_amount, status, payment_method) 
                  VALUES (?, ?, ?, ?, ?, 'Pending', ?)";
    $stmt_order = $con->prepare($sql_order);
    $stmt_order->bind_param(
        "isssds",
        $user_id,
        $customer_name,
        $shipping_address,
        $phone_number,
        $final_total_amount,
        $payment_method
    );
    $stmt_order->execute();

    // 5. LẤY order_id VỪA ĐƯỢC TẠO
    $order_id = $con->insert_id;
    $stmt_order->close();

    // 6. INSERT VÀO BẢNG order_items
    $sql_items = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt_items = $con->prepare($sql_items);

    foreach ($cart_items as $item) {
        $product_price_at_checkout = $item['Price']; // Giá tại thời điểm đặt hàng
        $stmt_items->bind_param(
            "isid",
            $order_id,
            $item['PID'],
            $item['Quantity'],
            $product_price_at_checkout
        );
        if (!$stmt_items->execute()) {
            $success = false;
            break;
        }
    }
    $stmt_items->close();

    // 7. XÓA GIỎ HÀNG (Nếu 6 thành công)
    // Chỉ xóa những sản phẩm đã được đặt hàng
    if ($success) {
        $delete_placeholders = implode(',', array_fill(0, count($selected_products), '?'));
        $sql_delete_cart = "DELETE FROM cart WHERE UserID = ? AND ProductID IN ($delete_placeholders)";
        $stmt_delete = $con->prepare($sql_delete_cart);
        $types = 'i' . str_repeat('s', count($selected_products));
        $stmt_delete->bind_param($types, $user_id, ...$selected_products);
        $stmt_delete->execute();
        $stmt_delete->close();
    } else {
        throw new Exception("Lỗi khi thêm chi tiết đơn hàng.");
    }

    // COMMIT TRANSACTION
    $con->commit();

    // Xóa session sản phẩm đã chọn
    unset($_SESSION['selected_products_for_order']);

    // 8. CHUYỂN HƯỚNG THÀNH CÔNG
    $_SESSION['flash_message'] = 'Đặt hàng thành công! Cảm ơn bạn đã mua sắm.';
    $_SESSION['flash_type'] = 'success';
    header("Location: order_tracking.php?id=" . $order_id);
    exit();

} catch (Exception $e) {
    // ROLLBACK TRANSACTION nếu có lỗi
    $con->rollback();
    $_SESSION['flash_message'] = 'Đã xảy ra lỗi hệ thống khi đặt hàng. Vui lòng thử lại.';
    $_SESSION['flash_type'] = 'error';
    header("Location: checkout.php");
    exit();
}
?>