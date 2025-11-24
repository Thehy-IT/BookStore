<?php
include 'header.php'; // Sử dụng header chung

$pid = isset($_GET['ID']) ? $_GET['ID'] : '';

// Query DB (Prepared Statement)
$stmt = $con->prepare("SELECT * FROM products WHERE PID = ?");
$stmt->bind_param("s", $pid);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Nếu không tìm thấy sách
if (!$row) {
    echo "<script>alert('Không tìm thấy sách!'); window.location.href='index.php';</script>";
    exit();
}

// --- Lấy sản phẩm liên quan (cùng thể loại, trừ sản phẩm hiện tại) ---
$related_products = [];
$current_category = $row['Category'];
$current_pid = $row['PID'];

$related_stmt = $con->prepare("SELECT * FROM products WHERE Category = ? AND PID != ? ORDER BY RAND() LIMIT 10");
$related_stmt->bind_param("ss", $current_category, $current_pid);
$related_stmt->execute();
$related_result = $related_stmt->get_result();
while ($related_row = $related_result->fetch_assoc()) {
    $related_products[] = $related_row;
}

// Lấy mảng dịch thể loại từ header.php
$category_translations = [
    'academic and professional' => 'Học thuật & Chuyên ngành', 'biographies and auto biographies' => 'Tiểu sử & Tự truyện',
    'business and management' => 'Kinh doanh & Quản lý', 'children and teens' => 'Sách thiếu nhi',
    'health and cooking' => 'Sức khỏe & Nấu ăn', 'literature and fiction' => 'Văn học & Hư cấu',
    'regional books' => 'Sách tiếng Việt', 'self-help' => 'Phát triển bản thân',
    'fiction' => 'Tiểu thuyết', 'thriller' => 'Kinh dị & Giật gân',
    'romance' => 'Lãng mạn', 'fantasy' => 'Giả tưởng',
];
?>
<style>
    /* Các style này đặc thù cho trang chi tiết sản phẩm */

    /* --- Main Product Card --- */
    .product-glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border: var(--glass-border);
        border-radius: 24px;
        padding: 30px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
    }

    .book-cover-wrapper {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        transition: 0.3s;
    }

    .book-cover-wrapper:hover {
        transform: scale(1.02);
    }

    .book-cover-wrapper img {
        width: 100%;
        height: auto;
        object-fit: cover;
    }

    .discount-badge {
        position: absolute;
        top: 20px;
        left: 20px;
        background: #ef4444;
        color: white;
        font-weight: 800;
        padding: 8px 16px;
        border-radius: 30px;
        z-index: 2;
        box-shadow: 0 5px 15px rgba(239, 68, 68, 0.4);
    }

    .book-title {
        font-family: 'Playfair Display', serif;
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .meta-tags span {
        background: rgba(15, 23, 42, 0.05);
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        margin-right: 10px;
        color: #64748b;
    }

    .price-area {
        margin: 25px 0;
        padding: 20px;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.8);
    }

    .price-current {
        font-size: 2rem;
        font-weight: 800;
        color: var(--primary);
    }

    .price-old {
        text-decoration: line-through;
        color: #94a3b8;
        font-size: 1.2rem;
        margin-left: 10px;
    }

    .btn-add-cart {
        background: linear-gradient(45deg, var(--primary), #1e293b);
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 12px;
        font-weight: 600;
        width: 100%;
        transition: 0.3s;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.2);
    }

    .btn-add-cart:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(15, 23, 42, 0.3);
        background: linear-gradient(45deg, var(--accent), #b39065);
        color: white;
    }

    /* --- Detail Table --- */
    .details-list {
        list-style: none;
        padding: 0;
    }

    .details-list li {
        display: flex;
        justify-content: space-between;
        padding: 12px 15px;
        border-radius: 8px;
        transition: background-color 0.2s ease;
    }

    .details-list li:nth-child(odd) {
        background-color: rgba(255, 255, 255, 0.4);
    }

    .details-list li:hover {
        background-color: rgba(255, 255, 255, 0.8);
    }

    /* --- Service Cards --- */
    .service-card {
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(10px);
        padding: 25px;
        border-radius: 16px;
        text-align: center;
        height: 100%;
        border: 1px solid rgba(255, 255, 255, 0.5);
        transition: 0.3s;
    }

    .service-card:hover {
        background: white;
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
    }

    .service-icon {
        font-size: 2rem;
        color: var(--accent);
        margin-bottom: 15px;
    }
</style>

<!-- ============== Product Details ==============-->
<div class="container pb-5" style="padding-top: 100px;">
    <!-- NEW: Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4" style="background-color: var(--glass-bg); padding: 15px; border-radius: 12px; backdrop-filter: blur(10px); border: var(--glass-border);">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="Product.php">Cửa hàng</a></li>
            <?php
            $category_slug = strtolower(trim($row['Category']));
            $category_name = $category_translations[$category_slug] ?? ucfirst($category_slug);
            echo '<li class="breadcrumb-item"><a href="Product.php?category=' . urlencode($category_slug) . '">' . htmlspecialchars($category_name) . '</a></li>';
            ?>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($row['Title']); ?></li>
        </ol>
    </nav>
    <div class="product-glass-card mb-5">
        <div class="row g-5">

            <!-- Left Column: Image -->
            <div class="col-lg-5">
                <div class="book-cover-wrapper">
                    <?php if ($row['Discount'] > 0): ?>
                        <div class="discount-badge">-<?php echo $row['Discount']; ?>%</div>
                    <?php endif; ?>
                    <img src="img/books/<?php echo $row['PID']; ?>.jpg" alt="<?php echo $row['Title']; ?>" onerror="this.src='https://placehold.co/600x900?text=Book+Cover'">
                </div>
            </div>

            <!-- Right Column: Info -->
            <div class="col-lg-7">
                <h1 class="book-title"><?php echo $row['Title']; ?></h1>

                <div class="meta-tags mb-4">
                    <span><i class="fas fa-pen-nib me-1"></i> <?php echo $row['Author']; ?></span>
                    <span><i class="fas fa-building me-1"></i> <?php echo $row['Publisher']; ?></span>
                </div>

                <div class="price-area d-flex align-items-center justify-content-between">
                    <div>
                        <span class="price-current"><?php echo number_format($row['Price']); ?> đ</span>
                        <?php if ($row['MRP'] > $row['Price']): ?>
                            <span class="price-old"><?php echo number_format($row['MRP']); ?> đ</span>
                        <?php endif; ?>
                    </div>
                    <div class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Còn hàng</div>
                </div>

                <p class="text-muted mb-4" style="line-height: 1.8;">
                    <?php echo $row['Description']; ?>
                </p>

                <!-- Quantity & Add to Cart Form -->
                <div class="row align-items-end mb-5">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label fw-bold small text-uppercase">Số lượng</label>
                        <select class="form-select form-select-lg border-0 bg-light rounded-3" id="qtySelect">
                            <?php
                            $max_qty = ($row['Available'] > 10) ? 10 : $row['Available'];
                            for ($i = 1; $i <= $max_qty; $i++) {
                                echo "<option value='$i'>$i</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <button onclick="addToCart('<?php echo $row['PID']; ?>')" class="btn-add-cart">
                            <i class="fas fa-shopping-bag me-2"></i> Thêm vào giỏ hàng
                        </button>
                    </div>
                </div>

                <!-- Technical Specs -->
                <h5 class="fw-bold mb-3 border-bottom pb-2">Chi tiết sản phẩm</h5>
                <ul class="details-list">
                    <li>
                        <span class="text-muted">Mã sản phẩm</span>
                        <strong class="text-dark"><?php echo $row['PID']; ?></strong>
                    </li>
                    <li>
                        <span class="text-muted">Phiên bản</span>
                        <strong class="text-dark"><?php echo $row['Edition']; ?></strong>
                    </li>
                    <li>
                        <span class="text-muted">Ngôn ngữ</span>
                        <strong class="text-dark"><?php echo $row['Language']; ?></strong>
                    </li>
                    <li>
                        <span class="text-muted">Số trang</span>
                        <strong class="text-dark"><?php echo $row['page']; ?></strong>
                    </li>
                    <li>
                        <span class="text-muted">Trọng lượng</span>
                        <strong class="text-dark"><?php echo $row['weight']; ?></strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- ============== Services Section ==============-->
<div class="container mb-5">
    <div class="row g-4">
        <div class="col-md-3 col-6">
            <div class="service-card">
                <i class="fas fa-headset service-icon"></i>
                <h6 class="fw-bold">Hỗ trợ 24/7</h6>
                <p class="small text-muted mb-0">Gọi chúng tôi bất cứ lúc nào</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="service-card">
                <i class="fas fa-shield-alt service-icon"></i>
                <h6 class="fw-bold">Thanh toán an toàn</h6>
                <p class="small text-muted mb-0">Bảo mật 100%</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="service-card">
                <i class="fas fa-undo service-icon"></i>
                <h6 class="fw-bold">Đổi trả dễ dàng</h6>
                <p class="small text-muted mb-0">Không hỏi lý do</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="service-card">
                <i class="fas fa-truck service-icon"></i>
                <h6 class="fw-bold">Giao hàng nhanh</h6>
                <p class="small text-muted mb-0">Giao hàng toàn quốc</p>
            </div>
        </div>
    </div>
</div>

<!-- ============== NEW: Related Products Section ==============-->
<?php if (!empty($related_products)): ?>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h6 class="text-uppercase text-warning fw-bold ls-2">Bạn cũng có thể thích</h6>
                <h2 class="fw-bold display-6">Sản phẩm liên quan</h2>
            </div>
            <a href="Product.php?category=<?php echo urlencode($current_category); ?>" class="btn btn-outline-dark rounded-pill px-4">Xem tất cả</a>
        </div>
        <div class="swiper related-products-swiper">
            <div class="swiper-wrapper">
                <?php foreach ($related_products as $related_book): ?>
                    <div class="swiper-slide h-auto">
                        <div class="book-card-glass h-100 d-flex flex-column">
                            <?php if ($related_book['Discount'] > 0): ?>
                                <div class="badge-glass badge-sale"><span>-<?php echo $related_book['Discount']; ?>%</span></div>
                            <?php endif; ?>
                            <div class="book-img-wrapper">
                                <img src="img/books/<?php echo $related_book['PID']; ?>.jpg" onerror="this.src='https://placehold.co/400x600/eee/31343C?text=Book+Cover'" alt="<?php echo htmlspecialchars($related_book['Title']); ?>">
                                <div class="action-overlay">
                                    <a href="cart.php?ID=<?php echo $related_book['PID']; ?>&quantity=1" class="btn-icon" title="Thêm vào giỏ"><i class="fas fa-shopping-cart"></i></a>
                                    <a href="description.php?ID=<?php echo $related_book['PID']; ?>" class="btn-icon" title="Xem chi tiết"><i class="fas fa-eye"></i></a>
                                </div>
                            </div>
                            <div class="mt-auto">
                                <h6 class="fw-bold text-truncate" title="<?php echo htmlspecialchars($related_book['Title']); ?>"><?php echo htmlspecialchars($related_book['Title']); ?></h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-primary fw-bold"><?php echo number_format($related_book['Price']); ?> đ</span>
                                    <div class="text-warning small"><i class="fas fa-star"></i> 4.8</div>
                                </div>
                            </div>
                            <a href="description.php?ID=<?php echo $related_book['PID']; ?>" class="stretched-link"></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination mt-4 position-relative"></div>
        </div>
    </div>
<?php endif; ?>

<!-- Logic Add to Cart (đã có trong footer.php) -->
<script>
    function addToCart(pid) {
        var qty = document.getElementById('qtySelect').value;
        // Chuyển hướng đến trang xử lý giỏ hàng
        window.location.href = "cart.php?ID=" + pid + "&quantity=" + qty;
    }

    // Init Related Products Swiper
    document.addEventListener('DOMContentLoaded', function() {
        const relatedSwiper = new Swiper('.related-products-swiper', {
            slidesPerView: 2,
            spaceBetween: 20,
            loop: false,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true
            },
            breakpoints: {
                768: {
                    slidesPerView: 4,
                },
                992: {
                    slidesPerView: 5,
                }
            }
        });
    });
</script>
<?php
include 'footer.php'; // Sử dụng footer chung
?>