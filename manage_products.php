<?php
session_start();
include "dbconnect.php";
// CHẶN KHÔNG CHO USER THƯỜNG VÀO
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Lấy tất cả sản phẩm
$products_res = mysqli_query($con, "SELECT * FROM products ORDER BY PID DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý Sản phẩm | BookZ</title>
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Dùng lại CSS từ admin.php -->
    <link rel="stylesheet" href="admin_style.css">
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
            height: 100%;
            position: fixed;
            width: 250px;
            border-right: 1px solid rgba(255, 255, 255, 0.5);
            z-index: 1030;
            transition: transform 0.3s ease-in-out;
        }

        .main-content {
            margin-left: 250px;
            padding: 40px;
        }

        .top-navbar {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            padding: 0.75rem 1.5rem;
            margin-left: 250px;
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

        /* Responsive styles */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content,
            .top-navbar {
                margin-left: 0;
            }
        }

        .sidebar-brand {
            padding: 20px 0;
            color: var(--primary);
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1029;
        }
    </style>
</head>

<body>
    <div class="bg-blobs"></div>

    <!-- Sidebar -->
    <div class="sidebar">
        <h3 class="text-center fw-bold sidebar-brand">BOOK<span class="text-accent">Z</span> ADMIN</h3>
        <ul class="nav flex-column" id="sidebar">
            <li class="nav-item"><a href="admin.php" class="nav-link"><i class="fas fa-tachometer-alt me-2"></i> Bảng điều khiển</a></li>
            <li class="nav-item"><a href="manage_products.php" class="nav-link active"><i class="fas fa-book me-2"></i> Quản lý sản phẩm</a></li>
            <li class="nav-item"><a href="manage_news.php" class="nav-link"><i class="fas fa-newspaper me-2"></i> Quản lý Tin tức</a></li>
            <li class="nav-item"><a href="manage_users.php" class="nav-link"><i class="fas fa-users me-2"></i> Quản lý người dùng</a></li>
            <li class="nav-item"><a href="manage_orders.php" class="nav-link"><i class="fas fa-shipping-fast me-2"></i> Quản lý đơn hàng</a></li>
            <li class="nav-item"><a href="index.php" class="nav-link"><i class="fas fa-home me-2"></i> Xem website</a></li>
            <li class="nav-item"><a href="destroy.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
        </ul>
    </div>

    <!-- Overlay để làm mờ nền khi sidebar mở trên mobile -->
    <div class="sidebar-overlay d-none" id="sidebar-overlay" onclick="toggleSidebar()"></div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar for Mobile Toggle -->
        <nav class="top-navbar d-lg-none d-flex justify-content-between align-items-center mb-4 sticky-top">
            <h4 class="fw-bold mb-0">Quản lý sản phẩm</h4>
            <button class="btn btn-outline-primary" type="button" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
        <div class="d-flex justify-content-between align-items-center mb-4 d-none d-lg-flex">
            <h2 class="fw-bold mb-0">Quản lý sản phẩm</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" onclick="prepareAddModal()">
                <i class="fas fa-plus me-2"></i> Thêm sản phẩm mới
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

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>PID</th>
                        <th>Hình ảnh</th>
                        <th>Tiêu đề</th>
                        <th>Tác giả</th>
                        <th>Giá</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($products_res)) : ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['PID']); ?></strong></td>
                            <td><img src="<?php echo htmlspecialchars($row['Image']); ?>" alt="" width="50" class="rounded"></td>
                            <td><?php echo htmlspecialchars($row['Title']); ?></td>
                            <td><?php echo htmlspecialchars($row['Author']); ?></td>
                            <td><?php echo number_format($row['Price']); ?> đ</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" onclick='prepareEditModal(<?php echo json_encode($row); ?>)' data-bs-toggle="modal" data-bs-target="#productModal">
                                    <i class="fas fa-edit me-1"></i> Sửa
                                </button>
                                <a href="product_action.php?action=delete&id=<?php echo $row['PID']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">
                                    <i class="fas fa-trash me-1"></i> Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Product Modal (Add/Edit) -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="product_action.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="productModalLabel">Thêm sản phẩm mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" id="formAction" value="add">
                        <input type="hidden" name="pid" id="formPid">

                        <div class="mb-3">
                            <label for="title" class="form-label">Tiêu đề</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="author" class="form-label">Tác giả</label>
                            <input type="text" class="form-control" id="author" name="author" required>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Giá</label>
                            <input type="number" class="form-control" id="price" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image_file" class="form-label">Tải ảnh sản phẩm</label>
                            <input type="file" class="form-control" id="image_file" name="image_file" accept="image/*">
                            <small class="form-text text-muted">Để trống nếu không muốn thay đổi ảnh. Tải ảnh mới sẽ ghi đè ảnh cũ.</small>
                            <!-- Trường ẩn để lưu đường dẫn ảnh hiện tại khi chỉnh sửa -->
                            <input type="hidden" name="current_image" id="current_image">
                            <!-- Hiển thị ảnh hiện tại -->
                            <img id="image_preview" src="" alt="Ảnh hiện tại" class="img-thumbnail mt-2" style="max-width: 100px; display: none;">
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
        // --- LOGIC CHO SIDEBAR RESPONSIVE ---
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        function toggleSidebar() {
            sidebar.classList.toggle('show');
            if (sidebar.classList.contains('show')) {
                overlay.classList.remove('d-none');
            } else {
                overlay.classList.add('d-none');
            }
        }

        const productModal = new bootstrap.Modal(document.getElementById('productModal'));
        const modalTitle = document.getElementById('productModalLabel');
        const formAction = document.getElementById('formAction');
        const formPid = document.getElementById('formPid');
        const formTitle = document.getElementById('title');
        const formAuthor = document.getElementById('author');
        const formPrice = document.getElementById('price');
        const formDescription = document.getElementById('description');
        const formImageFile = document.getElementById('image_file');
        const currentImage = document.getElementById('current_image');
        const imagePreview = document.getElementById('image_preview');

        function prepareAddModal() {
            document.querySelector('#productModal form').reset();
            modalTitle.textContent = 'Thêm sản phẩm mới';
            formAction.value = 'add';
            imagePreview.style.display = 'none';
            formPid.value = '';
        }

        function prepareEditModal(product) {
            document.querySelector('#productModal form').reset();
            modalTitle.textContent = 'Chỉnh sửa sản phẩm';
            formAction.value = 'edit';
            formPid.value = product.PID;
            formTitle.value = product.Title;
            formAuthor.value = product.Author;
            formPrice.value = product.Price;
            formDescription.value = product.Description;
            currentImage.value = product.Image; // Lưu ảnh hiện tại
            imagePreview.src = product.Image;
            imagePreview.style.display = 'block';
        }
    </script>
</body>

</html>