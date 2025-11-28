<?php
// session_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// error_reporting(0);
// ini_set('display_errors', 0);

// Cấu hình hiển thị lỗi cho môi trường phát triển (development)
error_reporting(E_ALL); // Báo cáo tất cả các lỗi PHP
ini_set('display_errors', 1); // Hiển thị lỗi trên màn hình
ini_set('display_startup_errors', 1); // Hiển thị cả lỗi khởi động của PHP

// Khởi tạo biến swal_script rỗng để header không báo lỗi "Undefined variable"
$swal_script = "";
// KHỞI TẠO BIẾN LỖI CHO MODAL
$login_error = "";
$register_error = "";

include_once "dbconnect.php";

function set_swal($icon, $title, $text = "", $is_toast = false)
{
    if ($is_toast) {
        return "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
                Toast.fire({ icon: '$icon', title: '$title' });
            });
        </script>";
    }

    return "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '$icon',
                    title: '$title',
                    text: '$text',
                    background: 'rgba(255, 255, 255, 0.9)',
                    backdrop: `rgba(0,0,0,0.4)`,
                    confirmButtonColor: '#0f172a',
                    customClass: { popup: 'glass-modal-alert' }
                });
            });
        </script>";
}

if ($con) {
    if (isset($_POST['login'])) {
        $identity = trim($_POST['login_identity'] ?? ''); // Có thể là username hoặc email
        $password_input = $_POST['login_password'] ?? '';

        if (empty($identity) || empty($password_input)) {
            $login_error = "Vui lòng nhập đầy đủ thông tin đăng nhập.";
        } else {
            // 1. Cho phép đăng nhập bằng cả UserName hoặc Email
            $stmt = $con->prepare("SELECT * FROM users WHERE UserName = ? OR Email = ?");
            if ($stmt) {
                $stmt->bind_param("ss", $identity, $identity);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {
                    $row = $result->fetch_assoc(); // Lấy thông tin người dùng

                    if (password_verify($password_input, $row['Password'])) {
                        $_SESSION['user'] = $row['UserName'];
                        $_SESSION['role'] = $row['Role'];
                        $_SESSION['user_id'] = $row['UserID'];

                        // Chuyển hướng dựa trên quyền
                        if ($row['Role'] == 'admin') {
                            header("Location: admin.php");
                            exit();
                        } else {
                            // Đặt thông báo chào mừng và tải lại trang hiện tại
                            $_SESSION['flash_message'] = "Đăng nhập thành công! Chào mừng trở lại, " . htmlspecialchars($row['UserName']) . ".";
                            $_SESSION['flash_type'] = "success";
                            header("Location: " . $_SERVER['PHP_SELF']); // Tải lại trang hiện tại
                            exit();
                        }
                    } else {
                        // Mật khẩu sai. Thông báo chung để tránh dò tên người dùng.
                        $login_error = "Tên đăng nhập hoặc mật khẩu không chính xác.";
                    }
                } else {
                    // Tên đăng nhập/email không tồn tại. Thông báo chung.
                    $login_error = "Tên đăng nhập hoặc mật khẩu không chính xác.";
                }
                $stmt->close();
            }
        }
    } else if (isset($_POST['register'])) {
        // Lấy dữ liệu từ form đăng ký mới
        $username = $_POST['register_username'] ?? '';
        $fullname = $_POST['register_fullname'] ?? '';
        $email = $_POST['register_email'] ?? '';
        $password = $_POST['register_password'] ?? '';
        $confirm_password = $_POST['register_confirm_password'] ?? '';

        // --- VALIDATION ---
        $username = $_POST['register_username'] ?? '';
        $fullname = $_POST['register_fullname'] ?? '';
        $email = $_POST['register_email'] ?? '';
        $password = $_POST['register_password'] ?? '';
        $confirm_password = $_POST['register_confirm_password'] ?? '';

        // --- VALIDATION ---
        if (empty($username) || empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
            $register_error = "Vui lòng điền đầy đủ các trường.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $register_error = "Vui lòng nhập một địa chỉ email hợp lệ.";
        } elseif (strlen($password) < 6) {
            $register_error = "Mật khẩu phải có ít nhất 6 ký tự.";
        } elseif ($password !== $confirm_password) {
            $register_error = "Mật khẩu xác nhận không giống nhau.";
        } else {
            // 1. Kiểm tra xem username hoặc email đã tồn tại chưa
            $stmt_check = $con->prepare("SELECT UserID FROM users WHERE UserName = ? OR Email = ?");
            $stmt_check->bind_param("ss", $username, $email);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $register_error = "Tên đăng nhập hoặc Email đã được sử dụng.";
            } else {
                // 2. Nếu chưa tồn tại, tiến hành thêm người dùng mới
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                // Thêm FullName và Email vào câu lệnh INSERT
                $stmt_insert = $con->prepare("INSERT INTO users (UserName, FullName, Email, Password, Role) VALUES (?, ?, ?, ?, 'user')");
                $stmt_insert->bind_param("ssss", $username, $fullname, $email, $hashed_password);

                if ($stmt_insert->execute()) {
                    // Chuyển hướng đến trang đăng nhập với thông báo thành công
                    $_SESSION['flash_message'] = "Đăng ký thành công! Vui lòng đăng nhập.";
                    $_SESSION['flash_type'] = "success";
                    // Chuyển hướng để tránh gửi lại form và hiển thị thông báo
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                } else {
                    $register_error = "Đã có lỗi xảy ra. Vui lòng thử lại sau.";
                }
            }
            $stmt_check->close();
        }
    }
}

// MỞ LẠI MODAL KHI CÓ LỖI
if (!empty($login_error)) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
        });
    </script>";
} elseif (!empty($register_error)) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            var registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
            registerModal.show();
        });
    </script>";
}

// --- Lấy danh sách thể loại tự động từ cơ sở dữ liệu cho menu ---
$categories_menu = [];
// Mảng ánh xạ từ giá trị trong DB sang tên hiển thị thân thiện hơn (có thể là tiếng Việt)
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

$sql_categories_menu = "SELECT DISTINCT Category FROM products WHERE Category IS NOT NULL AND Category != '' ORDER BY Category ASC";
$result_categories_menu = mysqli_query($con, $sql_categories_menu);
if ($result_categories_menu && mysqli_num_rows($result_categories_menu) > 0) {
    while ($row_cat_menu = mysqli_fetch_assoc($result_categories_menu)) {
        // Chuẩn hóa key: chuyển thành chữ thường và loại bỏ khoảng trắng thừa
        $category_key = strtolower(trim($row_cat_menu['Category']));
        $categories_menu[$category_key] = $category_translations[$category_key] ?? ucfirst($category_key);
    }
}

// --- Lấy số lượng sản phẩm trong giỏ hàng cho header ---
$cart_item_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id_for_cart = $_SESSION['user_id'];
    $stmt_cart_count = $con->prepare("SELECT SUM(Quantity) as total_items FROM cart WHERE UserID = ?");
    if ($stmt_cart_count) {
        $stmt_cart_count->bind_param("i", $user_id_for_cart);
        $stmt_cart_count->execute();
        $result_cart_count = $stmt_cart_count->get_result();
        $row_cart_count = $result_cart_count->fetch_assoc();
        $cart_item_count = $row_cart_count['total_items'] ?? 0;
        $stmt_cart_count->close();
    }
}

// --- Lấy số lượng sản phẩm trong wishlist cho header ---
$wishlist_item_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id_for_wishlist = $_SESSION['user_id'];
    $stmt_wishlist_count = $con->prepare("SELECT COUNT(*) as total_items FROM wishlist WHERE UserID = ?");
    if ($stmt_wishlist_count) {
        $stmt_wishlist_count->bind_param("i", $user_id_for_wishlist);
        $stmt_wishlist_count->execute();
        $result_wishlist_count = $stmt_wishlist_count->get_result();
        $row_wishlist_count = $result_wishlist_count->fetch_assoc();
        $wishlist_item_count = $row_wishlist_count['total_items'] ?? 0;
        $stmt_wishlist_count->close();
    }

    // --- LẤY THÔNG BÁO CHO HEADER ---
    $notifications = [];
    $unread_notification_count = 0;
    if (isset($_SESSION['user_id'])) {
        $user_id_for_notif = $_SESSION['user_id'];
        // Lấy 5 thông báo gần nhất
        $stmt_notif = $con->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
        if ($stmt_notif) {
            $stmt_notif->bind_param("i", $user_id_for_notif);
            $stmt_notif->execute();
            $notifications = $stmt_notif->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt_notif->close();
        }
        // Đếm số thông báo chưa đọc
        $stmt_unread_count = $con->prepare("SELECT COUNT(*) as unread_count FROM notifications WHERE user_id = ? AND is_read = 0");
        if ($stmt_unread_count) {
            $stmt_unread_count->bind_param("i", $user_id_for_notif);
            $stmt_unread_count->execute();
            $unread_notification_count = $stmt_unread_count->get_result()->fetch_assoc()['unread_count'] ?? 0;
            $stmt_unread_count->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BookZ | Cửa hàng sách cao cấp</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="css/style.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Hiển thị thông báo từ PHP (nếu có) -->
    <?php echo $swal_script; ?>

</head>

<style>
    .search-form-hover {
        background: #fff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    /* Ô nhập liệu */
    .search-input {
        border: none;
        outline: none;
        background: #fff;
        width: 100%;
        /* Luôn chiếm đủ chiều rộng */
        padding: 8px 15px;
        opacity: 1;
        font-size: 1rem;
        color: #333;
    }

    /* Nút kính lúp */
    .search-button:hover {
        background: rgba(0, 0, 0, 0.05);
    }

    /* Thêm CSS cho navbar khi cuộn */
    .header-container.scrolled #mainNavbar {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
        border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease-in-out;
    }

    /* CSS cho hiệu ứng fade-in khi cuộn */
    .fade-in-element {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }

    .fade-in-element.is-visible {
        opacity: 1;
        transform: translateY(0);
    }

    /* CSS cho modal tìm kiếm */
    .search-modal .modal-content {
        background-color: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(12px);
        border: var(--glass-border);
        border-radius: 16px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
    }

    .search-modal .modal-header,
    .search-modal .modal-footer {
        border: none;
    }

    /* CSS cho gợi ý tìm kiếm (autocomplete) */
    #searchResultsContainer {
        position: relative;
        /* Vùng chứa tương đối cho danh sách gợi ý */
    }

    .autocomplete-suggestions {
        position: absolute;
        top: 0;
        /* Hiển thị ngay bên dưới form */
        left: 0;
        right: 0;
        background-color: rgba(255, 255, 255, 0.99);
        z-index: 1056;
        /* Hiển thị trên các thành phần khác của modal */
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(0, 0, 0, 0.06);
    }

    .autocomplete-suggestion {
        padding: 10px 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        text-decoration: none;
        color: #333;
    }

    .autocomplete-suggestion:hover {
        background-color: #f8f9fa;
    }

    .autocomplete-suggestion img {
        width: 40px;
        height: 60px;
        object-fit: cover;
        margin-right: 15px;
        border-radius: 4px;
    }

    .autocomplete-suggestion .info .title {
        font-weight: 600;
    }

    .autocomplete-suggestion .info .author {
        font-size: 0.9em;
        color: #6c757d;
    }

    /* Scroll Progress Bar */
    .header-progress-bar {
        position: fixed;
        /* Thay đổi từ absolute sang fixed để nó nằm trên cùng của viewport */
        top: 0;
        /* Di chuyển lên trên cùng */
        left: 0;
        height: 4px;
        /* Chiều cao của thanh tiến trình */
        background: var(--accent);
        /* Màu của thanh tiến trình */
        width: 0%;
        /* Chiều rộng ban đầu */
        z-index: 9999;
        /* Tăng z-index để đảm bảo nó luôn ở trên cùng */
        transition: width 0.05s linear;
        /* Hiệu ứng chuyển động mượt mà */
    }

    /* Tối ưu header cho di động */
    @media (max-width: 991.98px) {
        .navbar-collapse {
            margin-top: 15px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
    }

    @media (max-width: 767.98px) {
        .search-modal .modal-dialog {
            padding: 0 10px;
        }

        #searchInputModal {
            font-size: 1.1rem;
            /* Tăng kích thước font cho dễ nhập liệu */
            padding: 12px 20px;
        }
    }

    /* Tùy chỉnh vị trí menu thả xuống */
    @media (min-width: 992px) {

        /* Chỉ áp dụng cho màn hình lớn (desktop) */
        .navbar-nav .dropdown-menu {
            margin-top: 1.3rem;
            /* Tạo khoảng cách 1rem (khoảng 16px) từ menu xuống */
            border-top: 3px solid var(--accent);
            /* Thêm một đường viền màu nhấn để đẹp hơn */
        }

        /* --- Hiệu ứng hover cho menu chính --- */
        .navbar-nav .nav-link {
            position: relative;
            transition: color 0.3s ease;
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: 5px;
            /* Điều chỉnh vị trí của gạch chân */
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background-color: var(--accent, #f5a623);
            /* Sử dụng màu nhấn, có màu dự phòng */
            transition: width 0.3s ease-in-out;
        }

        .navbar-nav .nav-link:hover::after,
        .navbar-nav .nav-link.active::after {
            width: calc(100% - 1rem);
            /* Chiều rộng gạch chân bằng padding của nav-link */
        }

        /* --- Keyframes cho hiệu ứng lắc --- */
        @keyframes shake {
            0% {
                transform: rotate(0deg);
            }

            25% {
                transform: rotate(8deg);
            }

            50% {
                transform: rotate(-8deg);
            }

            75% {
                transform: rotate(8deg);
            }

            100% {
                transform: rotate(0deg);
            }
        }

        /* --- Hiệu ứng hover cho các icon hành động --- */
        .navbar-nav.flex-row .nav-item .btn,
        .navbar-nav.flex-row .nav-item .nav-link,
        .navbar-nav.flex-row .nav-item img {
            /* Giữ lại transition để có hiệu ứng mượt mà khi không hover nữa */
            transition: transform 0.3s ease;
        }

        .navbar-nav.flex-row .nav-item .btn:hover,
        .navbar-nav.flex-row .nav-item .nav-link:hover,
        .navbar-nav.flex-row .nav-item a:hover img {
            /* Áp dụng animation lắc khi hover */
            animation: shake 0.4s ease-in-out;
        }
    }

    /* Đổi màu chữ khi hover vào item trong dropdown thể loại */
    .navbar-nav .dropdown-menu .dropdown-item {
        transition: color 0.18s ease, background-color 0.18s ease;
        color: inherit;
    }

    .navbar-nav .dropdown-menu .dropdown-item:hover,
    .navbar-nav .dropdown-menu .dropdown-item:focus {
        color: var(--accent);
        background-color: rgba(0, 0, 0, 0.06);
    }
</style>

<body>
    <header class="header-container" id="headerContainer">
        <!-- Hàng 1: Logo, Search, User -->
        <nav class="navbar navbar-expand-lg" id="mainNavbar">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <i class="fas fa-book-open text-warning me-2"></i>
                    <span>BOOK<span style="color: var(--accent)">Z</span></span>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent"
                    aria-controls="navContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navContent">
                    <!-- Menu chính -->
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item"><a class="nav-link active" href="index.php">Trang chủ</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Thể
                                loại</a>
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
                        <li class="nav-item"><a class="nav-link" href="index.php#new">Sách mới</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php#bestseller">Bán chạy</a></li>
                        <li class="nav-item"><a class="nav-link" href="deals.php">Khuyến mãi</a></li>
                        <li class="nav-item"><a class="nav-link" href="author.php">Tác giả</a></li>
                        <li class="nav-item"><a class="nav-link" href="news.php">Tin tức</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.php">Liên hệ</a></li>
                    </ul>

                    <!-- User Actions -->
                    <ul class="navbar-nav ms-auto flex-row align-items-center">
                        <?php if (!isset($_SESSION['user'])): ?>
                            <li class="nav-item"><button class="btn btn-primary-glass btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#loginModal">Đăng nhập</button></li>
                        <?php else: ?>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                <li class="nav-item"><a href="admin.php"
                                        class="btn btn-warning btn-sm rounded-pill fw-bold shadow-sm px-3"><i
                                            class="fas fa-user-shield me-1"></i> Admin</a></li>
                                <!-- Icon tìm kiếm cho Admin -->
                                <li class="nav-item ms-2">
                                    <button class="btn btn-outline-dark rounded-circle border-0"
                                        style="width:40px; height:40px;" title="Tìm kiếm" data-bs-toggle="modal"
                                        data-bs-target="#searchModal">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </li>
                            <?php else: ?>
                                <li class="nav-item">
                                    <button class="btn btn-outline-dark rounded-circle border-0"
                                        style="width:40px; height:40px;" title="Tìm kiếm" data-bs-toggle="modal"
                                        data-bs-target="#searchModal">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <a href="wishlist.php"
                                        class="btn btn-outline-dark rounded-circle position-relative border-0"
                                        style="width:40px; height:40px;" title="Danh sách yêu thích">
                                        <i class="fas fa-heart"></i>
                                        <span id="header-wishlist-count"
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger <?php echo ($wishlist_item_count > 0) ? '' : 'd-none'; ?>"><?php echo $wishlist_item_count; ?></span>
                                    </a>
                                </li>
                                <li class="nav-item ms-1">
                                    <a href="cart.php" class="btn btn-outline-dark rounded-circle position-relative border-0"
                                        style="width:40px; height:40px;" title="Giỏ hàng">
                                        <i class="fas fa-shopping-bag"></i>
                                        <?php if ($cart_item_count > 0): ?>
                                            <span id="header-cart-count"
                                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?php echo $cart_item_count; ?></span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <!-- Notification Dropdown -->
                            <li class="nav-item dropdown ms-1">
                                <a id="notificationBell" class="nav-link p-0" href="#" role="button"
                                    data-bs-toggle="dropdown"
                                    style="width:40px; height:40px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-bell fs-5"></i>
                                    <?php if ($unread_notification_count > 0): ?>
                                        <span id="notification-badge"
                                            class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"
                                            style="margin-top: 8px; margin-left: -12px;">
                                            <span class="visually-hidden">Thông báo mới</span>
                                        </span>
                                    <?php endif; ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 glass-panel mt-2"
                                    style="min-width: 300px; max-width: 90vw;">
                                    <li class="px-3 py-2 fw-bold">Thông báo</li>
                                    <li>
                                        <hr class="dropdown-divider m-0">
                                    </li>
                                    <div id="notification-list">
                                        <?php if (empty($notifications)): ?>
                                            <li><span class="dropdown-item-text text-center text-muted py-3">Không có thông báo
                                                    nào.</span></li>
                                        <?php else: ?>
                                            <?php foreach ($notifications as $notif): ?>
                                                <li>
                                                    <a class="dropdown-item py-2 <?php echo $notif['is_read'] ? '' : 'bg-light'; ?>"
                                                        href="<?php echo htmlspecialchars($notif['link'] ?? '#'); ?>">
                                                        <div class="d-flex align-items-start">
                                                            <i
                                                                class="<?php echo htmlspecialchars($notif['icon']); ?> mt-1 me-2 text-primary"></i>
                                                            <div>
                                                                <small
                                                                    class="fw-bold"><?php echo htmlspecialchars($notif['title']); ?></small><br>
                                                                <small
                                                                    class="text-muted"><?php echo htmlspecialchars($notif['message']); ?></small>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                    <li>
                                        <hr class="dropdown-divider m-0">
                                    </li>
                                    <li><a class="dropdown-item text-center small py-2" href="#">Xem tất cả</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown ms-2">
                                <a class="nav-link dropdown-toggle p-0" href="#" role="button" data-bs-toggle="dropdown">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo $_SESSION['user']; ?>&background=random"
                                        class="rounded-circle" width="35">
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 glass-panel mt-2">
                                    <li><span class="dropdown-item-text text-muted small">Đăng nhập với tên<br><strong
                                                class="text-dark"><?php echo $_SESSION['user']; ?></strong></span></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="profile.php">
                                            <i class="fas fa-user-circle me-2 text-muted"></i> Tài khoản của tôi
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="order_tracking.php">
                                            <i class="fas fa-box-open me-2 text-muted"></i> Đơn mua
                                        </a>
                                    </li>
                                    <li><a class="dropdown-item text-danger" href="destroy.php"><i
                                                class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Hàng 2: Thanh tìm kiếm đã được chuyển vào Modal -->

        <!-- Thanh tiến trình cuộn trang -->
        <div class="header-progress-bar" id="headerProgressBar"></div>
    </header>

    <!-- ========================= Search Modal ========================= -->
    <div class="modal fade search-modal" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchModalLabel" style="font-family: 'Playfair Display', serif;">Tìm
                        kiếm sách</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="Result.php" method="POST" class="d-flex search-form-hover rounded-pill border"
                        id="modalSearchForm">
                        <input type="search" id="searchInputModal" name="keyword"
                            class="form-control border-0 bg-transparent" placeholder="Nhập tên sách, tác giả..."
                            autocomplete="off">
                        <button type="submit" class="btn btn-link text-dark px-3"><i class="fas fa-search"></i></button>
                    </form>
                    <!-- Vùng chứa để hiển thị kết quả gợi ý -->
                    <div id="searchResultsContainer" class="mt-2"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Lấy phần tử modal
            var searchModal = document.getElementById('searchModal');
            // Lấy phần tử input bên trong modal
            var searchInput = document.getElementById('searchInputModal');

            // Thêm sự kiện 'shown.bs.modal' cho modal
            searchModal.addEventListener('shown.bs.modal', function () {
                // Tự động focus vào ô input khi modal được hiển thị
                searchInput.focus();
            });

            // --- LOGIC CHO AUTOCOMPLETE SEARCH ---
            const searchInputModal = document.getElementById('searchInputModal');
            const resultsContainer = document.getElementById('searchResultsContainer');

            searchInputModal.addEventListener('keyup', function () {
                const keyword = this.value.trim();

                // Nếu từ khóa rỗng hoặc quá ngắn, xóa gợi ý và dừng lại
                if (keyword.length < 2) {
                    resultsContainer.innerHTML = '';
                    return;
                }

                // Gửi yêu cầu AJAX đến server
                fetch(`ajax_search.php?keyword=${encodeURIComponent(keyword)}`)
                    .then(response => response.json())
                    .then(data => {
                        // Xóa các gợi ý cũ
                        resultsContainer.innerHTML = '';

                        if (data.length > 0) {
                            // Tạo một list group để chứa các gợi ý
                            const suggestionsList = document.createElement('div');
                            suggestionsList.className = 'autocomplete-suggestions';

                            data.forEach(book => {
                                const bookLink = `description.php?ID=${book.PID}`;
                                const bookImg = `img/books/${book.PID}.jpg`;

                                // Tạo một thẻ a cho mỗi gợi ý
                                const suggestionItem = document.createElement('a');
                                suggestionItem.href = bookLink;
                                suggestionItem.className = 'autocomplete-suggestion';
                                suggestionItem.innerHTML = `
                                <img src="${bookImg}" alt="${book.Title}" onerror="this.src='https://placehold.co/80x120?text=N/A'">
                                <div class="info">
                                    <div class="title">${book.Title}</div>
                                    <div class="author">${book.Author}</div>
                                </div>
                            `;
                                suggestionsList.appendChild(suggestionItem);
                            });

                            resultsContainer.appendChild(suggestionsList);
                        }
                    })
                    .catch(error => {
                        console.error('Lỗi khi tìm kiếm:', error);
                        resultsContainer.innerHTML = ''; // Xóa gợi ý nếu có lỗi
                    });
            });

            // Đóng gợi ý khi click ra ngoài
            document.addEventListener('click', (e) => { if (!resultsContainer.contains(e.target)) resultsContainer.innerHTML = ''; });
        });

        // --- LOGIC CHO SCROLL PROGRESS BAR ---
        window.onscroll = function () {
            updateScrollProgress();
        };

        function updateScrollProgress() {
            const progressBar = document.getElementById("headerProgressBar");
            if (!progressBar) return;

            const scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (scrollTop / scrollHeight) * 100;
            progressBar.style.width = scrolled + "%";
        }
    </script>

</body>

</html>