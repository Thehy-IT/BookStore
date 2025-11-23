<?php
session_start();
include "dbconnect.php"; // Đảm bảo file kết nối DB hoạt động

// Kiểm tra nếu chưa login thì chuyển hướng (Tuỳ bạn, thường tìm kiếm sách thì không cần login cũng được)
// if (!isset($_SESSION['user'])) {
//     header("location: index.php?Message=Login To Continue");
// }

// Xử lý từ khóa tìm kiếm
$keyword_raw = isset($_POST['keyword']) ? $_POST['keyword'] : '';
$keyword = "%{$keyword_raw}%";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Search Results | BookZ Store</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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

        /* --- Background Blobs --- */
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

        /* --- Navbar Glass --- */
        .navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* --- Book Card Glass --- */
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
        }

        .book-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.95);
            border-color: var(--accent);
        }

        .book-img {
            width: 100%;
            height: 280px;
            object-fit: contain;
            /* Đảm bảo ảnh sách không bị méo */
            border-radius: 8px;
            margin-bottom: 15px;
            filter: drop-shadow(0 5px 5px rgba(0, 0, 0, 0.1));
        }

        .discount-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #ef4444;
            color: white;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 8px;
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
        }

        .price-tag {
            color: var(--primary);
            font-weight: 700;
            font-size: 1.1rem;
        }

        .old-price {
            text-decoration: line-through;
            color: #94a3b8;
            font-size: 0.9rem;
            margin-left: 5px;
        }
    </style>
</head>

<body>

    <!-- Background -->
    <div class="bg-blobs"></div>

    <!-- ============== Navbar ==============-->
    <nav class="navbar navbar-expand-lg fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                BOOK<span style="color: var(--accent)">Z</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navContent">
                <!-- Search Form -->
                <form class="d-flex mx-auto my-2 my-lg-0" style="max-width: 500px; width: 100%;" action="Result.php" method="POST">
                    <div class="input-group">
                        <input class="form-control rounded-start-pill border-end-0 bg-light" type="search" name="keyword" value="<?php echo htmlspecialchars($keyword_raw); ?>" placeholder="Search books...">
                        <button class="btn btn-light border border-start-0 rounded-end-pill" type="submit">
                            <i class="fas fa-search text-muted"></i>
                        </button>
                    </div>
                </form>

                <ul class="navbar-nav ms-auto">
                    <?php if (!isset($_SESSION['user'])): ?>
                        <li class="nav-item"><a href="login.php" class="btn btn-outline-dark rounded-pill px-4 ms-2">Login</a></li>
                    <?php else: ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fw-bold" href="#" data-bs-toggle="dropdown">
                                Hello, <?php echo $_SESSION['user']; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                <li><a class="dropdown-item text-danger" href="destroy.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ============== Search Results Section ==============-->
    <div class="container" style="margin-top: 100px; margin-bottom: 50px;">

        <?php
        // Query DB
        $query = "SELECT * FROM products WHERE PID LIKE ? OR Title LIKE ? OR Author LIKE ? OR Publisher LIKE ? OR Category LIKE ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("sssss", $keyword, $keyword, $keyword, $keyword, $keyword);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->num_rows;
        ?>

        <!-- Header Result -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <p class="text-muted mb-0">Search Results for: "<strong class="text-dark"><?php echo htmlspecialchars($keyword_raw); ?></strong>"</p>
                <h2 class="fw-bold">Found <span style="color: var(--accent)"><?php echo $count; ?></span> Books</h2>
            </div>
            <a href="index.php" class="btn btn-light rounded-pill"><i class="fas fa-arrow-left me-2"></i>Back Home</a>
        </div>

        <!-- Grid Books -->
        <div class="row g-4">
            <?php if ($count > 0): ?>
                <?php while ($row = $result->fetch_assoc()):
                    // Xử lý đường dẫn ảnh & link
                    $path = "img/books/" . $row['PID'] . ".jpg";
                    // Nếu ảnh lỗi thì dùng ảnh placeholder (tuỳ chọn)
                    $link = "description.php?ID=" . $row["PID"];
                ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="<?php echo $link; ?>" class="text-decoration-none text-dark">
                            <div class="book-card">
                                <!-- Badge giảm giá -->
                                <?php if ($row['Discount'] > 0): ?>
                                    <div class="discount-badge">-<?php echo $row['Discount']; ?>%</div>
                                <?php endif; ?>

                                <!-- Ảnh -->
                                <img src="<?php echo $path; ?>" class="book-img img-fluid" alt="<?php echo $row['Title']; ?>" onerror="this.src='https://placehold.co/300x450?text=No+Image'">

                                <!-- Thông tin -->
                                <div class="mt-2">
                                    <h5 class="book-title"><?php echo $row['Title']; ?></h5>
                                    <p class="text-muted small mb-2"><i class="fas fa-pen-nib me-1"></i> <?php echo $row['Author']; ?></p>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="price-tag">
                                            <?php echo $row['Price']; ?> đ
                                            <?php if ($row['MRP'] > $row['Price']): ?>
                                                <span class="old-price"><?php echo $row['MRP']; ?> đ</span>
                                            <?php endif; ?>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary rounded-circle"><i class="fas fa-shopping-bag"></i></button>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- Empty State (Khi không tìm thấy sách) -->
                <div class="col-12 text-center py-5">
                    <div style="font-size: 5rem; color: #cbd5e1;"><i class="fas fa-search"></i></div>
                    <h3 class="mt-3 text-muted">No books found matching your search.</h3>
                    <p class="text-muted">Try checking your spelling or use different keywords.</p>
                    <a href="index.php" class="btn btn-primary rounded-pill px-4 mt-3" style="background: var(--primary);">Browse All Books</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>