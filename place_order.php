<?php
session_start();
include "dbconnect.php"; 

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// XÁC ĐỊNH CHẾ ĐỘ: XEM CHI TIẾT HAY XEM DANH SÁCH
$view_mode = isset($_GET['id']) ? 'detail' : 'list';
$order = null;
$order_items = [];
$orders_list = [];

if ($view_mode == 'detail') {
    // --- CHẾ ĐỘ 1: XEM CHI TIẾT ĐƠN HÀNG ---
    $order_id = intval($_GET['id']);

    // Lấy thông tin đơn hàng
    $sql_order = "SELECT * FROM orders WHERE order_id = ? AND user_id = ?";
    $stmt = $con->prepare($sql_order);
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $order_result = $stmt->get_result();

    if ($order_result->num_rows == 0) {
        // Nếu ID sai thì quay về danh sách
        header("Location: order_tracking.php");
        exit();
    }

    $order = $order_result->fetch_assoc();

    // Lấy chi tiết sản phẩm
    $sql_items = "SELECT oi.*, p.Title, p.PID, p.Author 
                  FROM order_items oi 
                  JOIN products p ON oi.product_id = p.PID 
                  WHERE oi.order_id = ?";
    $stmt_items = $con->prepare($sql_items);
    $stmt_items->bind_param("i", $order_id);
    $stmt_items->execute();
    $items_result = $stmt_items->get_result();
    while ($row = $items_result->fetch_assoc()) {
        $order_items[] = $row;
    }
} else {
    // --- CHẾ ĐỘ 2: XEM DANH SÁCH ĐƠN HÀNG (HISTORY) ---
    $sql_list = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
    $stmt_list = $con->prepare($sql_list);
    $stmt_list->bind_param("i", $user_id);
    $stmt_list->execute();
    $list_result = $stmt_list->get_result();
    while ($row = $list_result->fetch_assoc()) {
        $orders_list[] = $row;
    }
}

// HÀM HỖ TRỢ: Render Timeline Steps
$status_map = [
    'Pending' => 1,
    'Confirmed' => 2,
    'Shipped' => 3,
    'Delivered' => 4,
    'Completed' => 5,
    'Cancelled' => 0
];

function renderStep($step_index, $current_step, $icon, $label, $time) {
    $isActive = $step_index <= $current_step;
    $colorClass = $isActive ? 'text-green-500 border-green-500' : 'text-gray-300 border-gray-300';
    $bgClass = $isActive ? 'bg-white' : 'bg-white';
    
    echo '
    <div class="flex flex-col items-center w-1/5 relative z-10 group">
        <div class="w-14 h-14 rounded-full border-4 '.$colorClass.' '.$bgClass.' flex items-center justify-center transition-all duration-300">
            <span class="material-symbols-outlined text-3xl '.$colorClass.'">'.$icon.'</span>
        </div>
        <div class="text-center mt-3">
            <p class="text-sm font-medium '.($isActive ? 'text-black' : 'text-gray-400').'">'.$label.'</p>
            <p class="text-xs text-gray-500 mt-1">'.$time.'</p>
        </div>
    </div>';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $view_mode == 'detail' ? 'Chi tiết đơn hàng #'.$order['order_id'] : 'Đơn hàng của tôi'; ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        body { font-family: 'Roboto', sans-serif; background-color: #f5f5f5; }
        .shopee-border-top {
            background-image: repeating-linear-gradient(45deg, #6fa6d6, #6fa6d6 33px, transparent 0, transparent 41px, #f18d9b 0, #f18d9b 74px, transparent 0, transparent 82px);
            background-position-x: -1.875rem;
            background-size: 7.25rem .1875rem;
            height: .1875rem;
            width: 100%;
        }
        .btn-orange { background-color: #ee4d2d; color: white; }
        .btn-orange:hover { background-color: #d73211; }
        .text-orange { color: #ee4d2d; }
    </style>
</head>
<body class="pb-10">

    <!-- Header Chung -->
    <div class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <?php if($view_mode == 'detail'): ?>
                    <a href="order_tracking.php" class="text-gray-500 hover:text-orange flex items-center gap-1 text-sm">
                        <span class="material-symbols-outlined text-lg">arrow_back_ios</span> DANH SÁCH
                    </a>
                    <span class="text-gray-300">|</span>
                    <span class="text-lg font-medium text-gray-700">CHI TIẾT ĐƠN HÀNG</span>
                <?php else: ?>
                    <a href="index.php" class="text-gray-500 hover:text-orange flex items-center gap-1 text-sm">
                        <span class="material-symbols-outlined text-lg">arrow_back_ios</span> TRANG CHỦ
                    </a>
                    <span class="text-gray-300">|</span>
                    <span class="text-lg font-medium text-gray-700">ĐƠN HÀNG CỦA TÔI</span>
                <?php endif; ?>
            </div>
            
            <?php if($view_mode == 'detail'): ?>
            <div class="text-sm">
                <span class="text-gray-500">MÃ ĐƠN HÀNG. <?php echo $order['order_id']; ?></span>
                <span class="mx-2 text-gray-300">|</span>
                <span class="text-orange font-bold uppercase"><?php echo strtoupper($order['status']); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 mt-6">

        <?php if ($view_mode == 'detail'): ?>
            <!-- ================= GIAO DIỆN CHI TIẾT (Shopee Style) ================= -->
            <?php $current_step = $status_map[$order['status']] ?? 1; ?>
            
            <!-- STEPPER -->
            <div class="bg-white p-8 rounded-sm shadow-sm mb-4">
                <div class="relative flex justify-between items-start">
                    <div class="absolute top-7 left-0 w-full h-1 bg-gray-200 -z-0"></div>
                    <div class="absolute top-7 left-0 h-1 bg-green-500 -z-0 transition-all duration-500" style="width: <?php echo ($current_step - 1) * 25; ?>%;"></div>
                    <?php 
                        renderStep(1, $current_step, 'receipt_long', 'Đơn Hàng Đã Đặt', date('H:i d-m-Y', strtotime($order['order_date'])));
                        renderStep(2, $current_step, 'payments', 'Đã Xác Nhận', '');
                        renderStep(3, $current_step, 'local_shipping', 'Đã Giao ĐVVC', '');
                        renderStep(4, $current_step, 'inventory_2', 'Đã Nhận Hàng', '');
                        renderStep(5, $current_step, 'star', 'Hoàn Thành', '');
                    ?>
                </div>
            </div>

            <!-- INFO & ITEMS -->
            <div class="bg-white shadow-sm mb-4 relative overflow-hidden rounded-sm">
                <div class="shopee-border-top"></div>
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-3 text-gray-800">Địa Chỉ Nhận Hàng</h3>
                    <div class="text-sm text-gray-600">
                        <p class="font-bold text-gray-900"><?php echo htmlspecialchars($order['customer_name']); ?></p>
                        <p class="text-gray-500">(+84) <?php echo htmlspecialchars($order['phone_number']); ?></p>
                        <p><?php echo htmlspecialchars($order['shipping_address']); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-sm mb-4">
                <div class="p-4 border-b border-gray-100 flex items-center gap-2">
                    <span class="bg-red-500 text-white text-xs px-1 rounded">Yêu thích</span>
                    <span class="font-bold text-gray-800">BookZ Shop</span>
                </div>
                <?php 
                $subtotal = 0;
                foreach ($order_items as $item): 
                    $subtotal += $item['price'] * $item['quantity'];
                ?>
                <div class="p-4 border-b border-gray-50 flex gap-4 items-start">
                    <img src="img/books/<?php echo $item['PID']; ?>.jpg" onerror="this.src='https://placehold.co/80x80?text=Book'" class="w-20 h-20 object-cover border border-gray-200 rounded">
                    <div class="flex-1">
                        <h4 class="text-gray-800 font-medium"><?php echo htmlspecialchars($item['Title']); ?></h4>
                        <p class="text-gray-500 text-sm">x<?php echo $item['quantity']; ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-orange font-medium"><?php echo number_format($item['price']); ?>đ</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- TOTAL -->
            <div class="bg-white shadow-sm rounded-sm overflow-hidden mb-8">
                <div class="p-6 bg-gray-50/50 border-b border-gray-100">
                    <div class="w-full md:w-1/2 ml-auto space-y-3 text-sm">
                        <div class="flex justify-between text-gray-500">
                            <span>Tổng tiền hàng</span>
                            <span class="text-gray-800"><?php echo number_format($subtotal); ?>đ</span>
                        </div>
                        <div class="flex justify-between items-end pt-4 border-t border-gray-200">
                            <span class="text-gray-500">Thành tiền</span>
                            <span class="text-2xl font-bold text-orange"><?php echo number_format($order['total_amount']); ?>đ</span>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- ================= GIAO DIỆN DANH SÁCH (History Style) ================= -->
            
            <?php if (empty($orders_list)): ?>
                <div class="bg-white p-12 text-center rounded-sm shadow-sm">
                    <span class="material-symbols-outlined text-6xl text-gray-300">shopping_bag</span>
                    <p class="text-gray-500 mt-4">Bạn chưa có đơn hàng nào.</p>
                    <a href="index.php" class="inline-block mt-4 btn-orange px-6 py-2 rounded-sm text-sm">Mua sắm ngay</a>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($orders_list as $history_item): ?>
                    <div class="bg-white p-6 rounded-sm shadow-sm hover:shadow-md transition cursor-pointer" onclick="window.location.href='order_tracking.php?id=<?php echo $history_item['order_id']; ?>'">
                        <div class="flex justify-between items-start border-b border-gray-100 pb-4 mb-4">
                            <div>
                                <h3 class="font-bold text-gray-800">Đơn hàng #<?php echo $history_item['order_id']; ?></h3>
                                <p class="text-sm text-gray-500"><?php echo date('H:i d/m/Y', strtotime($history_item['order_date'])); ?></p>
                            </div>
                            <span class="text-orange font-bold uppercase text-sm border border-orange-200 bg-orange-50 px-3 py-1 rounded-sm">
                                <?php echo $history_item['status']; ?>
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-600">
                                <span class="material-symbols-outlined text-sm align-middle">payments</span>
                                <?php echo $history_item['payment_method']; ?>
                            </div>
                            <div class="text-right">
                                <span class="text-gray-500 text-sm">Tổng thanh toán:</span>
                                <span class="text-xl font-bold text-orange ml-2"><?php echo number_format($history_item['total_amount']); ?>đ</span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>

    </div>
</body>
</html>