<?php
session_start();
include "dbconnect.php";
// CHẶN KHÔNG CHO USER THƯỜNG VÀO
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Lấy tất cả tin tức, sắp xếp mới nhất lên đầu
$news_res = mysqli_query($con, "SELECT id, title, author, created_at FROM news ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý Tin tức | BookZ</title>
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Dùng lại CSS từ admin.php -->
    <link rel="stylesheet" href="admin_style.css">
    <!-- TinyMCE Script -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
        :root {
            --primary: #0f172a;
            --accent: #d4af37;
            --glass-bg: rgba(255, 255, 255, 0.8);
        }

        body {
            background: #f0f4f8;
            font-family: sans-serif;
        }

        .bg-blobs {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: radial-gradient(circle at 90% 10%, rgba(212, 175, 55, 0.15), transparent 40%),
                radial-gradient(circle at 10% 90%, rgba(15, 23, 42, 0.15), transparent 40%);
        }

        .sidebar {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            height: 100vh;
            position: fixed;
            width: 250px;
            padding-top: 20px;
            border-right: 1px solid rgba(255, 255, 255, 0.5);
        }

        .main-content {
            margin-left: 250px;
            padding: 40px;
        }

        .nav-link {
            color: var(--primary);
            padding: 15px 20px;
            font-weight: 600;
        }

        .nav-link:hover {
            background: rgba(212, 175, 55, 0.1);
            color: var(--accent);
        }

        .nav-link.active {
            background: var(--primary);
            color: white;
        }

        .text-accent {
            color: var(--accent);
        }
    </style>
</head>

<body>
    <div class="bg-blobs"></div>

    <!-- Sidebar -->
    <div class="sidebar">
        <h3 class="text-center fw-bold mb-4">BOOK<span class="text-accent">Z</span> ADMIN</h3>
        <ul class="nav flex-column">
            <li class="nav-item"><a href="admin.php" class="nav-link"><i class="fas fa-tachometer-alt me-2"></i> Bảng điều khiển</a></li>
            <li class="nav-item"><a href="manage_products.php" class="nav-link"><i class="fas fa-book me-2"></i> Quản lý sản phẩm</a></li>
            <li class="nav-item"><a href="manage_news.php" class="nav-link active"><i class="fas fa-newspaper me-2"></i> Quản lý Tin tức</a></li>
            <li class="nav-item"><a href="manage_users.php" class="nav-link"><i class="fas fa-users me-2"></i> Quản lý người dùng</a></li>
              <li class="nav-item"><a href="manage_orders.php" class="nav-link"><i class="fas fa-shipping-fast me-2"></i> Quản lý đơn hàng</a></li>
            <li class="nav-item"><a href="index.php" class="nav-link"><i class="fas fa-home me-2"></i> Xem website</a></li>
            <li class="nav-item"><a href="destroy.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Quản lý Tin tức</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newsModal" onclick="prepareAddModal()">
                <i class="fas fa-plus me-2"></i> Thêm bài viết mới
            </button>
        </div>

        <!-- Hiển thị thông báo (nếu có) -->
        <?php if (isset($_SESSION['message'])) : ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        endif;
        ?>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Tiêu đề</th>
                        <th>Tác giả</th>
                        <th>Ngày tạo</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($news_res)) : ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['id']); ?></strong></td>
                            <td class="text-truncate" style="max-width: 300px;"><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['author']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" onclick='prepareEditModal(<?php echo json_encode($row); ?>)' data-bs-toggle="modal" data-bs-target="#newsModal">
                                    <i class="fas fa-edit me-1"></i> Sửa
                                </button>
                                <a href="news_action.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này?')">
                                    <i class="fas fa-trash me-1"></i> Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- News Modal (Add/Edit) -->
    <div class="modal fade" id="newsModal" tabindex="-1" aria-labelledby="newsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="news_action.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newsModalLabel">Thêm bài viết mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" id="formAction" value="add">
                        <input type="hidden" name="id" id="formId">

                        <div class="mb-3">
                            <label for="title" class="form-label">Tiêu đề</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="author" class="form-label">Tác giả</label>
                            <input type="text" class="form-control" id="author" name="author" required>
                        </div>
                        <div class="mb-3">
                            <label for="image_url" class="form-label">URL Hình ảnh</label>
                            <input type="url" class="form-control" id="image_url" name="image_url" placeholder="https://example.com/image.jpg">
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Nội dung</label>
                            <textarea class="form-control" id="content" name="content" rows="10" placeholder="Sử dụng thẻ HTML để định dạng..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Khởi tạo TinyMCE
        tinymce.init({
            selector: 'textarea#content',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
            height: 400, // Chiều cao của trình soạn thảo
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save(); // Đảm bảo nội dung được cập nhật vào textarea gốc khi có thay đổi
                });
            }
        });

        const modalTitle = document.getElementById('newsModalLabel');
        const formAction = document.getElementById('formAction');
        const formId = document.getElementById('formId');
        const formTitle = document.getElementById('title');
        const formAuthor = document.getElementById('author');
        const formImageUrl = document.getElementById('image_url');
        const formContent = document.getElementById('content');

        function prepareAddModal() {
            document.querySelector('#newsModal form').reset();
            modalTitle.textContent = 'Thêm bài viết mới';
            formAction.value = 'add';
            formId.value = '';
            if (tinymce.get('content')) {
                tinymce.get('content').setContent(''); // Xóa nội dung trong TinyMCE
            }
        }

        function prepareEditModal(newsItem) {
            document.querySelector('#newsModal form').reset();
            modalTitle.textContent = 'Chỉnh sửa bài viết';
            formAction.value = 'edit';
            formId.value = newsItem.id; // ID đã có sẵn từ `json_encode`

            // Sử dụng Fetch API để lấy dữ liệu chi tiết của bài viết
            fetch(`get_news_details.php?id=${newsItem.id}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        formTitle.value = data.title;
                        formAuthor.value = data.author;
                        formImageUrl.value = data.image_url;
                        // Cập nhật nội dung cho TinyMCE
                        if (tinymce.get('content')) {
                            tinymce.get('content').setContent(data.content || '');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching news details:', error);
                    alert('Không thể tải chi tiết bài viết. Vui lòng thử lại.');
                });
        }
    </script>
</body>

</html>