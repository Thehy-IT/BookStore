        <!-- ============== Login Modal (Glass) ==============-->
        <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-3">
                    <div class="modal-header border-0">
                        <h4 class="modal-title fw-bold">Chào mừng trở lại!</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control rounded-4 bg-light border-0" id="uLogin" name="login_username" placeholder="Username" required>
                                <label for="uLogin">Username</label>
                            </div>
                            <div class="form-floating mb-4">
                                <input type="password" class="form-control rounded-4 bg-light border-0" id="pLogin" name="login_password" placeholder="Password" required>
                                <label for="pLogin">Password</label>
                            </div>
                            <button type="submit" name="login" class="btn btn-primary-glass w-100 btn-lg">Đăng nhập</button>
                        </form>
                        <div class="text-center mt-3">
                            <small class="text-muted">Chưa có tài khoản? <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" class="fw-bold text-primary">Đăng ký</a></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============== Register Modal (Glass) ==============-->
        <div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-3">
                    <div class="modal-header border-0">
                        <h4 class="modal-title fw-bold">Tạo tài khoản</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control rounded-4 bg-light border-0" name="register_username" placeholder="Username" required>
                                <label>Chọn tên đăng nhập</label>
                            </div>
                            <div class="form-floating mb-4">
                                <input type="password" class="form-control rounded-4 bg-light border-0" name="register_password" placeholder="Password" required>
                                <label>Chọn mật khẩu</label>
                            </div>
                            <button type="submit" name="register" class="btn btn-primary-glass w-100 btn-lg">Đăng ký</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============== Quick View Modal ==============-->
        <div class="modal fade" id="quickViewModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 rounded-4 overflow-hidden" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
                    <div class="modal-body p-0">
                        <div class="row g-0">
                            <div class="col-md-5 d-flex align-items-center justify-content-center p-4" style="background: #f8f9fa;">
                                <img id="qv-img" src="" class="img-fluid shadow-lg rounded" style="max-height: 300px;">
                            </div>
                            <div class="col-md-7 p-4 position-relative">
                                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                                <span class="badge bg-warning text-dark mb-2" id="qv-cat">Thể loại</span>
                                <h3 class="fw-bold font-playfair mb-1" id="qv-title">Tên sách</h3>
                                <p class="text-muted fst-italic mb-3" id="qv-author">Tên tác giả</p>
                                <h4 class="text-primary fw-bold mb-3" id="qv-price">100.000 đ</h4>
                                <p class="small text-muted mb-4" id="qv-desc" style="max-height: 100px; overflow-y: auto;">Mô tả ngắn của sách sẽ được hiển thị ở đây.</p>

                                <div class="d-flex gap-2">
                                    <a href="#" id="qv-add-cart" class="btn btn-dark rounded-pill px-4 flex-grow-1">Thêm vào giỏ</a>
                                    <a href="#" id="qv-detail" class="btn btn-outline-dark rounded-pill px-4">Xem chi tiết</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- ============== Footer ==============-->
        <footer id="contact">
            <div class="footer-wave">
                <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                    <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" fill="#f0f4f8"></path>
                </svg>
            </div>
            <style>
                .footer-logos-section {
                    padding-top: 20px;
                    border-top: 1px solid rgba(255, 255, 255, 0.1);
                }

                .footer-logo-group h6 {
                    color: var(--accent);
                    font-size: 0.9rem;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    margin-bottom: 15px;
                }

                .footer-logo-item img {
                    max-height: 30px;
                    max-width: 60px;
                    object-fit: contain;
                    transition: all 0.3s ease;
                }

                .footer-logo-item:hover img {
                    transform: scale(1.15);
                }

                .newsletter-input::placeholder {
                    color: #ffffff !important;
                    opacity: 0.7;
                }

                .newsletter-input::-webkit-input-placeholder { color: #ffffff !important; opacity: 0.7; }
                .newsletter-input::-moz-placeholder { color: #ffffff !important; opacity: 0.7; }
                .newsletter-input:-ms-input-placeholder { color: #ffffff !important; opacity: 0.7; }
            </style>

            <div class="container position-relative z-2">
                <div class="row gy-4">
                    <div class="col-lg-4 col-md-6">
                        <a class="navbar-brand text-white fs-3" href="index.php">
                            <i class="fas fa-book-open text-warning me-2"></i>
                            <span>BOOK<span style="color: var(--accent)">Z</span></span>
                        </a>
                        <p class="text-white-50 mt-3">Khám phá thế giới tri thức với bộ sưu tập sách chọn lọc, mang đến trải nghiệm đọc cao cấp cho độc giả hiện đại.</p>
                        <div class="mt-4 d-flex flex-row flex-wrap align-items-center gap-3">
                            <a href="#" class="text-white-50 fs-5 social-icon social-icon-facebook">
                                <i class="fab fa-facebook-f fa-fw"></i>
                                <span class="social-name">Facebook</span>
                            </a>
                            <a href="#" class="text-white-50 fs-5 social-icon social-icon-instagram">
                                <i class="fab fa-instagram fa-fw"></i>
                                <span class="social-name">Instagram</span>
                            </a>
                            <a href="#" class="text-white-50 fs-5 social-icon social-icon-twitter">
                                <i class="fab fa-twitter fa-fw"></i>
                                <span class="social-name">Twitter</span>
                            </a>
                            <a href="#" class="text-white-50 fs-5 social-icon social-icon-youtube">
                                <i class="fab fa-youtube fa-fw"></i>
                                <span class="social-name">YouTube</span>
                            </a>
                            <a href="#" class="text-white-50 fs-5 social-icon social-icon-pinterest">
                                <i class="fab fa-pinterest fa-fw"></i>
                                <span class="social-name">Pinterest</span>
                            </a>
                            <a href="#" class="text-white-50 fs-5 social-icon social-icon-tiktok">
                                <i class="fab fa-tiktok fa-fw"></i>
                                <span class="social-name">TikTok</span>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6 offset-lg-1">
                        <h5 class="text-warning mb-3">Shop</h5>
                        <ul class="list-unstyled text-white-50">
                            <li class="mb-2"><a href="#new" class="text-decoration-none text-white-50 footer-link">Sách mới</a></li>
                            <li class="mb-2"><a href="#bestseller" class="text-decoration-none text-white-50 footer-link">Bán chạy</a></li>
                            <li class="mb-2"><a href="#deals" class="text-decoration-none text-white-50 footer-link">Khuyến mãi</a></li>
                            <li class="mb-2"><a href="Product.php" class="text-decoration-none text-white-50 footer-link">Tất cả sách</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-2 col-6">
                        <h5 class="text-warning mb-3">Support</h5>
                        <ul class="list-unstyled text-white-50">
                            <li class="mb-2"><a href="#" class="text-decoration-none text-white-50 footer-link">Về BookZ</a></li>
                            <li class="mb-2"><a href="#contact" class="text-decoration-none text-white-50 footer-link">Liên hệ</a></li>
                            <li class="mb-2"><a href="#" class="text-decoration-none text-white-50 footer-link">FAQs</a></li>
                            <li class="mb-2"><a href="#" class="text-decoration-none text-white-50 footer-link">Chính sách</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <h5 class="text-warning mb-3">Newsletter</h5>
                        <p class="text-white-50 small">Đăng ký để nhận thông tin sách mới và ưu đãi độc quyền.</p>
                        <form class="input-group mt-3">
                            <input type="email" class="form-control bg-white bg-opacity-10 border-0 text-white newsletter-input" placeholder="Nhập email của bạn..." style="border-radius: 50px 0 0 50px;">
                            <button class="btn btn-warning" type="button" style="border-radius: 0 50px 50px 0;">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- NEW: Payment, Shipping, and App Logos -->
                <div class="row footer-logos-section mt-5">
                    <!-- Payment Methods -->
                    <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                        <h6 class="fw-bold">Phương thức thanh toán</h6>
                        <div class="d-flex flex-wrap align-items-center">
                            <a href="#" class="footer-logo-item me-3 mb-2"><img src="img/footer/momo.png" alt="Momo"></a>
                            <a href="#" class="footer-logo-item me-3 mb-2"><img src="img/footer/zalopay.png" alt="ZaloPay"></a>
                            <a href="#" class="footer-logo-item me-3 mb-2"><img src="img/footer/vnpay.png" alt="VNPAY"></a>
                            <a href="#" class="footer-logo-item me-3 mb-2"><img src="img/footer/shopee.png" alt="ShopeePay"></a>
                        </div>
                    </div>
                    <!-- Shipping Partners -->
                    <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                        <h6 class="fw-bold">Đối tác vận chuyển</h6>
                        <div class="d-flex flex-wrap align-items-center">
                            <a href="#" class="footer-logo-item me-3 mb-2"><img src="img/footer/vietnampost.png" alt="Vietnam Post"></a>
                            <a href="#" class="footer-logo-item me-3 mb-2"><img src="img/footer/ex.png" alt="Partner"></a>
                        </div>
                    </div>
                    <!-- App Download -->
                    <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                        <h6 class="fw-bold">Tải ứng dụng</h6>
                        <div class="d-flex flex-wrap align-items-center">
                            <a href="#" class="footer-logo-item me-3 mb-2"><img src="img/footer/store.png" alt="App Store"></a>
                            <a href="#" class="footer-logo-item me-3 mb-2"><img src="img/footer/play.png" alt="Google Play"></a>
                        </div>
                    </div>
                    <!-- BCT Logo -->
                    <div class="col-lg-2 col-md-6 text-lg-end">
                        <a href="#" class="footer-logo-item"><img src="img/footer/logo_bct.png" alt="Bộ Công Thương" style="max-height: 45px; max-width: 120px;"></a>
                    </div>
                </div>
                <div class="border-top border-secondary mt-5 pt-4 text-center text-white-50">
                    <small>&copy; 2025 BookZ Store.</small>
                </div>
            </div>
        </footer>

        <!-- ============== Scroll to Top Button ==============-->
        <a href="#" id="scrollTopBtn" title="Go to top"><i class="fas fa-arrow-up"></i></a>

        <!-- JS Bundle -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

        <script>
            // Preloader Logic
            window.addEventListener('load', function() {
                const preloader = document.getElementById('preloader');
                if (preloader) {
                    preloader.classList.add('hidden');
                }
            });

            const swiper = new Swiper('.hero-slider', {
                effect: 'fade',
                loop: true,
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true
                },
            });

            // Init Bestseller Swiper (NEW)
            const bestsellerSwiper = new Swiper('.bestseller-swiper', {
                slidesPerView: 1,
                spaceBetween: 20,
                loop: true,
                autoplay: {
                    delay: 2500,
                    disableOnInteraction: false
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 20
                    },
                    1024: {
                        slidesPerView: 5,
                        spaceBetween: 30
                    },
                }
            });

            // Scroll to Top Button Logic
            const scrollTopBtn = document.getElementById('scrollTopBtn');

            window.addEventListener('scroll', function() {
                if (window.scrollY > 200) {
                    scrollTopBtn.classList.add('show');
                } else {
                    scrollTopBtn.classList.remove('show');
                }
            });

            scrollTopBtn.addEventListener('click', function(e) {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // Snow Effect Logic
            document.addEventListener('DOMContentLoaded', function() {
                const snowContainer = document.getElementById('snow-container');
                const numberOfSnowflakes = 100; // Bạn có thể tăng/giảm số lượng tuyết

                for (let i = 0; i < numberOfSnowflakes; i++) {
                    let snowflake = document.createElement('div');
                    snowflake.className = 'snowflake';

                    let size = Math.random() * 4 + 1; // Kích thước từ 1px đến 5px
                    let left = Math.random() * 100; // Vị trí từ 0% đến 100%
                    let duration = Math.random() * 5 + 5; // Thời gian rơi từ 5s đến 10s
                    let delay = Math.random() * 5; // Độ trễ

                    snowflake.style.width = `${size}px`;
                    snowflake.style.height = `${size}px`;
                    snowflake.style.left = `${left}vw`;
                    snowflake.style.animationDuration = `${duration}s`;
                    snowflake.style.animationDelay = `${delay}s`;

                    snowContainer.appendChild(snowflake);
                }
            });


            window.addEventListener('scroll', function() {
                const nav = document.getElementById('mainNavbar');
                const headerContainer = nav.closest('.header-container');
                if (window.scrollY > 50) {
                    nav.classList.add('scrolled');
                    headerContainer.classList.add('scrolled');
                } else {
                    nav.classList.remove('scrolled');
                    headerContainer.classList.remove('scrolled');
                }
            });

            // Multi-level dropdown script
            document.addEventListener("DOMContentLoaded", function() {
                document.querySelectorAll('.dropdown-menu .dropdown-submenu').forEach(function(element) {
                    element.addEventListener('mouseover', function(e) {
                        let submenu = this.querySelector('.dropdown-menu');
                        if (submenu) {
                            submenu.classList.add('show');
                        }
                        e.stopPropagation();
                    });
                    element.addEventListener('mouseout', function(e) {
                        this.querySelector('.dropdown-menu').classList.remove('show');
                        e.stopPropagation();
                    });
                });
            });

            // Logic cho nút "Xem thêm" ở mục New Arrivals
            document.addEventListener('DOMContentLoaded', function() {
                const viewMoreBtn = document.getElementById('viewMoreBtn');
                const viewMoreContainer = document.getElementById('viewMoreContainer');
                const grid = document.getElementById('new-arrivals-grid');

                if (viewMoreBtn && grid && viewMoreContainer) {
                    const items = grid.querySelectorAll('.book-item');
                    const itemsToShow = 10; // Hiển thị 2 hàng (5 sản phẩm/hàng)
                    let isExpanded = false; // Trạng thái: false = thu gọn, true = mở rộng

                    // Ẩn các sản phẩm thừa ban đầu
                    items.forEach((item, index) => {
                        if (index >= itemsToShow) {
                            item.style.display = 'none';
                        }
                    });

                    // Nếu không có sản phẩm nào để ẩn, thì ẩn luôn nút "Xem thêm"
                    if (items.length <= itemsToShow) {
                        viewMoreContainer.style.display = 'none';
                        return; // Dừng thực thi nếu không cần nút
                    }

                    viewMoreBtn.addEventListener('click', function() {
                        isExpanded = !isExpanded; // Đảo ngược trạng thái

                        if (isExpanded) {
                            // Mở rộng: Hiển thị tất cả
                            items.forEach(item => item.style.display = 'block');
                            viewMoreBtn.innerHTML = 'Thu gọn <i class="fas fa-chevron-up ms-2"></i>';
                        } else {
                            // Thu gọn: Ẩn các mục thừa
                            items.forEach((item, index) => {
                                if (index >= itemsToShow) item.style.display = 'none';
                            });
                            viewMoreBtn.innerHTML = 'Xem thêm sách mới <i class="fas fa-chevron-down ms-2"></i>';
                        }
                    });
                }
            });

            // Quick View Logic
            function openQuickView(bookData) {
                if (!bookData) return;

                // Định dạng giá tiền
                const formattedPrice = new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                }).format(bookData.Price);

                // Điền dữ liệu vào modal
                document.getElementById('qv-img').src = 'img/books/' + bookData.PID + '.jpg';
                document.getElementById('qv-title').innerText = bookData.Title;
                document.getElementById('qv-author').innerText = 'Tác giả: ' + bookData.Author;
                document.getElementById('qv-cat').innerText = bookData.Category || 'Chưa phân loại';
                document.getElementById('qv-price').innerText = formattedPrice;
                document.getElementById('qv-desc').innerText = bookData.Description || 'Sản phẩm này chưa có mô tả.';

                // Cập nhật link
                document.getElementById('qv-add-cart').href = 'cart.php?ID=' + bookData.PID + '&quantity=1';
                document.getElementById('qv-detail').href = 'description.php?ID=' + bookData.PID;

                // Hiển thị modal
                var quickViewModal = new bootstrap.Modal(document.getElementById('quickViewModal'));
                quickViewModal.show();
            }

            // NEW: Add to Wishlist AJAX Logic
            function addToWishlist(productId) {
                fetch('add_to_wishlist_ajax.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `product_id=${productId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            // Nếu yêu cầu đăng nhập, mở modal
                            if (data.type === 'login_required') {
                                var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                                loginModal.show();
                            } else {
                                // Hiển thị các lỗi khác (đã tồn tại, lỗi server,...)
                                Swal.fire({
                                    icon: data.type, // 'info', 'error'
                                    title: 'Thông báo',
                                    text: data.message,
                                });
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi kết nối',
                            text: 'Không thể kết nối đến máy chủ. Vui lòng thử lại.'
                        });
                    });
            }
        </script>
        </body>

        </html>