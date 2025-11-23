<?php
session_start();
include "dbconnect.php"; // Giả định file kết nối DB

// --- Xử lý Logic Lọc & Tìm kiếm ---
$whereClauses = [];
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $cat = mysqli_real_escape_string($con, $_GET['category']);
    $whereClauses[] = "Category = '$cat'";
}
if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
    $key = mysqli_real_escape_string($con, $_GET['keyword']);
    $whereClauses[] = "(Title LIKE '%$key%' OR Author LIKE '%$key%')";
}

// Ghép câu truy vấn
$sql = "SELECT * FROM products";
if (count($whereClauses) > 0) {
    $sql .= " WHERE " . implode(' AND ', $whereClauses);
}
// Mặc định sắp xếp mới nhất
$sql .= " ORDER BY PID DESC";

// --- Phân trang (Pagination) ---
$results_per_page = 9; // Hiển thị 9 cuốn mỗi trang
$result_count = mysqli_query($con, $sql);
$number_of_results = mysqli_num_rows($result_count);
$number_of_pages = ceil($number_of_results / $results_per_page);

if (!isset($_GET['page'])) {
    $page = 1;
} else {
    $page = $_GET['page'];
}
$this_page_first_result = ($page - 1) * $results_per_page;

$sql .= " LIMIT " . $this_page_first_result . ',' . $results_per_page;
$result = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BookZ | Bộ Sưu Tập Sách</title>

    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Libraries -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nouislider/distribute/nouislider.min.css"> <!-- Range Slider -->

    <style>
        :root {
            --primary: #0f172a;
            --accent: #d4af37;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: 1px solid rgba(255, 255, 255, 0.5);
            --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(to bottom right, #e0e8f0, #f8fafc);
            color: var(--primary);
            min-height: 100vh;
        }

        /* --- Background Decoration --- */
        .bg-decoration {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background:
                radial-gradient(circle at 15% 50%, rgba(212, 175, 55, 0.05), transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(15, 23, 42, 0.05), transparent 25%);
        }

        /* --- Navbar Reuse Style (Simplified) --- */
        .navbar-glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            position: sticky;
            top: 0;
            z-index: 1000;
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
            top: 100px;
            /* Sticky sidebar */
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

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        /* --- Product Card Modern Style --- */
        .product-card {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            border: var(--glass-border);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            height: 100%;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.95);
            z-index: 2;
        }

        .card-img-top-wrapper {
            position: relative;
            overflow: hidden;
            padding-top: 150%;
            /* Aspect Ratio 2:3 */
        }

        .card-img-top-wrapper img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .product-card:hover .card-img-top-wrapper img {
            transform: scale(1.08);
        }

        /* Hover Actions Overlay */
        .card-actions {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            gap: 10px;
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 3;
        }

        .product-card:hover .card-actions {
            opacity: 1;
            top: 50%;
        }

        .btn-action {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: white;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: 0.2s;
            text-decoration: none;
        }

        .btn-action:hover {
            background: var(--primary);
            color: var(--accent);
            transform: scale(1.1);
        }

        /* Card Body */
        .card-body-glass {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .book-category {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin-bottom: 5px;
        }

        .book-title {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 5px;
            line-height: 1.3;
        }

        .book-author {
            font-size: 0.9rem;
            color: #64748b;
        }

        .price-wrapper {
            margin-top: auto;
            padding-top: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .current-price {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--primary);
        }

        .old-price {
            text-decoration: line-through;
            font-size: 0.85rem;
            color: #94a3b8;
            margin-left: 5px;
        }

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

        /* Pagination Modern */
        .pagination .page-link {
            border: none;
            background: rgba(255, 255, 255, 0.5);
            color: var(--primary);
            margin: 0 5px;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .pagination .page-item.active .page-link {
            background: var(--primary);
            color: var(--accent);
            box-shadow: 0 5px 15px rgba(15, 23, 42, 0.3);
        }
    </style>
</head>

<body>

    <div class="bg-decoration"></div>

    <!-- ============== Navbar (from index.php) ==============-->
    <header class="header-container navbar-glass">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <i class="fas fa-book-open text-warning me-2"></i>
                    <span>BOOK<span style="color: var(--accent)">Z</span></span>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent" aria-controls="navContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navContent">
                    <!-- Menu chính -->
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Trang chủ</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown">Thể loại</a>
                            <ul class="dropdown-menu glass-panel border-0 shadow-lg">
                                <li><a class="dropdown-item" href="Product.php?category=Literature and Fiction">Văn học & Hư cấu</a></li>
                                <li><a class="dropdown-item" href="Product.php?category=Biographies and Auto Biographies">Tiểu sử & Tự truyện</a></li>
                                <li><a class="dropdown-item" href="Product.php?category=Academic and Professional">Học thuật & Chuyên ngành</a></li>
                                <li><a class="dropdown-item" href="Product.php?category=Business and Management">Kinh doanh & Quản lý</a></li>
                                <li><a class="dropdown-item" href="Product.php?category=Children and Teens">Sách thiếu nhi</a></li>
                                <li><a class="dropdown-item" href="Product.php?category=Health and Cooking">Sức khỏe & Nấu ăn</a></li>
                                <li><a class="dropdown-item" href="Product.php?category=Regional Books">Sách tiếng Việt</a></li>
                            </ul>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="index.php#new">Sách mới</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php#bestseller">Bán chạy</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php#deals">Khuyến mãi</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php#news">Tin tức</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php#contact">Liên hệ</a></li>
                    </ul>

                    <!-- User Actions -->
                    <ul class="navbar-nav ms-auto flex-row align-items-center">
                        <!-- Search Icon with Hover Effect -->
                        <li class="nav-item me-2">
                            <form action="Result.php" method="POST" class="search-form-hover">
                                <input type="search" name="keyword" class="search-input" placeholder="Tìm kiếm sách...">
                                <button type="submit" class="search-button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </li>

                        <?php if (!isset($_SESSION['user'])): ?>
                            <li class="nav-item"><button class="btn btn-primary-glass btn-sm" data-bs-toggle="modal" data-bs-target="#loginModal">Sign In</button></li>
                        <?php else: ?>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                <li class="nav-item"><a href="admin.php" class="btn btn-warning btn-sm rounded-pill fw-bold shadow-sm px-3"><i class="fas fa-user-shield me-1"></i> Admin</a></li>
                            <?php else: ?>
                                <li class="nav-item"><a href="wishlist.php" class="btn btn-outline-dark rounded-circle border-0" style="width:40px; height:40px;" title="Wishlist"><i class="fas fa-heart"></i></a></li>
                                <li class="nav-item ms-1"><a href="cart.php" class="btn btn-outline-dark rounded-circle position-relative border-0" style="width:40px; height:40px;"><i class="fas fa-shopping-bag"></i></a></li>
                            <?php endif; ?>
                            <li class="nav-item dropdown ms-2">
                                <a class="nav-link dropdown-toggle p-0" href="#" role="button" data-bs-toggle="dropdown">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo $_SESSION['user']; ?>&background=random" class="rounded-circle" width="35">
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 glass-panel mt-2">
                                    <li><span class="dropdown-item-text text-muted small">Signed in as<br><strong class="text-dark"><?php echo $_SESSION['user']; ?></strong></span></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item text-danger" href="destroy.php">Logout</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Header Banner -->
    <div class="container mt-4">
        <div class="p-5 rounded-4 text-white position-relative overflow-hidden shadow-lg"
            style="background: linear-gradient(135deg, #0f172a 0%, #334155 100%);">
            <div class="position-relative z-2">
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
                        <span><i class="fas fa-filter me-2"></i>Bộ Lọc</span>
                        <a href="Product.php" class="text-decoration-none text-muted small" style="font-size: 0.8rem;">Xóa lọc</a>
                    </div>

                    <!-- Search -->
                    <form action="Product.php" method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="keyword" class="form-control border-end-0 bg-light" placeholder="Tìm trong trang..." value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">
                            <button class="btn btn-light border border-start-0" type="submit"><i class="fas fa-search text-muted"></i></button>
                        </div>
                    </form>

                    <!-- Categories -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Thể Loại</h6>
                        <?php
                        $cats = ["Literature and Fiction", "Academic and Professional", "Business and Management", "Children and Teens", "Health and Cooking", "Regional Books"];
                        foreach ($cats as $c) {
                            $active = (isset($_GET['category']) && str_replace(' ', '', strtolower($_GET['category'])) == str_replace(' ', '', strtolower($c))) ? 'fw-bold text-primary' : 'text-muted';
                            echo '<div class="mb-2"><a href="Product.php?category=' . urlencode($c) . '" class="text-decoration-none ' . $active . ' d-flex justify-content-between"><span>' . $c . '</span> <small class="bg-white px-2 rounded-pill shadow-sm">' . rand(10, 50) . '</small></a></div>';
                        }
                        ?>
                    </div>

                    <!-- Price Range (Demo UI) -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Khoảng Giá</h6>
                        <input type="range" class="form-range" id="priceRange">
                        <div class="d-flex justify-content-between small text-muted">
                            <span>0đ</span>
                            <span>500k+</span>
                        </div>
                    </div>

                    <!-- Rating -->
                    <div>
                        <h6 class="fw-bold mb-3">Đánh Giá</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="star5">
                            <label class="form-check-label text-warning" for="star5"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="star4">
                            <label class="form-check-label text-warning" for="star4"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i> & Up</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="col-lg-9">
                <!-- Toolbar -->
                <div class="d-flex justify-content-between align-items-center mb-4 p-3 rounded-3" style="background: rgba(255,255,255,0.4); border: 1px solid rgba(255,255,255,0.5);">
                    <div class="text-muted small">
                        Hiển thị <b><?php echo $number_of_results; ?></b> kết quả
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <select class="form-select form-select-sm border-0 bg-white shadow-sm" style="width: 150px;">
                            <option>Mới nhất</option>
                            <option>Giá tăng dần</option>
                            <option>Giá giảm dần</option>
                            <option>Bán chạy</option>
                        </select>
                        <div class="btn-group shadow-sm">
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
                                        <div class="book-category"><?php echo $row['Category']; ?></div>
                                        <a href="description.php?ID=<?php echo $row['PID']; ?>" class="text-decoration-none text-dark">
                                            <h5 class="book-title text-truncate"><?php echo $row['Title']; ?></h5>
                                        </a>
                                        <div class="book-author text-truncate">by <?php echo $row['Author']; ?></div>

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
                        echo '<div class="col-12 text-center py-5"><h4 class="text-muted">Không tìm thấy sách nào!</h4><i class="fas fa-box-open fa-3x text-black-50 mt-3"></i></div>';
                    }
                    ?>
                </div>

                <!-- Pagination -->
                <div class="mt-5 d-flex justify-content-center">
                    <nav>
                        <ul class="pagination">
                            <?php if ($page > 1) { ?>
                                <li class="page-item"><a class="page-link" href="Product.php?page=<?php echo $page - 1; ?>"><i class="fas fa-chevron-left"></i></a></li>
                            <?php } ?>

                            <?php
                            for ($i = 1; $i <= $number_of_pages; $i++) {
                                $active = ($i == $page) ? 'active' : '';
                                echo '<li class="page-item ' . $active . '"><a class="page-link" href="Product.php?page=' . $i . '">' . $i . '</a></li>';
                            }
                            ?>

                            <?php if ($page < $number_of_pages) { ?>
                                <li class="page-item"><a class="page-link" href="Product.php?page=<?php echo $page + 1; ?>"><i class="fas fa-chevron-right"></i></a></li>
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
                        <div class="col-md-5 bg-light d-flex align-items-center justify-content-center p-4">
                            <img id="qv-img" src="" class="img-fluid shadow-lg rounded" style="max-height: 300px;">
                        </div>
                        <div class="col-md-7 p-4">
                            <button type="button" class="btn-close float-end" data-bs-dismiss="modal" aria-label="Close"></button>
                            <span class="badge bg-warning text-dark mb-2" id="qv-cat">Category</span>
                            <h3 class="fw-bold font-playfair mb-1" id="qv-title">Book Title</h3>
                            <p class="text-muted fst-italic mb-3" id="qv-author">Author Name</p>
                            <h4 class="text-primary fw-bold mb-3" id="qv-price">100.000 đ</h4>
                            <p class="small text-muted mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
            // Demo data population
            document.getElementById('qv-img').src = 'img/books/' + id + '.jpg';
            document.getElementById('qv-title').innerText = 'Book Title #' + id;
            document.getElementById('qv-add-cart').href = 'cart.php?ID=' + id + '&quantity=1';

            // Show modal
            var myModal = new bootstrap.Modal(document.getElementById('quickViewModal'));
            myModal.show();
        }

        // Thêm các style cần thiết cho header mới
        const style = document.createElement('style');
        style.innerHTML = `
            .header-container { position: sticky; top: 0; z-index: 1030; transition: all 0.3s ease; }
            .search-form-hover { position: relative; display: flex; align-items: center; transition: all 0.4s ease; }
            .search-form-hover .search-input { width: 0; padding: 8px 0; border: none; border-bottom: 2px solid var(--primary); background-color: transparent; outline: none; font-size: 1rem; color: var(--primary); transition: width 0.4s cubic-bezier(0.25, 0.8, 0.25, 1); opacity: 0; }
            .search-form-hover .search-button { background: transparent; border: none; font-size: 1.2rem; color: var(--primary); cursor: pointer; padding: 8px; }
            .search-form-hover:hover .search-input { width: 200px; padding: 8px 10px; opacity: 1; }
            .nav-link { font-weight: 600; color: var(--primary) !important; position: relative; padding: 10px 15px !important; border-radius: 8px; transition: all 0.3s; }
            .nav-link:hover, .nav-link.active { background: rgba(0, 0, 0, 0.05); color: var(--accent) !important; }
            .dropdown-menu.glass-panel { background: var(--glass-bg); backdrop-filter: blur(16px); border: var(--glass-border); box-shadow: var(--glass-shadow); }
            .btn-primary-glass { background: var(--primary); color: white; border-radius: 50px; padding: 5px 20px; font-weight: 600; transition: 0.3s; box-shadow: 0 5px 15px rgba(15, 23, 42, 0.2); border: none; }
            .btn-primary-glass:hover { background: var(--accent); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(212, 175, 55, 0.3); }
        `;
        document.head.appendChild(style);

    </script>
</body>

</html>