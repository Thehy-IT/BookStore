<?php
session_start();
include "dbconnect.php";

// Kiểm tra đăng nhập (Giữ nguyên logic của bạn)
if (!isset($_SESSION['user'])) {
    header("location: index.php?Message=Login To Continue");
    exit();
}

// Xử lý Logic Danh mục & Sắp xếp
$category = "";
if (isset($_GET['value'])) {
    $category = $_GET['value'];
    $_SESSION['category'] = $category; // Cập nhật session
} elseif (isset($_SESSION['category'])) {
    $category = $_SESSION['category'];
} else {
    $category = "All Books"; // Mặc định nếu không có danh mục
}

// Xử lý Logic Sắp xếp
$sort_option = isset($_POST['sort']) ? $_POST['sort'] : 'default';
$sql_sort = "";

switch ($sort_option) {
    case 'price_asc':   $sql_sort = "ORDER BY Price ASC"; break;
    case 'price_desc':  $sql_sort = "ORDER BY Price DESC"; break;
    case 'discount_asc':$sql_sort = "ORDER BY Discount ASC"; break;
    case 'discount_desc':$sql_sort = "ORDER BY Discount DESC"; break;
    default:            $sql_sort = ""; break; // Mặc định
}

// Truy vấn CSDL (Prepared Statement an toàn)
$query = "SELECT * FROM products WHERE Category = ? " . $sql_sort;
$stmt = $con->prepare($query);
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($category); ?> | BookZ Store</title>

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

        /* --- Background Blobs --- */
        .bg-blobs {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;
            background: radial-gradient(circle at 20% 20%, rgba(212, 175, 55, 0.15), transparent 40%),
                        radial-gradient(circle at 80% 80%, rgba(15, 23, 42, 0.1), transparent 40%);
        }

        /* --- Navbar Glass --- */
        .navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        /* --- Header Section --- */
        .page-header {
            margin-top: 100px;
            margin-bottom: 30px;
            text-align: center;
        }
        .category-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--primary);
        }

        /* --- Sort Bar Glass --- */
        .sort-bar {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 50px;
            padding: 10px 25px;
            border: var(--glass-border);
            display: inline-block;
            margin-bottom: 40px;
        }

        .form-select-glass {
            background-color: transparent;
            border: none;
            font-weight: 600;
            color: var(--primary);
            cursor: pointer;
            padding-right: 30px;
        }
        .form-select-glass:focus {
            box-shadow: none;
        }

        /* --- Product Card Glass --- */
        .book-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border: var(--glass-border);
            border-radius: 20px;
            padding: 20px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .book-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            background: rgba(255, 255, 255, 0.95);
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
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.5s;
        }
        
        .book-card:hover .book-img-wrapper img {
            transform: scale(1.08);
        }

        .discount-badge {
            position: absolute;
            top: 10px; left: 10px;
            background: rgba(239, 68, 68, 0.9);
            color: white;
            font-weight: 700;
            font-size: 0.8rem;
            padding: 5px 12px;
            border-radius: 20px;
            backdrop-filter: blur(4px);
            z-index: 2;
        }

        .book-title {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .price-current {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary);
        }
        .price-old {
            text-decoration: line-through;
            color: #94a3b8;
            font-size: 0.9rem;
            margin-left: 8px;
        }

        .btn-view {
            width: 100%;
            border-radius: 12px;
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px;
            font-weight: 600;
            margin-top: 10px;
            transition: 0.3s;
        }
        .btn-view:hover {
            background: var(--accent);
        }
    </style>
</head>

<body>

    <!-- Background Elements -->
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

                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a href="cart.php" class="btn btn-outline-dark rounded-pill px-4 me-2 position-relative">
                            <i class="fas fa-shopping-cart me-1"></i> Cart
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="destroy.php" class="btn btn-danger rounded-pill px-4">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ============== Main Content ==============-->
    <div class="container">
        
        <!-- Page Header -->
        <div class="page-header fade-in-up">
            <h2 class="category-title"><?php echo htmlspecialchars($category); ?></h2>
            <p class="text-muted">Explore our curated collection</p>

            <!-- Sort Bar -->
            <div class="sort-bar shadow-sm mt-3">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>?value=<?php echo urlencode($category); ?>" method="post" id="sortForm">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-sort-amount-down text-muted me-2"></i>
                        <label class="me-2 small text-muted text-uppercase fw-bold">Sort by:</label>
                        <select name="sort" class="form-select form-select-glass" onchange="document.getElementById('sortForm').submit()">
                            <option value="default" <?php if($sort_option == 'default') echo 'selected'; ?>>Recommended</option>
                            <option value="price_asc" <?php if($sort_option == 'price_asc') echo 'selected'; ?>>Price: Low to High</option>
                            <option value="price_desc" <?php if($sort_option == 'price_desc') echo 'selected'; ?>>Price: High to Low</option>
                            <option value="discount_desc" <?php if($sort_option == 'discount_desc') echo 'selected'; ?>>Best Discount</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="row g-4 pb-5">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                        $path = "img/books/" . $row['PID'] . ".jpg";
                        $link = "description.php?ID=" . $row["PID"];
                        // Fallback image logic trong trường hợp ảnh lỗi
                        $img_src = file_exists($path) ? $path : "https://placehold.co/400x600?text=No+Image";
                    ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="book-card h-100 d-flex flex-column">
                            <!-- Discount Badge -->
                            <?php if($row['Discount'] > 0): ?>
                                <div class="discount-badge">-<?php echo $row['Discount']; ?>%</div>
                            <?php endif; ?>

                            <!-- Image -->
                            <div class="book-img-wrapper">
                                <a href="<?php echo $link; ?>">
                                    <img src="<?php echo $path; ?>" alt="<?php echo htmlspecialchars($row['Title']); ?>" onerror="this.src='https://placehold.co/400x600/f1f5f9/334155?text=Book+Cover'">
                                </a>
                            </div>

                            <!-- Info -->
                            <div class="mt-auto">
                                <h5 class="book-title">
                                    <a href="<?php echo $link; ?>" class="text-decoration-none text-dark"><?php echo $row['Title']; ?></a>
                                </h5>
                                
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
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- Empty State -->
                <div class="col-12 text-center py-5">
                    <div class="mb-3 text-muted" style="font-size: 5rem;"><i class="fas fa-book-open"></i></div>
                    <h3>No books found in this category.</h3>
                    <p class="text-muted">Please check back later or explore other categories.</p>
                    <a href="index.php" class="btn btn-primary rounded-pill px-4 mt-3" style="background: var(--primary); border:none;">Back to Home</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>