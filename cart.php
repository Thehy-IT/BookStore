<?php
include 'header.php'; // Sử dụng header chung

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    // Nếu chưa đăng nhập, hiển thị thông báo và yêu cầu đăng nhập
    echo "<div class='container text-center py-5' style='margin-top: 80px; min-height: 60vh;'>
            <h3>Vui lòng đăng nhập</h3>
            <p class='text-muted'>Bạn cần đăng nhập để xem giỏ hàng của mình.</p>
            <button class='btn btn-primary-glass' data-bs-toggle='modal' data-bs-target='#loginModal'>Đăng nhập ngay</button>
          </div>";
    include 'footer.php';
    exit();
}
$user_id = $_SESSION['user_id'];

// --- XỬ LÝ LOGIC ---

// NEW: Xử lý xóa tất cả sản phẩm
if (isset($_GET['clear_all']) && $_GET['clear_all'] == 'true') {
    $delete_all_stmt = $con->prepare("DELETE FROM cart WHERE UserID = ?");
    $delete_all_stmt->bind_param("i", $user_id);
    if ($delete_all_stmt->execute()) {
        header("Location: cart.php?action=cleared");
        exit();
    }
}

// 2. Xử lý thêm/cập nhật sản phẩm từ các trang khác (ví dụ: description.php)
if (isset($_GET['ID']) && isset($_GET['quantity'])) {
    $product_id = $_GET['ID'];
    $quantity = filter_var($_GET['quantity'], FILTER_VALIDATE_INT);

    if ($quantity > 0) {
        // Kiểm tra xem sản phẩm đã có trong giỏ chưa
        $check_stmt = $con->prepare("SELECT Quantity FROM cart WHERE UserID = ? AND ProductID = ?");
        $check_stmt->bind_param("is", $user_id, $product_id);
        $check_stmt->execute();
        $cart_result = $check_stmt->get_result();

        if ($cart_result->num_rows > 0) {
            // Đã có -> cộng dồn số lượng
            $row = $cart_result->fetch_assoc();
            $new_quantity = $row['Quantity'] + $quantity;
            $update_stmt = $con->prepare("UPDATE cart SET Quantity = ? WHERE UserID = ? AND ProductID = ?");
            $update_stmt->bind_param("iis", $new_quantity, $user_id, $product_id);
            $update_stmt->execute();
        } else {
            // Chưa có -> Thêm mới
            $insert_stmt = $con->prepare("INSERT INTO cart (UserID, ProductID, Quantity) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("isi", $user_id, $product_id, $quantity);
            $insert_stmt->execute();
        }
    }
    // Chuyển hướng để xóa tham số trên URL và hiển thị thông báo
    header("Location: cart.php?action=added");
    exit();
}

// 3. Xử lý thông báo flash một lần
$swal_script = "";
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'added':
            $swal_script = "Swal.fire({icon: 'success', title: 'Thành công!', text: 'Sản phẩm đã được thêm vào giỏ hàng.', timer: 2000, showConfirmButton: false});";
            break;
        case 'placed':
            $swal_script = "Swal.fire({icon: 'success', title: 'Đặt hàng thành công!', text: 'Cảm ơn bạn! Chúng tôi sẽ sớm liên hệ để xác nhận đơn hàng.', confirmButtonColor: '#0f172a'});";
            break;
        case 'cleared':
            $swal_script = "Swal.fire({icon: 'success', title: 'Đã dọn dẹp!', text: 'Tất cả sản phẩm đã được xóa khỏi giỏ hàng.', timer: 2000, showConfirmButton: false});";
            break;
    }
}

// --- TRUY VẤN DỮ LIỆU ĐỂ HIỂN THỊ ---
$sql = "SELECT p.PID, p.Title, p.Author, p.Price, p.Available, c.Quantity 
        FROM cart c 
        JOIN products p ON c.ProductID = p.PID 
        WHERE c.UserID = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$subtotal = 0;
$total_items = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $subtotal += $row['Price'] * $row['Quantity'];
    $total_items += $row['Quantity'];
}

?>
<style>
    .cart-item-card {
        background: var(--glass-bg);
        backdrop-filter: blur(15px);
        border: var(--glass-border);
        border-radius: 16px;
        padding: 1.25rem;
        transition: all 0.3s ease;
    }

    .cart-item-img {
        width: 80px;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .quantity-control {
        display: flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.7);
        border-radius: 50px;
        padding: 4px;
    }

    .quantity-control .form-control {
        width: 50px;
        text-align: center;
        border: none;
        background: transparent;
        box-shadow: none;
        font-weight: 600;
    }

    .quantity-control .btn-qty {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: none;
        background-color: white;
        color: var(--primary);
        font-weight: 600;
        transition: 0.3s;
    }

    .quantity-control .btn-qty:hover {
        background-color: var(--primary);
        color: white;
    }

    /* --- Summary Card --- */
    .btn-checkout {
        background: var(--primary);
        color: white;
        border: none;
        width: 100%;
        padding: 15px;
        border-radius: 50px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: 0.3s;
    }

    .btn-checkout:hover {
        background: var(--accent);
        box-shadow: 0 10px 20px rgba(212, 175, 55, 0.3);
        transform: translateY(-2px);
    }

    .empty-cart {
        padding: 60px 0;
        text-align: center;
        background: var(--glass-bg);
        backdrop-filter: blur(15px);
        border: var(--glass-border);
        border-radius: 20px;
    }
</style>
<?php if ($swal_script) echo "<script>$swal_script</script>"; ?>

<!-- ============== Cart Content ==============-->
<div class="container" style="padding-top: 100px; padding-bottom: 50px;">
    <!-- NEW: Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4" style="background-color: var(--glass-bg); padding: 15px; border-radius: 12px; backdrop-filter: blur(10px); border: var(--glass-border);">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
        </ol>
    </nav>
    <h2 class="fw-bold mb-4" style="font-family: 'Playfair Display', serif; margin-top: 2rem;">Giỏ hàng của bạn</h2>

    <?php if (!empty($cart_items)): ?>
        <div class="row g-4">
            <!-- Cột trái: Danh sách sản phẩm -->
            <div class="col-lg-8">
                <div class="vstack gap-3">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item-card" id="cart-item-<?php echo htmlspecialchars($item['PID']); ?>">
                            <div class="row g-3 align-items-center">
                                <!-- Ảnh -->
                                <div class="col-auto">
                                    <img src="img/books/<?php echo htmlspecialchars($item['PID']); ?>.jpg" class="cart-item-img" onerror="this.src='https://placehold.co/100x150?text=Book'">
                                </div>
                                <!-- Thông tin -->
                                <div class="col">
                                    <h6 class="fw-bold mb-1 text-truncate"><a href="description.php?ID=<?php echo htmlspecialchars($item['PID']); ?>" class="text-dark text-decoration-none"><?php echo htmlspecialchars($item['Title']); ?></a></h6>
                                    <small class="text-muted">bởi <?php echo htmlspecialchars($item['Author']); ?></small>
                                    <div class="fw-bold mt-2"><?php echo number_format($item['Price']); ?> đ</div>
                                </div>
                                <!-- Số lượng -->
                                <div class="col-md-3">
                                    <div class="quantity-control">
                                        <button class="btn-qty" onclick="changeQuantity('<?php echo htmlspecialchars($item['PID']); ?>', -1)">-</button>
                                        <input type="number" class="form-control" id="quantity-<?php echo htmlspecialchars($item['PID']); ?>" value="<?php echo htmlspecialchars($item['Quantity']); ?>" min="1" max="<?php echo htmlspecialchars($item['Available']); ?>" onchange="updateQuantity('<?php echo htmlspecialchars($item['PID']); ?>', this.value)">
                                        <button class="btn-qty" onclick="changeQuantity('<?php echo htmlspecialchars($item['PID']); ?>', 1)">+</button>
                                    </div>
                                </div>
                                <!-- Tổng phụ & Xóa -->
                                <div class="col-md-2 text-md-end">
                                    <div class="fw-bold text-primary mb-2" id="subtotal-<?php echo htmlspecialchars($item['PID']); ?>">
                                        <?php echo number_format($item['Price'] * $item['Quantity']); ?> đ
                                    </div>
                                    <button class="btn btn-sm btn-outline-danger border-0" onclick="removeItem('<?php echo htmlspecialchars($item['PID']); ?>')" title="Xóa sản phẩm">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center">
                    <a href="index.php" class="btn btn-light rounded-pill">
                        <i class="fas fa-arrow-left me-2"></i> Tiếp tục mua sắm
                    </a>
                    <button onclick="confirmClearAll()" class="btn btn-outline-danger rounded-pill">
                        <i class="fas fa-trash-alt me-1"></i> Xóa tất cả
                    </button>
                </div>
            </div>

            <!-- Cột phải: Tổng tiền (Order Summary) -->
            <div class="col-lg-4">
                <div class="cart-item-card p-4 sticky-top" style="top: 100px; z-index: 1;">
                    <h5 class="fw-bold mb-4">Tóm tắt đơn hàng</h5>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Tạm tính (<span id="total-items"><?php echo $total_items; ?></span> sản phẩm)</span>
                        <span class="fw-bold"><span id="subtotal-amount"><?php echo number_format($subtotal); ?></span> đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Phí vận chuyển</span>
                        <span class="text-success fw-bold">Miễn phí</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fs-5 fw-bold">Tổng cộng</span>
                        <span class="fs-4 fw-bold" style="color: var(--accent);"><span id="total-amount"><?php echo number_format($subtotal); ?></span> đ</span>
                    </div>

                    <!-- Form đặt hàng -->
                    <a href="checkout.php" class="btn-checkout text-center text-decoration-none">
                        Tiến hành thanh toán <i class="fas fa-arrow-right ms-2"></i>
                    </a>

                    <div class="mt-3 text-center small text-muted">
                        <i class="fas fa-shield-alt me-1"></i> Thanh toán an toàn
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- Giỏ hàng trống -->
        <div class="empty-cart">
            <div style="font-size: 5rem; color: #cbd5e1;"><i class="fas fa-shopping-basket"></i></div>
            <h3 class="mt-3">Giỏ hàng của bạn đang trống</h3>
            <p class="text-muted mb-4">Có vẻ như bạn chưa thêm cuốn sách nào vào giỏ.</p>
            <a href="index.php" class="btn btn-primary rounded-pill px-5 py-3 fw-bold" style="background: var(--primary);">
                Bắt đầu mua sắm
            </a>
        </div>
    <?php endif; ?>

</div>

<script>
    const formatter = new Intl.NumberFormat('vi-VN');
    let debounceTimer;

    // NEW: Xác nhận xóa tất cả
    function confirmClearAll() {
        Swal.fire({
            title: 'Bạn chắc chắn?',
            text: "Hành động này sẽ xóa tất cả sản phẩm khỏi giỏ hàng của bạn!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Vâng, xóa tất cả!',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "cart.php?clear_all=true";
            }
        });
    }


    // Hàm thay đổi số lượng bằng nút +/-
    function changeQuantity(productId, amount) {
        const input = document.getElementById(`quantity-${productId}`);
        let currentValue = parseInt(input.value);
        let newValue = currentValue + amount;
        const max = parseInt(input.max);

        if (newValue < 1) newValue = 1;
        if (newValue > max) newValue = max;

        if (newValue !== currentValue) {
            input.value = newValue;
            // Kích hoạt sự kiện onchange để cập nhật
            input.dispatchEvent(new Event('change'));
        }
    }

    // Hàm xóa sản phẩm bằng AJAX
    function removeItem(productId) {
        Swal.fire({
            title: 'Bạn chắc chứ?',
            text: "Bạn có muốn xóa cuốn sách này khỏi giỏ hàng không?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Vâng, xóa nó!',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('update_cart_quantity.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `product_id=${productId}&quantity=0` // Gửi số lượng 0 để xóa
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Xóa item khỏi DOM
                            const itemCard = document.getElementById(`cart-item-${productId}`);
                            if (itemCard) {
                                itemCard.style.transition = 'opacity 0.5s ease';
                                itemCard.style.opacity = '0';
                                setTimeout(() => itemCard.remove(), 500);
                            }
                            updateSummaryAndHeader(data);
                            Swal.fire('Đã xóa!', 'Sản phẩm đã được xóa khỏi giỏ hàng.', 'success');

                            // Kiểm tra nếu giỏ hàng trống thì reload để hiển thị empty state
                            if (data.new_total_items === 0) {
                                setTimeout(() => window.location.reload(), 1500);
                            }
                        } else {
                            Swal.fire('Lỗi!', data.message || 'Không thể xóa sản phẩm.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Lỗi kết nối!', 'Không thể kết nối đến máy chủ.', 'error');
                    });
            }
        });
    }

    // Hàm cập nhật số lượng (với debouncing)
    function updateQuantity(productId, newQuantity) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const input = document.getElementById(`quantity-${productId}`);
            const originalValue = input.defaultValue; // Lưu giá trị ban đầu

            fetch('update_cart_quantity.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `product_id=${productId}&quantity=${newQuantity}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Cập nhật tổng phụ của dòng sản phẩm
                        const subtotalEl = document.getElementById(`subtotal-${productId}`);
                        if (subtotalEl) {
                            subtotalEl.textContent = formatter.format(data.new_item_subtotal) + ' đ';
                        }
                        updateSummaryAndHeader(data);
                        input.defaultValue = newQuantity; // Cập nhật giá trị gốc mới
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi cập nhật',
                            text: data.message || 'Không thể cập nhật giỏ hàng.',
                        });
                        // Khôi phục giá trị cũ nếu cập nhật thất bại
                        input.value = originalValue;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi kết nối',
                        text: 'Đã xảy ra lỗi khi kết nối đến máy chủ.',
                    });
                    input.value = originalValue;
                });
        }, 500); // Chờ 500ms sau khi người dùng ngừng gõ/nhấn
    }

    // Hàm phụ để cập nhật tóm tắt đơn hàng và header
    function updateSummaryAndHeader(data) {
        document.getElementById('subtotal-amount').textContent = formatter.format(data.new_total_amount);
        document.getElementById('total-amount').textContent = formatter.format(data.new_total_amount);
        document.getElementById('total-items').textContent = data.new_total_items;

        const headerCartBadge = document.getElementById('header-cart-count');
        if (headerCartBadge) {
            if (data.new_total_items > 0) {
                headerCartBadge.textContent = data.new_total_items;
                headerCartBadge.style.display = 'flex';
            } else {
                headerCartBadge.style.display = 'none';
            }
        }
    }
</script>
<?php
include 'footer.php'; // Sử dụng footer chung
?>