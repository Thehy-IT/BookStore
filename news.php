<?php
include 'header.php';

// --- Lấy ID bài viết từ URL ---
$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 1; // Mặc định là bài 1 nếu không có ID

// --- Lấy bài viết chính từ CSDL ---
$stmt = $con->prepare("SELECT * FROM news WHERE id = ?");
$stmt->bind_param("i", $article_id);
$stmt->execute();
$result = $stmt->get_result();
$article = $result->fetch_assoc();

// Kiểm tra xem bài viết có tồn tại không
if (!$article) {
    // Nếu không tồn tại, chuyển hướng về trang chủ hoặc hiển thị lỗi
    $article = [
        'title' => 'Bài viết không tồn tại',
        'created_at' => '',
        'author' => '',
        'image_url' => 'https://images.unsplash.com/photo-1584824486509-112e4181ff6b?q=80&w=2070&auto=format&fit=crop',
        'content' => '<p class="lead text-center">Bài viết bạn đang tìm kiếm không tồn tại hoặc đã bị xóa. Vui lòng quay lại trang chủ.</p><div class="text-center"><a href="index.php" class="btn btn-primary-glass">Về Trang Chủ</a></div>'
    ];
}

// --- Lấy các bài viết khác cho sidebar ---
$other_articles = [];
$other_stmt = $con->prepare("SELECT id, title, image_url, created_at FROM news WHERE id != ? ORDER BY created_at DESC LIMIT 3");
$other_stmt->bind_param("i", $article_id);
$other_stmt->execute();
$other_result = $other_stmt->get_result();
while ($row = $other_result->fetch_assoc()) {
    $other_articles[] = $row;
}

// Hàm định dạng ngày tháng
function formatDate($dateString)
{
    if (empty($dateString)) return '';
    $date = new DateTime($dateString);
    return "Ngày " . $date->format('d') . " tháng " . $date->format('m') . ", " . $date->format('Y');
}

?>

<style>
    .news-detail-header {
        position: relative;
        height: 50vh;
        min-height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
        padding: 20px;
    }

    .news-detail-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.4));
        z-index: 1;
    }

    .news-detail-header-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 0;
    }

    .news-detail-title {
        font-family: 'Playfair Display', serif;
        font-size: 3.5rem;
        font-weight: 700;
        text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);
    }

    .news-meta {
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .news-content {
        line-height: 1.8;
        font-size: 1.1rem;
    }

    .news-content img {
        max-width: 100%;
        height: auto;
        border-radius: 15px;
        margin: 20px 0;
    }

    .sidebar-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 15px;
        padding: 20px;
        position: sticky;
        top: 120px;
    }

    .sidebar-news-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .sidebar-news-item img {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 10px;
        margin-right: 15px;
    }

    .sidebar-news-item h6 {
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 2px;
    }
</style>

<!-- ============== News Detail Header ==============-->
<div class="news-detail-header">
    <img src="<?php echo htmlspecialchars($article['image_url']); ?>" class="news-detail-header-bg" alt="Background">
    <div class="container position-relative z-2">
        <h1 class="news-detail-title"><?php echo htmlspecialchars($article['title']); ?></h1>
        <div class="news-meta text-white-50">
            <span><i class="fas fa-calendar-alt me-2"></i><?php echo formatDate($article['created_at']); ?></span>
            <span class="mx-2">|</span>
            <span><i class="fas fa-user-edit me-2"></i><?php echo htmlspecialchars($article['author']); ?></span>
        </div>
    </div>
</div>

<!-- ============== News Content ==============-->
<div class="container my-5" style="padding-top: 40px;">
    <!-- NEW: Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4" style="background-color: var(--glass-bg); padding: 15px; border-radius: 12px; backdrop-filter: blur(10px); border: var(--glass-border);">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="news.php">Tin tức</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($article['title']); ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="news-content">
                <?php echo $article['content']; // In ra nội dung HTML 
                ?>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="sidebar-card">
                <h5 class="fw-bold mb-4">Bài viết khác</h5>
                <?php
                // Hiển thị các bài viết khác (trừ bài hiện tại)
                foreach ($other_articles as $sidebar_article) {
                    echo '
                        <a href="news.php?id=' . $sidebar_article['id'] . '" class="text-decoration-none text-dark">
                            <div class="sidebar-news-item">
                                <img src="' . htmlspecialchars($sidebar_article['image_url']) . '" alt="' . htmlspecialchars($sidebar_article['title']) . '">
                                <div>
                                    <h6>' . htmlspecialchars($sidebar_article['title']) . '</h6>
                                    <small class="text-muted">' . formatDate($sidebar_article['created_at']) . '</small>
                                </div>
                            </div>
                        </a>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>