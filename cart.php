<?php
session_start();
include "dbconnect.php";

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user'])) {
    header("location: login.php"); // Chuyển hướng sang trang login mới
    exit();
}

$customer = $_SESSION['user'];
$swal_script = ""; // Biến chứa script thông báo

// 2. Xử lý Logic: THÊM / CẬP NHẬT SẢN PHẨM (Từ trang chi tiết)
if (isset($_GET['ID']) && isset($_GET['quantity'])) {
    $product_id = $_GET['ID'];
    $qty = intval($_GET['quantity']); // Đảm bảo là số nguyên

    // Kiểm tra xem sản phẩm đã có trong giỏ chưa
    $check = $con->prepare("SELECT * FROM cart WHERE Customer=? AND Product=?");
    $check->bind_param("ss", $customer, $product_id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows == 0) {
        // Chưa có -> Thêm mới
        $ins = $con->prepare("INSERT INTO cart (Customer, Product, Quantity) VALUES (?, ?, ?)");
        $ins->bind_param("ssi", $customer, $product_id, $qty);
        $ins->execute();
    } else {
        // Đã có -> Cập nhật số lượng (Ghi đè số lượng mới)
        $upd = $con->prepare("UPDATE cart SET Quantity=? WHERE Customer=? AND Product=?");
        $upd->bind_param("iss", $qty, $customer, $product_id);
        $upd->execute();
    }
    // Chuyển hướng để xóa tham số trên URL (Tránh F5 lại bị thêm lần nữa)
    header("Location: cart.php?action=added");
    exit();
}

// 3. Xử lý Logic: XÓA SẢN PHẨM
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    $del = $con->prepare("DELETE FROM cart WHERE Customer=? AND Product=?");
    $del->bind_param("ss", $customer, $product_id);

    if ($del->execute()) {
        header("Location: cart.php?action=removed");
        exit();
    }
}

// 4. Xử lý Logic: ĐẶT HÀNG (Place Order)
if (isset($_POST['place_order'])) {
    // Xóa sạch giỏ hàng của user đó
    $clear = $con->prepare("DELETE FROM cart WHERE Customer=?");
    $clear->bind_param("s", $customer);

    if ($clear->execute()) {
        header("Location: cart.php?action=placed");
        exit();
    }
}

// 5. Xử lý thông báo dựa trên tham số 'action'
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'added') $swal_script = "Swal.fire({icon: 'success', title: 'Updated!', text: 'Cart updated successfully.', timer: 2000, showConfirmButton: false});";
    if ($_GET['action'] == 'removed') $swal_script = "Swal.fire({icon: 'success', title: 'Removed!', text: 'Item removed from cart.', timer: 2000, showConfirmButton: false});";
    if ($_GET['action'] == 'placed') $swal_script = "Swal.fire({icon: 'success', title: 'Order Placed!', text: 'Thank you! Cash on Delivery.', confirmButtonColor: '#0f172a'});";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Cart | BookZ Store</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #0f172a;
            --accent: #d4af37;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: 1px solid rgba(255, 255, 255, 0.6);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f0f4f8;
            color: var(--primary);
            overflow-x: hidden;
        }

        /* --- Background Blobs --- */
        .bg-blobs {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: radial-gradient(circle at 5% 80%, rgba(212, 175, 55, 0.1), transparent 40%),
                radial-gradient(circle at 95% 10%, rgba(15, 23, 42, 0.1), transparent 40%);
        }

        /* --- Navbar --- */
        .navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* --- Glass Panel --- */
        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        /* --- Cart Table --- */
        .cart-table thead {
            background: rgba(15, 23, 42, 0.05);
        }

        .cart-table th {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
            color: #64748b;
            border: none;
            padding: 15px 20px;
        }

        .cart-table td {
            vertical-align: middle;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 20px;
        }

        .cart-img {
            width: 60px;
            height: 90px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* --- Buttons --- */
        .btn-remove {
            color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            transition: 0.3s;
        }

        .btn-remove:hover {
            background: #ef4444;
            color: white;
            transform: rotate(90deg);
        }

        .btn-checkout {
            background: var(--primary);
            color: white;
            border: none;
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        .btn-checkout:hover {
            background: var(--accent);
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.3);
            transform: translateY(-2px);
        }

        /* --- Empty State --- */
        .empty-cart {
            padding: 60px 0;
            text-align: center;
        }

        .empty-icon {
            font-size: 5rem;
            color: #cbd5e1;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="bg-blobs"></div>
    <?php if ($swal_script) echo "<script>$swal_script</script>"; ?>

    <!-- ============== Navbar ==============-->
    <nav class="navbar navbar-expand-lg fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="index.php">
                <img src="img/logo.png" height="40" alt="Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a href="index.php" class="btn btn-outline-dark rounded-pill px-4 me-2">Continue Shopping</a></li>
                    <li class="nav-item"><a href="destroy.php" class="btn btn-danger rounded-pill px-4">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ============== Cart Content ==============-->
    <div class="container" style="margin-top: 100px; margin-bottom: 50px;">
        <h2 class="fw-bold mb-4" style="font-family: 'Playfair Display', serif;">Your Shopping Cart</h2>

        <?php
        // Lấy dữ liệu giỏ hàng
        $sql = "SELECT cart.Product, cart.Quantity, products.Title, products.Author, products.Price, products.PID 
                FROM cart 
                INNER JOIN products ON cart.Product = products.PID 
                WHERE cart.Customer = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $customer);
        $stmt->execute();
        $result = $stmt->get_result();

        $total = 0;
        $count = 0;
        ?>

        <?php if ($result->num_rows > 0): ?>
            <div class="row g-4">
                <!-- Cột trái: Danh sách sản phẩm -->
                <div class="col-lg-8">
                    <div class="glass-panel">
                        <div class="table-responsive">
                            <table class="table cart-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th class="text-end">Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()):
                                        $subtotal = $row['Price'] * $row['Quantity'];
                                        $total += $subtotal;
                                        $count += $row['Quantity'];
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="img/books/<?php echo $row['PID']; ?>.jpg" class="cart-img me-3" onerror="this.src='https://placehold.co/100x150?text=Book'">
                                                    <div>
                                                        <h6 class="fw-bold mb-1"><?php echo $row['Title']; ?></h6>
                                                        <small class="text-muted">by <?php echo $row['Author']; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="fw-bold"><?php echo number_format($row['Price']); ?></td>
                                            <td>
                                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fs-6"><?php echo $row['Quantity']; ?></span>
                                            </td>
                                            <td class="text-end fw-bold text-primary"><?php echo number_format($subtotal); ?></td>
                                            <td class="text-end">
                                                <a href="#" onclick="confirmRemove('<?php echo $row['PID']; ?>')" class="btn-remove d-inline-flex align-items-center justify-content-center">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="index.php" class="text-decoration-none text-muted fw-bold">
                            <i class="fas fa-arrow-left me-2"></i> Continue Shopping
                        </a>
                    </div>
                </div>

                <!-- Cột phải: Tổng tiền (Order Summary) -->
                <div class="col-lg-4">
                    <div class="glass-panel p-4 sticky-top" style="top: 100px; z-index: 1;">
                        <h5 class="fw-bold mb-4">Order Summary</h5>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Subtotal (<?php echo $count; ?> items)</span>
                            <span class="fw-bold"><?php echo number_format($total); ?> đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Shipping</span>
                            <span class="text-success fw-bold">Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fs-5 fw-bold">Total Amount</span>
                            <span class="fs-4 fw-bold" style="color: var(--accent);"><?php echo number_format($total); ?> đ</span>
                        </div>

                        <!-- Form đặt hàng -->
                        <form method="POST" id="orderForm">
                            <input type="hidden" name="place_order" value="1">
                            <button type="button" onclick="confirmOrder()" class="btn-checkout">
                                Checkout <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </form>

                        <div class="mt-3 text-center small text-muted">
                            <i class="fas fa-shield-alt me-1"></i> Secure Checkout
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Giỏ hàng trống -->
            <div class="glass-panel empty-cart">
                <div class="empty-icon">
                    <i class="fas fa-shopping-basket"></i>
                </div>
                <h3>Your cart is currently empty</h3>
                <p class="text-muted mb-4">Looks like you haven't added any books yet.</p>
                <a href="index.php" class="btn btn-primary rounded-pill px-5 py-3 fw-bold" style="background: var(--primary);">
                    Start Shopping
                </a>
            </div>
        <?php endif; ?>

    </div>

    <!-- JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Xác nhận xóa sản phẩm
        function confirmRemove(pid) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to remove this book from cart?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#0f172a',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "cart.php?remove=" + pid;
                }
            })
        }

        // Xác nhận đặt hàng
        function confirmOrder() {
            Swal.fire({
                title: 'Place Order?',
                text: "You are about to place this order. Payment will be collected on delivery.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0f172a',
                confirmButtonText: 'Confirm Order'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('orderForm').submit();
                }
            })
        }
    </script>
</body>

</html>