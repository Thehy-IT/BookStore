    <?php
    session_start();

    include "dbconnect.php";

    $swal_script = "";

    function set_swal($icon, $title, $text = "")
    {
        return "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '$icon',
                    title: '$title',
                    text: '$text',
                    background: 'rgba(255, 255, 255, 0.9)',
                    backdrop: `rgba(0,0,123,0.1)`,
                    confirmButtonColor: '#0f172a',
                    customClass: { popup: 'glass-modal-alert' }
                });
            });
        </script>";
    }

    if (isset($_POST['submit']) && $con) {
        if ($_POST['submit'] == "login") {
            $username = $_POST['login_username'];
            $password_input = $_POST['login_password'];

            // 1. SELECT CẢ CỘT ROLE ĐỂ PHÂN QUYỀN
            $stmt = $con->prepare("SELECT * FROM users WHERE UserName = ?");
            if ($stmt) {
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();

                    if (password_verify($password_input, $row['Password'])) {
                        $_SESSION['user'] = $row['UserName'];
                        $_SESSION['role'] = $row['Role'];
                        $_SESSION['user_id'] = $row['UserID'];

                        // Chuyển hướng dựa trên quyền
                        if ($row['Role'] == 'admin') {
                            header("Location: admin.php");
                            exit();
                        } else {
                            // Với user thường, chỉ cần tải lại trang để cập nhật header
                            header("Location: index.php");
                            exit();
                        }
                    } else {
                        $swal_script = set_swal('error', 'Login Failed', 'Sai mật khẩu.');
                    }
                } else {
                    $swal_script = set_swal('error', 'Login Failed', 'Tài khoản không tồn tại.');
                }
                $stmt->close();
            }
        } else if ($_POST['submit'] == "register") {
            $username = $_POST['register_username'];
            $password = $_POST['register_password'];
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $con->prepare("INSERT INTO users (UserName, Password, Role) VALUES (?, ?, 'user')");
            $stmt->bind_param("ss", $username, $hashed_password);

            if ($stmt->execute()) {
                $swal_script = set_swal('success', 'Registered!', 'Tài khoản đã tạo thành công. Vui lòng đăng nhập.');
            } else {
                $swal_script = set_swal('warning', 'Registration Failed', 'Tên đăng nhập đã tồn tại.');
            }
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>BookZ | Premium Glassmorphism Store</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Bootstrap 5.3 & Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <style>
            :root {
                --primary: #0f172a;
                --accent: #d4af37;
                --glass-bg: rgba(255, 255, 255, 0.65);
                --glass-border: 1px solid rgba(255, 255, 255, 0.4);
                --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
                --blur-amt: 16px;
            }

            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                color: var(--primary);
                background: linear-gradient(to bottom, #e0e8f0, #ffffff);
                overflow-x: hidden;
                position: relative;
            }

            .bg-blobs {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: -1;
                background: radial-gradient(circle at 10% 10%, rgba(212, 175, 55, 0.15), transparent 40%),
                    radial-gradient(circle at 90% 80%, rgba(15, 23, 42, 0.1), transparent 40%),
                    radial-gradient(circle at 50% 50%, rgba(255, 255, 255, 0.8), transparent 100%);
            }

            h1,
            h2,
            h3,
            h4,
            h5 {
                font-family: 'Playfair Display', serif;
            }

            /* --- GLASS & NAVBAR STYLES (KEEP EXISTING) --- */
            .glass-panel {
                background: var(--glass-bg);
                backdrop-filter: blur(var(--blur-amt));
                -webkit-backdrop-filter: blur(var(--blur-amt));
                border: var(--glass-border);
                box-shadow: var(--glass-shadow);
            }

            .navbar {
                padding: 15px 0;
                transition: all 0.4s ease;
            }

            .navbar.glass-nav {
                margin: 15px auto 0;
                width: 95%;
                border-radius: 50px;
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(20px);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.5);
            }

            .header-container {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                z-index: 1030;
                transition: all 0.3s ease;
            }

            .navbar-brand span {
                font-weight: 800;
                letter-spacing: -0.5px;
                font-size: 1.6rem;
            }

            .nav-link {
                font-weight: 600;
                color: var(--primary) !important;
                position: relative;
                padding: 10px 15px !important;
                border-radius: 8px;
                transition: all 0.3s;
            }

            .nav-link:hover {
                background: rgba(0, 0, 0, 0.05);
                color: var(--accent) !important;
            }

            .nav-link::after {
                display: none;
            }

            .main-menu-bar {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(16px);
                width: fit-content;
                margin: 0 auto;
                border-radius: 50px;
                padding: 5px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.5);
            }

            .search-glass {
                background: rgba(255, 255, 255, 0.5);
                border: 1px solid rgba(0, 0, 0, 0.1);
                border-radius: 30px;
                padding-left: 20px;
            }

            .search-glass:focus {
                background: white;
                box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
                border-color: var(--accent);
            }

            .navbar.glass-nav {
                background: transparent;
                box-shadow: none;
                border: none;
            }

            .hero-wrapper {
                padding-top: 120px;
                padding-bottom: 50px;
            }

            .hero-slider {
                border-radius: 24px;
                overflow: hidden;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            }

            .category-glass-card {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(12px);
                border-radius: 16px;
                padding: 20px;
                border: 1px solid rgba(255, 255, 255, 0.5);
                transition: 0.3s;
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .category-glass-card:hover {
                background: rgba(255, 255, 255, 0.9);
                transform: translateY(-5px);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            }

            /* Book Card Styles */
            .book-card-glass {
                background: rgba(255, 255, 255, 0.6);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.6);
                border-radius: 20px;
                padding: 15px;
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                position: relative;
                overflow: hidden;
            }

            .book-card-glass:hover {
                background: rgba(255, 255, 255, 0.95);
                transform: translateY(-10px) scale(1.02);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
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
                transition: 0.5s;
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
                transition: 0.3s;
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
                transition: 0.3s;
            }

            .btn-icon:hover {
                background: var(--accent);
                transform: rotate(90deg);
            }

            .badge-glass {
                /* Ribbon Style Container */
                position: absolute;
                top: -6px;
                left: -6px;
                width: 120px;
                height: 120px;
                overflow: hidden;
                z-index: 3;
                /* Ensure it's above the image */
            }

            .badge-glass span {
                position: absolute;
                display: block;
                width: 160px;
                padding: 8px 0;
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
                color: #fff;
                font-weight: 700;
                text-align: center;
                text-transform: uppercase;
                font-size: 0.8rem;
                transform: rotate(-45deg) translateY(-20px);
                left: -40px;
                top: 32px;
            }

            /* --- Badge Colors --- */
            .badge-new span {
                background: linear-gradient(45deg, #3b82f6, #60a5fa);
                /* Blue */
            }

            .badge-hot span {
                background: linear-gradient(45deg, #ef4444, #f87171);
                /* Red */
            }

            .badge-sale span {
                background: linear-gradient(45deg, #f59e0b, #fcd34d);
                /* Amber */
            }

            .badge-best span {
                background: linear-gradient(45deg, var(--accent), #e7c86a);
                /* Gold */
                color: var(--primary) !important;
            }

            /* Modal & Footer */
            .modal-content {
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.6);
                border-radius: 24px;
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            }

            .modal-header {
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            }

            .btn-primary-glass {
                background: var(--primary);
                color: white;
                border-radius: 50px;
                padding: 10px 30px;
                font-weight: 600;
                transition: 0.3s;
                box-shadow: 0 10px 20px rgba(15, 23, 42, 0.2);
            }

            .btn-primary-glass:hover {
                background: var(--accent);
                transform: translateY(-2px);
                box-shadow: 0 15px 30px rgba(212, 175, 55, 0.3);
            }

            footer {
                margin-top: 80px;
                background: linear-gradient(to top, #0f172a 0%, #1e293b 100%);
                color: white;
                padding: 60px 0 20px;
                position: relative;
                overflow: hidden;
            }

            .footer-wave {
                position: absolute;
                top: -50px;
                left: 0;
                width: 100%;
                line-height: 0;
            }

            .dropdown-menu .dropdown-submenu {
                position: relative;
            }

            .dropdown-menu .dropdown-submenu .dropdown-menu {
                top: 0;
                left: 100%;
                margin-top: -1px;
            }

            .dropdown-menu .dropdown-submenu:hover>.dropdown-menu {
                display: block;
            }

            .promo-card {
                display: flex;
                align-items: center;
                background: rgba(255, 255, 255, 0.6);
                backdrop-filter: blur(10px);
                padding: 15px;
                border-radius: 12px;
                border: 1px solid rgba(255, 255, 255, 0.5);
                transition: 0.3s;
            }

            .promo-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            }

            .promo-icon {
                font-size: 2rem;
                color: var(--primary);
                margin-right: 15px;
            }

            /* ================= NEW SECTION STYLES ================= */

            /* 1. Bestsellers (Carousel Style) */
            .bestseller-section {
                background: linear-gradient(135deg, rgba(255, 255, 255, 0.4), rgba(255, 255, 255, 0.1));
                border-top: 1px solid rgba(255, 255, 255, 0.5);
                border-bottom: 1px solid rgba(255, 255, 255, 0.5);
                padding: 60px 0;
                margin-top: 60px;
            }

            .bestseller-card {
                background: transparent;
                padding: 10px;
            }

            .rank-number {
                font-size: 4rem;
                font-weight: 900;
                color: rgba(15, 23, 42, 0.1);
                position: absolute;
                top: -20px;
                right: 0;
                z-index: 0;
                line-height: 1;
            }

            /* 2. Deals of the Day (Feature Block Style) */
            .deal-wrapper {
                background: linear-gradient(135deg, var(--primary), #1e293b);
                border-radius: 30px;
                overflow: hidden;
                position: relative;
                color: white;
                box-shadow: 0 20px 50px rgba(15, 23, 42, 0.3);
            }

            .deal-content {
                padding: 50px;
                z-index: 2;
                position: relative;
            }

            .deal-timer-box {
                display: inline-block;
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(5px);
                padding: 10px 20px;
                border-radius: 10px;
                margin: 0 5px;
                text-align: center;
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            .deal-timer-number {
                font-size: 1.5rem;
                font-weight: 700;
                display: block;
                color: var(--accent);
            }

            .deal-timer-label {
                font-size: 0.7rem;
                text-transform: uppercase;
                letter-spacing: 1px;
            }

            .deal-image-container {
                position: relative;
                height: 100%;
                min-height: 400px;
            }

            .deal-image {
                position: absolute;
                right: 0;
                bottom: 0;
                width: 90%;
                height: auto;
                object-fit: contain;
                filter: drop-shadow(-20px 20px 30px rgba(0, 0, 0, 0.5));
                transform: scale(1.1) translateY(20px);
            }

            /* 3. News (Grid Card Style) */
            .news-card {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                transition: 0.3s;
                border: 1px solid rgba(0, 0, 0, 0.05);
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .news-card:hover {
                transform: translateY(-10px);
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
            }

            .news-img {
                height: 200px;
                object-fit: cover;
                width: 100%;
            }

            .news-body {
                padding: 25px;
                flex-grow: 1;
                display: flex;
                flex-direction: column;
            }

            .news-date {
                font-size: 0.8rem;
                color: var(--accent);
                font-weight: 700;
                text-transform: uppercase;
                margin-bottom: 10px;
            }

            .news-title {
                font-weight: 700;
                margin-bottom: 10px;
                font-family: 'Playfair Display', serif;
                font-size: 1.25rem;
            }

            .read-more-link {
                margin-top: auto;
                color: var(--primary);
                font-weight: 700;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
            }

            .read-more-link:hover {
                color: var(--accent);
            }

            /* --- Footer hover effect --- */
            .footer-link:hover {
                color: white !important;
                padding-left: 5px;
                transition: all 0.3s ease;
            }

            /* --- Social Media Icon Hover --- */
            .social-icon {
                transition: color 0.3s ease, transform 0.3s ease;
                display: inline-block;
                /* Needed for transform */
            }

            .social-icon:hover {
                transform: scale(1.2);
            }

            .social-icon-facebook:hover {
                color: #1877F2 !important;
            }

            .social-icon-instagram:hover {
                color: #E4405F !important;
            }

            .social-icon-twitter:hover {
                color: #1DA1F2 !important;
            }

            .social-icon-youtube:hover {
                color: #FF0000 !important;
            }

            /* --- Search bar hover effect --- */
            .search-form-hover {
                display: flex;
                align-items: center;
                transition: all 0.4s ease;
                position: relative;
                /* Làm mốc cho input con */
            }

            .search-form-hover .search-input {
                position: absolute;
                /* Nổi lên trên, không chiếm không gian */
                right: 100%;
                /* Bắt đầu từ bên phải của form */
                top: 50%;
                transform: translateY(-50%) scaleX(0);
                /* Ẩn bằng cách co lại theo chiều ngang */
                transform-origin: right;
                /* Hiệu ứng bung ra từ bên phải */
                width: 95px;
                /* Chiều rộng cố định (bạn có thể thay đổi giá trị này) */
                border: none;
                border-bottom: 2px solid var(--primary);
                background-color: transparent;
                outline: none;
                font-size: 1rem;
                color: var(--primary);
                transition: transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
                padding: 8px 10px;
            }

            .search-form-hover .search-button {
                background: transparent;
                border: none;
                font-size: 1.2rem;
                color: var(--primary);
                cursor: pointer;
                padding: 8px;
            }

            .search-form-hover:hover .search-input {
                transform: translateY(-50%) scaleX(1);
                /* Hiện ra bằng cách giãn ra */
            }

            /* --- Scroll to Top Button --- */
            #scrollTopBtn {
                position: fixed;
                bottom: 25px;
                right: 25px;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                background: var(--primary);
                color: white;
                border: none;
                z-index: 1000;
                box-shadow: 0 8px 20px rgba(15, 23, 42, 0.2);
                transition: opacity 0.3s ease, transform 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
                text-decoration: none;
                opacity: 0;
                transform: translateY(20px);
                visibility: hidden;
            }

            #scrollTopBtn.show {
                opacity: 1;
                transform: translateY(0);
                visibility: visible;
            }

            #scrollTopBtn:hover {
                background: var(--accent);
                transform: translateY(-5px);
                box-shadow: 0 12px 25px rgba(212, 175, 55, 0.3);
            }

            #scrollTopBtn:active {
                transform: translateY(-2px) scale(0.95);
                box-shadow: 0 5px 15px rgba(15, 23, 42, 0.2);
            }

            /* --- Snow Effect --- */
            .snowflake {
                position: fixed;
                top: -10px;
                background: white;
                border-radius: 50%;
                opacity: 0.7;
                pointer-events: none;
                z-index: 9999;
                box-shadow: 0 0 5px #fff, 0 0 10px #fff, 0 0 15px #b0e0e6;
                animation: fall linear infinite;
            }

            @keyframes fall {
                to {
                    transform: translateY(105vh);
                }
            }
        </style>
    </head>

    <body>
        <?php echo $swal_script; ?>

        <!-- Container cho tuyết rơi (sẽ được tạo bằng JS) -->
        <div id="snow-container"></div>

        <!-- Background Elements -->
        <div class="bg-blobs"></div>

        <!-- ============== Navbar ==============-->
        <header class="header-container">
            <!-- Hàng 1: Logo, Search, User -->
            <nav class="navbar navbar-expand-lg glass-nav" id="mainNavbar">
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
                            <li class="nav-item"><a class="nav-link active" href="index.php">Trang chủ</a></li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Thể loại</a>
                                <ul class="dropdown-menu glass-panel border-0 shadow-lg">
                                    <li><a class="dropdown-item" href="Product.php?value=Literature and Fiction">Văn học & Hư cấu</a></li>
                                    <li><a class="dropdown-item" href="Product.php?value=Biographies and Auto Biographies">Tiểu sử & Tự truyện</a></li>
                                    <li><a class="dropdown-item" href="Product.php?value=Academic and Professional">Học thuật & Chuyên ngành</a></li>
                                    <li><a class="dropdown-item" href="Product.php?value=Business and Management">Kinh doanh & Quản lý</a></li>
                                    <li><a class="dropdown-item" href="Product.php?value=Children and Teens">Sách thiếu nhi</a></li>
                                    <li><a class="dropdown-item" href="Product.php?value=Health and Cooking">Sức khỏe & Nấu ăn</a></li>
                                    <li><a class="dropdown-item" href="Product.php?value=Regional Books">Sách tiếng Việt</a></li>
                                </ul>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#new">Sách mới</a></li>
                            <li class="nav-item"><a class="nav-link" href="#bestseller">Bán chạy</a></li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Tác giả</a>
                                <ul class="dropdown-menu glass-panel border-0 shadow-lg">
                                    <li><a class="dropdown-item" href="Author.php?value=Chetan Bhagat">Chetan Bhagat</a></li>
                                    <li><a class="dropdown-item" href="Author.php?value=J K Rowling">J.K. Rowling</a></li>
                                    <li><a class="dropdown-item" href="Author.php?value=Ravinder Singh">Ravinder Singh</a></li>
                                    <li><a class="dropdown-item" href="Author.php?value=Jeffrey Archer">Jeffrey Archer</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="author.php">Xem tất cả tác giả</a></li>
                                </ul>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#deals">Khuyến mãi</a></li>
                            <li class="nav-item"><a class="nav-link" href="#news">Tin tức</a></li>
                            <li class="nav-item"><a class="nav-link" href="#contact">Liên hệ</a></li>
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
                                <!-- Notification Dropdown -->
                                <li class="nav-item dropdown ms-1">
                                    <a class="nav-link p-0" href="#" role="button" data-bs-toggle="dropdown" style="width:40px; height:40px; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-bell fs-5"></i>
                                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="margin-top: 8px; margin-left: -12px;">
                                            <span class="visually-hidden">New alerts</span>
                                        </span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 glass-panel mt-2" style="width: 300px;">
                                        <li class="px-3 py-2 fw-bold">Notifications</li>
                                        <li>
                                            <hr class="dropdown-divider m-0">
                                        </li>
                                        <li><a class="dropdown-item py-2" href="#">
                                                <small class="fw-bold">Đơn hàng #12345 đã được giao</small><br>
                                                <small class="text-muted">15 phút trước</small>
                                            </a></li>
                                        <li><a class="dropdown-item py-2" href="#">
                                                <small class="fw-bold">Khuyến mãi Black Friday sắp bắt đầu!</small><br>
                                                <small class="text-muted">Hôm qua</small>
                                            </a></li>
                                    </ul>
                                </li>
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
                    ['icon' => 'fa-graduation-cap', 'title' => 'Entrance Exam', 'color' => '#4e54c8'],
                    ['icon' => 'fa-book-open', 'title' => 'Literature', 'color' => '#11998e'],
                    ['icon' => 'fa-briefcase', 'title' => 'Business', 'color' => '#ee0979'],
                    ['icon' => 'fa-child', 'title' => 'Kids & Teens', 'color' => '#f12711']
                ];
                foreach ($cats as $c) {
                    echo '<div class="col-6 col-md-3"><a href="Product.php?category=' . urlencode($c['title']) . '" class="text-decoration-none text-dark"><div class="category-glass-card text-center"><div class="mb-3" style="font-size: 2rem; color: ' . $c['color'] . '"><i class="fas ' . $c['icon'] . '"></i></div><h6 class="fw-bold mb-0">' . $c['title'] . '</h6></div></a></div>';
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
            <div class="row g-4">
                <?php
                $books = [
                    ['title' => 'The Great Adventure', 'price' => '150.000', 'img' => 'img/new/1.jpg', 'tag' => 'NEW'],
                    ['title' => 'Minimalist Living', 'price' => '200.000', 'img' => 'img/new/2.jpg', 'tag' => 'HOT'],
                    ['title' => 'Code Your Future', 'price' => '180.000', 'img' => 'img/new/3.png', 'tag' => '-20%'],
                    ['title' => 'History of Art', 'price' => '350.000', 'img' => 'img/new/4.jpg', 'tag' => 'BEST']
                ];
                foreach ($books as $idx => $book) {
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
                    <div class="col-6 col-md-3">
                        <div class="book-card-glass h-100 d-flex flex-column">
                            <div class="badge-glass <?php echo $badge_class; ?>"><span><?php echo $book['tag']; ?></span></div>
                            <div class="book-img-wrapper">
                                <img src="<?php echo $book['img']; ?>" onerror="this.src='https://placehold.co/400x600/eee/31343C?text=Book+Cover'" alt="Book">
                                <div class="action-overlay">
                                    <button class="btn-icon"><i class="fas fa-shopping-cart"></i></button>
                                    <a href="description.php?ID=<?php echo $idx; ?>" class="btn-icon"><i class="fas fa-eye"></i></a>
                                    <a href="wishlist.php?ID=<?php echo $idx; ?>" class="btn-icon"><i class="fas fa-heart"></i></a>
                                </div>
                            </div>
                            <div class="mt-auto">
                                <h6 class="fw-bold text-truncate"><?php echo $book['title']; ?></h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-primary fw-bold"><?php echo $book['price']; ?> đ</span>
                                    <div class="text-warning small"><i class="fas fa-star"></i> 4.8</div>
                                </div>
                            </div>
                            <a href="description.php?ID=<?php echo $idx; ?>" class="stretched-link"></a>
                        </div>
                    </div>
                <?php } ?>
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

                <div class="swiper bestseller-swiper pb-5">
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
                    </div>
                    <div class="swiper-pagination"></div>
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

        <!-- ============== Login Modal (Glass) ==============-->
        <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-3">
                    <div class="modal-header border-0">
                        <h4 class="modal-title fw-bold">Hello Again!</h4>
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
                            <button type="submit" name="submit" value="login" class="btn btn-primary-glass w-100 btn-lg">Sign In</button>
                        </form>
                        <div class="text-center mt-3">
                            <small class="text-muted">Don't have an account? <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" class="fw-bold text-primary">Sign Up</a></small>
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
                        <h4 class="modal-title fw-bold">Create Account</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control rounded-4 bg-light border-0" name="register_username" placeholder="Username" required>
                                <label>Choose Username</label>
                            </div>
                            <div class="form-floating mb-4">
                                <input type="password" class="form-control rounded-4 bg-light border-0" name="register_password" placeholder="Password" required>
                                <label>Choose Password</label>
                            </div>
                            <button type="submit" name="submit" value="register" class="btn btn-primary-glass w-100 btn-lg">Register</button>
                        </form>
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

            <div class="container position-relative z-2">
                <div class="row gy-4">
                    <div class="col-lg-4 col-md-6">
                        <a class="navbar-brand text-white fs-3" href="index.php">
                            <i class="fas fa-book-open text-warning me-2"></i>
                            <span>BOOK<span style="color: var(--accent)">Z</span></span>
                        </a>
                        <p class="text-white-50 mt-3">Khám phá thế giới tri thức với bộ sưu tập sách chọn lọc, mang đến trải nghiệm đọc cao cấp cho độc giả hiện đại.</p>
                        <div class="mt-4">
                            <a href="#" class="text-white-50 me-3 fs-5 social-icon social-icon-facebook" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="text-white-50 me-3 fs-5 social-icon social-icon-instagram" title="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="text-white-50 me-3 fs-5 social-icon social-icon-twitter" title="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-white-50 fs-5 social-icon social-icon-youtube" title="YouTube"><i class="fab fa-youtube"></i></a>
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
                            <input type="email" class="form-control bg-white bg-opacity-10 border-0 text-white" placeholder="Nhập email của bạn..." style="border-radius: 50px 0 0 50px;">
                            <button class="btn btn-warning" type="button" style="border-radius: 0 50px 50px 0;">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
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
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 20
                    },
                    1024: {
                        slidesPerView: 4,
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
                const nav = document.querySelector('.navbar');
                if (window.scrollY > 50) {
                    nav.classList.add('shadow-sm');
                    nav.style.background = 'rgba(255, 255, 255, 0.95)';
                } else {
                    nav.classList.remove('shadow-sm');
                    nav.style.background = 'rgba(255, 255, 255, 0.85)';
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
        </script>
    </body>

    </html>