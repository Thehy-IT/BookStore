<?php
include 'header.php'; // Sử dụng header chung

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    // Nếu chưa đăng nhập, chuyển hướng về trang giỏ hàng để hiển thị thông báo
    header("Location: cart.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// 2. Lấy dữ liệu giỏ hàng để hiển thị và tính toán
$sql = "SELECT p.PID, p.Title, p.Price, c.Quantity 
        FROM cart c 
        JOIN products p ON c.ProductID = p.PID 
        WHERE c.UserID = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total_amount = 0;
$total_items = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
        $total_amount += $row['Price'] * $row['Quantity'];
        $total_items += $row['Quantity'];
    }
} else {
    // Nếu giỏ hàng trống, chuyển về trang giỏ hàng
    header("Location: cart.php");
    exit();
}

// Hiển thị thông báo nếu có
$swal_script = "";
if (isset($_SESSION['flash_message'])) {
    $swal_script = set_swal(
        $_SESSION['flash_type'],
        'Thông báo',
        $_SESSION['flash_message']
    );
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}
?>
<style>
    .summary-card {
        background: var(--glass-bg);
        backdrop-filter: blur(15px);
        border: var(--glass-border);
        border-radius: 20px;
        padding: 25px;
    }

    .summary-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .summary-item-img {
        width: 50px;
        height: 75px;
        object-fit: cover;
        border-radius: 6px;
        margin-right: 15px;
    }

    .form-control-glass {
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        padding: 12px 15px;
    }

    .form-control-glass:focus {
        background: white;
        box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.15);
        border-color: var(--accent);
    }

    .btn-place-order {
        background: var(--primary);
        color: white;
        border-radius: 12px;
        padding: 15px;
        font-weight: 600;
        transition: 0.3s;
        width: 100%;
    }

    .btn-place-order:hover {
        background: var(--accent);
        transform: translateY(-2px);
    }
</style>

<?php if ($swal_script) echo $swal_script; ?>

<!-- ============== Checkout Content ==============-->
<div class="container" style="padding-top: 40px; padding-bottom: 50px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4" style="background-color: var(--glass-bg); padding: 15px; border-radius: 12px; backdrop-filter: blur(10px); border: var(--glass-border);">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="cart.php">Giỏ hàng</a></li>
            <li class="breadcrumb-item active" aria-current="page">Thanh toán</li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-4" style="font-family: 'Playfair Display', serif; margin-top: 2rem;">Thông tin thanh toán</h2>

    <form action="place_order.php" method="POST">
        <div class="row g-5">
            <!-- Cột trái: Thông tin giao hàng -->
            <div class="col-lg-7">
                <div class="summary-card">
                    <h5 class="fw-bold mb-4">Thông tin giao hàng</h5>
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Họ và tên người nhận</label>
                        <input type="text" class="form-control form-control-glass" id="customer_name" name="customer_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Số điện thoại</label>
                        <input type="tel" class="form-control form-control-glass" id="phone_number" name="phone_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="shipping_address" class="form-label">Địa chỉ nhận hàng</label>
                        <textarea class="form-control form-control-glass" id="shipping_address" name="shipping_address" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phương thức thanh toán</label>
                        <div class="form-check p-3 rounded-3" style="background: rgba(255,255,255,0.7);">
                            <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                            <label class="form-check-label fw-bold" for="cod">
                                <i class="fas fa-truck me-2"></i> Thanh toán khi nhận hàng (COD)
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cột phải: Tóm tắt đơn hàng -->
            <div class="col-lg-5">
                <div class="summary-card sticky-top" style="top: 100px;">
                    <h5 class="fw-bold mb-4">Đơn hàng của bạn (<?php echo $total_items; ?> sản phẩm)</h5>

                    <div style="max-height: 300px; overflow-y: auto;" class="mb-3 pe-2">
                        <?php foreach ($cart_items as $item) : ?>
                            <div class="summary-item">
                                <img src="img/books/<?php echo $item['PID']; ?>.jpg" class="summary-item-img" onerror="this.src='https://placehold.co/100x150?text=Book'">
                                <div class="flex-grow-1">
                                    <p class="fw-bold mb-0 small text-truncate"><?php echo htmlspecialchars($item['Title']); ?></p>
                                    <small class="text-muted">Số lượng: <?php echo $item['Quantity']; ?></small>
                                </div>
                                <p class="fw-bold small mb-0"><?php echo number_format($item['Price'] * $item['Quantity']); ?> đ</p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tạm tính</span>
                        <span class="fw-bold"><?php echo number_format($total_amount); ?> đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Phí vận chuyển</span>
                        <span class="text-success fw-bold">Miễn phí</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fs-5 fw-bold">Tổng cộng</span>
                        <span class="fs-4 fw-bold" style="color: var(--accent);"><?php echo number_format($total_amount); ?> đ</span>
                    </div>

                    <button type="submit" class="btn-place-order">
                        <i class="fas fa-check-circle me-2"></i> Hoàn tất đặt hàng
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php
include 'footer.php'; // Sử dụng footer chung
?>

```

### 4. Tạo file xử lý `place_order.php`

File mới này sẽ chứa toàn bộ logic để lưu đơn hàng vào cơ sở dữ liệu.

```diff