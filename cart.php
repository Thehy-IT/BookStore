<?php
include 'header.php'; // Sử dụng header chung

$swal_script = ""; // Biến chứa script thông báo

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user'])) {
    // Nếu chưa đăng nhập, hiển thị thông báo và yêu cầu đăng nhập
    echo "<div class='container text-center py-5' style='margin-top: 80px; min-height: 60vh;'>
            <h3>Vui lòng đăng nhập</h3>
            <p class='text-muted'>Bạn cần đăng nhập để xem giỏ hàng của mình.</p>
            <button class='btn btn-primary-glass' data-bs-toggle='modal' data-bs-target='#loginModal'>Đăng nhập ngay</button>
          </div>";
    include 'footer.php';
    exit();
}
$customer = $_SESSION['user'];

// 2. Xử lý Logic: THÊM / CẬP NHẬT SẢN PHẨM (Từ trang chi tiết)
if (isset($_GET['ID']) && isset($_GET['quantity'])) {
    $product_id = $_GET['ID'];
    $qty = intval($_GET['quantity']); // Đảm bảo là số nguyên

    // Kiểm tra xem sản phẩm đã có trong giỏ chưa
    $check = $con->prepare("SELECT * FROM cart WHERE Customer=? AND Product=?");
    $check->bind_param("ss", $customer, $product_id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows == 0) {
        // Chưa có -> Thêm mới
        $ins = $con->prepare("INSERT INTO cart (Customer, Product, Quantity) VALUES (?, ?, ?)");
        $ins->bind_param("ssi", $customer, $product_id, $qty);
        $ins->execute();
    } else {
        // Đã có -> Cập nhật số lượng (Ghi đè số lượng mới)
        $upd = $con->prepare("UPDATE cart SET Quantity=? WHERE Customer=? AND Product=?");
        $upd->bind_param("iss", $qty, $customer, $product_id);
        $upd->execute();
    }
    // Chuyển hướng để xóa tham số trên URL (Tránh F5 lại bị thêm lần nữa)
    header("Location: cart.php?action=added");
    exit();
}

// 3. Xử lý Logic: XÓA SẢN PHẨM
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    $del = $con->prepare("DELETE FROM cart WHERE Customer=? AND Product=?");
    $del->bind_param("ss", $customer, $product_id);

    if ($del->execute()) {
        header("Location: cart.php?action=removed");
        exit();
    }
}

// 5. Xử lý thông báo dựa trên tham số 'action'
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'added') $swal_script = "Swal.fire({icon: 'success', title: 'Đã cập nhật!', text: 'Giỏ hàng đã được cập nhật thành công.', timer: 2000, showConfirmButton: false});";
    if ($_GET['action'] == 'removed') $swal_script = "Swal.fire({icon: 'success', title: 'Đã xóa!', text: 'Sản phẩm đã được xóa khỏi giỏ hàng.', timer: 2000, showConfirmButton: false});";
    if ($_GET['action'] == 'placed') $swal_script = "Swal.fire({icon: 'success', title: 'Đặt hàng thành công!', text: 'Cảm ơn bạn! Đơn hàng sẽ được thanh toán khi nhận hàng.', confirmButtonColor: '#0f172a'});";
}
?>
<style>
    /* --- Glass Panel --- */
    .glass-panel {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border: var(--glass-border);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    /* --- Cart Table --- */
    .cart-table thead {
        background: rgba(15, 23, 42, 0.05);
    }

    .cart-table th {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.85rem;
        color: #64748b;
        border: none;
        padding: 15px 20px;
    }

    .cart-table td {
        vertical-align: middle;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 20px;
    }

    .cart-img {
        width: 60px;
        height: 90px;
        object-fit: cover;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* --- Buttons --- */
    .btn-remove {
        color: #ef4444;
        background: rgba(239, 68, 68, 0.1);
        border: none;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        transition: 0.3s;
    }

    .btn-remove:hover {
        background: #ef4444;
        color: white;
        transform: rotate(90deg);
    }

    .btn-checkout {
        background: var(--primary);
        color: white;
        border: none;
        width: 100%;
        padding: 15px;
        border-radius: 12px;
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

    /* --- Empty State --- */
    .empty-cart {
        padding: 60px 0;
        text-align: center;
    }

    .empty-icon {
        font-size: 5rem;
        color: #cbd5e1;
        margin-bottom: 20px;
    }
</style>
<?php if ($swal_script) echo "<script>$swal_script</script>"; ?>

<!-- ============== Cart Content ==============-->
<div class="container" style="padding-top: 40px; padding-bottom: 50px;">
    <!-- NEW: Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4" style="background-color: var(--glass-bg); padding: 15px; border-radius: 12px; backdrop-filter: blur(10px); border: var(--glass-border);">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
        </ol>
    </nav>
    <h2 class="fw-bold mb-4" style="font-family: 'Playfair Display', serif; margin-top: 2rem;">Giỏ hàng của bạn</h2>

    <?php
    // Lấy dữ liệu giỏ hàng
    $sql = "SELECT cart.Product, cart.Quantity, products.Title, products.Author, products.Price, products.PID 
                FROM cart 
                INNER JOIN products ON cart.Product = products.PID 
                WHERE cart.Customer = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $customer);
    $stmt->execute();
    $result = $stmt->get_result();

    $total = 0;
    $count = 0;
    ?>

    <?php if ($result->num_rows > 0): ?>
        <div class="row g-4">
            <!-- Cột trái: Danh sách sản phẩm -->
            <div class="col-lg-8">
                <div class="glass-panel">
                    <div class="table-responsive">
                        <table class="table cart-table mb-0">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Đơn giá</th>
                                    <th>Số lượng</th>
                                    <th class="text-end">Tổng</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()):
                                    $subtotal = $row['Price'] * $row['Quantity'];
                                    $total += $subtotal;
                                    $count += $row['Quantity'];
                                ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="img/books/<?php echo $row['PID']; ?>.jpg" class="cart-img me-3" onerror="this.src='https://placehold.co/100x150?text=Book'">
                                                <div>
                                                    <h6 class="fw-bold mb-1"><?php echo $row['Title']; ?></h6>
                                                    <small class="text-muted">bởi <?php echo $row['Author']; ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="fw-bold"><?php echo number_format($row['Price']); ?></td>
                                        <td>
                                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fs-6"><?php echo $row['Quantity']; ?></span>
                                        </td>
                                        <td class="text-end fw-bold text-primary"><?php echo number_format($subtotal); ?></td>
                                        <td class="text-end">
                                            <a href="#" onclick="confirmRemove('<?php echo $row['PID']; ?>')" class="btn-remove d-inline-flex align-items-center justify-content-center">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="index.php" class="text-decoration-none text-muted fw-bold">
                        <i class="fas fa-arrow-left me-2"></i> Tiếp tục mua sắm
                    </a>
                </div>
            </div>

            <!-- Cột phải: Tổng tiền (Order Summary) -->
            <div class="col-lg-4">
                <div class="glass-panel p-4 sticky-top" style="top: 100px; z-index: 1;">
                    <h5 class="fw-bold mb-4">Tóm tắt đơn hàng</h5>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Tạm tính (<?php echo $count; ?> sản phẩm)</span>
                        <span class="fw-bold"><?php echo number_format($total); ?> đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Phí vận chuyển</span>
                        <span class="text-success fw-bold">Miễn phí</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fs-5 fw-bold">Tổng cộng</span>
                        <span class="fs-4 fw-bold" style="color: var(--accent);"><?php echo number_format($total); ?> đ</span>
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
        <div class="glass-panel empty-cart">
            <div class="empty-icon">
                <i class="fas fa-shopping-basket"></i>
            </div>
            <h3>Giỏ hàng của bạn đang trống</h3>
            <p class="text-muted mb-4">Có vẻ như bạn chưa thêm cuốn sách nào vào giỏ.</p>
            <a href="index.php" class="btn btn-primary rounded-pill px-5 py-3 fw-bold" style="background: var(--primary);">
                Bắt đầu mua sắm
            </a>
        </div>
    <?php endif; ?>

</div>

<script>
    // Xác nhận xóa sản phẩm
    function confirmRemove(pid) {
        Swal.fire({
            title: 'Bạn chắc chứ?',
            text: "Bạn có muốn xóa cuốn sách này khỏi giỏ hàng không?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Vâng, xóa nó!',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "cart.php?remove=" + pid;
            }
        })
    }
</script>
<?php
include 'footer.php'; // Sử dụng footer chung
?>