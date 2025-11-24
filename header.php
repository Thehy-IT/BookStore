<?php
session_start();

include "dbconnect.php";

$swal_script = "";

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
                    $swal_script = set_swal('error', 'Đăng nhập thất bại', 'Sai mật khẩu.');
                }
            } else {
                $swal_script = set_swal('error', 'Đăng nhập thất bại', 'Tài khoản không tồn tại.');
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
            $swal_script = set_swal('success', 'Đăng ký thành công!', 'Tài khoản đã tạo thành công. Vui lòng đăng nhập.');
        } else {
            $swal_script = set_swal('warning', 'Đăng ký thất bại', 'Tên đăng nhập đã tồn tại.');
        }
    }
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
    'self-help' => 'Phát triển bản thân', // Giữ lại các mục đã có
    'fiction' => 'Tiểu thuyết', // Giữ lại các mục đã có
    'thriller' => 'Kinh dị & Giật gân', // Giữ lại các mục đã có
    'romance' => 'Lãng mạn', // Giữ lại các mục đã có
    'fantasy' => 'Giả tưởng', // Giữ lại các mục đã có
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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="css/style.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>
    <?php echo $swal_script; ?>

    <!-- ============== Preloader ==============-->
    <div id="preloader">
        <div class="spinner"></div>
    </div>
    <!-- Container cho tuyết rơi (sẽ được tạo bằng JS) -->
    <div id="snow-container"></div>

    <!-- Background Elements -->
    <div class="bg-blobs"></div>

    <!-- ============== Navbar ==============-->
    <header class="header-container">
        <!-- Hàng 1: Logo, Search, User -->
        <nav class="navbar navbar-expand-lg" id="mainNavbar">
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
                        <li class="nav-item"><a class="nav-link" href="index.php#new">Sách mới</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php#bestseller">Bán chạy</a></li>
                        <li class="nav-item"><a class="nav-link" href="deals.php">Khuyến mãi</a></li>
                        <li class="nav-item"><a class="nav-link" href="author.php">Tác giả</a></li>
                        <li class="nav-item"><a class="nav-link" href="news.php">Tin tức</a></li>
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
                            <li class="nav-item"><button class="btn btn-primary-glass btn-sm" data-bs-toggle="modal" data-bs-target="#loginModal">Đăng nhập</button></li>
                        <?php else: ?>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                <li class="nav-item"><a href="admin.php" class="btn btn-warning btn-sm rounded-pill fw-bold shadow-sm px-3"><i class="fas fa-user-shield me-1"></i> Admin</a></li>
                            <?php else: ?>
                                <li class="nav-item">
                                    <a href="wishlist.php" class="btn btn-outline-dark rounded-circle position-relative border-0" style="width:40px; height:40px;" title="Danh sách yêu thích">
                                        <i class="fas fa-heart"></i>
                                        <span id="header-wishlist-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger <?php echo ($wishlist_item_count > 0) ? '' : 'd-none'; ?>"><?php echo $wishlist_item_count; ?></span>
                                    </a>
                                </li>
                                <li class="nav-item ms-1">
                                    <a href="cart.php" class="btn btn-outline-dark rounded-circle position-relative border-0" style="width:40px; height:40px;" title="Giỏ hàng">
                                        <i class="fas fa-shopping-bag"></i>
                                        <?php if ($cart_item_count > 0): ?>
                                            <span id="header-cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?php echo $cart_item_count; ?></span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <!-- Notification Dropdown -->
                            <li class="nav-item dropdown ms-1">
                                <a class="nav-link p-0" href="#" role="button" data-bs-toggle="dropdown" style="width:40px; height:40px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-bell fs-5"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="margin-top: 8px; margin-left: -12px;">
                                        <span class="visually-hidden">Thông báo mới</span>
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 glass-panel mt-2" style="width: 300px;">
                                    <li class="px-3 py-2 fw-bold">Thông báo</li>
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
                                    <li><span class="dropdown-item-text text-muted small">Đăng nhập với tên<br><strong class="text-dark"><?php echo $_SESSION['user']; ?></strong></span></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item text-danger" href="destroy.php">Đăng xuất</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>