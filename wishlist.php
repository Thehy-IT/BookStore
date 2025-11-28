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

// Hiển thị thông báo flash nếu có (chuyển xuống sau khi kiểm tra login)
if (isset($_SESSION['flash_message'])) {
    $swal_script = set_swal(
        $_SESSION['flash_type'],
        'Thông báo',
        $_SESSION['flash_message']
    );
    echo $swal_script;
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
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

    /* Tối ưu cho di động */
    @media (max-width: 767.98px) {
        .wishlist-header {
            flex-direction: column;
            align-items: flex-start !important;
        }

        .wishlist-actions {
            width: 100%;
            margin-top: 1rem;
            flex-direction: column;
            gap: 0.5rem;
        }

        .wishlist-actions .btn {
            width: 100%;
        }

        h1.display-4 {
            font-size: 2.5rem;
        }
    }
</style>

<!-- ============== Wishlist Content ==============-->
<div class="container" style="padding-top: 100px; padding-bottom: 50px;">
    <!-- NEW: Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4"
        style="background-color: var(--glass-bg); padding: 15px; border-radius: 12px; backdrop-filter: blur(10px); border: var(--glass-border);">
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
    <!-- Sử dụng class mới để dễ dàng target bằng CSS -->
    <div class="d-flex justify-content-between align-items-center mb-4 wishlist-header">
        <div>
            <h1 class="fw-bold display-4" style="font-family: 'Playfair Display', serif;">Danh sách yêu thích</h1>
            <p class="text-muted">Bộ sưu tập những cuốn sách bạn yêu thích.</p>
        </div>
        <div class="d-flex align-items-center wishlist-actions">
            <?php if ($wishlist_result->num_rows > 0): ?>
                <button onclick="confirmClearAll()" class="btn btn-outline-danger rounded-pill me-2"><i
                        class="fas fa-trash-alt me-1"></i> Xóa tất cả</button>
                <a href="wishlist_action.php?action=add_all_to_cart" class="btn btn-primary rounded-pill me-2"><i
                        class="fas fa-cart-plus me-1"></i> Thêm tất cả vào giỏ</a>
            <?php endif; ?>
            <a href="Product.php" class="btn btn-light rounded-pill"><i class="fas fa-arrow-left me-2"></i>Quay lại cửa
                hàng</a>
        </div>
    </div>

    <div class="row g-4">
        <?php if ($wishlist_result->num_rows > 0): ?>
            <?php while ($row = $wishlist_result->fetch_assoc()):
                $path = "img/books/" . $row['PID'] . ".jpg";
                $link = "description.php?ID=" . $row["PID"];
                ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="book-card">
                        <a href="<?php echo $link; ?>">
                            <img src="<?php echo $path; ?>" class="book-img"
                                alt="<?php echo htmlspecialchars($row['Title']); ?>"
                                onerror="this.src='https://placehold.co/400x600?text=No+Image'">
                        </a>
                        <h5 class="book-title"><a href="<?php echo $link; ?>"
                                class="text-decoration-none text-dark"><?php echo htmlspecialchars($row['Title']); ?></a></h5>
                        <p class="text-muted small mb-2"><i class="fas fa-pen-nib me-1"></i>
                            <?php echo htmlspecialchars($row['Author']); ?></p>
                        <div class="price-tag mb-3"><?php echo number_format($row['Price']); ?> đ</div>

                        <div class="card-footer-actions d-flex gap-2" style="z-index: 2; position: relative;">
                            <a href="wishlist_action.php?action=add_to_cart&id=<?php echo $row['PID']; ?>"
                                class="btn btn-sm btn-primary flex-grow-1" title="Thêm vào giỏ hàng"><i
                                    class="fas fa-cart-plus"></i></a>
                            <a href="wishlist_action.php?action=remove&id=<?php echo $row['PID']; ?>"
                                class="btn btn-sm btn-outline-danger" title="Xóa khỏi danh sách yêu thích"><i
                                    class="fas fa-trash-alt"></i></a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <!-- Empty State -->
            <div class="col-12">
                <div class="empty-wishlist">
                    <div style="font-size: 4rem; color: #cbd5e1;"><i class="far fa-heart"></i></div>
                    <h3 class="mt-3 text-muted">Danh sách yêu thích của bạn đang trống</h3>
                    <p class="text-muted">Hãy thêm những cuốn sách bạn thích vào đây nhé.</p>
                    <a href="index.php" class="btn btn-primary rounded-pill px-4 mt-3"
                        style="background: var(--primary);">Xem sách</a>
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
                window.location.href = "wishlist_action.php?action=clear_all";
            }
        })
    }
</script>

<?php
include 'footer.php'; // Sử dụng footer chung
?>