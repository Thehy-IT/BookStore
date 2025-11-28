<?php
session_start();
include_once "dbconnect.php";
// CHẶN KHÔNG CHO USER THƯỜNG VÀO
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// --- PHẦN LOGIC: LẤY DỮ LIỆU TỪ CSDL ---

// 1. Đếm tổng số sản phẩm (hiệu quả hơn)
$p_res = mysqli_query($con, "SELECT COUNT(*) as total FROM products");
$p_count = mysqli_fetch_assoc($p_res)['total'];

// 2. Đếm tổng số người dùng
$u_res = mysqli_query($con, "SELECT COUNT(*) as total FROM users");
$u_count = mysqli_fetch_assoc($u_res)['total'];

// 3. Đếm tổng số sản phẩm trong giỏ hàng
$c_res = mysqli_query($con, "SELECT COUNT(*) as total FROM cart");
$c_count = mysqli_fetch_assoc($c_res)['total'];

// 4. Tính tổng doanh thu dự kiến (từ giỏ hàng)
$rev_res = mysqli_query($con, "SELECT SUM(c.Quantity * p.Price) as total_revenue FROM cart c JOIN products p ON c.ProductID = p.PID");
$potential_revenue = mysqli_fetch_assoc($rev_res)['total_revenue'] ?? 0;


// 4. Lấy 5 sản phẩm gần đây
$recent_products_res = mysqli_query($con, "SELECT * FROM products ORDER BY PID DESC LIMIT 5");

// --- DỮ LIỆU CHO BIỂU ĐỒ ---

// 1. Thống kê sản phẩm theo thể loại
$category_data = [];
$cat_res = mysqli_query($con, "SELECT Category, COUNT(*) as count FROM products WHERE Category IS NOT NULL AND Category != '' GROUP BY Category");
while ($row = mysqli_fetch_assoc($cat_res)) {
    $category_data['labels'][] = $row['Category'];
    $category_data['counts'][] = $row['count'];
}

// 2. Thống kê người dùng theo vai trò
$role_data = [];
$role_res = mysqli_query($con, "SELECT Role, COUNT(*) as count FROM users GROUP BY Role");
while ($row = mysqli_fetch_assoc($role_res)) {
    $role_data['labels'][] = ucfirst($row['Role']); // Viết hoa chữ đầu: User, Admin
    $role_data['counts'][] = $row['count'];
}

// 3. Thống kê người dùng đăng ký trong 30 ngày qua
$user_registration_chart_data = [];
$labels = [];
$counts = [];

// Tạo một mảng chứa 30 ngày gần nhất, khởi tạo số lượng là 0
$date_range = [];
for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('d/m', strtotime($date)); // Định dạng ngày/tháng cho dễ nhìn
    $date_range[$date] = 0;
}

// Truy vấn CSDL để lấy số lượng đăng ký thực tế
$reg_res = mysqli_query($con, "SELECT DATE(created_at) as registration_date, COUNT(*) as count FROM users WHERE created_at >= CURDATE() - INTERVAL 30 DAY GROUP BY DATE(created_at)");

// Cập nhật số lượng từ CSDL vào mảng
while ($row = mysqli_fetch_assoc($reg_res)) {
    if (isset($date_range[$row['registration_date']])) {
        $date_range[$row['registration_date']] = (int) $row['count'];
    }
}

$user_registration_chart_data['labels'] = $labels;
$user_registration_chart_data['counts'] = array_values($date_range);

// 4. Thống kê doanh thu dự kiến theo thể loại
$revenue_by_category_data = [];
$rev_cat_res = mysqli_query($con, "SELECT p.Category, SUM(c.Quantity * p.Price) as revenue FROM cart c JOIN products p ON c.ProductID = p.PID WHERE p.Category IS NOT NULL AND p.Category != '' GROUP BY p.Category ORDER BY revenue DESC");
while ($row = mysqli_fetch_assoc($rev_cat_res)) {
    $revenue_by_category_data['labels'][] = $row['Category'];
    $revenue_by_category_data['counts'][] = (float) $row['revenue'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard | BookZ</title>
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border-left: 5px solid var(--accent);
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

        /* CSS classes để thay thế inline styles */
        .text-accent {
            color: var(--accent);
        }

        .stat-card.border-blue {
            border-color: #4e54c8;
        }

        .stat-card.border-green {
            border-color: #11998e;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <div class="bg-blobs"></div>

    <!-- Sidebar -->
    <div class="sidebar">
        <h3 class="text-center fw-bold mb-4">BOOK<span class="text-accent">Z</span> ADMIN</h3>
        <ul class="nav flex-column">
            <li class="nav-item"><a href="#" class="nav-link active"><i class="fas fa-tachometer-alt me-2"></i> Bảng
                    điều khiển</a></li>
            <li class="nav-item"><a href="manage_products.php" class="nav-link"><i class="fas fa-book me-2"></i> Quản lý
                    sản phẩm</a></li>
            <li class="nav-item"><a href="manage_news.php" class="nav-link"><i class="fas fa-newspaper me-2"></i> Quản
                    lý Tin tức</a></li>
            <li class="nav-item"><a href="manage_users.php" class="nav-link"><i class="fas fa-users me-2"></i> Quản lý
                    người dùng</a></li>
            <li class="nav-item"><a href="manage_orders.php" class="nav-link"><i class="fas fa-shipping-fast me-2"></i>
                    Quản lý đơn hàng</a></li>
            <li class="nav-item"><a href="index.php" class="nav-link"><i class="fas fa-home me-2"></i> Xem website</a>
            </li>
            <li class="nav-item"><a href="destroy.php" class="nav-link text-danger"><i
                        class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2 class="fw-bold mb-4">Tổng quan</h2>

        <!-- Hiển thị thông báo (nếu có) -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php
            // Xóa thông báo sau khi hiển thị
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        endif;
        ?>


        <div class="row g-4">
            <!-- Thống kê sản phẩm -->
            <div class="col-md-3">
                <div class="stat-card">
                    <h3 class="fw-bold"><?php echo $p_count; ?></h3>
                    <p class="text-muted mb-0">Tổng số sản phẩm</p>
                </div>
            </div>

            <!-- Thống kê người dùng -->
            <div class="col-md-3">
                <div class="stat-card border-blue">
                    <h3 class="fw-bold"><?php echo $u_count; ?></h3>
                    <p class="text-muted mb-0">Tổng số người dùng</p>
                </div>
            </div>

            <!-- Thống kê sản phẩm trong giỏ -->
            <div class="col-md-3">
                <div class="stat-card border-green">
                    <h3 class="fw-bold"><?php echo $c_count; ?></h3>
                    <p class="text-muted mb-0">Sản phẩm trong giỏ</p>
                </div>
            </div>

            <!-- Thống kê doanh thu dự kiến -->
            <div class="col-md-3">
                <div class="stat-card" style="border-color: #ffc107;">
                    <h3 class="fw-bold"><?php echo number_format($potential_revenue, 0, ',', '.'); ?> đ</h3>
                    <p class="text-muted mb-0">Doanh thu dự kiến</p>
                </div>
            </div>
        </div>

        <!-- Hàng chứa biểu đồ -->
        <div class="row g-4 mt-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Sản phẩm theo thể loại</h5>
                        <canvas id="productsByCategoryChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h5 class="fw-bold mb-3">Phân bổ người dùng</h5>
                        <canvas id="usersByRoleChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Biểu đồ người dùng đăng ký mới -->
        <div class="row g-4 mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Người dùng mới trong 30 ngày qua</h5>
                        <canvas id="userRegistrationsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Biểu đồ doanh thu dự kiến theo thể loại -->
        <div class="row g-4 mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Doanh thu dự kiến theo thể loại</h5>
                        <canvas id="revenueByCategoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <h4 class="fw-bold mt-5 mb-3">Sản phẩm gần đây</h4>
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>PID</th>
                        <th>Tiêu đề</th>
                        <th>Tác giả</th>
                        <th>Giá</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = mysqli_fetch_assoc($recent_products_res)) {
                        echo "<tr>
                            <td>" . htmlspecialchars($row['PID']) . "</td>
                            <td>" . htmlspecialchars($row['Title']) . "</td>
                            <td>" . htmlspecialchars($row['Author']) . "</td>
                            <td>" . htmlspecialchars($row['Price']) . " đ</td>
                            <td>
                                <button class='btn btn-sm btn-outline-primary' onclick='prepareEditModal(" . json_encode($row) . ")' data-bs-toggle='modal' data-bs-target='#productModal'>
                                    <i class='fas fa-edit'></i>
                                </button>
                                <a href='product_action.php?action=delete&id=" . $row['PID'] . "&return_url=admin.php' class='btn btn-sm btn-outline-danger' onclick=\"return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')\">
                                    <i class='fas fa-trash'></i>
                                </a>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Product Modal (Add/Edit) - Lấy từ manage_products.php -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="product_action.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="productModalLabel">Chỉnh sửa sản phẩm</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" id="formAction" value="edit">
                        <input type="hidden" name="pid" id="formPid">
                        <input type="hidden" name="return_url" value="admin.php">

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
                            <small class="form-text text-muted">Để trống nếu không muốn thay đổi ảnh. Tải ảnh mới sẽ ghi
                                đè ảnh cũ.</small>
                            <input type="hidden" name="current_image" id="current_image">
                            <img id="image_preview" src="" alt="Ảnh hiện tại" class="img-thumbnail mt-2"
                                style="max-width: 100px; display: none;">
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
        // JS cho modal chỉnh sửa sản phẩm - Lấy từ manage_products.php
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

        function prepareEditModal(product) {
            document.querySelector('#productModal form').reset();
            modalTitle.textContent = 'Chỉnh sửa sản phẩm';
            formAction.value = 'edit';
            formPid.value = product.PID;
            formTitle.value = product.Title;
            formAuthor.value = product.Author;
            formPrice.value = product.Price;
            formDescription.value = product.Description;
            currentImage.value = product.Image;
            imagePreview.src = product.Image;
            imagePreview.style.display = 'block';
        }

        // --- KHỞI TẠO BIỂU ĐỒ ---
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Biểu đồ sản phẩm theo thể loại
            const ctxCategory = document.getElementById('productsByCategoryChart');
            if (ctxCategory) {
                new Chart(ctxCategory, {
                    type: 'doughnut',
                    data: {
                        labels: <?php echo json_encode($category_data['labels'] ?? []); ?>,
                        datasets: [{
                            label: 'Số lượng',
                            data: <?php echo json_encode($category_data['counts'] ?? []); ?>,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.8)', 'rgba(54, 162, 235, 0.8)',
                                'rgba(255, 206, 86, 0.8)', 'rgba(75, 192, 192, 0.8)',
                                'rgba(153, 102, 255, 0.8)', 'rgba(255, 159, 64, 0.8)',
                                'rgba(199, 199, 199, 0.8)', 'rgba(83, 102, 255, 0.8)'
                            ],
                            borderColor: 'rgba(255, 255, 255, 0.7)',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'right',
                            },
                            title: {
                                display: false,
                                text: 'Thống kê sản phẩm theo thể loại'
                            }
                        }
                    }
                });
            }

            // 2. Biểu đồ người dùng theo vai trò
            const ctxRole = document.getElementById('usersByRoleChart');
            if (ctxRole) {
                new Chart(ctxRole, {
                    type: 'pie',
                    data: {
                        labels: <?php echo json_encode($role_data['labels'] ?? []); ?>,
                        datasets: [{
                            data: <?php echo json_encode($role_data['counts'] ?? []); ?>,
                            backgroundColor: ['rgba(212, 175, 55, 0.8)', 'rgba(15, 23, 42, 0.8)'],
                            borderColor: 'rgba(255, 255, 255, 0.7)',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                        }
                    }
                });
            }

            // 3. Biểu đồ người dùng đăng ký theo ngày
            const ctxRegistration = document.getElementById('userRegistrationsChart');
            if (ctxRegistration) {
                new Chart(ctxRegistration, {
                    type: 'line',
                    data: {
                        labels: <?php echo json_encode($user_registration_chart_data['labels'] ?? []); ?>,
                        datasets: [{
                            label: 'Người dùng mới',
                            data: <?php echo json_encode($user_registration_chart_data['counts'] ?? []); ?>,
                            fill: true,
                            backgroundColor: 'rgba(78, 84, 200, 0.1)',
                            borderColor: 'rgba(78, 84, 200, 1)',
                            tension: 0.3,
                            pointBackgroundColor: 'rgba(78, 84, 200, 1)',
                            pointRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1 // Chỉ hiển thị số nguyên trên trục Y
                                }
                            }
                        }
                    }
                });
            }

            // 4. Biểu đồ doanh thu dự kiến theo thể loại
            const ctxRevenue = document.getElementById('revenueByCategoryChart');
            if (ctxRevenue) {
                new Chart(ctxRevenue, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($revenue_by_category_data['labels'] ?? []); ?>,
                        datasets: [{
                            label: 'Doanh thu dự kiến (đ)',
                            data: <?php echo json_encode($revenue_by_category_data['counts'] ?? []); ?>,
                            backgroundColor: 'rgba(255, 193, 7, 0.7)',
                            borderColor: 'rgba(255, 193, 7, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('vi-VN', {
                                                style: 'currency',
                                                currency: 'VND'
                                            }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>

</html>