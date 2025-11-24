<?php
include 'header.php';

// Hiển thị thông báo flash nếu có
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
<?php
// Xử lý chuyển hướng khi nhấn vào nút khuyến mãi
if (isset($_GET['action']) && $_GET['action'] == 'view_deals') {
    header("Location: deals.php");
    exit();
}
?>

<style>
    /* CSS cho hiệu ứng Marquee */
    .marquee-container {
        overflow: hidden;
        position: relative;
        width: 100%;
        padding: 5px 0;
    }

    .marquee-track {
        display: flex;
        width: fit-content;
        will-change: transform;
    }

    .marquee-track-rtl {
        animation: scroll-rtl 30s linear infinite;
    }

    .marquee-track-ltr {
        animation: scroll-ltr 35s linear infinite;
    }

    .marquee-container:hover .marquee-track {
        animation-play-state: paused;
    }

    @keyframes scroll-rtl {
        from {
            transform: translateX(0);
        }

        to {
            transform: translateX(-50%);
        }
    }

    @keyframes scroll-ltr {
        from {
            transform: translateX(-50%);
        }

        to {
            transform: translateX(0);
        }
    }

    .marquee-track .btn {
        flex-shrink: 0;
        margin: 0 5px;
    }
</style>

<!-- ============== Hero Section ==============-->
<div class="container hero-wrapper" style="padding-top: 100px;">
    <div class="row g-4 align-items-center">
        <div class="col-lg-4">
            <div class="p-4">
                <span class="badge bg-warning text-dark mb-3 px-3 py-2 rounded-pill">Bán Chạy Nhất 2025</span>
                <h1 class="display-4 fw-bold mb-3">Khám Phá Cuốn Sách <span style="color: var(--accent); font-style: italic;">Tuyệt Vời</span></h1>
                <p class="lead text-muted mb-4">Khám phá bộ sưu tập sách cao cấp được tuyển chọn của chúng tôi từ khắp nơi trên thế giới.</p>
                <a href="#new" class="btn btn-primary-glass btn-lg">Khám Phá Ngay <i class="fas fa-arrow-right ms-2"></i></a>
                <div class="mt-5">
                    <!-- Hàng 1: 6 nút, chạy từ phải sang trái -->
                    <div class="marquee-container mb-2">
                        <div class="marquee-track marquee-track-rtl">
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Tiểu Thuyết</a>
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Khoa Học</a>
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Lịch Sử</a>
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Văn Học</a>
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Hoạt Hình</a>
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Kinh Dị</a>
                            <!-- Lặp lại để tạo hiệu ứng liền mạch -->
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Tiểu Thuyết</a>
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Khoa Học</a>
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Lịch Sử</a>
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Văn Học</a>
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Hoạt Hình</a>
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Kinh Dị</a>
                        </div>
                    </div>
                    <!-- Hàng 2: Các nút còn lại, chạy từ trái sang phải -->
                    <div class="marquee-container">
                        <div class="marquee-track marquee-track-ltr">
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Kinh Doanh</a>
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Triết Học</a>
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Du Lịch</a>
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Nấu Ăn</a>
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Thể Dục</a>
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Khoa Học Viễn Tưởng</a>
                            <!-- Lặp lại để tạo hiệu ứng liền mạch -->
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Kinh Doanh</a>
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Triết Học</a>
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Du Lịch</a>
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Nấu Ăn</a>
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Thể Dục</a>
                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Khoa Học Viễn Tưởng</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="swiper hero-slider">
                <div class="swiper-wrapper">
                    <div class="swiper-slide"><img src="https://images.unsplash.com/photo-1512820790803-83ca734da794?q=80&w=2098&auto=format&fit=crop" class="w-100" style="height: 450px; object-fit: cover;" alt="Library">
                        <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);">
                            <h3 class="text-white">Bộ Sưu Tập Kinh Điển</h3>
                        </div>
                    </div>
                    <div class="swiper-slide"><img src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?q=80&w=2070&auto=format&fit=crop" class="w-100" style="height: 450px; object-fit: cover;" alt="Reading"></div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>
</div>

<!-- ============== Promotions Cards ==============-->
<div class="container mt-n4 mb-5">
    <div class="row g-4">
        <div class="col-lg-4 col-md-6">
            <a href="deals.php" class="text-decoration-none">
                <div class="promo-card">
                    <div class="promo-icon"><i class="fas fa-shipping-fast"></i></div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Miễn Phí Vận Chuyển</h6>
                        <p class="small text-muted mb-0">Cho đơn hàng trên 500.000đ</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6">
            <a href="deals.php" class="text-decoration-none">
                <div class="promo-card">
                    <div class="promo-icon"><i class="fas fa-tags"></i></div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Ưu Đãi Đặc Biệt</h6>
                        <p class="small text-muted mb-0">Giảm giá đến 30% cho sách mới</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-12">
            <div class="promo-card bg-primary text-white" style="background: var(--primary);">
                <div class="promo-icon text-warning" style="color: var(--accent) !important;"><i class="fas fa-gift"></i></div>
                <div>
                    <h6 class="fw-bold mb-1 text-white">Quà Tặng Độc Quyền</h6>
                    <p class="small text-white-50 mb-0">Khi đăng ký thành viên mới</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============== Categories Cards ==============-->
<div class="container my-5">
    <div class="row g-4">
        <?php
        $cats = [
            ['icon' => 'fa-book-open', 'title' => 'Tiểu thuyết', 'slug' => 'fiction', 'bg' => 'img/new/th1.jpg', 'text_color' => '#c8dcdaff'],
            ['icon' => 'fa-brain', 'title' => 'Phát triển bản thân', 'slug' => 'self-help', 'bg' => 'img/new/th2.jpg', 'text_color' => '#c8dcdaff'],
            ['icon' => 'fa-heart', 'title' => 'Lãng mạn', 'slug' => 'romance', 'bg' => 'img/new/th3.jpg', 'text_color' => '#c8dcdaff'],
            ['icon' => 'fa-dragon', 'title' => 'Giả tưởng', 'slug' => 'fantasy', 'bg' => 'img/new/th4.jpg', 'text_color' => '#c8dcdaff'],
            ['icon' => 'fa-user-tie', 'title' => 'Tiểu sử', 'slug' => 'biography', 'bg' => 'img/new/th5.jpg', 'text_color' => '#c8dcdaff'],
            ['icon' => 'fa-mask', 'title' => 'Kinh dị & Giật gân', 'slug' => 'thriller', 'bg' => 'img/new/th6.jpg', 'text_color' => '#c8dcdaff'],
            ['icon' => 'fa-briefcase', 'title' => 'Kinh doanh', 'slug' => 'business', 'bg' => 'img/new/th7.jpg', 'text_color' => '#c8dcdaff'],
            ['icon' => 'fa-child', 'title' => 'Thiếu nhi', 'slug' => 'kids', 'bg' => 'img/new/th8.jpg', 'text_color' => '#c8dcdaff']
        ];
        foreach ($cats as $c) {
            $bg = htmlspecialchars($c['bg'], ENT_QUOTES);
            $slug = urlencode($c['slug']);
            $icon = htmlspecialchars($c['icon'], ENT_QUOTES);
            $title = htmlspecialchars($c['title'], ENT_QUOTES);
            $text_color = isset($c['text_color']) ? htmlspecialchars($c['text_color'], ENT_QUOTES) : '#ffffff';

            echo '<div class="col-6 col-md-3 col-lg-3 mb-4">';
            echo '<a href="Product.php?category=' . $slug . '" class="text-decoration-none">';
            echo '<div class="category-glass-card" style="background: linear-gradient(rgba(0,0,0,0.28), rgba(0,0,0,0.28)), url(\'' . $bg . '\') center/cover no-repeat;">';
            echo '<div class="cat-icon" style="color: ' . $text_color . ';"><i class="fas ' . $icon . '"></i></div>';
            echo '<h6 class="cat-title" style="color: ' . $text_color . ';">' . $title . '</h6>';
            echo '</div></a></div>';
        }
        ?>
    </div>
</div>

<!-- ============== New Arrivals Section ==============-->
<div class="container py-5" id="new">
    <div class="d-flex justify-content-between align-items-end mb-5">
        <div>
            <h6 class="text-uppercase text-warning fw-bold ls-2">Mới phát hành</h6>
            <h2 class="fw-bold display-6">Sách Mới</h2>
        </div>
        <a href="Product.php" class="btn btn-outline-dark rounded-pill px-4">Xem Tất Cả</a>
    </div>
    <div class="row g-4 row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5" id="new-arrivals-grid">
        <style>
            @keyframes shake {

                0%,
                100% {
                    transform: translateX(0) scale(1.1);
                }

                25% {
                    transform: translateX(-2px) scale(1.1);
                }

                50% {
                    transform: translateX(2px) scale(1.1);
                }

                75% {
                    transform: translateX(-2px) scale(1.1);
                }
            }

            .book-card-glass .btn-icon {
                transition: all 0.3s ease;
            }

            .book-card-glass .btn-icon:hover {
                transform: scale(1.1);
                animation: shake 0.4s ease-in-out;
                background: var(--accent);
            }

            .book-card-glass .btn-icon:hover i {
                color: white !important;
            }
        </style>
        <?php
        // Lấy 15 sản phẩm mới nhất từ CSDL
        $new_arrivals_query = "SELECT * FROM products ORDER BY PID DESC LIMIT 15";
        $new_arrivals_result = mysqli_query($con, $new_arrivals_query);

        while ($book = mysqli_fetch_assoc($new_arrivals_result)) {
            $pid = htmlspecialchars($book['PID']);
            $img_path = "img/books/" . $pid . ".jpg";

            // Logic để thêm class màu cho badge
            $badge_class = '';
            $badge_text = '';

            if ($book['Discount'] > 0) {
                $badge_class = 'badge-sale';
                $badge_text = '-' . $book['Discount'] . '%';
            } else {
                // Bạn có thể thêm logic khác ở đây, ví dụ dựa trên ngày thêm sản phẩm để hiện tag "MỚI"
                $badge_class = 'badge-new';
                $badge_text = 'MỚI';
            }
        ?>
            <div class="col book-item">
                <div class="book-card-glass h-100 d-flex flex-column">
                    <div class="badge-glass <?php echo $badge_class; ?>"><span><?php echo $badge_text; ?></span></div>
                    <div class="book-img-wrapper">
                        <img src="<?php echo $img_path; ?>" onerror="this.src='https://placehold.co/400x600/eee/31343C?text=Book+Cover'" alt="<?php echo htmlspecialchars($book['Title']); ?>">
                        <div class="action-overlay">
                            <button onclick="addToCartAjax('<?php echo $pid; ?>', 1)" class="btn-icon" title="Thêm vào giỏ"><i class="fas fa-shopping-cart"></i></button>
                            <button onclick='openQuickView(<?php echo json_encode($book); ?>)' class="btn-icon" title="Xem nhanh"><i class="fas fa-eye"></i></button>
                            <button onclick="addToWishlist('<?php echo $pid; ?>')" class="btn-icon" title="Yêu thích"><i class="fas fa-heart"></i></button>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <h6 class="fw-bold text-truncate" title="<?php echo htmlspecialchars($book['Title']); ?>">
                            <a href="description.php?ID=<?php echo $pid; ?>" class="text-decoration-none text-dark"><?php echo htmlspecialchars($book['Title']); ?></a>
                        </h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-primary fw-bold"><?php echo number_format($book['Price']); ?> đ</span>
                            <div class="text-warning small"><i class="fas fa-star"></i> 4.8</div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <!-- Nút Xem Thêm -->
    <div class="text-center mt-5" id="viewMoreContainer">
        <button id="viewMoreBtn" class="btn btn-primary-glass px-5 py-3">Xem thêm sách <i class="fas fa-chevron-down ms-2"></i></button>
    </div>
</div>

<!-- ============== SECTION 1: BESTSELLERS (LAYOUT: CAROUSEL) ==============-->
<div class="bestseller-section" id="bestseller">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-primary px-3 py-2 rounded-pill mb-2">Xu Hướng</span>
            <h2 class="display-6 fw-bold">Sách Bán Chạy Tuần Này</h2>
            <p class="text-muted">Những cuốn sách được cộng đồng đánh giá cao nhất</p>
        </div>

        <div class="swiper bestseller-swiper pb-5 position-relative">
            <div class="swiper-wrapper">
                <?php
                // Lấy 6 sản phẩm bán chạy (ví dụ: có giảm giá cao nhất)
                $bestseller_query = "SELECT * FROM products ORDER BY Discount DESC, PID DESC LIMIT 6";
                $bestseller_result = mysqli_query($con, $bestseller_query);
                $rank = 1;

                while ($book = mysqli_fetch_assoc($bestseller_result)) {
                    $pid = htmlspecialchars($book['PID']);
                    $img_path = "img/books/" . $pid . ".jpg";
                ?>
                    <div class="swiper-slide">
                        <div class="bestseller-card position-relative text-center">
                            <div class="rank-number"><?php echo str_pad($rank++, 2, '0', STR_PAD_LEFT); ?></div>
                            <div class="book-card-glass border-0 bg-transparent shadow-none">
                                <div class="book-img-wrapper shadow-lg mb-3">
                                    <img src="<?php echo $img_path; ?>" onerror="this.src='https://placehold.co/400x600?text=Bestseller'" class="rounded-3">
                                </div>
                                <h5 class="fw-bold mt-3 text-truncate" title="<?php echo htmlspecialchars($book['Title']); ?>"><?php echo htmlspecialchars($book['Title']); ?></h5>
                                <p class="text-muted small">Tác giả: <?php echo htmlspecialchars($book['Author']); ?></p>
                                <div class="btn-group">
                                    <a href="description.php?ID=<?php echo $pid; ?>" class="btn btn-outline-dark rounded-pill btn-sm px-3">Chi tiết</a>
                                    <button onclick='openQuickView(<?php echo json_encode($book); ?>)' class="btn btn-outline-dark rounded-pill btn-sm px-3" title="Xem nhanh">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
            <div class="swiper-pagination"></div>

            <!-- Navigation Buttons -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>

        </div>
    </div>
</div>

<!-- ============== SECTION 2: DEALS (LAYOUT: SPLIT BANNER) ==============-->
<div class="container py-5" id="deals">
    <div class="deal-wrapper">
        <div class="row g-0 align-items-center">
            <div class="col-lg-6">
                <div class="deal-content">
                    <div class="text-warning fw-bold mb-2 ls-2 text-uppercase"><i class="fas fa-bolt me-2"></i>Ưu đãi chớp nhoáng trong ngày</div>
                    <h2 class="display-5 fw-bold mb-4">Giảm 50% cho "Biểu Tượng Thất Truyền"</h2>
                    <p class="mb-4 text-white-50 lead">Khám phá kiệt tác của Dan Brown với mức giá không thể tốt hơn. Ưu đãi sắp kết thúc, đừng bỏ lỡ cuốn tiểu thuyết ly kỳ này.</p>

                    <div class="mb-5 d-flex flex-wrap">
                        <div class="deal-timer-box">
                            <span class="deal-timer-number">05</span>
                            <span class="deal-timer-label">Giờ</span>
                        </div>
                        <div class="deal-timer-box">
                            <span class="deal-timer-number">42</span>
                            <span class="deal-timer-label">Phút</span>
                        </div>
                        <div class="deal-timer-box">
                            <span class="deal-timer-number">18</span>
                            <span class="deal-timer-label">Giây</span>
                        </div>
                    </div>

                    <a href="description.php?ID=LIT-20" class="btn btn-light btn-lg rounded-pill px-5 fw-bold text-primary">Mua Ngay</a>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block" style="height: 100%;">
                <div class="deal-image-container">
                    <img src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?q=80&w=2000&auto=format&fit=crop" class="deal-image" alt="Book Deal">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============== SECTION 3: NEWS (LAYOUT: GRID MASONRY) ==============-->
<div class="container py-5" id="news">
    <div class="section-header text-center mb-5">
        <h6 class="text-uppercase text-primary fw-bold">Từ Blog</h6>
        <h2 class="fw-bold">Tin Tức Văn Học Mới Nhất</h2>
        <div style="width: 50px; height: 3px; background: var(--accent); margin: 15px auto;"></div>
    </div>

    <div class="row g-4">
        <!-- News 1 -->
        <div class="col-md-4">
            <div class="news-card">
                <img src="https://images.unsplash.com/photo-1457369804613-52c61a468e7d?q=80&w=2070&auto=format&fit=crop" class="news-img" alt="Blog 1">
                <div class="news-body">
                    <div class="news-date">15 Tháng 10, 2025</div>
                    <h4 class="news-title">Top 10 Sách Nên Đọc Mùa Đông Này</h4>
                    <p class="text-muted small mb-4">Khi thời tiết trở lạnh, hãy cuộn mình với những câu chuyện bí ẩn ấm cúng và những câu chuyện cảm động do biên tập viên của chúng tôi lựa chọn...</p>
                    <a href="news.php?id=1" class="read-more-link">Đọc Bài Viết <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>
        <!-- News 2 -->
        <div class="col-md-4">
            <div class="news-card">
                <img src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?q=80&w=2128&auto=format&fit=crop" class="news-img" alt="Blog 2">
                <div class="news-body">
                    <div class="news-date">12 Tháng 10, 2025</div>
                    <h4 class="news-title">Phỏng vấn J.K. Rowling</h4>
                    <p class="text-muted small mb-4">Một cái nhìn độc quyền về quá trình sáng tạo đằng sau loạt phim Harry Potter huyền thoại và những gì sẽ xảy ra tiếp theo...</p>
                    <a href="news.php?id=2" class="read-more-link">Đọc Bài Viết <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>
        <!-- News 3 -->
        <div class="col-md-4">
            <div class="news-card">
                <img src="https://images.unsplash.com/photo-1519682337058-a94d519337bc?q=80&w=2070&auto=format&fit=crop" class="news-img" alt="Blog 3">
                <div class="news-body">
                    <div class="news-date">08 Tháng 10, 2025</div>
                    <h4 class="news-title">Sự Trỗi Dậy Của Thư Viện Số</h4>
                    <p class="text-muted small mb-4">Công nghệ đang định hình lại cách chúng ta tiếp cận và tiêu thụ văn học trong thời đại kỹ thuật số hiện đại như thế nào...</p>
                    <a href="news.php?id=3" class="read-more-link">Đọc Bài Viết <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>