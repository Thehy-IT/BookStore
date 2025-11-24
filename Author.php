<?php
// 1. Include header chung
// File header.php sẽ khởi tạo session và kết nối CSDL ($con)
include 'header.php';

// 2. Xử lý Logic Tác giả & Sắp xếp (SAU KHI ĐÃ CÓ KẾT NỐI CSDL)
$author = ""; // Khởi tạo biến

// Ưu tiên lấy từ GET (khi click link), nếu không thì lấy từ Session, nếu không nữa thì báo lỗi
if (isset($_GET['value'])) {
    $author = $_GET['value'];
    $_SESSION['author'] = $author;
} elseif (isset($_SESSION['author'])) {
    $author = $_SESSION['author'];
} else {
    $author = "Unknown Author";
}

// 3. Xử lý Sắp xếp (Tối ưu hóa switch-case)
$sort_option = isset($_POST['sort']) ? $_POST['sort'] : 'default';
$sql_sort = "";

switch ($sort_option) {
    case 'price_asc':
        $sql_sort = "ORDER BY Price ASC";
        break;
    case 'price_desc':
        $sql_sort = "ORDER BY Price DESC";
        break;
    case 'discount_asc':
        $sql_sort = "ORDER BY Discount ASC";
        break; // Sửa lại logic cũ (Low to High)
    case 'discount_desc':
        $sql_sort = "ORDER BY Discount DESC";
        break;
    default:
        $sql_sort = "";
        break;
}

// 4. Truy vấn CSDL (Prepared Statement)
$query = "SELECT * FROM products WHERE Author = ? " . $sql_sort;
$stmt = $con->prepare($query);
$stmt->bind_param("s", $author);
$stmt->execute();
$result = $stmt->get_result();
?>

<style>
    /* Kế thừa các biến CSS từ header.php */
    :root {
        --primary: #0f172a;
        --accent: #d4af37;
        --glass-bg: rgba(255, 255, 255, 0.65);
        --glass-border: 1px solid rgba(255, 255, 255, 0.5);
        --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
    }

    /* --- NEW: Author Header Section --- */
    .author-hero {
        margin-top: 80px;
        /* Khoảng cách từ navbar */
        padding: 60px 0;
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.05), transparent),
            linear-gradient(225deg, rgba(212, 175, 55, 0.05), transparent);
        border-radius: 24px;
        margin-bottom: 50px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .author-title {
        font-family: 'Playfair Display', serif;
        font-size: 3.5rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 15px;
    }

    /* --- NEW: Sort Bar --- */
    .sort-bar {
        background: rgba(255, 255, 255, 0.5);
        backdrop-filter: blur(8px);
        border-radius: 50px;
        padding: 8px 15px;
        border: 1px solid rgba(255, 255, 255, 0.8);
        display: inline-flex;
        align-items: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .form-select-glass {
        background: transparent;
        border: none;
        font-weight: 500;
        color: var(--primary);
        cursor: pointer;
        padding-right: 30px;
        padding-left: 5px;
    }

    .form-select-glass:focus {
        box-shadow: none;
    }

    /* --- NEW: Modern Book Card (Consistent with index.php) --- */
    .book-card-glass {
        background: var(--glass-bg);
        backdrop-filter: blur(15px);
        border: var(--glass-border);
        border-radius: 20px;
        padding: 15px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .book-card-glass:hover {
        transform: translateY(-10px);
        background: rgba(255, 255, 255, 0.95);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
        border-color: var(--accent);
    }

    .book-img-wrapper {
        position: relative;
        border-radius: 15px;
        overflow: hidden;
        margin-bottom: 15px;
        aspect-ratio: 2/3;
    }

    .book-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .book-card-glass:hover .book-img-wrapper img {
        transform: scale(1.1);
    }

    .action-overlay {
        position: absolute;
        bottom: -50px;
        /* Start hidden */
        left: 0;
        width: 100%;
        display: flex;
        justify-content: center;
        gap: 10px;
        transition: bottom 0.3s ease;
        padding-bottom: 10px;
    }

    .book-card-glass:hover .action-overlay {
        bottom: 10px;
        /* Slide in on hover */
    }

    .btn-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn-icon:hover {
        background: var(--accent);
        transform: scale(1.1) rotate(15deg);
    }

    .discount-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: linear-gradient(45deg, #ef4444, #f87171);
        color: white;
        font-weight: 700;
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 20px;
        z-index: 2;
        box-shadow: 0 3px 8px rgba(239, 68, 68, 0.4);
    }

    .book-title {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        font-size: 1.15rem;
        margin-bottom: 8px;
    }

    .price-current {
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--primary);
    }

    .price-old {
        text-decoration: line-through;
        color: #94a3b8;
        font-size: 0.9rem;
        margin-left: 5px;
    }
</style>

<!-- ============== Author Content ==============-->
<div class="container">

    <!-- Header -->
    <div class="author-hero">
        <h5 class="text-muted text-uppercase letter-spacing-2 mb-3">Bộ sưu tập của tác giả</h5>
        <h1 class="author-title"><?php echo htmlspecialchars($author); ?></h1>
        <p class="lead text-muted col-md-6 mx-auto">Khám phá những tác phẩm đặc sắc nhất từ một trong những tác giả được yêu thích.</p>

        <!-- Sort Dropdown -->
        <div class="sort-bar mt-4">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>?value=<?php echo urlencode($author); ?>" method="post" id="sortForm">
                <i class="fas fa-sort-amount-down text-muted me-2"></i>
                <label class="me-2 small text-uppercase fw-bold text-muted">Sắp xếp:</label>
                <select name="sort" class="form-select form-select-glass" onchange="document.getElementById('sortForm').submit()">
                    <option value="default" <?php if ($sort_option == 'default') echo 'selected'; ?>>Mặc định</option>
                    <option value="price_asc" <?php if ($sort_option == 'price_asc') echo 'selected'; ?>>Giá: Thấp đến Cao</option>
                    <option value="price_desc" <?php if ($sort_option == 'price_desc') echo 'selected'; ?>>Giá: Cao đến Thấp</option>
                    <option value="discount_desc" <?php if ($sort_option == 'discount_desc') echo 'selected'; ?>>Giảm giá tốt nhất</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row g-4 row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 pb-5">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()):
                $path = "img/books/" . $row['PID'] . ".jpg";
                $link = "description.php?ID=" . $row["PID"];
            ?>
                <div class="col">
                    <div class="book-card-glass h-100 d-flex flex-column">
                        <!-- Discount -->
                        <?php if ($row['Discount'] > 0): ?>
                            <div class="discount-badge">-<?php echo $row['Discount']; ?>%</div>
                        <?php endif; ?>

                        <!-- Image -->
                        <div class="book-img-wrapper">
                            <img src="<?php echo $path; ?>" alt="<?php echo htmlspecialchars($row['Title']); ?>" onerror="this.src='https://placehold.co/400x600/eee/31343C?text=Book+Cover'">
                            <div class="action-overlay">
                                <a href="cart.php?ID=<?php echo $row['PID']; ?>&quantity=1" class="btn-icon" title="Thêm vào giỏ"><i class="fas fa-shopping-cart"></i></a>
                                <a href="<?php echo $link; ?>" class="btn-icon" title="Xem chi tiết"><i class="fas fa-eye"></i></a>
                                <a href="wishlist.php?ID=<?php echo $row['PID']; ?>" class="btn-icon" title="Yêu thích"><i class="fas fa-heart"></i></a>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="mt-auto">
                            <h6 class="book-title fw-bold text-truncate" title="<?php echo htmlspecialchars($row['Title']); ?>">
                                <a href="<?php echo $link; ?>" class="text-decoration-none text-dark"><?php echo $row['Title']; ?></a>
                            </h6>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="price-current">
                                    <?php echo number_format($row['Price']); ?> đ
                                    <?php if ($row['MRP'] > $row['Price']): ?>
                                        <span class="price-old"><?php echo number_format($row['MRP']); ?> đ</span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-warning small"><i class="fas fa-star"></i> 4.8</div>
                            </div>
                        </div>
                        <a href="<?php echo $link; ?>" class="stretched-link"></a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <!-- Empty State -->
            <div class="col-12 text-center py-5">
                <div class="text-muted mb-3" style="font-size: 4rem;"><i class="fas fa-feather-alt"></i></div>
                <h3>Không tìm thấy sách của tác giả này.</h3>
                <p class="text-muted">Chúng tôi đang cập nhật bộ sưu tập. Vui lòng quay lại sau.</p>
                <a href="index.php" class="btn btn-primary-glass mt-3">Quay về Trang chủ</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
include 'footer.php'; // Thêm footer để hoàn thiện trang
?>