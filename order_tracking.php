<?php
session_start();
include "dbconnect.php";
// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


$user_id = $_SESSION['user_id'];

// --- XỬ LÝ HỦY ĐƠN HÀNG ---
if (isset($_POST['cancel_order']) && isset($_POST['order_id_to_cancel'])) {
    $cancel_id = intval($_POST['order_id_to_cancel']);

    // Kiểm tra xem đơn hàng có thuộc về user này và đang ở trạng thái Pending không
    $check_stmt = $con->prepare("SELECT status FROM orders WHERE order_id = ? AND user_id = ?");
    $check_stmt->bind_param("ii", $cancel_id, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $order_data = $check_result->fetch_assoc();
        if ($order_data['status'] == 'Pending') {
            // Thực hiện hủy
            $update_stmt = $con->prepare("UPDATE orders SET status = 'Cancelled' WHERE order_id = ?");
            $update_stmt->bind_param("i", $cancel_id);

            if ($update_stmt->execute()) {
                header("Location: order_tracking.php?id=" . $cancel_id);
                exit();
            }
        }
    }
}

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
        header("Location: order_tracking.php");
        exit();
    }

    $order = $order_result->fetch_assoc();

    // Lấy chi tiết sản phẩm
    $sql_items = "SELECT oi.*, p.Title, p.PID, p.Author 
                  FROM order_items oi 
                  LEFT JOIN products p ON oi.product_id = p.PID 
                  WHERE oi.order_id = ?";
    $stmt_items = $con->prepare($sql_items);
    $stmt_items->bind_param("i", $order_id);
    $stmt_items->execute();
    $items_result = $stmt_items->get_result();

    while ($row = $items_result->fetch_assoc()) {
        if ($row['Title'] == null) {
            $row['Title'] = "Sản phẩm không tồn tại hoặc đã bị xóa";
            $row['PID'] = 0;
        }
        $order_items[] = $row;
    }
} else {
    // --- CHẾ ĐỘ 2: XEM DANH SÁCH ĐƠN HÀNG ---
    $sql_list = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
    $stmt_list = $con->prepare($sql_list);
    $stmt_list->bind_param("i", $user_id);
    $stmt_list->execute();
    $list_result = $stmt_list->get_result();
    while ($row = $list_result->fetch_assoc()) {
        $orders_list[] = $row;
    }
}

// --- Mảng dịch trạng thái ---
$status_map = [
    'Pending' => 1,
    'Confirmed' => 2,
    'Shipped' => 3,
    'Delivered' => 4,
    'Completed' => 5,
    'Cancelled' => 0
];

$status_labels = [
    'Pending' => 'Chờ xác nhận',
    'Confirmed' => 'Đã xác nhận',
    'Shipped' => 'Đang giao hàng',
    'Delivered' => 'Đã giao hàng',
    'Completed' => 'Hoàn thành',
    'Cancelled' => 'Đã hủy'
];

function getStatusLabel($status, $labels)
{
    return $labels[$status] ?? $status;
}

function renderStep($step_index, $current_step, $icon, $label, $time)
{
    $isActive = ($current_step > 0 && $step_index <= $current_step);

    if ($current_step == 0) {
        $colorClass = 'text-gray-300 border-gray-300';
        $bgClass = 'bg-white';
    } else {
        $colorClass = $isActive ? 'text-green-500 border-green-500' : 'text-gray-300 border-gray-300';
        $bgClass = 'bg-white';
    }

    echo '
    <div class="flex flex-col items-center w-1/5 relative z-10 group">
        <div class="w-14 h-14 rounded-full border-4 ' . $colorClass . ' ' . $bgClass . ' flex items-center justify-center transition-all duration-300">
            <span class="material-symbols-outlined text-3xl ' . $colorClass . '">' . $icon . '</span>
        </div>
        <div class="text-center mt-3">
            <p class="text-sm font-medium ' . ($isActive ? 'text-black' : 'text-gray-400') . '">' . $label . '</p>
            <p class="text-xs text-gray-500 mt-1">' . $time . '</p>
        </div>
    </div>';
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $view_mode == 'detail' ? 'BookZ | Chi tiết đơn hàng #' . $order['order_id'] : 'BookZ | Lịch sử đơn hàng'; ?>
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="./css/style.css">
    <style>
        html,
        body {
            min-height: 100%;
            font-family: 'Roboto', sans-serif;
        }

        .bookz-border-top {
            background-image: repeating-linear-gradient(45deg, #6fa6d6, #6fa6d6 33px, transparent 0, transparent 41px, #f18d9b 0, #f18d9b 74px, transparent 0, transparent 82px);
            background-position-x: -1.875rem;
            background-size: 7.25rem .1875rem;
            height: .1875rem;
            width: 100%;
        }

        .text-orange {
            color: #ee4d2d;
        }

        /* CSS Timeline Dọc */
        .timeline-vertical {
            border-left: 2px solid #e5e7eb;
            margin-left: 8px;
            padding-left: 24px;
            padding-bottom: 0;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 24px;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-dot {
            position: absolute;
            left: -31px;
            top: 0;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background-color: #d1d5db;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #d1d5db;
        }

        .timeline-dot.active {
            background-color: #22c55e;
            box-shadow: 0 0 0 2px #22c55e;
        }

        .timeline-dot.cancelled {
            background-color: #ef4444;
            box-shadow: 0 0 0 2px #ef4444;
        }

        .timeline-time {
            color: #22c55e;
            font-weight: 500;
            font-size: 0.875rem;
        }

        /* Snow CSS */
        #snow-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            pointer-events: none;
            z-index: 9999;
            overflow: hidden;
        }

        .snowflake {
            position: absolute;
            top: -10px;
            background-color: white;
            border-radius: 50%;
            opacity: 0.7;
            animation: fall linear infinite;
        }

        @keyframes fall {
            0% {
                transform: translateY(0) translateX(0);
            }

            100% {
                transform: translateY(105vh) translateX(var(--drift));
            }
        }
    </style>
</head>

<body class="pb-10 min-h-screen">
    <div id="snow-container"></div>

    <div class="max-w-6xl mx-auto px-4 mt-8 mb-10">

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden min-h-[500px]">

            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <ol class="list-reset flex text-gray-700 text-sm">
                    <li>
                        <?php if ($view_mode == 'detail'): ?>
                            <a href="order_tracking.php" class="text-blue-600 hover:text-blue-800 no-underline font-medium">
                                Đơn hàng của tôi
                            </a>
                        <?php else: ?>
                            <a href="index.php" class="text-blue-600 hover:text-blue-800 no-underline font-medium">
                                Trang chủ
                            </a>
                        <?php endif; ?>
                    </li>
                    <li><span class="mx-2 text-gray-400">/</span></li>
                    <li class="text-gray-500">
                        <?php echo $view_mode == 'detail' ? 'Chi tiết đơn hàng' : 'Đơn hàng của tôi'; ?>
                    </li>
                </ol>
            </div>

            <div class="p-6 md:p-8">

                <?php if ($view_mode == 'detail'): ?>
                    <?php $current_step = $status_map[$order['status']] ?? 1; ?>

                    <div class="mb-0 pb-8 border-b border-gray-100">
                        <div class="relative flex justify-between items-start">
                            <div class="absolute top-7 left-0 w-full h-1 bg-gray-200 -z-0"></div>

                            <?php if ($current_step > 0): ?>
                                <div class="absolute top-7 left-0 h-1 bg-green-500 -z-0 transition-all duration-500"
                                    style="width: <?php echo ($current_step - 1) * 25; ?>%;"></div>
                            <?php endif; ?>

                            <?php
                            renderStep(1, $current_step, 'receipt_long', 'Đơn Hàng Đã Đặt', date('H:i d-m-Y', strtotime($order['order_date'])));
                            renderStep(2, $current_step, 'payments', 'Đã Xác Nhận', '');
                            renderStep(3, $current_step, 'local_shipping', 'Đã Giao ĐVVC', '');
                            renderStep(4, $current_step, 'inventory_2', 'Đã Nhận Hàng', '');
                            renderStep(5, $current_step, 'star', 'Hoàn Thành', '');
                            ?>
                        </div>
                    </div>

                    <div class="bookz-border-top mb-8"></div>
                    <!-- Địa chỉ -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-10 mb-8">
                        <div class="md:col-span-4">
                            <h3 class="text-lg font-bold mb-4 text-gray-800 flex items-center">
                                <span class="material-symbols-outlined mr-2 text-orange">location_on</span>
                                Địa Chỉ Nhận Hàng
                            </h3>
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <p class="font-bold text-gray-900 text-base mb-1">
                                    <?php echo htmlspecialchars($order['customer_name']); ?>
                                </p>
                                <p class="text-gray-500 text-sm mb-2">(+84)
                                    <?php echo htmlspecialchars($order['phone_number']); ?>
                                </p>
                                <p class="text-gray-700 text-sm"><?php echo htmlspecialchars($order['shipping_address']); ?>
                                </p>
                            </div>
                        </div>
                        <!-- Trang thái đơn hàng -->
                        <div class="md:col-span-8 md:border-l md:border-gray-100 md:pl-10">
                            <h3 class="text-lg font-bold mb-4 text-gray-800 flex items-center">
                                <span class="material-symbols-outlined mr-2 text-blue-500">local_shipping</span>
                                Trạng Thái Vận Chuyển
                            </h3>

                            <div class="timeline-vertical mt-3">

                                <?php
                                // ĐỊNH NGHĨA CẤP ĐỘ (LEVEL)
                                $level = 0;
                                switch ($order['status']) {
                                    case 'Pending':
                                        $level = 1;
                                        break; // Chờ xác nhận
                                    case 'Confirmed':
                                        $level = 2;
                                        break; // Đã xác nhận
                                    case 'Shipped':
                                        $level = 3;
                                        break; // Đang giao
                                    case 'Delivered':
                                        $level = 4;
                                        break; // Đã giao
                                    case 'Completed':
                                        $level = 5;
                                        break; // Hoàn thành
                                    case 'Cancelled':
                                        $level = -1;
                                        break; // Hủy
                                }
                                ?>

                                <?php if ($level == -1): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-dot cancelled"></div>
                                        <div class="text-sm">
                                            <span class="text-red-500 font-bold">Đã hủy đơn hàng</span>
                                            <div class="text-gray-500 mt-1">Đơn hàng này đã bị hủy.</div>
                                        </div>
                                    </div>

                                <?php else: ?>

                                    <?php if ($level >= 5): ?>
                                        <div class="timeline-item">
                                            <div class="timeline-dot active"></div>
                                            <div class="text-sm">
                                                <span class="text-green-600 font-bold">Hoàn thành</span>
                                                <div class="text-gray-800 font-medium mt-1">Đơn hàng đã hoàn tất</div>
                                                <div class="text-gray-500">Cảm ơn bạn đã mua sắm tại BookZ.</div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($level >= 4): ?>
                                        <div class="timeline-item">
                                            <div class="timeline-dot <?php echo ($level == 4) ? 'active' : 'bg-gray-300'; ?>"></div>
                                            <div class="text-sm">
                                                <span
                                                    class="<?php echo ($level == 4) ? 'text-green-600 font-bold' : 'text-gray-600 font-medium'; ?>">
                                                    Đã giao hàng
                                                </span>
                                                <div class="text-gray-800 font-medium mt-1">Giao kiện hàng thành công</div>
                                                <?php if ($level == 4): ?>
                                                    <div class="text-gray-500">Bạn hãy kiểm tra và đánh giá sản phẩm nhé.</div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($level >= 3): ?>
                                        <div class="timeline-item">
                                            <div class="timeline-dot <?php echo ($level == 3) ? 'active' : 'bg-gray-300'; ?>"></div>
                                            <div class="text-sm">
                                                <span
                                                    class="<?php echo ($level == 3) ? 'text-green-600 font-bold' : 'text-gray-600 font-medium'; ?>">
                                                    Đang vận chuyển
                                                </span>
                                                <div class="text-gray-800 font-medium mt-1">Đơn hàng đang trên đường đến bạn</div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($level >= 2): ?>
                                        <div class="timeline-item">
                                            <div class="timeline-dot <?php echo ($level == 2) ? 'active' : 'bg-gray-300'; ?>"></div>
                                            <div class="text-sm">
                                                <span
                                                    class="<?php echo ($level == 2) ? 'text-green-600 font-bold' : 'text-gray-600 font-medium'; ?>">
                                                    Đã xác nhận
                                                </span>
                                                <div class="text-gray-800 font-medium mt-1">Người bán đã xác nhận đơn hàng</div>
                                                <?php if ($level == 2): ?>
                                                    <div class="text-gray-500">Đơn hàng đang được đóng gói.</div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($level == 1): ?>
                                        <div class="timeline-item">
                                            <div class="timeline-dot bg-gray-300"></div>
                                            <div class="text-sm">
                                                <span class="text-gray-500 font-bold">Chờ xác nhận</span>
                                                <div class="text-gray-800 font-medium mt-1">Đơn hàng đang được người bán xử lý</div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                <?php endif; ?>

                                <div class="timeline-item">
                                    <div class="timeline-dot <?php echo ($level == 1) ? 'active' : 'bg-gray-300'; ?>"></div>
                                    <div class="text-sm">
                                        <span
                                            class="timeline-time <?php echo ($level == 1) ? 'text-green-600 font-bold' : 'text-gray-500 font-normal'; ?>">
                                            <?php echo date('H:i d-m-Y', strtotime($order['order_date'])); ?>
                                        </span>
                                        <div class="text-gray-800 font-medium mt-1">Đặt hàng thành công</div>
                                        <div class="text-gray-500">Đơn hàng đã được tạo.</div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
                <!-- Sách trong hóa đơn -->
                <div class="mb-8">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded font-bold">Yêu thích</span>
                        <span class="font-bold text-gray-800 text-lg">BookZ Shop</span>
                    </div>

                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <?php
                        $subtotal = 0;
                        foreach ($order_items as $item):
                            $subtotal += $item['price'] * $item['quantity'];
                            ?>
                            <div
                                class="p-4 border-b border-gray-100 flex gap-4 items-start last:border-b-0 hover:bg-gray-50 transition">
                                <img src="img/books/<?php echo $item['product_id']; ?>.jpg"
                                    onerror="this.src='https://placehold.co/80x80?text=No+Image'"
                                    class="w-20 h-20 object-cover border border-gray-200 rounded-md">

                                <div class="flex-1">
                                    <h4 class="text-gray-800 font-medium text-base">
                                        <?php echo htmlspecialchars($item['Title']); ?>
                                    </h4>
                                    <div class="text-gray-500 text-sm mt-1">Số lượng: <span
                                            class="font-bold">x<?php echo $item['quantity']; ?></span></div>
                                </div>
                                <div class="text-right">
                                    <p class="text-orange font-bold text-base"><?php echo number_format($item['price']); ?>đ
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!-- Tổng tiền hàng -->
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-100">
                    <div class="w-full md:w-1/2 ml-auto space-y-3 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Tổng tiền hàng</span>
                            <span class="text-gray-900 font-medium"><?php echo number_format($subtotal); ?>đ</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Phí vận chuyển</span>
                            <span class="text-green-600 font-medium">Miễn phí</span>
                        </div>
                        <div class="flex justify-between items-end pt-4 border-t border-gray-200">
                            <span class="text-gray-800 font-medium text-base">Thành tiền</span>
                            <span
                                class="text-2xl font-bold text-orange"><?php echo number_format($order['total_amount']); ?>đ</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-between text-sm text-gray-700">
                        <div>
                            <?php if ($order['status'] == 'Pending'): ?>
                                <form method="POST" action=""
                                    onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">
                                    <input type="hidden" name="order_id_to_cancel" value="<?php echo $order['order_id']; ?>">
                                    <button type="submit" name="cancel_order"
                                        class="bg-white border border-red-500 text-red-500 px-6 py-2 rounded-lg hover:bg-red-50 font-medium transition flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sm">cancel</span>
                                        Hủy Đơn Hàng
                                    </button>
                                </form>
                            <?php elseif ($order['status'] == 'Cancelled'): ?>
                                <span class="bg-red-100 text-red-600 px-4 py-2 rounded-lg font-medium border border-red-200">Đơn
                                    hàng đã hủy</span>
                            <?php endif; ?>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-orange">payments</span>
                            Phương thức thanh toán: <span
                                class="font-bold uppercase text-gray-900"><?php echo $order['payment_method']; ?></span>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <?php if (empty($orders_list)): ?>
                    <div class="text-center py-16">
                        <div class="bg-gray-50 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-4">
                            <span class="material-symbols-outlined text-5xl text-gray-300">shopping_bag</span>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Bạn chưa có đơn hàng nào</h3>
                        <p class="text-gray-500 mt-2 mb-6">Hãy khám phá thêm các cuốn sách thú vị tại cửa hàng.</p>
                        <a href="index.php"
                            class="inline-block bg-orange-500 text-white px-8 py-3 rounded-full font-medium hover:bg-orange-600 transition shadow-lg shadow-orange-500/30">
                            Mua sắm ngay
                        </a>
                    </div>
                <?php else: ?>
                    <div class="space-y-6">
                        <?php foreach ($orders_list as $history_item): ?>
                            <div class="border border-gray-200 rounded-xl p-6 hover:shadow-md hover:border-orange-200 transition cursor-pointer group"
                                onclick="window.location.href='order_tracking.php?id=<?php echo $history_item['order_id']; ?>'">

                                <div
                                    class="flex flex-col md:flex-row justify-between md:items-center border-b border-gray-100 pb-4 mb-4 gap-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="bg-blue-50 text-blue-600 p-2 rounded-lg group-hover:bg-orange-50 group-hover:text-orange transition-colors">
                                            <span class="material-symbols-outlined">receipt_long</span>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-gray-800 text-lg">Đơn hàng
                                                #<?php echo $history_item['order_id']; ?>
                                            </h3>
                                            <p class="text-sm text-gray-500 flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[16px]">schedule</span>
                                                <?php echo date('H:i d/m/Y', strtotime($history_item['order_date'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <span class="text-center md:text-right">
                                        <?php if ($history_item['status'] == 'Cancelled'): ?>
                                            <span
                                                class="inline-block text-red-500 font-bold uppercase text-xs border border-red-200 bg-red-50 px-3 py-1.5 rounded-full">
                                                Đã hủy
                                            </span>
                                        <?php else: ?>
                                            <span
                                                class="inline-block text-orange font-bold uppercase text-xs border border-orange-200 bg-orange-50 px-3 py-1.5 rounded-full">
                                                <?php echo getStatusLabel($history_item['status'], $status_labels); ?>
                                            </span>
                                        <?php endif; ?>
                                    </span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <div class="text-sm text-gray-600 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-gray-400">payments</span>
                                        <?php echo $history_item['payment_method']; ?>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-gray-500 text-sm">Tổng thanh toán:</span>
                                        <span
                                            class="text-xl font-bold text-orange ml-2"><?php echo number_format($history_item['total_amount']); ?>đ</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            <?php endif; ?>

        </div>
    </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const snowContainer = document.getElementById('snow-container');
            const numberOfSnowflakes = 150;

            for (let i = 0; i < numberOfSnowflakes; i++) {
                const snowflake = document.createElement('div');
                const size = Math.random() * 4 + 1;
                snowflake.className = 'snowflake';
                snowflake.style.width = `${size}px`;
                snowflake.style.height = `${size}px`;
                snowflake.style.left = `${Math.random() * 100}%`;
                snowflake.style.animationDuration = `${Math.random() * 10 + 5}s`;
                snowflake.style.animationDelay = `${Math.random() * 5}s`;
                snowflake.style.setProperty('--drift', `${Math.random() * 200 - 100}px`);
                snowContainer.appendChild(snowflake);
            }
        });
    </script>
</body>

</html>