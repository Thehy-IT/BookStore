<?php
include 'header.php';

// --- Dữ liệu tin tức giả (Trong thực tế, bạn sẽ lấy từ CSDL) ---
$news_articles = [
    '1' => [
        'title' => 'Top 10 Sách Nên Đọc Mùa Đông Này',
        'date' => '15 Tháng 10, 2025',
        'author' => 'Biên tập viên BookZ',
        'image' => 'https://images.unsplash.com/photo-1457369804613-52c61a468e7d?q=80&w=2070&auto=format&fit=crop',
        'content' => "
            <p class='lead'>Khi thời tiết trở lạnh, không có gì tuyệt vời hơn là cuộn mình trong chăn ấm cùng một cuốn sách hay. Dưới đây là danh sách 10 cuốn sách mà các biên tập viên của chúng tôi đề xuất cho mùa đông này.</p>
            <p>Từ những câu chuyện bí ẩn ấm cúng đến những cuốn tiểu thuyết lãng mạn cảm động, danh sách này có đủ mọi thể loại để làm hài lòng bất kỳ độc giả nào. Chúng tôi đã lựa chọn cẩn thận những tác phẩm không chỉ có nội dung hấp dẫn mà còn mang lại cảm giác ấm áp, phù hợp với không khí mùa đông.</p>
            <h5>1. Thư Viện Nửa Đêm - Matt Haig</h5>
            <p>Một câu chuyện đầy suy ngẫm về những lựa chọn trong cuộc sống và cơ hội thứ hai. Cuốn sách này sẽ khiến bạn phải suy nghĩ về con đường mình đã chọn và những khả năng vô tận của cuộc đời.</p>
            <h5>2. Bệnh Nhân Thầm Lặng - Alex Michaelides</h5>
            <p>Nếu bạn yêu thích thể loại giật gân, đây là một lựa chọn không thể bỏ qua. Một câu chuyện ly kỳ với những cú twist bất ngờ sẽ giữ bạn đọc đến tận trang cuối cùng.</p>
            <blockquote class='blockquote fst-italic my-4 p-3 bg-light border-start border-5 border-warning'>
                'Một cuốn sách hay trên giá sách là một người bạn, dù có quay lưng lại nhưng vẫn không bao giờ quay mặt đi.' - Khuyết danh
            </blockquote>
            <p>Ngoài ra, danh sách còn có những tác phẩm kinh điển, sách phát triển bản thân và cả những câu chuyện thiếu nhi ý nghĩa để bạn có thể đọc cùng gia đình. Hãy chuẩn bị một tách trà nóng và bắt đầu khám phá thế giới văn học mùa đông này!</p>
        "
    ],
    '2' => [
        'title' => 'Phỏng vấn độc quyền J.K. Rowling',
        'date' => '12 Tháng 10, 2025',
        'author' => 'Jane Doe',
        'image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?q=80&w=2128&auto=format&fit=crop',
        'content' => "
            <p class='lead'>Chúng tôi đã có cơ hội ngồi lại với tác giả huyền thoại J.K. Rowling để trò chuyện về quá trình sáng tạo đằng sau loạt phim Harry Potter và những dự định sắp tới của bà.</p>
            <p>Trong buổi phỏng vấn, J.K. Rowling đã chia sẻ những chi tiết thú vị về việc xây dựng thế giới phù thủy, từ nguồn cảm hứng ban đầu cho đến những khó khăn trong quá trình viết. Bà cũng tiết lộ một vài bí mật nhỏ về các nhân vật mà người hâm mộ chưa từng được biết đến.</p>
            <p>Khi được hỏi về dự án tiếp theo, bà mỉm cười bí ẩn và nói rằng mình luôn có những câu chuyện mới để kể. 'Thế giới luôn đầy ắp những điều kỳ diệu, bạn chỉ cần biết cách tìm kiếm chúng,' bà chia sẻ.</p>
        "
    ],
    '3' => [
        'title' => 'Sự Trỗi Dậy Của Thư Viện Số',
        'date' => '08 Tháng 10, 2025',
        'author' => 'John Smith',
        'image' => 'https://images.unsplash.com/photo-1519682337058-a94d519337bc?q=80&w=2070&auto=format&fit=crop',
        'content' => "
            <p class='lead'>Công nghệ đang định hình lại cách chúng ta tiếp cận và tiêu thụ văn học. Các thư viện số và sách điện tử (ebook) đang ngày càng trở nên phổ biến, mang lại sự tiện lợi chưa từng có cho độc giả.</p>
            <p>Với một thiết bị đọc sách nhỏ gọn, bạn có thể mang theo cả một thư viện hàng ngàn cuốn sách. Điều này không chỉ giúp tiết kiệm không gian mà còn cho phép bạn đọc sách ở bất cứ đâu, bất cứ lúc nào. Các nền tảng như Kindle, Kobo đã thay đổi hoàn toàn thói quen đọc của nhiều người.</p>
            <p>Tuy nhiên, sự trỗi dậy của sách điện tử cũng đặt ra những câu hỏi về tương lai của sách giấy truyền thống. Liệu chúng có biến mất? Hay cả hai sẽ cùng tồn tại, phục vụ những nhu cầu khác nhau của độc giả? Hãy cùng chúng tôi phân tích xu hướng này.</p>
        "
    ],
];

// --- Lấy ID bài viết từ URL ---
$article_id = isset($_GET['id']) ? $_GET['id'] : '1'; // Mặc định là bài 1 nếu không có ID

// Kiểm tra xem bài viết có tồn tại không
if (!array_key_exists($article_id, $news_articles)) {
    // Nếu không tồn tại, chuyển hướng về trang chủ hoặc hiển thị lỗi
    $article = [
        'title' => 'Bài viết không tồn tại',
        'date' => '',
        'author' => '',
        'image' => 'https://images.unsplash.com/photo-1584824486509-112e4181ff6b?q=80&w=2070&auto=format&fit=crop',
        'content' => '<p class="lead text-center">Bài viết bạn đang tìm kiếm không tồn tại hoặc đã bị xóa. Vui lòng quay lại trang chủ.</p><div class="text-center"><a href="index.php" class="btn btn-primary-glass">Về Trang Chủ</a></div>'
    ];
} else {
    $article = $news_articles[$article_id];
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
    <img src="<?php echo htmlspecialchars($article['image']); ?>" class="news-detail-header-bg" alt="Background">
    <div class="container position-relative z-2">
        <h1 class="news-detail-title"><?php echo htmlspecialchars($article['title']); ?></h1>
        <div class="news-meta text-white-50">
            <span><i class="fas fa-calendar-alt me-2"></i><?php echo htmlspecialchars($article['date']); ?></span>
            <span class="mx-2">|</span>
            <span><i class="fas fa-user-edit me-2"></i><?php echo htmlspecialchars($article['author']); ?></span>
        </div>
    </div>
</div>

<!-- ============== News Content ==============-->
<div class="container my-5">
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
                foreach ($news_articles as $id => $sidebar_article) {
                    if ($id != $article_id) {
                        echo '
                        <a href="news.php?id=' . $id . '" class="text-decoration-none text-dark">
                            <div class="sidebar-news-item">
                                <img src="' . htmlspecialchars($sidebar_article['image']) . '" alt="' . htmlspecialchars($sidebar_article['title']) . '">
                                <div>
                                    <h6>' . htmlspecialchars($sidebar_article['title']) . '</h6>
                                    <small class="text-muted">' . htmlspecialchars($sidebar_article['date']) . '</small>
                                </div>
                            </div>
                        </a>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>