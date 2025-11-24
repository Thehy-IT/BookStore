<?php
include 'header.php'; // Bao gồm header để có session, kết nối DB và layout

// Xử lý từ khóa tìm kiếm
$keyword_raw = isset($_POST['keyword']) ? $_POST['keyword'] : '';
$keyword = "%{$keyword_raw}%";
?>
<style>
    /* Các style này có thể được chuyển vào file CSS chung nếu muốn */
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
        aspect-ratio: 2/3;
        object-fit: cover;
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

<!-- ============== Search Results Section ==============-->
<div class="container" style="padding-top: 100px; padding-bottom: 50px;">

    <?php
    // Query DB
    $query = "SELECT * FROM products WHERE PID LIKE ? OR Title LIKE ? OR Author LIKE ? OR Publisher LIKE ? OR Category LIKE ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("sssss", $keyword, $keyword, $keyword, $keyword, $keyword);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->num_rows;
    ?>

    <!-- NEW: Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4" style="background-color: var(--glass-bg); padding: 15px; border-radius: 12px; backdrop-filter: blur(10px); border: var(--glass-border);">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tìm kiếm</li>
        </ol>
    </nav>

    <!-- Header Result -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-muted mb-0">Kết quả tìm kiếm cho: "<strong class="text-dark"><?php echo htmlspecialchars($keyword_raw); ?></strong>"</p>
            <h2 class="fw-bold" style="font-family: 'Playfair Display', serif;">Tìm thấy <span style="color: var(--accent)"><?php echo $count; ?></span> cuốn sách</h2>
        </div>
        <a href="index.php" class="btn btn-light rounded-pill"><i class="fas fa-arrow-left me-2"></i>Về trang chủ</a>
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
                            <img src="<?php echo $path; ?>" class="book-img" alt="<?php echo $row['Title']; ?>" onerror="this.src='https://placehold.co/400x600?text=No+Image'">

                            <!-- Thông tin -->
                            <div class="mt-2">
                                <h5 class="book-title"><?php echo $row['Title']; ?></h5>
                                <p class="text-muted small mb-2"><i class="fas fa-pen-nib me-1"></i> <?php echo $row['Author']; ?></p>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="price-tag">
                                        <?php echo number_format($row['Price']); ?> đ
                                        <?php if ($row['MRP'] > $row['Price']): ?>
                                            <span class="old-price"><?php echo number_format($row['MRP']); ?> đ</span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="text-warning small"><i class="fas fa-star"></i> 4.7</span>
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
                <h3 class="mt-3 text-muted">Không tìm thấy sách phù hợp.</h3>
                <p class="text-muted">Hãy thử kiểm tra lại chính tả hoặc dùng từ khóa khác.</p>
                <a href="Product.php" class="btn btn-primary-glass mt-3">Xem tất cả sách</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
include 'footer.php'; // Bao gồm footer
?>