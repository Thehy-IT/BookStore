<?php
include 'header.php'; // Sử dụng header chung

// --- XỬ LÝ LOGIC LỌC, TÌM KIẾM VÀ SẮP XẾP ---
$whereClauses = [];
$urlParams = []; // Mảng để giữ các tham số trên URL cho phân trang

// 1. Lọc theo Thể loại
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $cat = mysqli_real_escape_string($con, $_GET['category']);
    $whereClauses[] = "Category = '$cat'";
    $urlParams['category'] = $cat;
}
// 2. Lọc theo Từ khóa
if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
    $key = mysqli_real_escape_string($con, $_GET['keyword']);
    $whereClauses[] = "(Title LIKE '%$key%' OR Author LIKE '%$key%')";
    $urlParams['keyword'] = $key;
}
// 3. Lọc theo Khoảng giá
if (isset($_GET['min_price']) && is_numeric($_GET['min_price'])) {
    $min_price = (int)$_GET['min_price'];
    $whereClauses[] = "Price >= $min_price";
    $urlParams['min_price'] = $min_price;
}
if (isset($_GET['max_price']) && is_numeric($_GET['max_price'])) {
    $max_price = (int)$_GET['max_price'];
    $whereClauses[] = "Price <= $max_price";
    $urlParams['max_price'] = $max_price;
}
// 4. Lọc theo Đánh giá (Giả định có cột Rating trong DB)
if (isset($_GET['rating']) && is_numeric($_GET['rating'])) {
    $rating = (int)$_GET['rating'];
    // Giả định bảng products có cột 'Rating'
    // $whereClauses[] = "Rating >= $rating"; 
    $urlParams['rating'] = $rating;
    // Do chưa có cột Rating, logic này sẽ được comment lại ở phần truy vấn
    // nhưng vẫn giữ để bạn dễ dàng mở rộng sau này.
}

// 5. Sắp xếp
$sort_option = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$urlParams['sort'] = $sort_option;
$sql_sort = "";
switch ($sort_option) {
    case 'price_asc':
        $sql_sort = "ORDER BY Price ASC";
        break;
    case 'price_desc':
        $sql_sort = "ORDER BY Price DESC";
        break;
    case 'bestseller':
        $sql_sort = "ORDER BY PID ASC";
        break; // Giả định bán chạy theo PID
    default:
        $sql_sort = "ORDER BY PID DESC";
        break; // Mới nhất
}

// --- Ghép câu truy vấn SQL ---
$sql = "SELECT * FROM products";
if (count($whereClauses) > 0) {
    $sql .= " WHERE " . implode(' AND ', $whereClauses);
}
$sql .= " " . $sql_sort;

// --- PHÂN TRANG (PAGINATION) ---
$results_per_page = 9;
$result_count = mysqli_query($con, $sql);
$number_of_results = mysqli_num_rows($result_count);
$number_of_pages = ceil($number_of_results / $results_per_page);

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page > $number_of_pages) $page = $number_of_pages;
if ($page < 1) $page = 1;

$this_page_first_result = ($page - 1) * $results_per_page;

$sql .= " LIMIT " . $this_page_first_result . ',' . $results_per_page;
$result = mysqli_query($con, $sql);

// --- Lấy danh sách thể loại tự động từ cơ sở dữ liệu ---
// Mảng ánh xạ từ giá trị trong DB sang tên hiển thị tiếng Việt
$category_translations = [
    'academic and professional' => 'Học thuật & Chuyên ngành',
    'biographies and auto biographies' => 'Tiểu sử & Tự truyện',
    'business and management' => 'Kinh doanh & Quản lý',
    'children and teens' => 'Sách thiếu nhi',
    'health and cooking' => 'Sức khỏe & Nấu ăn',
    'literature and fiction' => 'Văn học & Hư cấu',
    'regional books' => 'Sách tiếng Việt',
    'self-help' => 'Phát triển bản thân',
    'fiction' => 'Tiểu thuyết',
    'thriller' => 'Kinh dị & Giật gân',
    'romance' => 'Lãng mạn',
    'fantasy' => 'Giả tưởng',
];

$categories = [];
$sql_categories = "SELECT DISTINCT Category FROM products WHERE Category IS NOT NULL AND Category != '' ORDER BY Category ASC";
$result_categories = mysqli_query($con, $sql_categories);
if ($result_categories && mysqli_num_rows($result_categories) > 0) {
    while ($row_cat = mysqli_fetch_assoc($result_categories)) {
        $category_key = strtolower(trim($row_cat['Category']));
        // Lấy tên đã dịch, nếu không có thì dùng tên gốc và viết hoa chữ cái đầu
        $categories[$category_key] = $category_translations[$category_key] ?? ucfirst($category_key);
    }
}

// Lấy category hiện tại từ URL để đánh dấu 'active'
$current_category = isset($_GET['category']) ? $_GET['category'] : '';

// Tạo chuỗi query cho URL phân trang
$pagination_query_string = http_build_query(array_merge($urlParams, ['page' => '']));

?>
<script src="https://cdn.jsdelivr.net/npm/nouislider/distribute/nouislider.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nouislider/distribute/nouislider.min.css">

<style>
    /* --- List View Mode Styles --- */
    .list-view .product-card {
        flex-direction: row;
        height: 220px;
    }

    .list-view .card-img-top-wrapper {
        width: 160px;
        padding-top: 0;
        flex-shrink: 0;
    }

    .list-view .card-body-glass {
        align-items: flex-start;
        justify-content: center;
    }

    .list-view .book-desc {
        display: block !important;
        font-size: 0.9rem;
        color: #64748b;
        margin: 10px 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .list-view .card-actions {
        position: static;
        transform: none;
        opacity: 1;
        margin-top: 15px;
    }
    
    /* --- Sidebar Filter Style --- */
    .sidebar-glass {
        background: var(--glass-bg);
        backdrop-filter: blur(16px);
        border-radius: 20px;
        padding: 25px;
        border: var(--glass-border);
        box-shadow: var(--glass-shadow);
        position: sticky;
        top: 120px; /* Sticky sidebar */
    }

    .filter-title {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        border-bottom: 2px solid var(--accent);
        padding-bottom: 10px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>

    <!-- Header Banner -->
    <div class="container" style="padding-top: 20px;">
        <div class="p-5 rounded-4 text-white position-relative overflow-hidden shadow-lg"
            style="background: linear-gradient(135deg, #0f172a 0%, #334155 100%);">
            <div class="position-relative" style="z-index: 2;">
                <h1 class="display-5 fw-bold font-playfair">Khám Phá Tri Thức</h1>
                <p class="lead opacity-75 col-md-8">Hàng ngàn đầu sách thuộc mọi lĩnh vực đang chờ bạn khám phá.</p>
            </div>
            <i class="fas fa-book-reader position-absolute bottom-0 end-0 mb-n4 me-n4 opacity-10" style="font-size: 15rem; transform: rotate(-15deg);"></i>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="row g-4">

            <!-- Sidebar Filters -->
            <div class="col-lg-3">
                <div class="sidebar-glass">
                    <div class="filter-title">
                        <span><i class="fas fa-filter me-2"></i>Bộ lọc</span>
                        <a href="Product.php" class="text-decoration-none text-muted small" style="font-size: 0.8rem;">Xóa lọc</a>
                    </div>

                    <!-- Search -->
                    <form action="Product.php" method="GET" class="mb-4" id="searchFormSidebar">
                        <div class="input-group">
                            <input type="text" name="keyword" class="form-control border-end-0 bg-light" placeholder="Tìm trong trang..." value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">
                            <button class="btn btn-light border border-start-0" type="submit"><i class="fas fa-search text-muted"></i></button>
                        </div>
                    </form>

                    <!-- Categories -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Thể loại</h6>
                        <?php
                        foreach ($categories as $cat_slug => $cat_name) {
                            $active_class = ($current_category == $cat_slug) ? 'fw-bold text-primary' : 'text-dark';
                            echo '<div class="mb-2"><a href="Product.php?category=' . urlencode($cat_slug) . '" class="text-decoration-none ' . $active_class . ' d-flex justify-content-between"><span>' . ucfirst($cat_name) . '</span></a></div>';
                        }
                        ?>
                    </div>

                    <form id="filterForm" action="Product.php" method="GET">
                        <!-- Hidden inputs for existing filters -->
                        <?php if (!empty($current_category)) echo '<input type="hidden" name="category" value="' . htmlspecialchars($current_category) . '">'; ?>
                        <?php if (isset($_GET['keyword'])) echo '<input type="hidden" name="keyword" value="' . htmlspecialchars($_GET['keyword']) . '">'; ?>

                        <!-- Price Range -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Khoảng giá</h6>
                            <div id="price-slider" class="my-4"></div>
                            <div class="d-flex justify-content-between small text-muted">
                                <span id="min-price-display">0đ</span>
                                <span id="max-price-display">1,000,000đ</span>
                            </div>
                            <input type="hidden" name="min_price" id="min_price" value="<?php echo isset($_GET['min_price']) ? $_GET['min_price'] : '0'; ?>">
                            <input type="hidden" name="max_price" id="max_price" value="<?php echo isset($_GET['max_price']) ? $_GET['max_price'] : '1000000'; ?>">
                        </div>

                        <!-- Rating -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Đánh giá</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="rating" value="4" id="star4" onchange="this.form.submit()" <?php if (isset($_GET['rating']) && $_GET['rating'] == 4) echo 'checked'; ?>>
                                <label class="form-check-label text-warning" for="star4"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i> &amp; Up</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="rating" value="3" id="star3" onchange="this.form.submit()" <?php if (isset($_GET['rating']) && $_GET['rating'] == 3) echo 'checked'; ?>>
                                <label class="form-check-label text-warning" for="star3"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i> &amp; Up</label>
                            </div>
                        </div>
                        <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort_option); ?>">
                    </form>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="col-lg-9">
                <!-- Toolbar -->
                <div class="d-flex justify-content-between align-items-center mb-4 p-3 rounded-3" style="background: rgba(255,255,255,0.4); border: 1px solid rgba(255,255,255,0.5);">
                    <div class="text-muted small">
                        Tìm thấy <b><?php echo $number_of_results; ?></b> kết quả
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <form id="sortForm" action="Product.php" method="GET" class="d-flex align-items-center">
                            <?php foreach ($urlParams as $key => $value) {
                                if ($key != 'sort') echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
                            } ?>
                            <select name="sort" class="form-select form-select-sm border-0 bg-white shadow-sm" style="width: 150px;" onchange="this.form.submit()">
                                <option value="newest" <?php if ($sort_option == 'newest') echo 'selected'; ?>>Mới nhất</option>
                                <option value="price_asc" <?php if ($sort_option == 'price_asc') echo 'selected'; ?>>Giá tăng dần</option>
                                <option value="price_desc" <?php if ($sort_option == 'price_desc') echo 'selected'; ?>>Giá giảm dần</option>
                                <option value="bestseller" <?php if ($sort_option == 'bestseller') echo 'selected'; ?>>Bán chạy</option>
                            </select>
                        </form>
                        <div class="btn-group shadow-sm ms-2">
                            <button class="btn btn-white btn-sm active" id="btnGridView"><i class="fas fa-th-large"></i></button>
                            <button class="btn btn-white btn-sm" id="btnListView"><i class="fas fa-list"></i></button>
                        </div>
                    </div>
                </div>

                <!-- Products Loop -->
                <div class="row g-4" id="productsContainer">
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_array($result)) {
                            $img = "img/books/" . $row['PID'] . ".jpg";
                            // Fallback image logic

                            // Tính giảm giá giả lập nếu chưa có
                            $discount_html = "";
                            if ($row['Discount'] > 0) {
                                $discount_html = '<span class="badge bg-danger position-absolute top-0 start-0 m-3 shadow-sm">-' . $row['Discount'] . '%</span>';
                            }
                    ?>
                            <div class="col-md-4 col-sm-6 product-item">
                                <div class="product-card">
                                    <?php echo $discount_html; ?>

                                    <div class="card-img-top-wrapper">
                                        <img src="<?php echo $img; ?>" onerror="this.src='https://placehold.co/400x600/eee/31343C?text=No+Image'" alt="<?php echo $row['Title']; ?>">
                                        <div class="card-actions">
                                            <button class="btn-action" onclick="openQuickView('<?php echo $row['PID']; ?>')" title="Xem nhanh" data-bs-toggle="tooltip"><i class="fas fa-eye"></i></button>
                                            <a href="wishlist.php?ID=<?php echo $row['PID']; ?>" class="btn-action" title="Yêu thích" data-bs-toggle="tooltip"><i class="fas fa-heart"></i></a>
                                            <a href="cart.php?ID=<?php echo $row['PID']; ?>&quantity=1" class="btn-action bg-dark text-white" title="Thêm vào giỏ" data-bs-toggle="tooltip"><i class="fas fa-cart-plus"></i></a>
                                        </div>
                                    </div>

                                    <div class="card-body-glass">
                                        <div class="book-category"><?php echo $category_translations[strtolower($row['Category'])] ?? ucfirst($row['Category']); ?></div>
                                        <a href="description.php?ID=<?php echo $row['PID']; ?>" class="text-decoration-none text-dark">
                                            <h5 class="book-title text-truncate"><?php echo $row['Title']; ?></h5>
                                        </a>
                                        <div class="book-author text-truncate">Tác giả: <?php echo $row['Author']; ?></div>

                                        <!-- Mô tả ngắn chỉ hiện ở List View -->
                                        <p class="book-desc d-none">
                                            <?php echo substr($row['Description'], 0, 150) . '...'; ?>
                                        </p>

                                        <div class="price-wrapper">
                                            <div>
                                                <span class="current-price"><?php echo number_format($row['Price']); ?> đ</span>
                                                <?php if ($row['MRP'] > $row['Price']) { ?>
                                                    <span class="old-price"><?php echo number_format($row['MRP']); ?> đ</span>
                                                <?php } ?>
                                            </div>
                                            <div class="text-warning small">
                                                <i class="fas fa-star"></i> <?php echo number_format((float)rand(40, 50) / 10, 1); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                        echo '<div class="col-12 text-center py-5"><h4 class="text-muted">Không tìm thấy sách nào phù hợp!</h4><p class="text-muted">Vui lòng thử lại với bộ lọc khác.</p><i class="fas fa-box-open fa-3x text-black-50 mt-3"></i></div>';
                    }
                    ?>
                </div>

                <!-- Pagination -->
                <div class="mt-5 d-flex justify-content-center">
                    <nav>
                        <ul class="pagination">
                            <?php if ($page > 1) { ?>
                                <li class="page-item"><a class="page-link" href="Product.php?<?php echo $pagination_query_string . ($page - 1); ?>"><i class="fas fa-chevron-left"></i></a></li>
                            <?php } ?>

                            <?php
                            for ($i = 1; $i <= $number_of_pages; $i++) {
                                $active = ($i == $page) ? 'active' : '';
                                echo '<li class="page-item ' . $active . '"><a class="page-link" href="Product.php?' . $pagination_query_string . $i . '">' . $i . '</a></li>';
                            }
                            ?>

                            <?php if ($page < $number_of_pages && $number_of_pages > 1) { ?>
                                <li class="page-item"><a class="page-link" href="Product.php?<?php echo $pagination_query_string . ($page + 1); ?>"><i class="fas fa-chevron-right"></i></a></li>
                            <?php } ?>
                        </ul>
                    </nav>
                </div>

            </div>
        </div>
    </div>

    <!-- Quick View Modal (Bootstrap 5) -->
    <div class="modal fade" id="quickViewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 overflow-hidden" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <div class="col-md-5 d-flex align-items-center justify-content-center p-4" style="background: #f8f9fa;">
                            <img id="qv-img" src="" class="img-fluid shadow-lg rounded" style="max-height: 300px;">
                        </div>
                        <div class="col-md-7 p-4">
                            <button type="button" class="btn-close float-end" data-bs-dismiss="modal" aria-label="Close"></button>
                            <span class="badge bg-warning text-dark mb-2" id="qv-cat">Thể loại</span>
                            <h3 class="fw-bold font-playfair mb-1" id="qv-title">Tên sách</h3>
                            <p class="text-muted fst-italic mb-3" id="qv-author">Tên tác giả</p>
                            <h4 class="text-primary fw-bold mb-3" id="qv-price">100.000 đ</h4>
                            <p class="small text-muted mb-4" id="qv-desc">Mô tả ngắn của sách sẽ được hiển thị ở đây để người dùng có cái nhìn tổng quan nhanh chóng.</p>

                            <div class="d-flex gap-2">
                                <a href="#" id="qv-add-cart" class="btn btn-dark rounded-pill px-4 flex-grow-1">Thêm vào giỏ</a>
                                <a href="#" id="qv-detail" class="btn btn-outline-dark rounded-pill px-4">Chi tiết</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Grid/List View Toggle Logic
        const container = document.getElementById('productsContainer');
        const btnGrid = document.getElementById('btnGridView');
        const btnList = document.getElementById('btnListView');
        const items = document.querySelectorAll('.product-item');

        btnList.addEventListener('click', () => {
            container.classList.add('list-view');
            // Change column classes for list view
            items.forEach(item => {
                item.className = 'col-12 product-item'; // Full width for list
            });
            btnList.classList.add('active');
            btnGrid.classList.remove('active');
        });

        btnGrid.addEventListener('click', () => {
            container.classList.remove('list-view');
            // Restore column classes for grid view
            items.forEach(item => {
                item.className = 'col-md-4 col-sm-6 product-item';
            });
            btnGrid.classList.add('active');
            btnList.classList.remove('active');
        });

        // Initialize Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // Fake Quick View Logic (In real app, fetch data via AJAX)
        function openQuickView(id) {
            // Lấy thông tin từ card sản phẩm đã click
            const card = document.querySelector(`a[href="description.php?ID=${id}"]`).closest('.product-card');

            // Điền dữ liệu vào modal
            document.getElementById('qv-img').src = 'img/books/' + id + '.jpg';
            document.getElementById('qv-title').innerText = card.querySelector('.book-title').innerText;
            document.getElementById('qv-add-cart').href = 'cart.php?ID=' + id + '&quantity=1';
            document.getElementById('qv-detail').href = 'description.php?ID=' + id;

            // Show modal
            var myModal = new bootstrap.Modal(document.getElementById('quickViewModal'));
            myModal.show();
        }

        // Price Slider Logic
        document.addEventListener('DOMContentLoaded', function() {
            const priceSlider = document.getElementById('price-slider');
            if (priceSlider) {
                const minPriceInput = document.getElementById('min_price');
                const maxPriceInput = document.getElementById('max_price');
                const minPriceDisplay = document.getElementById('min-price-display');
                const maxPriceDisplay = document.getElementById('max-price-display');

                noUiSlider.create(priceSlider, {
                    start: [parseInt(minPriceInput.value), parseInt(maxPriceInput.value)],
                    connect: true,
                    range: {
                        'min': 0,
                        'max': 1000000
                    },
                    step: 10000,
                    format: {
                        to: function(value) {
                            return Math.round(value);
                        },
                        from: function(value) {
                            return Number(value);
                        }
                    }
                });

                priceSlider.noUiSlider.on('update', function(values, handle) {
                    minPriceInput.value = values[0];
                    maxPriceInput.value = values[1];
                    minPriceDisplay.innerHTML = new Intl.NumberFormat('vi-VN', {
                        style: 'currency',
                        currency: 'VND'
                    }).format(values[0]);
                    maxPriceDisplay.innerHTML = new Intl.NumberFormat('vi-VN', {
                        style: 'currency',
                        currency: 'VND'
                    }).format(values[1]);
                });
                priceSlider.noUiSlider.on('end', function() {
                    document.getElementById('filterForm').submit();
                });
            }
        });

    </script>
<?php
include 'footer.php'; // Sử dụng footer chung
?>