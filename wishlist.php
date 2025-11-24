<?php
include 'header.php'; // Sử dụng header chung

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    // Nếu chưa đăng nhập, hiển thị thông báo và yêu cầu đăng nhập
    echo "<div class='container text-center py-5' style='margin-top: 80px; min-height: 60vh;'>
            <h3>Vui lòng đăng nhập</h3>
            <p class='text-muted'>Bạn cần đăng nhập để xem danh sách yêu thích.</p>
            <button class='btn btn-primary-glass' data-bs-toggle='modal' data-bs-target='#loginModal'>Đăng nhập ngay</button>
          </div>";
    include 'footer.php';
    exit();
}
$user_id = $_SESSION['user_id'];

// 2. Xử lý Logic: THÊM SẢN PHẨM VÀO WISHLIST
if (isset($_GET['ID'])) {
    $product_id = $_GET['ID'];

    // Lấy URL của trang trước đó để quay lại
    $redirect_url = $_SERVER['HTTP_REFERER'] ?? 'index.php';

    // Kiểm tra xem sản phẩm đã có trong wishlist chưa
    $check_stmt = $con->prepare("SELECT * FROM wishlist WHERE UserID = ? AND ProductID = ?");
    $check_stmt->bind_param("is", $user_id, $product_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows == 0) {
        // Nếu chưa có, thêm vào
        $insert_stmt = $con->prepare("INSERT INTO wishlist (UserID, ProductID) VALUES (?, ?)");
        $insert_stmt->bind_param("is", $user_id, $product_id);
        if ($insert_stmt->execute()) {
            $_SESSION['flash_message'] = "Đã thêm vào danh sách yêu thích!";
            $_SESSION['flash_type'] = "success";
        }
    } else {
        // Nếu đã có, đặt thông báo cho người dùng biết
        $_SESSION['flash_message'] = "Sách này đã có trong danh sách yêu thích của bạn.";
        $_SESSION['flash_type'] = "info";
    }

    // Luôn chuyển hướng về trang trước đó sau khi xử lý
    header("Location: " . $redirect_url);
    exit();
}

// 3. Xử lý Logic: XÓA SẢN PHẨM KHỎI WISHLIST
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    $delete_stmt = $con->prepare("DELETE FROM wishlist WHERE UserID = ? AND ProductID = ?");
    $delete_stmt->bind_param("is", $user_id, $product_id);
    if ($delete_stmt->execute()) {
        header("Location: wishlist.php?action=removed");
        exit();
    }
}

// 3.2. Xử lý Logic: XÓA TẤT CẢ SẢN PHẨM KHỎI WISHLIST
if (isset($_GET['clear_all']) && $_GET['clear_all'] == 'true') {
    $delete_all_stmt = $con->prepare("DELETE FROM wishlist WHERE UserID = ?");
    $delete_all_stmt->bind_param("i", $user_id);
    if ($delete_all_stmt->execute()) {
        header("Location: wishlist.php?action=cleared");
        exit();
    }
}


// 3.5. Xử lý Logic: THÊM SẢN PHẨM VÀO GIỎ HÀNG TỪ WISHLIST
if (isset($_GET['add_to_cart'])) {
    $product_id = $_GET['add_to_cart'];
    $quantity = 1; // Mặc định thêm 1 sản phẩm

    // Logic mới: Tương tác với CSDL thay vì session
    // 1. Kiểm tra xem sản phẩm đã có trong giỏ hàng của người dùng chưa
    $check_cart_stmt = $con->prepare("SELECT Quantity FROM cart WHERE UserID = ? AND ProductID = ?");
    $check_cart_stmt->bind_param("is", $user_id, $product_id);
    $check_cart_stmt->execute();
    $cart_result = $check_cart_stmt->get_result();

    if ($cart_result->num_rows > 0) {
        // Nếu đã có, cập nhật số lượng (cộng thêm 1)
        $row = $cart_result->fetch_assoc();
        $new_quantity = $row['Quantity'] + $quantity;
        $update_cart_stmt = $con->prepare("UPDATE cart SET Quantity = ? WHERE UserID = ? AND ProductID = ?");
        $update_cart_stmt->bind_param("iis", $new_quantity, $user_id, $product_id);
        $update_cart_stmt->execute();
    } else {
        // Nếu chưa có, thêm mới vào giỏ hàng
        $insert_cart_stmt = $con->prepare("INSERT INTO cart (UserID, ProductID, Quantity) VALUES (?, ?, ?)");
        $insert_cart_stmt->bind_param("isi", $user_id, $product_id, $quantity);
        $insert_cart_stmt->execute();
    }

    // Chuyển hướng lại trang wishlist để hiển thị thông báo
    header("Location: wishlist.php?action=cart_added");
    exit();
}

?>

<style>
    .book-card {
        background: var(--glass-bg);
        backdrop-filter: blur(15px);
        border: var(--glass-border);
        border-radius: 16px;
        padding: 15px;
        transition: all 0.3s ease;
        height: 100%;
        position: relative;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
        display: flex;
        flex-direction: column;
    }

    .book-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        background: rgba(255, 255, 255, 0.95);
    }

    .book-img {
        width: 100%;
        aspect-ratio: 2/3;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .book-title {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        font-size: 1.1rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 2.6em;
        margin-bottom: 5px;
    }

    .price-tag {
        color: var(--primary);
        font-weight: 700;
        font-size: 1.1rem;
    }

    .card-footer-actions {
        margin-top: auto;
        padding-top: 15px;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    .empty-wishlist {
        padding: 80px 0;
        text-align: center;
        background: var(--glass-bg);
        backdrop-filter: blur(15px);
        border: var(--glass-border);
        border-radius: 20px;
    }
</style>

<!-- ============== Wishlist Content ==============-->
<div class="container" style="padding-top: 40px; padding-bottom: 50px;">
    <!-- NEW: Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4" style="background-color: var(--glass-bg); padding: 15px; border-radius: 12px; backdrop-filter: blur(10px); border: var(--glass-border);">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Danh sách yêu thích</li>
        </ol>
    </nav>

    <?php
    // Lấy dữ liệu wishlist
    $query = "SELECT p.* FROM products p JOIN wishlist w ON p.PID = w.ProductID WHERE w.UserID = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $wishlist_result = $stmt->get_result();
    ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold" style="font-family: 'Playfair Display', serif;">Danh sách yêu thích</h1>
            <p class="text-muted">Bộ sưu tập những cuốn sách bạn yêu thích.</p>
        </div>
        <div class="d-flex align-items-center">
            <?php if ($wishlist_result->num_rows > 0) : ?>
                <button onclick="confirmClearAll()" class="btn btn-outline-danger rounded-pill me-2"><i class="fas fa-trash-alt me-1"></i> Xóa tất cả</button>
            <?php endif; ?>
            <a href="index.php" class="btn btn-light rounded-pill"><i class="fas fa-arrow-left me-2"></i>Quay lại cửa hàng</a>
        </div>
    </div>

    <div class="row g-4">
        <?php if ($wishlist_result->num_rows > 0) : ?>
            <?php while ($row = $wishlist_result->fetch_assoc()) :
                $path = "img/books/" . $row['PID'] . ".jpg";
                $link = "description.php?ID=" . $row["PID"];
            ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="book-card">
                        <a href="<?php echo $link; ?>">
                            <img src="<?php echo $path; ?>" class="book-img" alt="<?php echo htmlspecialchars($row['Title']); ?>" onerror="this.src='https://placehold.co/400x600?text=No+Image'">
                        </a>
                        <h5 class="book-title"><a href="<?php echo $link; ?>" class="text-decoration-none text-dark"><?php echo htmlspecialchars($row['Title']); ?></a></h5>
                        <p class="text-muted small mb-2"><i class="fas fa-pen-nib me-1"></i> <?php echo htmlspecialchars($row['Author']); ?></p>
                        <div class="price-tag mb-3"><?php echo number_format($row['Price']); ?> đ</div>

                        <div class="card-footer-actions d-flex gap-2">
                            <a href="wishlist.php?add_to_cart=<?php echo $row['PID']; ?>" class="btn btn-sm btn-primary flex-grow-1"><i class="fas fa-cart-plus"></i></a>
                            <a href="wishlist.php?remove=<?php echo $row['PID']; ?>" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt"></i></a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <!-- Empty State -->
            <div class="col-12">
                <div class="empty-wishlist">
                    <div style="font-size: 4rem; color: #cbd5e1;"><i class="far fa-heart"></i></div>
                    <h3 class="mt-3 text-muted">Danh sách yêu thích của bạn đang trống</h3>
                    <p class="text-muted">Hãy thêm những cuốn sách bạn thích vào đây nhé.</p>
                    <a href="index.php" class="btn btn-primary rounded-pill px-4 mt-3" style="background: var(--primary);">Xem sách</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Xác nhận xóa tất cả sản phẩm khỏi wishlist
    function confirmClearAll() {
        Swal.fire({
            title: 'Bạn chắc chắn?',
            text: "Hành động này sẽ xóa tất cả sách khỏi danh sách yêu thích của bạn!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Vâng, xóa tất cả!',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "wishlist.php?clear_all=true";
            }
        })
    }
</script>

<?php
include 'footer.php'; // Sử dụng footer chung
?>