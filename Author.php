<?php
include 'header.php';

// --- Lấy tên tác giả từ URL ---
$author_name = isset($_GET['value']) ? trim($_GET['value']) : '';

if (empty($author_name)) {
    // --- Nếu không có tên tác giả, hiển thị tất cả tác giả ---
    $all_authors = [];
    $result_all_authors = $con->query("SELECT name, image_url FROM authors ORDER BY name ASC");
    if ($result_all_authors && $result_all_authors->num_rows > 0) {
        while ($row = $result_all_authors->fetch_assoc()) {
            $all_authors[] = $row;
        }
    }
    // Đặt tiêu đề trang
    $page_title = "Tất cả tác giả";
} else {
    // --- Xử lý cho một tác giả cụ thể ---
    $page_title = "Tác giả: " . htmlspecialchars($author_name);
    // --- Truy vấn thông tin tác giả ---
    $author_info = null;
    $stmt_author = $con->prepare("SELECT name, biography, image_url FROM authors WHERE name = ?");
    $stmt_author->bind_param("s", $author_name);
    $stmt_author->execute();
    $result_author = $stmt_author->get_result();
    if ($result_author && $result_author->num_rows > 0) {
        $author_info = $result_author->fetch_assoc();
    }

    // --- Truy vấn sách của tác giả ---
    $books = [];
    $stmt_books = $con->prepare("SELECT PID, Title, Price, MRP, Category FROM products WHERE Author = ? ORDER BY Title ASC");
    $stmt_books->bind_param("s", $author_name);
    $stmt_books->execute();
    $result_books = $stmt_books->get_result();
    if ($result_books && $result_books->num_rows > 0) {
        while ($row = $result_books->fetch_assoc()) {
            $books[] = $row;
        }
    }

    // Nếu không có thông tin tác giả và không có sách nào, hiển thị lỗi
    if (!$author_info && empty($books)) {
        echo "<div class='container text-center py-5 vh-100 d-flex flex-column justify-content-center align-items-center'>
                <div class='text-muted mb-3' style='font-size: 4rem;'><i class='fas fa-question-circle'></i></div>
                <h3>Không tìm thấy thông tin cho tác giả này.</h3>
                <p class='text-muted'>Có thể tác giả bạn tìm kiếm không tồn tại hoặc chưa có sách trong cửa hàng.</p>
                <a href='author.php' class='btn btn-primary-glass mt-3'>Xem tất cả tác giả</a>
              </div>";
        include 'footer.php';
        exit;
    }

    // Xử lý ảnh đại diện: ưu tiên ảnh từ DB, nếu không có thì dùng ảnh mặc định
    $avatar_url = !empty($author_info['image_url'])
        ? htmlspecialchars($author_info['image_url'])
        : "https://ui-avatars.com/api/?name=" . urlencode($author_name) . "&background=d2af37&color=fff&size=150&font-size=0.33&bold=true";
}
?>

<style>
    .author-header {
        margin-top: 80px;
        padding: 50px 0;
        background: var(--glass-bg);
        border-bottom: var(--glass-border);
    }

    .author-avatar-lg {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid white;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .author-title {
        font-family: 'Playfair Display', serif;
        font-size: 3rem;
        font-weight: 700;
        color: var(--primary);
    }

    .author-bio {
        font-size: 1.1rem;
        color: #475569;
        line-height: 1.7;
    }

    .book-count-badge {
        display: inline-block;
        padding: 8px 15px;
        background-color: rgba(212, 175, 55, 0.1);
        color: var(--accent);
        border-radius: 50px;
        font-weight: 600;
    }

    /* Sử dụng lại style từ product_card.css hoặc định nghĩa lại nếu cần */
    .product-card {
        transition: all 0.3s ease;
        border-radius: 15px;
        overflow: hidden;
        background: white;
        border: 1px solid #e2e8f0;
    }

    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
    }

    .product-img img {
        height: 250px;
        object-fit: cover;
    }

    .product-title {
        font-weight: 600;
        color: #1e293b;
        font-size: 1rem;
    }

    .product-price {
        color: var(--primary);
        font-weight: 700;
    }

    .product-mrp {
        text-decoration: line-through;
        font-size: 0.9rem;
    }
</style>

<div class="container" style="padding-top: 100px;">
    <!-- NEW: Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4" style="background-color: var(--glass-bg); padding: 15px; border-radius: 12px; backdrop-filter: blur(10px); border: var(--glass-border);">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="author.php">Tác giả</a></li>
            <?php if (!empty($author_name)) : ?>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($author_name); ?></li>
            <?php endif; ?>
        </ol>
    </nav>
    <?php if (empty($author_name)) : ?>
        <!-- Hiển thị danh sách tất cả tác giả -->
        <div class="py-5" style="margin-top: 80px;">
            <h1 class="author-title text-center mb-5">Tất Cả Tác Giả</h1>
            <?php if (!empty($all_authors)) : ?>
                <div class="row g-4">
                    <?php foreach ($all_authors as $author) : ?>
                        <?php
                        $author_avatar = !empty($author['image_url'])
                            ? htmlspecialchars($author['image_url'])
                            : "https://ui-avatars.com/api/?name=" . urlencode($author['name']) . "&background=d2af37&color=fff&size=150&font-size=0.33&bold=true";
                        ?>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="card product-card h-100 text-center">
                                <a href="author.php?value=<?php echo urlencode($author['name']); ?>" class="text-decoration-none">
                                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                        <img src="<?php echo $author_avatar; ?>" alt="Ảnh của <?php echo htmlspecialchars($author['name']); ?>" class="author-avatar-lg mb-3">
                                        <h6 class="product-title text-dark"><?php echo htmlspecialchars($author['name']); ?></h6>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p class="text-center text-muted">Chưa có tác giả nào trong cửa hàng.</p>
            <?php endif; ?>
        </div>
    <?php else : ?>
        <!-- Hiển thị chi tiết một tác giả -->
        <!-- Author Header -->
        <div class="author-header">
            <div class="row align-items-center">
                <div class="col-md-3 text-center">
                    <img src="<?php echo $avatar_url; ?>" alt="Ảnh của <?php echo htmlspecialchars($author_name); ?>" class="author-avatar-lg">
                </div>
                <div class="col-md-9">
                    <h5 class="text-muted text-uppercase letter-spacing-2 mb-2">Tác giả</h5>
                    <h1 class="author-title mb-3"><?php echo htmlspecialchars($author_name); ?></h1>
                    <?php if (!empty($author_info['biography'])) : ?>
                        <p class="author-bio"><?php echo htmlspecialchars($author_info['biography']); ?></p>
                    <?php else : ?>
                        <p class="author-bio fst-italic text-muted">Chưa có thông tin tiểu sử cho tác giả này.</p>
                    <?php endif; ?>
                    <div class="book-count-badge mt-3">
                        <i class="fas fa-book me-2"></i>
                        Tìm thấy <?php echo count($books); ?> tác phẩm trong cửa hàng
                    </div>
                </div>
            </div>
        </div>

        <!-- Books by Author -->
        <div class="py-5">
            <h2 class="mb-4">Các tác phẩm của <?php echo htmlspecialchars($author_name); ?></h2>
            <?php if (!empty($books)) : ?>
                <div class="row g-4">
                    <?php foreach ($books as $book) : ?>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="card product-card h-100">
                                <a href="product.php?pid=<?php echo $book['PID']; ?>">
                                    <div class="product-img">
                                        <img src="img/books/<?php echo $book['PID']; ?>.jpg" class="card-img-top" alt="<?php echo htmlspecialchars($book['Title']); ?>">
                                    </div>
                                </a>
                                <div class="card-body d-flex flex-column">
                                    <h6 class="product-title mb-2">
                                        <a href="product.php?pid=<?php echo $book['PID']; ?>" class="text-decoration-none text-dark"><?php echo htmlspecialchars($book['Title']); ?></a>
                                    </h6>
                                    <div class="mt-auto">
                                        <p class="text-muted small mb-1"><?php echo htmlspecialchars($book['Category']); ?></p>
                                        <div class="d-flex align-items-center">
                                            <p class="product-price mb-0"><?php echo number_format($book['Price']); ?>đ</p>
                                            <p class="product-mrp text-muted ms-2 mb-0"><?php echo number_format($book['MRP']); ?>đ</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p class="text-center text-muted">Chưa có sách nào của tác giả này được thêm vào cửa hàng.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php
include 'footer.php';
?>