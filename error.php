<?php
include 'header.php';

// Thiết lập thông tin lỗi mặc định
$error_code = isset($_GET['code']) ? htmlspecialchars($_GET['code']) : '404';
$error_title = 'Trang không tồn tại';
$error_message = 'Rất tiếc, trang bạn đang tìm kiếm có thể đã bị xóa, đổi tên hoặc tạm thời không có sẵn.';

// Tùy chỉnh thông báo dựa trên mã lỗi
switch ($error_code) {
    case '403':
        $error_title = 'Truy cập bị từ chối';
        $error_message = 'Bạn không có quyền truy cập vào tài nguyên này. Vui lòng liên hệ quản trị viên nếu bạn cho rằng đây là một lỗi.';
        break;
    case '500':
        $error_title = 'Lỗi máy chủ nội bộ';
        $error_message = 'Đã có lỗi xảy ra ở phía máy chủ. Chúng tôi đang làm việc để khắc phục sự cố. Vui lòng thử lại sau.';
        break;
    case 'empty_cart':
        $error_code = 'Giỏ hàng trống';
        $error_title = 'Giỏ hàng của bạn chưa có sản phẩm';
        $error_message = 'Hãy khám phá cửa hàng và thêm những cuốn sách bạn yêu thích vào giỏ hàng nhé!';
        break;
}

?>

<style>
    .error-page-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: calc(100vh - 200px); /* Chiều cao màn hình trừ đi header/footer */
        text-align: center;
        padding: 50px 15px;
        background: url('img/bg/bg-1.jpg') center/cover no-repeat;
    }
    .error-container {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: var(--glass-border);
        padding: 40px;
        border-radius: 20px;
        max-width: 600px;
    }
    .error-code {
        font-size: 5rem;
        font-weight: 700;
        color: var(--accent);
        line-height: 1;
    }
</style>

<div class="error-page-wrapper">
    <div class="error-container shadow-lg">
        <div class="error-code mb-3"><?php echo $error_code; ?></div>
        <h1 class="fw-bold display-5" style="font-family: 'Playfair Display', serif;"><?php echo $error_title; ?></h1>
        <p class="lead text-muted mt-3 mb-4"><?php echo $error_message; ?></p>
        <a href="index.php" class="btn btn-primary-glass btn-lg px-5">
            <i class="fas fa-home me-2"></i> Quay về Trang Chủ
        </a>
    </div>
</div>

<?php include 'footer.php'; ?>
