<?php
// --- 1. CẤU HÌNH PHP & SESSION ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'dbconnect.php'; // Chỉ include dbconnect để xử lý logic

// --- 2. XỬ LÝ LOGIC TRƯỚC KHI HIỂN THỊ ---

// Kiểm tra đăng nhập sớm
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// XỬ LÝ LOGIC HỦY ĐƠN
if (isset($_POST['cancel_order']) && isset($_POST['order_id_to_cancel'])) {
    $cancel_id = intval($_POST['order_id_to_cancel']);
    $check_stmt = $con->prepare("SELECT status FROM orders WHERE order_id = ? AND user_id = ?");
    $check_stmt->bind_param("ii", $cancel_id, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $order_data = $check_result->fetch_assoc();
        if ($order_data['status'] == 'Pending') {
            $update_stmt = $con->prepare("UPDATE orders SET status = 'Cancelled' WHERE order_id = ?");
            $update_stmt->bind_param("i", $cancel_id);
            if ($update_stmt->execute()) {
                $_SESSION['flash_message'] = "Đã hủy đơn hàng #" . $cancel_id . " thành công.";
                $_SESSION['flash_type'] = "success";
                header("Location: order_tracking.php"); // Chuyển hướng về trang danh sách
                exit();
            }
        }
    }
}

// --- BẮT ĐẦU HIỂN THỊ GIAO DIỆN ---
include 'header.php';

/*
// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    // Nếu chưa đăng nhập, hiển thị thông báo và yêu cầu đăng nhập
    echo "<div class='container text-center py-5' style='margin-top: 80px; min-height: 60vh;'>
            <h3>Vui lòng đăng nhập</h3>
            <p class='text-muted'>Bạn cần đăng nhập để xem lịch sử đơn hàng.</p>
            <button class='btn btn-primary-glass' data-bs-toggle='modal' data-bs-target='#loginModal'>Đăng nhập ngay</button>
          </div>";
    include 'footer.php';
    header("Location: login.php");
    exit();
}
*/

// Hiển thị thông báo flash nếu có
if (isset($_SESSION['flash_message'])) {
    $swal_script = set_swal(
        $_SESSION['flash_type'],
        'Thông báo',
        $_SESSION['flash_message']
    );
    echo $swal_script;
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}

// --- 3. LẤY DỮ LIỆU ---
$view_mode = isset($_GET['id']) ? 'detail' : 'list';
$order = null;
$order_items = [];
$orders_list = [];

if ($view_mode == 'detail') {
    $order_id = intval($_GET['id']);
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

    $sql_items = "SELECT oi.*, p.Title, p.PID, p.Author FROM order_items oi LEFT JOIN products p ON oi.product_id = p.PID WHERE oi.order_id = ?";
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
    $sql_list = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
    $stmt_list = $con->prepare($sql_list);
    $stmt_list->bind_param("i", $user_id);
    $stmt_list->execute();
    $list_result = $stmt_list->get_result();
    while ($row = $list_result->fetch_assoc()) {
        $orders_list[] = $row;
    }
}

// --- 4. HÀM HỖ TRỢ ---
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

function renderStep($step_index, $current_step, $icon, $label, $time, $isCancelled)
{
    $isActive = (!$isCancelled && $current_step > 0 && $step_index <= $current_step);

    $colorClass = 'tw-text-gray-300 tw-border-gray-300';
    $bgClass = 'tw-bg-white';
    $iconColor = 'tw-text-gray-300';
    $ringEffect = '';

    if ($isCancelled) {
        if ($step_index == 1) {
            $colorClass = 'tw-text-red-500 tw-border-red-500';
            $bgClass = 'tw-bg-red-50';
            $iconColor = 'tw-text-red-500';
            $icon = 'cancel';
            $label = 'Đã Hủy';
            $ringEffect = 'tw-ring-4 tw-ring-red-200';
        }
    } elseif ($isActive) {
        $colorClass = 'tw-text-green-500 tw-border-green-500';
        $bgClass = 'tw-bg-white';
        $iconColor = 'tw-text-green-500';
        $ringEffect = 'tw-ring-4 tw-ring-green-500 tw-ring-opacity-100';
    }

    echo '
    <div class="tw-flex tw-flex-col tw-items-center tw-w-1/5 tw-relative tw-z-10 tw-group">
        <div class="tw-w-14 tw-h-14 tw-rounded-full tw-border-4 ' . $colorClass . ' ' . $bgClass . ' ' . $ringEffect . ' tw-flex tw-items-center tw-justify-center tw-transition-all tw-duration-300">
            <span class="material-symbols-outlined tw-text-3xl ' . $iconColor . '">' . $icon . '</span>
        </div>
        <div class="tw-text-center tw-mt-3">
            <p class="tw-text-sm tw-font-medium ' . ($isActive || ($isCancelled && $step_index == 1) ? 'tw-text-black' : 'tw-text-gray-400') . '">' . $label . '</p>
            <p class="tw-text-xs tw-text-gray-500 tw-mt-1">' . $time . '</p>
        </div>
    </div>';
}
?>
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        prefix: 'tw-',
        corePlugins: {
            preflight: false
        }
    }
</script>

<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

<style>
    .bookz-border-top {
        background-image: repeating-linear-gradient(45deg, #6fa6d6, #6fa6d6 33px, transparent 0, transparent 41px, #f18d9b 0, #f18d9b 74px, transparent 0, transparent 82px);
        background-position-x: -1.875rem;
        background-size: 7.25rem .1875rem;
        height: .1875rem;
        width: 100%;
    }

    /* Timeline Styles */
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
</style>

<div class="container" style="padding-top: 100px; padding-bottom: 50px;">
    <div class="tw-max-w-6xl tw-mx-auto tw-w-full">
        <div class="tw-bg-white/70 tw-backdrop-blur-xl tw-rounded-2xl tw-shadow-lg tw-overflow-hidden tw-min-h-[500px] tw-border tw-border-white/50">

            <div class="tw-px-6 tw-py-4 tw-border-b tw-border-gray-100">
                <ol class="tw-list-none tw-flex tw-text-gray-700 tw-text-sm tw-m-0 tw-p-0">
                    <li>
                        <?php if ($view_mode == 'detail'): ?>
                            <a href="order_tracking.php"
                                class="tw-text-blue-600 tw-no-underline tw-font-medium hover:tw-text-blue-800">Đơn hàng của
                                tôi</a>
                        <?php else: ?>
                            <span class="tw-font-medium tw-text-gray-800">Đơn hàng của tôi</span>
                        <?php endif; ?>
                    </li>
                    <li><span class="tw-mx-2 tw-text-gray-400">/</span></li>
                    <li class="tw-text-gray-500">
                        <?php echo $view_mode == 'detail' ? 'Chi tiết đơn hàng' : 'Đơn hàng của tôi'; ?>
                    </li>
                </ol>
            </div>

            <div class="tw-p-4 md:tw-p-8">

                <?php if ($view_mode == 'detail'): ?>
                    <?php
                    $status = trim($order['status']);
                    $isCancelled = ($status === 'Cancelled');
                    $current_step = $isCancelled ? 0 : ($status_map[$status] ?? 1);
                    ?>

                    <div class="tw-mb-0 tw-pb-8 tw-border-b tw-border-gray-100">
                        <div class="tw-relative tw-flex tw-justify-between tw-items-start">

                            <div class="tw-absolute tw-top-7 tw-left-[10%] tw-w-[80%] tw-h-1 tw-bg-gray-200"></div>

                            <?php if (!$isCancelled && $current_step > 1): ?>
                                <div class="tw-absolute tw-top-7 tw-left-[10%] tw-h-1 tw-bg-green-500 tw-transition-all tw-duration-500"
                                    style="width: <?php echo ($current_step - 1) * 20; ?>%;"></div>
                            <?php endif; ?>

                            <?php
                            renderStep(1, $current_step, 'receipt_long', 'Đơn Hàng Đã Đặt', date('H:i d-m-Y', strtotime($order['order_date'])), $isCancelled);
                            renderStep(2, $current_step, 'payments', 'Đã Xác Nhận', '', $isCancelled);
                            renderStep(3, $current_step, 'local_shipping', 'Đã Giao ĐVVC', '', $isCancelled);
                            renderStep(4, $current_step, 'inventory_2', 'Đã Nhận Hàng', '', $isCancelled);
                            renderStep(5, $current_step, 'star', 'Hoàn Thành', '', $isCancelled);
                            ?>
                        </div>
                    </div>

                    <div class="bookz-border-top tw-mb-8"></div>

                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-12 tw-gap-10 tw-mb-8">
                        <div class="md:tw-col-span-4">
                            <h3 class="tw-text-lg tw-font-bold tw-mb-4 tw-text-gray-800 tw-flex tw-items-center" style="color: var(--primary);">
                                <span class="material-symbols-outlined tw-mr-2 text-orange">location_on</span> Địa Chỉ Nhận
                                Hàng
                            </h3>
                            <div class="tw-bg-gray-50 tw-p-4 tw-rounded-xl tw-border tw-border-gray-100">
                                <p class="tw-font-bold tw-text-gray-900 tw-text-base tw-mb-1">
                                    <?php echo htmlspecialchars($order['customer_name']); ?>
                                </p>
                                <p class="tw-text-gray-500 tw-text-sm tw-mb-2">(+84)
                                    <?php echo htmlspecialchars($order['phone_number']); ?>
                                </p>
                                <p class="tw-text-gray-700 tw-text-sm">
                                    <?php echo htmlspecialchars($order['shipping_address']); ?>
                                </p>
                            </div>
                        </div>

                        <div class="md:tw-col-span-8 md:tw-border-l md:tw-border-gray-100 md:tw-pl-10">
                            <h3 class="tw-text-lg tw-font-bold tw-mb-4 tw-text-gray-800 tw-flex tw-items-center" style="color: var(--primary);">
                                <span class="material-symbols-outlined tw-mr-2 tw-text-blue-500">local_shipping</span> Trạng
                                Thái Vận Chuyển
                            </h3>
                            <div class="timeline-vertical tw-mt-3">
                                <?php
                                $level = 0;
                                switch ($order['status']) {
                                    case 'Pending':
                                        $level = 1;
                                        break;
                                    case 'Confirmed':
                                        $level = 2;
                                        break;
                                    case 'Shipped':
                                        $level = 3;
                                        break;
                                    case 'Delivered':
                                        $level = 4;
                                        break;
                                    case 'Completed':
                                        $level = 5;
                                        break;
                                    case 'Cancelled':
                                        $level = -1;
                                        break;
                                }
                                ?>
                                <?php if ($level == -1): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-dot cancelled"></div>
                                        <div class="tw-text-sm">
                                            <span class="tw-text-red-500 tw-font-bold">Đã hủy đơn hàng</span>
                                            <div class="tw-text-gray-500 tw-mt-1">Đơn hàng này đã bị hủy.</div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <?php if ($level >= 5): ?>
                                        <div class="timeline-item">
                                            <div class="timeline-dot <?php echo ($level == 5) ? 'active' : 'tw-bg-gray-300'; ?>">
                                            </div>
                                            <div class="tw-text-sm">
                                                <span
                                                    class="<?php echo ($level == 5) ? 'tw-text-green-600 tw-font-bold' : 'tw-text-gray-400 tw-font-medium'; ?>">Hoàn
                                                    thành</span>
                                                <div class="tw-text-gray-800 tw-font-medium tw-mt-1">Đơn hàng đã hoàn tất</div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($level >= 4): ?>
                                        <div class="timeline-item">
                                            <div class="timeline-dot <?php echo ($level == 4) ? 'active' : 'tw-bg-gray-300'; ?>">
                                            </div>
                                            <div class="tw-text-sm">
                                                <span
                                                    class="<?php echo ($level == 4) ? 'tw-text-green-600 tw-font-bold' : 'tw-text-gray-400 tw-font-medium'; ?>">Đã
                                                    giao hàng</span>
                                                <div class="tw-text-gray-800 tw-font-medium tw-mt-1">Giao kiện hàng thành công</div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($level >= 3): ?>
                                        <div class="timeline-item">
                                            <div class="timeline-dot <?php echo ($level == 3) ? 'active' : 'tw-bg-gray-300'; ?>">
                                            </div>
                                            <div class="tw-text-sm">
                                                <span
                                                    class="<?php echo ($level == 3) ? 'tw-text-green-600 tw-font-bold' : 'tw-text-gray-400 tw-font-medium'; ?>">Đang
                                                    vận chuyển</span>
                                                <div class="tw-text-gray-800 tw-font-medium tw-mt-1">Đơn hàng đang trên đường đến
                                                    bạn</div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($level >= 2): ?>
                                        <div class="timeline-item">
                                            <div class="timeline-dot <?php echo ($level == 2) ? 'active' : 'tw-bg-gray-300'; ?>">
                                            </div>
                                            <div class="tw-text-sm">
                                                <span
                                                    class="<?php echo ($level == 2) ? 'tw-text-green-600 tw-font-bold' : 'tw-text-gray-400 tw-font-medium'; ?>">Đã
                                                    xác nhận</span>
                                                <div class="tw-text-gray-800 tw-font-medium tw-mt-1">Người bán đã xác nhận đơn hàng
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($level >= 1): ?>
                                        <div class="timeline-item">
                                            <div class="timeline-dot tw-bg-gray-300"></div>
                                            <div class="tw-text-sm">
                                                <span class="tw-text-gray-500 tw-font-bold">Chờ xác nhận</span>
                                                <div class="tw-text-gray-800 tw-font-medium tw-mt-1">Đơn hàng đang được người bán xử
                                                    lý</div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <div class="timeline-item">
                                    <div class="timeline-dot <?php echo ($level == 1) ? 'active' : 'tw-bg-gray-300'; ?>">
                                    </div>
                                    <div class="tw-text-sm tw-flex tw-items-center tw-gap-4">
                                        <span
                                            class="timeline-time <?php echo ($level == 1) ? '!tw-text-green-600 tw-font-bold' : '!tw-text-gray-500 tw-font-normal'; ?>">
                                            <?php echo date('H:i d-m-Y', strtotime($order['order_date'])); ?>
                                        </span>
                                        <div class="tw-text-gray-800 tw-font-medium tw-mt-1">Đặt hàng thành công</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tw-mb-6">
                        <div class="tw-flex tw-items-center tw-gap-2 tw-mb-4">
                            <a href="index.php" class="tw-flex tw-items-center tw-gap-2 tw-no-underline">
                                <i class="fas fa-book-open tw-text-yellow-500"></i>
                                <span class="tw-font-bold tw-text-gray-800 tw-text-lg">BookZ Shop</span>
                            </a>
                        </div>

                        <div class="tw-bg-white/50 tw-border tw-border-gray-100 tw-rounded-xl tw-overflow-hidden tw-shadow-sm">
                            <?php
                            $subtotal = 0;
                            foreach ($order_items as $item):
                                $subtotal += $item['price'] * $item['quantity'];
                                ?>
                                <div
                                    class="tw-p-4 tw-border-b tw-border-gray-100 tw-flex tw-gap-4 tw-items-start last:tw-border-b-0 hover:tw-bg-gray-50 tw-transition">
                                    <img src="img/books/<?php echo $item['product_id']; ?>.jpg"
                                        onerror="this.src='https://placehold.co/80x120?text=No+Image'"
                                        class="tw-w-20 tw-h-20 tw-object-cover tw-border tw-border-gray-200 tw-rounded-md">

                                    <div class="tw-flex-1">
                                        <h4 class="tw-text-gray-800 tw-font-medium tw-text-base">
                                            <?php echo htmlspecialchars($item['Title']); ?>
                                        </h4>
                                        <div class="tw-text-gray-500 tw-text-sm tw-mt-1">Số lượng: <span
                                                class="tw-font-bold">x<?php echo $item['quantity']; ?></span></div>
                                    </div>

                                    <div class="tw-text-right">
                                        <p class="tw-font-bold tw-text-base" style="color: var(--primary);">
                                            <?php echo number_format($item['price']); ?>đ
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="tw-bg-white/50 tw-rounded-xl tw-border tw-border-gray-200 tw-shadow-sm tw-overflow-hidden">

                        <div class="tw-p-6 tw-border-b tw-border-gray-100">
                            <div class="tw-w-full md:tw-w-1/2 tw-ml-auto tw-space-y-3 tw-text-sm">
                                <div class="tw-flex tw-justify-between tw-text-gray-600">
                                    <span>Tổng tiền hàng</span>
                                    <span
                                        class="tw-text-gray-900 tw-font-medium"><?php echo number_format($subtotal); ?>đ</span>
                                </div>
                                <div class="tw-flex tw-justify-between tw-text-gray-600">
                                    <span>Phí vận chuyển</span>
                                    <span class="tw-text-green-600 tw-font-medium">Miễn phí</span>
                                </div>
                                <div
                                    class="tw-flex tw-justify-between tw-items-end tw-pt-4 tw-mt-4 tw-border-0 tw-border-t tw-border-gray-200 tw-border-solid">
                                    <span class="tw-text-gray-800 tw-font-medium tw-text-base">Thành tiền</span>
                                    <span
                                        class="tw-text-2xl tw-font-bold" style="color: var(--accent);"><?php echo number_format($order['total_amount']); ?>đ</span>
                                </div>
                            </div>
                        </div>

                        <div
                            class="tw-bg-gray-50/50 tw-p-4 tw-flex tw-flex-col md:tw-flex-row tw-items-center tw-justify-between tw-gap-4">
                            <div>
                                <?php if ($order['status'] == 'Pending'): ?>
                                    <form method="POST" action=""
                                        onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">
                                        <input type="hidden" name="order_id_to_cancel"
                                            value="<?php echo $order['order_id']; ?>">
                                        <button type="submit" name="cancel_order"
                                            class="tw-bg-white tw-border tw-border-red-500 tw-text-red-500 tw-px-6 tw-py-2 tw-rounded-lg hover:tw-bg-red-50 tw-font-medium tw-transition tw-flex tw-items-center tw-gap-2 tw-shadow-sm">
                                            <span class="material-symbols-outlined tw-text-sm">cancel</span> Hủy Đơn Hàng
                                        </button>
                                    </form>
                                <?php elseif ($order['status'] == 'Completed' || $order['status'] == 'Delivered'): ?>
                                    <a href="reorder_action.php?order_id=<?php echo $order['order_id']; ?>"
                                        class="tw-bg-white tw-border tw-border-blue-500 tw-text-blue-500 tw-px-6 tw-py-2 tw-rounded-lg hover:tw-bg-blue-50 tw-font-medium tw-transition tw-flex tw-items-center tw-gap-2 tw-shadow-sm tw-no-underline"
                                        onclick="return confirm('Bạn có chắc chắn muốn thêm tất cả sản phẩm từ đơn hàng này vào giỏ hàng không?');"
                                        title="Thêm tất cả sản phẩm của đơn hàng này vào giỏ hàng hiện tại của bạn">
                                        <span class="material-symbols-outlined tw-text-sm">replay</span> Mua lại
                                    </a>
                                <?php elseif ($order['status'] == 'Cancelled'): ?>
                                    <span
                                        class="tw-bg-red-100 tw-text-red-600 tw-px-4 tw-py-2 tw-rounded-lg tw-font-medium tw-border tw-border-red-200">
                                        Đơn hàng đã hủy
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="tw-flex tw-items-center tw-gap-2 tw-text-sm tw-text-gray-700">
                                <span class="material-symbols-outlined" style="color: var(--accent);">payments</span>
                                Phương thức thanh toán:
                                <span
                                    class="tw-font-bold tw-uppercase tw-text-gray-900"><?php echo $order['payment_method']; ?></span>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <?php if (empty($orders_list)): ?>
                        <div class="tw-text-center tw-py-16">
                            <div
                                class="tw-bg-gray-50 tw-rounded-full tw-w-24 tw-h-24 tw-flex tw-items-center tw-justify-center tw-mx-auto tw-mb-4">
                                <span class="material-symbols-outlined tw-text-5xl tw-text-gray-300">shopping_bag</span>
                            </div>
                            <h3 class="tw-text-xl tw-font-medium tw-text-gray-900">Bạn chưa có đơn hàng nào</h3>
                            <a href="index.php"
                                class="tw-inline-block tw-bg-slate-800 tw-text-white tw-px-8 tw-py-3 tw-rounded-full tw-font-medium hover:tw-bg-yellow-500 tw-transition tw-shadow-lg tw-mt-4 tw-no-underline">Mua
                                sắm ngay</a>
                        </div>
                    <?php else: ?>
                        <div class="tw-space-y-6">
                            <?php foreach ($orders_list as $history_item): ?>
                                <div class="tw-border tw-border-gray-200 tw-rounded-xl tw-p-6 hover:tw-shadow-md hover:tw-border-yellow-300 tw-transition tw-cursor-pointer tw-group"
                                    onclick="window.location.href='order_tracking.php?id=<?php echo $history_item['order_id']; ?>'">

                                    <div
                                        class="tw-flex tw-flex-col md:tw-flex-row tw-justify-between md:tw-items-center tw-border-b tw-border-gray-100 tw-pb-4 tw-mb-4 tw-gap-4">
                                        <div class="tw-flex tw-items-center tw-gap-3">
                                            <div
                                                class="tw-bg-blue-50 tw-text-blue-600 tw-p-2 tw-rounded-lg group-hover:tw-bg-yellow-50 group-hover:tw-text-yellow-600 tw-transition-colors">
                                                <span class="material-symbols-outlined">receipt_long</span>
                                            </div>
                                            <div>
                                                <h3 class="tw-font-bold tw-text-gray-800 tw-text-lg">Đơn hàng
                                                    #<?php echo $history_item['order_id']; ?></h3>
                                                <p class="tw-text-sm tw-text-gray-500 tw-flex tw-items-center tw-gap-1">
                                                    <span
                                                        class="material-symbols-outlined tw-text-[16px]">schedule</span><?php echo date('H:i d/m/Y', strtotime($history_item['order_date'])); ?>
                                                </p>
                                            </div>
                                        </div>
                                        <span class="tw-text-center md:tw-text-right">
                                            <?php if ($history_item['status'] == 'Cancelled'): ?>
                                                <span
                                                    class="tw-inline-block tw-text-red-500 tw-font-bold tw-uppercase tw-text-xs tw-border tw-border-red-200 tw-bg-red-50 tw-px-3 tw-py-1.5 tw-rounded-full">Đã
                                                    hủy</span>
                                            <?php else: ?>
                                                <span class="tw-inline-block tw-font-bold tw-uppercase tw-text-xs tw-border tw-px-3 tw-py-1.5 tw-rounded-full" style="color: var(--primary); border-color: #e2e8f0; background-color: #f8fafc;">
                                                    <?php echo getStatusLabel($history_item['status'], $status_labels); ?>
                                                </span>
                                            <?php endif; ?>
                                        </span>
                                    </div>

                                    <div class="tw-flex tw-justify-between tw-items-center">
                                        <div class="tw-text-sm tw-text-gray-600 tw-flex tw-items-center tw-gap-2">
                                            <span class="material-symbols-outlined tw-text-gray-400">payments</span>
                                            <?php echo $history_item['payment_method']; ?>
                                        </div>
                                        <div class="tw-text-right">
                                            <span class="tw-text-gray-500 tw-text-sm">Tổng thanh toán:</span>
                                            <span
                                                class="tw-text-xl tw-font-bold tw-ml-2" style="color: var(--accent);"><?php echo number_format($history_item['total_amount']); ?>đ</span>
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
</div>

<?php include "footer.php"; ?>