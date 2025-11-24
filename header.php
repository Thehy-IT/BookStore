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

if ($con) {
    if (isset($_POST['login'])) {
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
    } else if (isset($_POST['register'])) {
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

// --- Lấy danh sách thể loại tự động từ cơ sở dữ liệu cho menu ---
$categories_menu = [];
// Mảng ánh xạ từ giá trị trong DB sang tên hiển thị thân thiện hơn (có thể là tiếng Việt)
$category_translations = [
    'self-help' => 'Phát triển bản thân',
    'fiction' => 'Tiểu thuyết',
    'thriller' => 'Kinh dị & Giật gân',
    'romance' => 'Lãng mạn',
    'fantasy' => 'Giả tưởng',
    'biography' => 'Tiểu sử',
];

$sql_categories_menu = "SELECT DISTINCT Category FROM products WHERE Category IS NOT NULL AND Category != '' ORDER BY Category ASC";
$result_categories_menu = mysqli_query($con, $sql_categories_menu);
if ($result_categories_menu && mysqli_num_rows($result_categories_menu) > 0) {
    while ($row_cat_menu = mysqli_fetch_assoc($result_categories_menu)) {
        // Chuẩn hóa key: chuyển thành chữ thường và loại bỏ khoảng trắng thừa
        $category_key = strtolower(trim($row_cat_menu['Category']));
        $categories_menu[$category_key] = $category_translations[$category_key] ?? ucfirst($category_key);
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
                                <?php
                                // Sử dụng mảng categories_menu vừa lấy được
                                foreach ($categories_menu as $cat_slug => $cat_name) {
                                    // Lưu ý: param trên URL là 'category', không phải 'value'
                                    echo '<li><a class="dropdown-item" href="Product.php?category=' . urlencode($cat_slug) . '">' . htmlspecialchars($cat_name) . '</a></li>';
                                }
                                ?>
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