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

// Xóa session coupon cũ khi vào trang để tránh áp dụng nhầm
unset($_SESSION['coupon_code']);
unset($_SESSION['discount_amount']);

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

    .payment-option {
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid transparent;
        transition: all 0.3s ease;
    }

    .payment-option:has(input:checked) {
        border-color: var(--accent);
        background: rgba(255, 255, 255, 1);
    }
</style>

<?php if ($swal_script) echo $swal_script; ?>

<!-- ============== Checkout Content ==============-->
<div class="container" style="padding-top: 100px; padding-bottom: 50px;">
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
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="customer_name" class="form-label">Họ và tên người nhận</label>
                            <input type="text" class="form-control form-control-glass" id="customer_name" name="customer_name" placeholder="Nguyễn Văn A" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control form-control-glass" id="email" name="email" placeholder="example@email.com" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone_number" class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control form-control-glass" id="phone_number" name="phone_number" placeholder="09xxxxxxxx" required>
                        </div>
                        <div class="col-md-6">
                            <label for="country" class="form-label">Quốc gia</label>
                            <select class="form-select form-control-glass" id="country" name="country" required>
                                <option value="Vietnam" selected>Việt Nam</option>
                                <!-- Thêm các quốc gia khác nếu cần -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="province" class="form-label">Tỉnh/Thành Phố</label>
                            <input type="text" class="form-control form-control-glass" id="province" name="province" required>
                        </div>
                        <div class="col-md-6">
                            <label for="district" class="form-label">Quận/Huyện</label>
                            <input type="text" class="form-control form-control-glass" id="district" name="district" required>
                        </div>
                        <div class="col-md-6">
                            <label for="ward" class="form-label">Phường/Xã</label>
                            <input type="text" class="form-control form-control-glass" id="ward" name="ward" required>
                        </div>
                        <div class="col-12">
                            <label for="shipping_address" class="form-label">Địa chỉ nhận hàng (Số nhà, tên đường)</label>
                            <textarea class="form-control form-control-glass" id="shipping_address" name="shipping_address" rows="2" placeholder="Ví dụ: 123 Đường ABC" required></textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="form-label">Phương thức thanh toán</label>
                        <div class="vstack gap-2">
                            <!-- Thanh toán khi nhận hàng (COD) -->
                            <div class="form-check p-3 rounded-3 payment-option">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                <label class="form-check-label fw-bold" for="cod">
                                    <i class="fas fa-truck me-2 text-muted"></i> Thanh toán bằng tiền mặt khi nhận hàng
                                </label>
                            </div>
                            <!-- Ví Momo -->
                            <div class="form-check p-3 rounded-3 payment-option">
                                <input class="form-check-input" type="radio" name="payment_method" id="momo" value="momo">
                                <label class="form-check-label fw-bold" for="momo">
                                    <img src="img/footer/momo.png" alt="Momo" style="height: 20px; margin-right: 8px;"> Ví Momo
                                </label>
                            </div>
                            <!-- Ví ZaloPay -->
                            <div class="form-check p-3 rounded-3 payment-option">
                                <input class="form-check-input" type="radio" name="payment_method" id="zalopay" value="zalopay">
                                <label class="form-check-label fw-bold" for="zalopay">
                                    <img src="img/footer/zalopay.png" alt="ZaloPay" style="height: 20px; margin-right: 8px;"> Ví ZaloPay
                                </label>
                            </div>
                            <!-- Ví ShopeePay -->
                            <div class="form-check p-3 rounded-3 payment-option">
                                <input class="form-check-input" type="radio" name="payment_method" id="shopeepay" value="shopeepay">
                                <label class="form-check-label fw-bold" for="shopeepay">
                                    <img src="img/footer/shopee.png" alt="ShopeePay" style="height: 20px; margin-right: 8px;"> Ví ShopeePay
                                </label>
                            </div>
                            <!-- VNPAY -->
                            <div class="form-check p-3 rounded-3 payment-option">
                                <input class="form-check-input" type="radio" name="payment_method" id="vnpay" value="vnpay">
                                <label class="form-check-label fw-bold" for="vnpay">
                                    <img src="img/footer/vnpay.png" alt="VNPAY" style="height: 20px; margin-right: 8px;"> VNPAY (ATM/Internet Banking/Visa/Master/JCB)
                                </label>
                            </div>

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

                    <!-- Mã giảm giá -->
                    <div class="mb-3">
                        <label class="form-label">Mã giảm giá</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-glass" placeholder="Nhập mã giảm giá" id="coupon-code">
                            <button class="btn btn-outline-dark" type="button" id="apply-coupon-btn" onclick="applyCoupon()">Áp dụng</button>
                        </div>
                        <div id="coupon-message" class="small mt-2"></div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tạm tính</span>
                        <span class="fw-bold"><?php echo number_format($total_amount); ?> đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2" id="discount-row" style="display: none;">
                        <span class="text-muted">Giảm giá</span>
                        <span class="fw-bold text-danger">-<span id="discount-amount">0</span> đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Phí vận chuyển</span>
                        <span class="text-success fw-bold">Miễn phí</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fs-5 fw-bold">Tổng cộng</span>
                        <span class="fs-4 fw-bold" style="color: var(--accent);" id="final-total"><?php echo number_format($total_amount); ?> đ</span>
                    </div>

                    <button type="submit" class="btn-place-order">
                        <i class="fas fa-check-circle me-2"></i> Hoàn tất đặt hàng
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    const formatter = new Intl.NumberFormat('vi-VN');

    function applyCoupon() {
        const couponCode = document.getElementById('coupon-code').value.trim();
        const applyBtn = document.getElementById('apply-coupon-btn');
        const couponMessage = document.getElementById('coupon-message');

        if (!couponCode) {
            couponMessage.innerHTML = `<span class="text-danger">Vui lòng nhập mã giảm giá.</span>`;
            return;
        }

        applyBtn.disabled = true;
        applyBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;

        fetch('apply_coupon.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `coupon_code=${couponCode}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    couponMessage.innerHTML = `<span class="text-success">${data.message}</span>`;
                    document.getElementById('discount-row').style.display = 'flex';
                    document.getElementById('discount-amount').textContent = formatter.format(data.discount_amount);
                    document.getElementById('final-total').textContent = formatter.format(data.new_total) + ' đ';
                } else {
                    couponMessage.innerHTML = `<span class="text-danger">${data.message}</span>`;
                    // Reset if coupon is invalid
                    document.getElementById('discount-row').style.display = 'none';
                    document.getElementById('discount-amount').textContent = '0';
                    document.getElementById('final-total').textContent = formatter.format(<?php echo $total_amount; ?>) + ' đ';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                couponMessage.innerHTML = `<span class="text-danger">Lỗi kết nối. Vui lòng thử lại.</span>`;
            })
            .finally(() => {
                applyBtn.disabled = false;
                applyBtn.innerHTML = 'Áp dụng';
            });
    }
</script>

<?php
include 'footer.php'; // Sử dụng footer chung
?>

