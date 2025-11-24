<?php
session_start();
include "dbconnect.php";
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

// 4. Lấy 5 sản phẩm gần đây
$recent_products_res = mysqli_query($con, "SELECT * FROM products ORDER BY PID DESC LIMIT 5");
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
            <li class="nav-item"><a href="#" class="nav-link active"><i class="fas fa-tachometer-alt me-2"></i> Bảng điều khiển</a></li>
            <li class="nav-item"><a href="manage_products.php" class="nav-link"><i class="fas fa-book me-2"></i> Quản lý sản phẩm</a></li>
            <li class="nav-item"><a href="manage_users.php" class="nav-link"><i class="fas fa-users me-2"></i> Quản lý người dùng</a></li>
            <li class="nav-item"><a href="index.php" class="nav-link"><i class="fas fa-home me-2"></i> Xem website</a></li>
            <li class="nav-item"><a href="destroy.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2 class="fw-bold mb-4">Tổng quan</h2>

        <div class="row g-4">
            <!-- Thống kê sản phẩm -->
            <div class="col-md-4">
                <div class="stat-card">
                    <h3 class="fw-bold"><?php echo $p_count; ?></h3>
                    <p class="text-muted mb-0">Tổng số sản phẩm</p>
                </div>
            </div>

            <!-- Thống kê người dùng -->
            <div class="col-md-4">
                <div class="stat-card border-blue">
                    <h3 class="fw-bold"><?php echo $u_count; ?></h3>
                    <p class="text-muted mb-0">Tổng số người dùng</p>
                </div>
            </div>

            <!-- Thống kê sản phẩm trong giỏ -->
            <div class="col-md-4">
                <div class="stat-card border-green">
                    <h3 class="fw-bold"><?php echo $c_count; ?></h3>
                    <p class="text-muted mb-0">Sản phẩm trong giỏ</p>
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
                                <button class='btn btn-sm btn-primary'><i class='fas fa-edit'></i></button>
                                <button class='btn btn-sm btn-danger'><i class='fas fa-trash'></i></button>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>