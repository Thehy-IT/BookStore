<?php
session_start();
include "dbconnect.php";

// 1. Kiểm tra đăng nhập (Giữ nguyên yêu cầu của bạn)
if (!isset($_SESSION['user'])) {
    header("location: login.php"); // Chuyển hướng sang trang login mới đẹp hơn
    exit();
}

// 2. Xử lý Logic Tác giả & Sắp xếp
$author = "";
// Ưu tiên lấy từ GET (khi click link), nếu không thì lấy từ Session, nếu không nữa thì báo lỗi
if (isset($_GET['value'])) {
    $author = $_GET['value'];
    $_SESSION['author'] = $author; 
} elseif (isset($_SESSION['author'])) {
    $author = $_SESSION['author'];
} else {
    $author = "Unknown Author";
}

// 3. Xử lý Sắp xếp (Tối ưu hóa switch-case)
$sort_option = isset($_POST['sort']) ? $_POST['sort'] : 'default';
$sql_sort = "";

switch ($sort_option) {
    case 'price_asc':   $sql_sort = "ORDER BY Price ASC"; break;
    case 'price_desc':  $sql_sort = "ORDER BY Price DESC"; break;
    case 'discount_asc':$sql_sort = "ORDER BY Discount ASC"; break; // Sửa lại logic cũ (Low to High)
    case 'discount_desc':$sql_sort = "ORDER BY Discount DESC"; break;
    default:            $sql_sort = ""; break;
}

// 4. Truy vấn CSDL (Prepared Statement)
$query = "SELECT * FROM products WHERE Author = ? " . $sql_sort;
$stmt = $con->prepare($query);
$stmt->bind_param("s", $author);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($author); ?> | BookZ Store</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #0f172a;
            --accent: #d4af37;
            --glass-bg: rgba(255, 255, 255, 0.65);
            --glass-border: 1px solid rgba(255, 255, 255, 0.5);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f0f4f8;
            color: var(--primary);
            overflow-x: hidden;
        }

        /* --- Background Blobs (Nền động) --- */
        .bg-blobs {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;
            background: radial-gradient(circle at 80% 10%, rgba(212, 175, 55, 0.15), transparent 40%),
                        radial-gradient(circle at 10% 90%, rgba(15, 23, 42, 0.1), transparent 40%);
        }

        /* --- Navbar Glass --- */
        .navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        /* --- Header Section --- */
        .author-header {
            margin-top: 100px;
            margin-bottom: 40px;
            text-align: center;
            position: relative;
        }
        
        .author-avatar {
            width: 100px; height: 100px;
            border-radius: 50%;
            background: var(--accent);
            color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 20px;
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.3);
        }

        .author-title {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        /* --- Sort Bar Glass --- */
        .sort-bar {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 50px;
            padding: 8px 25px;
            border: var(--glass-border);
            display: inline-flex;
            align-items: center;
            margin-top: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .form-select-glass {
            background: transparent; border: none;
            font-weight: 600; color: var(--primary);
            cursor: pointer; padding-right: 30px;
        }
        .form-select-glass:focus { box-shadow: none; }

        /* --- Product Card Glass --- */
        .book-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border: var(--glass-border);
            border-radius: 20px;
            padding: 20px;
            transition: all 0.4s ease;
            height: 100%;
            display: flex; flex-direction: column;
        }

        .book-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-color: var(--accent);
        }

        .book-img-wrapper {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 15px;
            aspect-ratio: 2/3;
        }
        
        .book-img-wrapper img {
            width: 100%; height: 100%; object-fit: cover;
            transition: 0.5s;
        }
        .book-card:hover .book-img-wrapper img { transform: scale(1.08); }

        .discount-badge {
            position: absolute; top: 10px; left: 10px;
            background: #ef4444; color: white;
            font-weight: 700; font-size: 0.75rem;
            padding: 4px 10px; border-radius: 20px;
            z-index: 2;
        }

        .book-title {
            font-family: 'Playfair Display', serif;
            font-weight: 700; font-size: 1.15rem;
            margin-bottom: 5px;
            display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .price-current { font-weight: 700; font-size: 1.1rem; color: var(--primary); }
        .price-old { text-decoration: line-through; color: #94a3b8; font-size: 0.9rem; margin-left: 5px; }

        .btn-view {
            margin-top: auto; /* Đẩy nút xuống đáy */
            background: var(--primary); color: white;
            border: none; padding: 10px; border-radius: 10px;
            font-weight: 600; width: 100%; transition: 0.3s;
        }
        .btn-view:hover { background: var(--accent); }
    </style>
</head>

<body>

    <!-- Background -->
    <div class="bg-blobs"></div>

    <!-- ============== Navbar ==============-->
    <nav class="navbar navbar-expand-lg fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="index.php">
                <img src="img/logo.png" height="40" alt="Logo" class="me-2">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navContent">
                <form class="d-flex mx-auto my-3 my-lg-0" style="max-width: 400px; width: 100%;" action="Result.php" method="POST">
                    <div class="input-group">
                        <input class="form-control rounded-pill bg-light border-0 px-3" type="search" name="keyword" placeholder="Search books...">
                    </div>
                </form>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a href="cart.php" class="btn btn-outline-dark rounded-pill px-4 me-2"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                    <li class="nav-item"><a href="destroy.php" class="btn btn-danger rounded-pill px-4">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ============== Author Content ==============-->
    <div class="container">
        
        <!-- Header -->
        <div class="author-header fade-in-up">
            <!-- Icon đại diện cho tác giả -->
            <div class="author-avatar">
                <i class="fas fa-pen-nib"></i>
            </div>
            <h5 class="text-muted text-uppercase ls-2 mb-2">Author Collection</h5>
            <h1 class="author-title"><?php echo htmlspecialchars($author); ?></h1>

            <!-- Sort Dropdown -->
            <div class="sort-bar">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>?value=<?php echo urlencode($author); ?>" method="post" id="sortForm">
                    <i class="fas fa-sort-amount-down text-muted me-2"></i>
                    <label class="me-2 small text-uppercase fw-bold text-muted">Sort:</label>
                    <select name="sort" class="form-select form-select-glass" onchange="document.getElementById('sortForm').submit()">
                        <option value="default" <?php if($sort_option == 'default') echo 'selected'; ?>>Recommended</option>
                        <option value="price_asc" <?php if($sort_option == 'price_asc') echo 'selected'; ?>>Price: Low to High</option>
                        <option value="price_desc" <?php if($sort_option == 'price_desc') echo 'selected'; ?>>Price: High to Low</option>
                        <option value="discount_desc" <?php if($sort_option == 'discount_desc') echo 'selected'; ?>>Best Discount</option>
                    </select>
                </form>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="row g-4 pb-5">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): 
                    $path = "img/books/" . $row['PID'] . ".jpg";
                    $link = "description.php?ID=" . $row["PID"];
                ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="book-card">
                        <!-- Discount -->
                        <?php if($row['Discount'] > 0): ?>
                            <div class="discount-badge">-<?php echo $row['Discount']; ?>%</div>
                        <?php endif; ?>

                        <!-- Image -->
                        <div class="book-img-wrapper">
                            <a href="<?php echo $link; ?>">
                                <img src="<?php echo $path; ?>" alt="<?php echo htmlspecialchars($row['Title']); ?>" onerror="this.src='https://placehold.co/400x600?text=No+Image'">
                            </a>
                        </div>

                        <!-- Content -->
                        <h5 class="book-title" title="<?php echo htmlspecialchars($row['Title']); ?>">
                            <?php echo $row['Title']; ?>
                        </h5>
                        <p class="small text-muted mb-2"><i class="fas fa-book-open me-1"></i> <?php echo $row['Publisher']; ?></p>

                        <div class="d-flex align-items-center mb-3">
                            <span class="price-current"><?php echo number_format($row['Price']); ?> đ</span>
                            <?php if($row['MRP'] > $row['Price']): ?>
                                <span class="price-old"><?php echo number_format($row['MRP']); ?> đ</span>
                            <?php endif; ?>
                        </div>

                        <a href="<?php echo $link; ?>" class="btn btn-view">
                            View Details <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- Empty State -->
                <div class="col-12 text-center py-5">
                    <div class="text-muted mb-3" style="font-size: 4rem;"><i class="fas fa-feather-alt"></i></div>
                    <h3>No books found for this author.</h3>
                    <p class="text-muted">We are updating our collection. Please check back later.</p>
                    <a href="index.php" class="btn btn-primary rounded-pill px-4 mt-3">Back Home</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>