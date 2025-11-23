<?php
session_start();
include "dbconnect.php";

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?Message=Please login to view your wishlist.");
    exit();
}

$user_id = $_SESSION['user_id'];
$swal_script = ""; // Biến chứa script thông báo

// 2. Xử lý Logic: THÊM SẢN PHẨM VÀO WISHLIST
if (isset($_GET['ID'])) {
    $product_id = $_GET['ID'];

    // Kiểm tra xem sản phẩm đã có trong wishlist chưa
    $check_stmt = $con->prepare("SELECT * FROM wishlist WHERE UserID = ? AND ProductID = ?");
    $check_stmt->bind_param("is", $user_id, $product_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows == 0) {
        // Nếu chưa có, thêm vào
        $insert_stmt = $con->prepare("INSERT INTO wishlist (UserID, ProductID) VALUES (?, ?)");
        $insert_stmt->bind_param("is", $user_id, $product_id);
        if ($insert_stmt->execute()) {
            // Thêm thành công, chuyển hướng để xóa param trên URL
            header("Location: wishlist.php?action=added");
            exit();
        }
    } else {
        // Đã có sẵn, chỉ cần chuyển hướng
        header("Location: wishlist.php");
        exit();
    }
}

// 3. Xử lý Logic: XÓA SẢN PHẨM KHỎI WISHLIST
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    $delete_stmt = $con->prepare("DELETE FROM wishlist WHERE UserID = ? AND ProductID = ?");
    $delete_stmt->bind_param("is", $user_id, $product_id);
    if ($delete_stmt->execute()) {
        header("Location: wishlist.php?action=removed");
        exit();
    }
}

// 4. Xử lý thông báo dựa trên 'action'
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'added') {
        $swal_script = "Swal.fire({icon: 'success', title: 'Added!', text: 'Book added to your wishlist.', timer: 2000, showConfirmButton: false});";
    }
    if ($_GET['action'] == 'removed') {
        $swal_script = "Swal.fire({icon: 'info', title: 'Removed', text: 'Book removed from your wishlist.', timer: 2000, showConfirmButton: false});";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Wishlist | BookZ Store</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #0f172a;
            --accent: #d4af37;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: 1px solid rgba(255, 255, 255, 0.5);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f0f4f8;
            min-height: 100vh;
            color: var(--primary);
        }

        .bg-blobs {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: radial-gradient(circle at 10% 20%, rgba(212, 175, 55, 0.1), transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(15, 23, 42, 0.1), transparent 40%);
        }

        .navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .book-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border: var(--glass-border);
            border-radius: 16px;
            padding: 15px;
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
        }

        .book-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.95);
        }

        .book-img {
            width: 100%;
            aspect-ratio: 2/3;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .book-title {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.1rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 2.6em;
            margin-bottom: 5px;
        }

        .price-tag {
            color: var(--primary);
            font-weight: 700;
            font-size: 1.1rem;
        }

        .card-footer-actions {
            margin-top: auto;
            padding-top: 15px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .empty-wishlist {
            padding: 80px 0;
            text-align: center;
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border: var(--glass-border);
            border-radius: 20px;
        }
    </style>
</head>

<body>
    <?php if ($swal_script) echo "<script>$swal_script</script>"; ?>

    <div class="bg-blobs"></div>

    <!-- ============== Navbar (Reused from index.php) ==============-->
àn    <header class="header-container fixed-top">
        <nav class="navbar navbar-expand-lg navbar-light" style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(0,0,0,0.05);">
            <div class="container">
                <a class="navbar-brand" href="index.php" style="font-family: 'Playfair Display', serif; font-weight: 700; font-size: 1.5rem;">
                    <i class="fas fa-book-open text-warning me-2"></i>
                    BOOK<span style="color: var(--accent)">Z</span>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent" aria-controls="navContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navContent">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Trang chủ</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Thể loại</a>
                            <ul class="dropdown-menu border-0 shadow-sm" style="background: rgba(255,255,255,0.9); backdrop-filter: blur(10px);">
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
                    </ul>

                    <ul class="navbar-nav ms-auto flex-row align-items-center">
                        <li class="nav-item me-2">
                            <form action="Result.php" method="POST" class="d-flex">
                                <input type="search" name="keyword" class="form-control form-control-sm rounded-pill border-0 bg-light" placeholder="Tìm kiếm...">
                            </form>
                        </li>

                        <?php if (!isset($_SESSION['user'])): ?>
                            <li class="nav-item"><a href="login.php" class="btn btn-sm btn-outline-dark rounded-pill">Sign In</a></li>
                        <?php else: ?>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                <li class="nav-item"><a href="admin.php" class="btn btn-warning btn-sm rounded-pill fw-bold shadow-sm px-3"><i class="fas fa-user-shield me-1"></i> Admin</a></li>
                            <?php else: ?>
                                <li class="nav-item"><a href="wishlist.php" class="btn btn-outline-dark rounded-circle border-0 active" style="width:40px; height:40px;" title="Wishlist"><i class="fas fa-heart"></i></a></li>
                                <li class="nav-item ms-1"><a href="cart.php" class="btn btn-outline-dark rounded-circle position-relative border-0" style="width:40px; height:40px;"><i class="fas fa-shopping-bag"></i></a></li>
                            <?php endif; ?>
                            <li class="nav-item dropdown ms-2">
                                <a class="nav-link dropdown-toggle p-0" href="#" role="button" data-bs-toggle="dropdown">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user']); ?>&background=random" class="rounded-circle" width="35">
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="background: rgba(255,255,255,0.9); backdrop-filter: blur(10px);">
                                    <li><span class="dropdown-item-text text-muted small">Signed in as<br><strong class="text-dark"><?php echo htmlspecialchars($_SESSION['user']); ?></strong></span></li>
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

    <!-- ============== Wishlist Content ==============-->
    <div class="container" style="margin-top: 100px; margin-bottom: 50px;">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold" style="font-family: 'Playfair Display', serif;">My Wishlist</h1>
                <p class="text-muted">Your collection of favorite books.</p>
            </div>
            <a href="index.php" class="btn btn-light rounded-pill"><i class="fas fa-arrow-left me-2"></i>Back to Shop</a>
        </div>

        <?php
        // Lấy dữ liệu wishlist
        $query = "SELECT p.* FROM products p JOIN wishlist w ON p.PID = w.ProductID WHERE w.UserID = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $wishlist_result = $stmt->get_result();
        ?>

        <div class="row g-4">
            <?php if ($wishlist_result->num_rows > 0) : ?>
                <?php while ($row = $wishlist_result->fetch_assoc()) :
                    $path = "img/books/" . $row['PID'] . ".jpg";
                    $link = "description.php?ID=" . $row["PID"];
                ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="book-card">
                            <a href="<?php echo $link; ?>">
                                <img src="<?php echo $path; ?>" class="book-img" alt="<?php echo htmlspecialchars($row['Title']); ?>" onerror="this.src='https://placehold.co/400x600?text=No+Image'">
                            </a>
                            <h5 class="book-title"><a href="<?php echo $link; ?>" class="text-decoration-none text-dark"><?php echo htmlspecialchars($row['Title']); ?></a></h5>
                            <p class="text-muted small mb-2"><i class="fas fa-pen-nib me-1"></i> <?php echo htmlspecialchars($row['Author']); ?></p>
                            <div class="price-tag mb-3"><?php echo number_format($row['Price']); ?> đ</div>

                            <div class="card-footer-actions d-flex gap-2">
                                <a href="cart.php?ID=<?php echo $row['PID']; ?>&quantity=1" class="btn btn-sm btn-primary flex-grow-1"><i class="fas fa-cart-plus"></i></a>
                                <a href="wishlist.php?remove=<?php echo $row['PID']; ?>" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt"></i></a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <!-- Empty State -->
                <div class="col-12">
                    <div class="empty-wishlist">
                        <div style="font-size: 4rem; color: #cbd5e1;"><i class="far fa-heart"></i></div>
                        <h3 class="mt-3 text-muted">Your Wishlist is Empty</h3>
                        <p class="text-muted">Add your favourite books to your wishlist and they will show up here.</p>
                        <a href="index.php" class="btn btn-primary rounded-pill px-4 mt-3" style="background: var(--primary);">Browse Books</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>