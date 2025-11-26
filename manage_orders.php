<?php
session_start();
include "dbconnect.php";

// 1. KIỂM TRA QUYỀN ADMIN
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// --- XỬ LÝ XÓA ĐƠN HÀNG ---
if (isset($_POST['delete_order'])) {
    $del_id = intval($_POST['order_id']);
    // Xóa chi tiết đơn hàng trước
    $con->query("DELETE FROM order_items WHERE order_id = $del_id");
    // Xóa đơn hàng chính
    $stmt = $con->prepare("DELETE FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $del_id);

    if ($stmt->execute()) {
        if (isset($_GET['id']) && $_GET['id'] == $del_id) {
            header("Location: manage_orders.php");
            exit();
        }
        $msg = "Đã xóa vĩnh viễn đơn hàng #$del_id!";
        $msg_type = "success";
    } else {
        $msg = "Lỗi xóa đơn hàng: " . $con->error;
        $msg_type = "danger";
    }
}

// --- XỬ LÝ CẬP NHẬT TRẠNG THÁI ---
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['status'];
    $update_sql = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = $con->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $order_id);

    if ($stmt->execute()) {
        $msg = "Cập nhật trạng thái đơn hàng #$order_id thành công!";
        $msg_type = "success";
    } else {
        $msg = "Lỗi cập nhật: " . $con->error;
        $msg_type = "danger";
    }
}

// XÁC ĐỊNH CHẾ ĐỘ
$view_mode = isset($_GET['id']) ? 'detail' : 'list';

// MẢNG TRẠNG THÁI
$status_definitions = [
    'Pending' => ['label' => 'Chờ xác nhận', 'class' => 'bg-warning text-dark'],
    'Confirmed' => ['label' => 'Đã xác nhận', 'class' => 'bg-info text-dark'],
    'Shipped' => ['label' => 'Đang giao hàng', 'class' => 'bg-primary'],
    'Delivered' => ['label' => 'Đã giao hàng', 'class' => 'bg-success'],
    'Completed' => ['label' => 'Hoàn thành', 'class' => 'bg-success'],
    'Cancelled' => ['label' => 'Đã hủy', 'class' => 'bg-danger']
];
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng | BookZ Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            z-index: 100;
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

    <div class="sidebar">
        <h3 class="text-center fw-bold mb-4">BOOK<span class="text-accent">Z</span> ADMIN</h3>
        <ul class="nav flex-column">
            <li class="nav-item"><a href="dashboard.php" class="nav-link"><i class="fas fa-tachometer-alt me-2"></i> Bảng điều khiển</a></li>
            <li class="nav-item"><a href="manage_products.php" class="nav-link"><i class="fas fa-book me-2"></i> Quản lý sản phẩm</a></li>
            <li class="nav-item"><a href="manage_news.php" class="nav-link"><i class="fas fa-newspaper me-2"></i> Quản lý Tin tức</a></li>
            <li class="nav-item"><a href="manage_users.php" class="nav-link"><i class="fas fa-users me-2"></i> Quản lý người dùng</a></li>
            <li class="nav-item"><a href="manage_orders.php" class="nav-link active"><i class="fas fa-shipping-fast me-2"></i> Quản lý đơn hàng</a></li>
            <li class="nav-item"><a href="index.php" class="nav-link"><i class="fas fa-home me-2"></i> Xem website</a></li>
            <li class="nav-item"><a href="destroy.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
        </ul>
    </div>

    <div class="main-content">
        
        <?php if (isset($msg)): ?>
            <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show" role="alert">
                <?php echo $msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($view_mode == 'detail'):
            $order_id = intval($_GET['id']);
            $sql = "SELECT * FROM orders WHERE order_id = $order_id";
            $result = $con->query($sql);

            if ($result->num_rows > 0) {
                $order = $result->fetch_assoc();
                $sql_items = "SELECT oi.*, p.Title FROM order_items oi LEFT JOIN products p ON oi.product_id = p.PID WHERE oi.order_id = $order_id";
                $items = $con->query($sql_items);
                ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Chi tiết đơn hàng #<?php echo $order_id; ?></h2>
                    <div>
                        <form method="POST" action="" class="d-inline" onsubmit="return confirm('CẢNH BÁO: Bạn có chắc chắn muốn xóa vĩnh viễn đơn hàng này không?');">
                            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                            <button type="submit" name="delete_order" class="btn btn-danger me-2">
                                <i class="fas fa-trash"></i> Xóa đơn
                            </button>
                        </form>
                        <a href="manage_orders.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header fw-bold">Thông tin khách hàng</div>
                            <div class="card-body">
                                <p><strong>Họ tên:</strong> <?php echo $order['customer_name']; ?></p>
                                <p><strong>SĐT:</strong> <?php echo $order['phone_number']; ?></p>
                                <p><strong>Địa chỉ:</strong> <?php echo $order['shipping_address']; ?></p>
                                <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></p>
                                <p><strong>Thanh toán:</strong> <?php echo strtoupper($order['payment_method']); ?></p>
                            </div>
                        </div>

                        <div class="card shadow-sm border-primary">
                            <div class="card-header bg-primary text-white fw-bold">Cập nhật trạng thái</div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Trạng thái hiện tại:</label>
                                        <select name="status" class="form-select">
                                            <?php foreach ($status_definitions as $key => $def): ?>
                                                <option value="<?php echo $key; ?>" <?php echo ($order['status'] == $key) ? 'selected' : ''; ?>>
                                                    <?php echo $def['label']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="submit" name="update_status" class="btn btn-primary w-100">Cập nhật đơn hàng</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="card shadow-sm">
                            <div class="card-header fw-bold">Sản phẩm đã đặt</div>
                            <div class="card-body p-0">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th class="text-center">Số lượng</th>
                                            <th class="text-end">Đơn giá</th>
                                            <th class="text-end">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $subtotal = 0;
                                        while ($item = $items->fetch_assoc()):
                                            $row_total = $item['price'] * $item['quantity'];
                                            $subtotal += $row_total;
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="img/books/<?php echo $item['product_id']; ?>.jpg" onerror="this.src='https://placehold.co/40x40'" class="rounded border me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                        <span><?php echo $item['Title']; ?></span>
                                                    </div>
                                                </td>
                                                <td class="text-center"><?php echo $item['quantity']; ?></td>
                                                <td class="text-end"><?php echo number_format($item['price']); ?>đ</td>
                                                <td class="text-end"><?php echo number_format($row_total); ?>đ</td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold">Tổng tiền:</td>
                                            <td class="text-end fw-bold text-danger fs-5">
                                                <?php echo number_format($order['total_amount']); ?>đ
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } else {
                echo "<div class='alert alert-warning'>Đơn hàng không tồn tại hoặc đã bị xóa. <a href='manage_orders.php'>Quay lại</a></div>";
            } ?>

        <?php else: ?>
            <h2 class="mb-4 fw-bold">Quản lý Đơn hàng</h2>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#ID</th>
                                    <th>Khách hàng</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th class="text-end">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql_list = "SELECT * FROM orders ORDER BY order_date DESC";
                                $result_list = $con->query($sql_list);

                                if ($result_list->num_rows > 0) {
                                    while ($row = $result_list->fetch_assoc()) {
                                        $st = $status_definitions[$row['status']] ?? ['label' => $row['status'], 'class' => 'bg-secondary'];
                                        ?>
                                        <tr>
                                            <td><strong>#<?php echo $row['order_id']; ?></strong></td>
                                            <td>
                                                <div><?php echo $row['customer_name']; ?></div>
                                                <small class="text-muted"><?php echo $row['phone_number']; ?></small>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($row['order_date'])); ?></td>
                                            <td class="fw-bold text-danger"><?php echo number_format($row['total_amount']); ?>đ</td>
                                            <td>
                                                <span class="badge rounded-pill <?php echo $st['class']; ?>">
                                                    <?php echo $st['label']; ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="manage_orders.php?id=<?php echo $row['order_id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <form method="POST" action="" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đơn hàng #<?php echo $row['order_id']; ?>? Hành động này không thể hoàn tác!');">
                                                    <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                                    <button type="submit" name="delete_order" class="btn btn-sm btn-outline-danger" title="Xóa">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center py-4'>Chưa có đơn hàng nào.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>