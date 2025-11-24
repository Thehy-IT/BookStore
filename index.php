<?php include 'header.php'; ?>

<!-- ============== Hero Section ==============-->
<div class="container hero-wrapper">
    <div class="row g-4 align-items-center">
        <div class="col-lg-4">
            <div class="p-4">
                <span class="badge bg-warning text-dark mb-3 px-3 py-2 rounded-pill">Best Seller 2025</span>
                <h1 class="display-4 fw-bold mb-3">Discover Your Next <span style="color: var(--accent); font-style: italic;">Great Read</span></h1>
                <p class="lead text-muted mb-4">Explore our curated collection of premium books from around the globe.</p>
                <a href="#new" class="btn btn-primary-glass btn-lg">Explore Now <i class="fas fa-arrow-right ms-2"></i></a>
                <div class="d-flex flex-wrap gap-2 mt-5">
                    <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Fiction</a>
                    <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">Science</a>
                    <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill bg-white border-0 shadow-sm">History</a>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="swiper hero-slider">
                <div class="swiper-wrapper">
                    <div class="swiper-slide"><img src="https://images.unsplash.com/photo-1512820790803-83ca734da794?q=80&w=2098&auto=format&fit=crop" class="w-100" style="height: 450px; object-fit: cover;" alt="Library">
                        <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);">
                            <h3 class="text-white">Classic Collections</h3>
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
            ['icon' => 'fa-book-open', 'title' => 'Tiểu thuyết', 'slug' => 'fiction', 'color' => '#11998e'],
            ['icon' => 'fa-brain', 'title' => 'Phát triển bản thân', 'slug' => 'self-help', 'color' => '#4e54c8'],
            ['icon' => 'fa-heart', 'title' => 'Lãng mạn', 'slug' => 'romance', 'color' => '#ee0979'],
            ['icon' => 'fa-dragon', 'title' => 'Giả tưởng', 'slug' => 'fantasy', 'color' => '#f12711'],
            ['icon' => 'fa-user-tie', 'title' => 'Tiểu sử', 'slug' => 'biography', 'color' => '#0f172a'],
            ['icon' => 'fa-mask', 'title' => 'Kinh dị & Giật gân', 'slug' => 'thriller', 'color' => '#6d28d9'],
            ['icon' => 'fa-briefcase', 'title' => 'Kinh doanh', 'slug' => 'business', 'color' => '#db2777'],
            ['icon' => 'fa-child', 'title' => 'Thiếu nhi', 'slug' => 'kids', 'color' => '#f59e0b']
        ];
        foreach ($cats as $c) {
            echo '<div class="col-6 col-md-3 col-lg-3 mb-4"><a href="Product.php?category=' . urlencode($c['slug']) . '" class="text-decoration-none text-dark"><div class="category-glass-card text-center"><div class="mb-3" style="font-size: 2rem; color: ' . $c['color'] . '"><i class="fas ' . $c['icon'] . '"></i></div><h6 class="fw-bold mb-0">' . $c['title'] . '</h6></div></a></div>';
        }
        ?>
    </div>
</div>

<!-- ============== New Arrivals Section ==============-->
<div class="container py-5" id="new">
    <div class="d-flex justify-content-between align-items-end mb-5">
        <div>
            <h6 class="text-uppercase text-warning fw-bold ls-2">Fresh from press</h6>
            <h2 class="fw-bold display-6">New Arrivals</h2>
        </div>
        <a href="Product.php" class="btn btn-outline-dark rounded-pill px-4">View All</a>
    </div>
    <div class="row g-4 row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5" id="new-arrivals-grid">
        <?php
        $books = [
            ['title' => 'Like A Love Song', 'price' => '150.000', 'img' => 'img/new/1.jpg', 'tag' => 'NEW', 'pid' => 'NEW-01'],
            ['title' => 'General Knowledge 2017', 'price' => '200.000', 'img' => 'img/new/2.jpg', 'tag' => 'HOT', 'pid' => 'NEW-02'],
            ['title' => 'Indian Family Business', 'price' => '180.000', 'img' => 'img/new/3.png', 'tag' => '-20%', 'pid' => 'NEW-03'],
            ['title' => 'SSC Mathematics', 'price' => '350.000', 'img' => 'img/new/4.jpg', 'tag' => 'BEST', 'pid' => 'NEW-04'],
            ['title' => 'Cosmic Wonders', 'price' => '220.000', 'img' => 'img/new/5.jpg', 'tag' => 'NEW', 'pid' => 'NEW-05'],
            ['title' => 'The Silent Patient', 'price' => '250.000', 'img' => 'img/new/6.jpg', 'tag' => 'HOT', 'pid' => 'NEW-06'],
            ['title' => 'Atomic Habits', 'price' => '210.000', 'img' => 'img/new/7.jpg', 'tag' => 'BEST', 'pid' => 'NEW-07'],
            ['title' => 'Where the Crawdads Sing', 'price' => '190.000', 'img' => 'img/new/8.jpg', 'tag' => 'NEW', 'pid' => 'NEW-08'],
            ['title' => 'Sapiens: A Brief History', 'price' => '300.000', 'img' => 'img/new/9.jpg', 'tag' => '-15%', 'pid' => 'NEW-09'],
            ['title' => 'The Midnight Library', 'price' => '230.000', 'img' => 'img/new/10.jpg', 'tag' => 'HOT', 'pid' => 'NEW-10'],
            ['title' => 'Educated: A Memoir', 'price' => '240.000', 'img' => 'img/new/11.jpg', 'tag' => 'NEW', 'pid' => 'NEW-11'],
            ['title' => 'Becoming', 'price' => '320.000', 'img' => 'img/new/12.jpg', 'tag' => 'BEST', 'pid' => 'NEW-12'],
            ['title' => 'The Alchemist', 'price' => '160.000', 'img' => 'img/new/13.jpg', 'tag' => 'HOT', 'pid' => 'NEW-13'],
            ['title' => 'Project Hail Mary', 'price' => '280.000', 'img' => 'img/new/14.jpg', 'tag' => '-10%', 'pid' => 'NEW-14'],
            ['title' => 'The Four Winds', 'price' => '260.000', 'img' => 'img/new/15.jpg', 'tag' => 'NEW', 'pid' => 'NEW-15']
        ];
        foreach ($books as $idx => $book) {
            // Lấy PID từ mảng book, nếu không có thì tạo một giá trị giả
            $pid = isset($book['pid']) ? $book['pid'] : 'BOOK-' . str_pad($idx + 1, 2, '0', STR_PAD_LEFT);


            // Logic để thêm class màu cho badge
            $badge_class = '';
            switch (strtoupper($book['tag'])) {
                case 'NEW':
                    $badge_class = 'badge-new';
                    break;
                case 'HOT':
                    $badge_class = 'badge-hot';
                    break;
                case 'BEST':
                    $badge_class = 'badge-best';
                    break;
                default: // Mặc định cho các tag có % (giảm giá)
                    if (strpos($book['tag'], '%') !== false) {
                        $badge_class = 'badge-sale';
                    }
                    break;
            }
        ?>
            <div class="col book-item">
                <div class="book-card-glass h-100 d-flex flex-column">
                    <div class="badge-glass <?php echo $badge_class; ?>"><span><?php echo $book['tag']; ?></span></div>
                    <div class="book-img-wrapper">
                        <img src="<?php echo $book['img']; ?>" onerror="this.src='https://placehold.co/400x600/eee/31343C?text=Book+Cover'" alt="Book">
                        <div class="action-overlay">
                            <a href="cart.php?ID=<?php echo $pid; ?>&quantity=1" class="btn-icon" title="Thêm vào giỏ"><i class="fas fa-shopping-cart"></i></a>
                            <a href="description.php?ID=<?php echo $pid; ?>" class="btn-icon" title="Xem chi tiết"><i class="fas fa-eye"></i></a>
                            <a href="wishlist.php?ID=<?php echo $pid; ?>" class="btn-icon" title="Yêu thích"><i class="fas fa-heart"></i></a>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <h6 class="fw-bold text-truncate"><?php echo $book['title']; ?></h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-primary fw-bold"><?php echo $book['price']; ?> đ</span>
                            <div class="text-warning small"><i class="fas fa-star"></i> 4.8</div>
                        </div>
                    </div>
                    <a href="description.php?ID=<?php echo $pid; ?>" class="stretched-link"></a>
                </div>
            </div>
        <?php } ?>
    </div>

    <!-- Nút Xem Thêm -->
    <div class="text-center mt-5" id="viewMoreContainer">
        <button id="viewMoreBtn" class="btn btn-primary-glass px-5 py-3">Xem thêm sách mới <i class="fas fa-chevron-down ms-2"></i></button>
    </div>
</div>

<!-- ============== SECTION 1: BESTSELLERS (LAYOUT: CAROUSEL) ==============-->
<div class="bestseller-section" id="bestseller">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-primary px-3 py-2 rounded-pill mb-2">Trending Now</span>
            <h2 class="display-6 fw-bold">This Week's Bestsellers</h2>
            <p class="text-muted">Top rated books chosen by our community</p>
        </div>

        <div class="swiper bestseller-swiper pb-5 position-relative">
            <div class="swiper-wrapper">
                <!-- Slide 1 -->
                <div class="swiper-slide">
                    <div class="bestseller-card position-relative text-center">
                        <div class="rank-number">01</div>
                        <div class="book-card-glass border-0 bg-transparent shadow-none">
                            <div class="book-img-wrapper shadow-lg mb-3">
                                <img src="img/new/1.jpg" onerror="this.src='https://placehold.co/400x600?text=Bestseller'" class="rounded-3">
                            </div>
                            <h5 class="fw-bold mt-3">Like A Love Song</h5>
                            <p class="text-muted small">Nikita Singh</p>
                            <a href="#" class="btn btn-outline-dark rounded-pill btn-sm">View Details</a>
                        </div>
                    </div>
                </div>
                <!-- Slide 2 -->
                <div class="swiper-slide">
                    <div class="bestseller-card position-relative text-center">
                        <div class="rank-number">02</div>
                        <div class="book-card-glass border-0 bg-transparent shadow-none">
                            <div class="book-img-wrapper shadow-lg mb-3">
                                <img src="img/new/3.png" onerror="this.src='https://placehold.co/400x600?text=Bestseller'" class="rounded-3">
                            </div>
                            <h5 class="fw-bold mt-3">Indian Family Mantras</h5>
                            <p class="text-muted small">Peter Leach</p>
                            <a href="#" class="btn btn-outline-dark rounded-pill btn-sm">View Details</a>
                        </div>
                    </div>
                </div>
                <!-- Slide 3 -->
                <div class="swiper-slide">
                    <div class="bestseller-card position-relative text-center">
                        <div class="rank-number">03</div>
                        <div class="book-card-glass border-0 bg-transparent shadow-none">
                            <div class="book-img-wrapper shadow-lg mb-3">
                                <img src="img/new/2.jpg" onerror="this.src='https://placehold.co/400x600?text=Bestseller'" class="rounded-3">
                            </div>
                            <h5 class="fw-bold mt-3">General Knowledge</h5>
                            <p class="text-muted small">Manohar Pandey</p>
                            <a href="#" class="btn btn-outline-dark rounded-pill btn-sm">View Details</a>
                        </div>
                    </div>
                </div>
                <!-- Slide 4 -->
                <div class="swiper-slide">
                    <div class="bestseller-card position-relative text-center">
                        <div class="rank-number">04</div>
                        <div class="book-card-glass border-0 bg-transparent shadow-none">
                            <div class="book-img-wrapper shadow-lg mb-3">
                                <img src="img/new/4.jpg" onerror="this.src='https://placehold.co/400x600?text=Bestseller'" class="rounded-3">
                            </div>
                            <h5 class="fw-bold mt-3">SSC Mathematics</h5>
                            <p class="text-muted small">Kiran</p>
                            <a href="#" class="btn btn-outline-dark rounded-pill btn-sm">View Details</a>
                        </div>
                    </div>
                </div>
                <!-- Slide 5 -->
                <div class="swiper-slide">
                    <div class="bestseller-card position-relative text-center">
                        <div class="rank-number">05</div>
                        <div class="book-card-glass border-0 bg-transparent shadow-none">
                            <div class="book-img-wrapper shadow-lg mb-3">
                                <img src="https://images.unsplash.com/photo-1589829085413-56de8ae18c73?q=80&w=2000&auto=format&fit=crop" onerror="this.src='https://placehold.co/400x600?text=Bestseller'" class="rounded-3">
                            </div>
                            <h5 class="fw-bold mt-3">The Psychology of Money</h5>
                            <p class="text-muted small">Morgan Housel</p>
                            <a href="#" class="btn btn-outline-dark rounded-pill btn-sm">View Details</a>
                        </div>
                    </div>
                </div>
                <!-- Slide 5 -->
                <div class="swiper-slide">
                    <div class="bestseller-card position-relative text-center">
                        <div class="rank-number">06</div>
                        <div class="book-card-glass border-0 bg-transparent shadow-none">
                            <div class="book-img-wrapper shadow-lg mb-3">
                                <img src="https://images.unsplash.com/photo-1589829085413-56de8ae18c73?q=80&w=2000&auto=format&fit=crop" onerror="this.src='https://placehold.co/400x600?text=Bestseller'" class="rounded-3">
                            </div>
                            <h5 class="fw-bold mt-3">The Psychology of Money</h5>
                            <p class="text-muted small">Morgan Housel</p>
                            <a href="#" class="btn btn-outline-dark rounded-pill btn-sm">View Details</a>
                        </div>
                    </div>
                </div>
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
                    <div class="text-warning fw-bold mb-2 ls-2 text-uppercase"><i class="fas fa-bolt me-2"></i>Flash Deal of the Day</div>
                    <h2 class="display-5 fw-bold mb-4">Save 50% on "The Lost Symbol"</h2>
                    <p class="mb-4 text-white-50 lead">Discover Dan Brown's masterpiece at an unbeatable price. Offer ends soon, don't miss out on this thriller.</p>

                    <div class="mb-5 d-flex flex-wrap">
                        <div class="deal-timer-box">
                            <span class="deal-timer-number">05</span>
                            <span class="deal-timer-label">Hours</span>
                        </div>
                        <div class="deal-timer-box">
                            <span class="deal-timer-number">42</span>
                            <span class="deal-timer-label">Minutes</span>
                        </div>
                        <div class="deal-timer-box">
                            <span class="deal-timer-number">18</span>
                            <span class="deal-timer-label">Seconds</span>
                        </div>
                    </div>

                    <a href="description.php?ID=LIT-20" class="btn btn-light btn-lg rounded-pill px-5 fw-bold text-primary">Shop Now</a>
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
        <h6 class="text-uppercase text-primary fw-bold">From the Blog</h6>
        <h2 class="fw-bold">Latest Literary News</h2>
        <div style="width: 50px; height: 3px; background: var(--accent); margin: 15px auto;"></div>
    </div>

    <div class="row g-4">
        <!-- News 1 -->
        <div class="col-md-4">
            <div class="news-card">
                <img src="https://images.unsplash.com/photo-1457369804613-52c61a468e7d?q=80&w=2070&auto=format&fit=crop" class="news-img" alt="Blog 1">
                <div class="news-body">
                    <div class="news-date">Oct 15, 2025</div>
                    <h4 class="news-title">Top 10 Books to Read This Winter</h4>
                    <p class="text-muted small mb-4">As the weather gets colder, curl up with these cozy mysteries and heartwarming tales selected by our editors...</p>
                    <a href="news.php" class="read-more-link">Read Article <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>
        <!-- News 2 -->
        <div class="col-md-4">
            <div class="news-card">
                <img src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?q=80&w=2128&auto=format&fit=crop" class="news-img" alt="Blog 2">
                <div class="news-body">
                    <div class="news-date">Oct 12, 2025</div>
                    <h4 class="news-title">Interview with J.K. Rowling</h4>
                    <p class="text-muted small mb-4">An exclusive look into the creative process behind the legendary Harry Potter series and what comes next...</p>
                    <a href="news.php" class="read-more-link">Read Article <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>
        <!-- News 3 -->
        <div class="col-md-4">
            <div class="news-card">
                <img src="https://images.unsplash.com/photo-1519682337058-a94d519337bc?q=80&w=2070&auto=format&fit=crop" class="news-img" alt="Blog 3">
                <div class="news-body">
                    <div class="news-date">Oct 08, 2025</div>
                    <h4 class="news-title">The Rise of Digital Libraries</h4>
                    <p class="text-muted small mb-4">How technology is reshaping the way we access and consume literature in the modern digital age...</p>
                    <a href="news.php" class="read-more-link">Read Article <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>