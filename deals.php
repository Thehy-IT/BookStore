<?php
include 'header.php'; // Bao gồm header để khởi tạo session và kết nối CSDL ($con)

// --- XỬ LÝ LOGIC LỌC VÀ SẮP XẾP ---
$sort_option = isset($_GET['sort']) ? $_GET['sort'] : 'discount_desc'; // Mặc định giảm giá cao nhất
$sql_sort = "";

switch ($sort_option) {
    case 'price_asc':
        $sql_sort = "ORDER BY Price ASC";
        break;
    case 'price_desc':
        $sql_sort = "ORDER BY Price DESC";
        break;
    case 'discount_desc':
        $sql_sort = "ORDER BY Discount DESC";
        break;
    default:
        $sql_sort = "ORDER BY Discount DESC, Price ASC";
        break;
}

// Truy vấn CSDL để lấy các sản phẩm có khuyến mãi
$query = "SELECT * FROM products WHERE Discount > 0 " . $sql_sort; // Nối chuỗi an toàn vì $sql_sort được kiểm soát nội bộ
$result = mysqli_query($con, $query); // Với ứng dụng lớn, nên dùng prepared statement và phân trang
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

    /* --- NEW: Deals Hero Section --- */
    .deals-hero {
        margin-top: 20px;
        /* Giảm margin-top vì đã có breadcrumb */
        padding: 60px 0;
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.08), transparent),
            linear-gradient(225deg, rgba(15, 23, 42, 0.08), transparent);
        border-radius: 24px;
        margin-bottom: 50px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .deals-title {
        font-family: 'Playfair Display', serif;
        font-size: 3.5rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 15px;
    }

    /* --- Toolbar for sorting --- */
    .deals-toolbar {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: var(--glass-border);
        border-radius: 12px;
        padding: 1rem 1.5rem;
        margin-bottom: 2rem;
    }

    .deals-toolbar .form-select {
        max-width: 200px;
    }

    .form-select-glass:focus {
        box-shadow: none;
    }

    /* --- Modern Book Card (Copied from author.php) --- */
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

    /* --- NEW: Christmas Promotions Section --- */
    .christmas-promo-section {
        background: linear-gradient(135deg, #f8fafc, #eef2f5);
        border-radius: 24px;
        padding: 50px 0;
    }

    .promo-section-title {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        color: var(--primary);
    }

    .promo-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 30px 25px;
        text-align: center;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .promo-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(15, 23, 42, 0.1);
        border-color: var(--accent);
    }

    .promo-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        margin-bottom: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
    }

    .promo-title {
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 10px;
    }

    .promo-description {
        color: #475569;
        margin-bottom: 20px;
        flex-grow: 1;
    }

    .promo-code {
        background: #f1f5f9;
        color: #ef4444;
        padding: 3px 8px;
        border-radius: 6px;
        font-family: 'Courier New', Courier, monospace;
    }
</style>

<!-- ============== Deals Content ==============-->
<div class="container" style="padding-top: 100px;">
    <!-- NEW: Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4" style="background-color: var(--glass-bg); padding: 15px; border-radius: 12px; backdrop-filter: blur(10px); border: var(--glass-border);">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Khuyến mãi</li>
        </ol>
    </nav>
    <!-- Header -->
    <div class="deals-hero">
        <h5 class="text-muted text-uppercase letter-spacing-2 mb-3">Ưu đãi đặc biệt</h5>
        <h1 class="deals-title">Săn Sách Hay, Giá Hời</h1>
        <p class="lead text-muted col-md-6 mx-auto">Đừng bỏ lỡ cơ hội sở hữu những cuốn sách tuyệt vời với mức giá tốt nhất.</p>
    </div>

    <!-- ============== NEW: Christmas Promotions Section ==============-->
    <div class="christmas-promo-section my-5">
        <div class="text-center mb-5 px-3">
            <h2 class="promo-section-title">Quà Tặng Giáng Sinh <i class="fas fa-gifts text-danger"></i></h2>
            <p class="lead text-muted">Những ưu đãi đặc biệt chỉ có trong mùa lễ hội này!</p>
        </div>
        <div class="row g-4">
            <!-- Promo Card 1: Discount Code -->
            <div class="col-lg-4 col-md-6">
                <div class="promo-card">
                    <div class="promo-icon bg-danger"><i class="fas fa-ticket-alt"></i></div>
                    <h5 class="promo-title">Giảm giá 20%</h5>
                    <p class="promo-description">Nhập mã <strong class="promo-code">XMAS2024</strong> để được giảm 20% cho tất cả đơn hàng từ 500.000đ.</p>
                    <button class="btn btn-outline-danger btn-sm" onclick="copyCode('XMAS2024', this)">Sao chép mã</button>
                </div>
            </div>
            <!-- Promo Card 2: Free Gift -->
            <div class="col-lg-4 col-md-6">
                <div class="promo-card">
                    <div class="promo-icon" style="background-color: #10b981;"><i class="fas fa-gift"></i></div>
                    <h5 class="promo-title">Quà tặng miễn phí</h5>
                    <p class="promo-description">Nhận ngay một sổ tay Giáng Sinh xinh xắn cho mọi đơn hàng trên 300.000đ.</p>
                    <a href="#product-grid" class="btn btn-outline-success btn-sm">Mua sắm ngay</a>
                </div>
            </div>
            <!-- Promo Card 3: Free Shipping -->
            <div class="col-lg-4 col-md-12 mx-auto">
                <div class="promo-card">
                    <div class="promo-icon" style="background-color: #3b82f6;"><i class="fas fa-shipping-fast"></i></div>
                    <h5 class="promo-title">Miễn phí vận chuyển</h5>
                    <p class="promo-description">Miễn phí giao hàng toàn quốc cho tất cả các đơn hàng trong dịp Giáng Sinh.</p>
                    <a href="#product-grid" class="btn btn-outline-primary btn-sm">Khám phá sách</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Toolbar: Sorting and Result Count -->
    <div class="deals-toolbar d-flex justify-content-between align-items-center">
        <span class="text-muted">Tìm thấy <strong><?php echo mysqli_num_rows($result); ?></strong> sản phẩm khuyến mãi</span>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" id="sortForm" class="d-flex align-items-center gap-2">
            <label for="sortSelect" class="form-label mb-0 small text-muted">Sắp xếp:</label>
            <select name="sort" id="sortSelect" class="form-select form-select-sm" onchange="document.getElementById('sortForm').submit()">
                <option value="discount_desc" <?php if ($sort_option == 'discount_desc') echo 'selected'; ?>>Giảm giá nhiều nhất</option>
                <option value="price_asc" <?php if ($sort_option == 'price_asc') echo 'selected'; ?>>Giá: Thấp đến Cao</option>
                <option value="price_desc" <?php if ($sort_option == 'price_desc') echo 'selected'; ?>>Giá: Cao đến Thấp</option>
            </select>
        </form>
    </div>

    <!-- Products Grid -->
    <div id="product-grid" class="row g-4 row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 pb-5">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)):
                $path = "img/books/" . $row['PID'] . ".jpg";
                $link = "description.php?ID=" . $row["PID"];
            ?>
                <div class="col">
                    <div class="book-card-glass h-100 d-flex flex-column">
                        <div class="discount-badge">-<?php echo $row['Discount']; ?>%</div>

                        <div class="book-img-wrapper">
                            <img src="<?php echo $path; ?>" alt="<?php echo htmlspecialchars($row['Title']); ?>" onerror="this.src='https://placehold.co/400x600/eee/31343C?text=Book+Cover'">
                            <div class="action-overlay">
                                <a href="cart.php?ID=<?php echo $row['PID']; ?>&quantity=1" class="btn-icon" title="Thêm vào giỏ"><i class="fas fa-shopping-cart"></i></a>
                                <button onclick='openQuickView(<?php echo json_encode($row); ?>)' class="btn-icon" title="Xem nhanh"><i class="fas fa-eye"></i></button>
                                <a href="wishlist.php?ID=<?php echo $row['PID']; ?>" class="btn-icon" title="Yêu thích"><i class="fas fa-heart"></i></a>
                            </div>
                        </div>

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
                <div class="text-muted mb-3" style="font-size: 4rem;"><i class="fas fa-tags"></i></div>
                <h3>Hiện tại không có ưu đãi nào.</h3>
                <p class="text-muted">Vui lòng quay lại sau để săn những cuốn sách giá hời nhé!</p>
                <a href="index.php" class="btn btn-primary-glass mt-3">Quay về Trang chủ</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function copyCode(code, element) {
        navigator.clipboard.writeText(code).then(function() {
            const originalText = element.innerHTML;
            element.innerHTML = 'Đã sao chép!';
            element.disabled = true;
            setTimeout(function() {
                element.innerHTML = originalText;
                element.disabled = false;
            }, 2000);
        }, function(err) {
            alert('Không thể sao chép mã. Vui lòng thử lại.');
        });
    }
</script>

<?php
include 'footer.php'; // Thêm footer để hoàn thiện trang
?>