<?php
session_start();
include 'header.php'; // Bao gồm header để có giao diện chung

// 1. Kiểm tra các tham số cần thiết từ URL
if (!isset($_GET['order_id']) || !isset($_GET['amount']) || !isset($_GET['method'])) {
    // Nếu thiếu thông tin, chuyển hướng về trang lỗi hoặc giỏ hàng
    $_SESSION['flash_message'] = "Thông tin thanh toán không hợp lệ.";
    $_SESSION['flash_type'] = "error";
    header("Location: cart.php");
    exit();
}

$order_id = (int) $_GET['order_id'];
$amount = (float) $_GET['amount'];
$payment_method = htmlspecialchars($_GET['method']);

?>
<!-- Hiển thị thông báo chức năng đang phát triển -->
<div class="container text-center" style="padding-top: 120px; min-height: 70vh;">
    <div class="col-md-8 col-lg-6 mx-auto">
        <div class="p-5 rounded-4" style="background-color: var(--glass-bg); border: var(--glass-border);">
            <div style="font-size: 4rem; color: var(--accent);">
                <i class="fas fa-cogs"></i>
            </div>
            <h2 class="fw-bold mt-4" style="font-family: 'Playfair Display', serif;">Chức năng đang phát triển</h2>
            <p class="lead text-muted mt-3">
                Cảm ơn bạn đã đặt hàng! Chức năng thanh toán trực tuyến qua
                <strong><?php echo strtoupper($payment_method); ?></strong> hiện đang được chúng tôi hoàn thiện và sẽ
                sớm ra mắt.
            </p>
            <p class="text-muted">
                Đơn hàng <strong>#<?php echo $order_id; ?></strong> của bạn đã được ghi nhận với trạng thái "Chờ xác
                nhận".
            </p>
            <hr class="my-4">
            <a href="order_tracking.php?id=<?php echo $order_id; ?>" class="btn btn-primary-glass px-4">Xem chi tiết đơn
                hàng</a>
        </div>
    </div>
</div>
<?php
include 'footer.php';
?>