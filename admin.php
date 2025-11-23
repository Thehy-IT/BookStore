<?php
session_start();
include "dbconnect.php";

// CHẶN KHÔNG CHO USER THƯỜNG VÀO
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
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
    
    <style>
        :root {
            --primary: #0f172a;
            --accent: #d4af37;
            --glass-bg: rgba(255, 255, 255, 0.8);
        }
        body { background: #f0f4f8; font-family: sans-serif; }
        
        .bg-blobs {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;
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
            border-right: 1px solid rgba(255,255,255,0.5);
        }

        .main-content { margin-left: 250px; padding: 40px; }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border-left: 5px solid var(--accent);
        }
        
        .nav-link { color: var(--primary); padding: 15px 20px; font-weight: 600; }
        .nav-link:hover { background: rgba(212, 175, 55, 0.1); color: var(--accent); }
        .nav-link.active { background: var(--primary); color: white; }
    </style>
</head>
<body>
    <div class="bg-blobs"></div>

    <!-- Sidebar -->
    <div class="sidebar">
        <h3 class="text-center fw-bold mb-4">BOOK<span style="color: var(--accent)">Z</span> ADMIN</h3>
        <ul class="nav flex-column">
            <li class="nav-item"><a href="#" class="nav-link active"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
            <li class="nav-item"><a href="#" class="nav-link"><i class="fas fa-book me-2"></i> Manage Products</a></li>
            <li class="nav-item"><a href="#" class="nav-link"><i class="fas fa-users me-2"></i> Manage Users</a></li>
            <li class="nav-item"><a href="index.php" class="nav-link"><i class="fas fa-home me-2"></i> View Website</a></li>
            <li class="nav-item"><a href="destroy.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2 class="fw-bold mb-4">Overview</h2>
        
        <div class="row g-4">
            <!-- Thống kê sản phẩm -->
            <div class="col-md-4">
                <div class="stat-card">
                    <?php
                        $p_count = mysqli_num_rows(mysqli_query($con, "SELECT * FROM products"));
                    ?>
                    <h3 class="fw-bold"><?php echo $p_count; ?></h3>
                    <p class="text-muted mb-0">Total Products</p>
                </div>
            </div>

            <!-- Thống kê người dùng -->
            <div class="col-md-4">
                <div class="stat-card" style="border-color: #4e54c8;">
                    <?php
                        $u_count = mysqli_num_rows(mysqli_query($con, "SELECT * FROM users"));
                    ?>
                    <h3 class="fw-bold"><?php echo $u_count; ?></h3>
                    <p class="text-muted mb-0">Total Users</p>
                </div>
            </div>

            <!-- Thống kê đơn hàng (Ví dụ Cart) -->
            <div class="col-md-4">
                <div class="stat-card" style="border-color: #11998e;">
                    <?php
                        $c_count = mysqli_num_rows(mysqli_query($con, "SELECT * FROM cart"));
                    ?>
                    <h3 class="fw-bold"><?php echo $c_count; ?></h3>
                    <p class="text-muted mb-0">Items in Carts</p>
                </div>
            </div>
        </div>

        <h4 class="fw-bold mt-5 mb-3">Recent Products</h4>
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>PID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = mysqli_query($con, "SELECT * FROM products LIMIT 5");
                    while($row = mysqli_fetch_assoc($res)){
                        echo "<tr>
                            <td>{$row['PID']}</td>
                            <td>{$row['Title']}</td>
                            <td>{$row['Author']}</td>
                            <td>{$row['Price']} đ</td>
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