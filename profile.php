<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'dbconnect.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_message'] = "Bạn cần đăng nhập để truy cập trang này.";
    $_SESSION['flash_type'] = "warning";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Lấy thông tin hiện tại của người dùng
$stmt = $con->prepare("SELECT UserName, FullName, Email, PhoneNumber, Address FROM users WHERE UserID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

if (!$user_data) {
    // Xử lý trường hợp không tìm thấy user (hiếm khi xảy ra nếu đã đăng nhập)
    session_destroy();
    header("Location: login.php");
    exit();
}

// --- BẮT ĐẦU HIỂN THỊ GIAO DIỆN ---
include 'header.php';

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
?>

<style>
    .profile-card {
        background: var(--glass-bg);
        backdrop-filter: blur(15px);
        border: var(--glass-border);
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
    }

    .form-control-glass {
        background-color: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .form-control-glass:focus {
        background-color: white;
        border-color: var(--accent);
        box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
    }

    .btn-save {
        background-color: var(--primary);
        color: white;
        border-radius: 50px;
        padding: 10px 30px;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-save:hover {
        background-color: var(--accent);
        transform: translateY(-2px);
    }
</style>

<div class="container" style="padding-top: 100px; padding-bottom: 50px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4"
        style="background-color: var(--glass-bg); padding: 15px; border-radius: 12px; backdrop-filter: blur(10px); border: var(--glass-border);">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Thông tin tài khoản</li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Cột trái: Thông tin cá nhân -->
        <div class="col-lg-6">
            <div class="profile-card h-100">
                <h4 class="fw-bold mb-4" style="font-family: 'Playfair Display', serif;">Thông tin cá nhân</h4>
                <form action="profile_action.php" method="POST">
                    <input type="hidden" name="action" value="update_info">
                    <div class="mb-3">
                        <label for="username" class="form-label">Tên đăng nhập</label>
                        <input type="text" class="form-control form-control-glass" id="username"
                            value="<?php echo htmlspecialchars($user_data['UserName']); ?>" disabled readonly>
                    </div>
                    <div class="mb-3">
                        <label for="fullname" class="form-label">Họ và tên</label>
                        <input type="text" class="form-control form-control-glass" id="fullname" name="fullname"
                            value="<?php echo htmlspecialchars($user_data['FullName']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control form-control-glass" id="email" name="email"
                            value="<?php echo htmlspecialchars($user_data['Email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phonenumber" class="form-label">Số điện thoại</label>
                        <input type="tel" class="form-control form-control-glass" id="phonenumber" name="phonenumber"
                            value="<?php echo htmlspecialchars($user_data['PhoneNumber'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Địa chỉ</label>
                        <textarea class="form-control form-control-glass" id="address" name="address"
                            rows="3"><?php echo htmlspecialchars($user_data['Address'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-save">Lưu thay đổi</button>
                </form>
            </div>
        </div>

        <!-- Cột phải: Đổi mật khẩu -->
        <div class="col-lg-6">
            <div class="profile-card h-100">
                <h4 class="fw-bold mb-4" style="font-family: 'Playfair Display', serif;">Đổi mật khẩu</h4>
                <form action="profile_action.php" method="POST">
                    <input type="hidden" name="action" value="update_password">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                        <input type="password" class="form-control form-control-glass" id="current_password"
                            name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Mật khẩu mới</label>
                        <input type="password" class="form-control form-control-glass" id="new_password"
                            name="new_password" required>
                        <small class="form-text text-muted">Mật khẩu phải có ít nhất 6 ký tự.</small>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_new_password" class="form-label">Xác nhận mật khẩu mới</label>
                        <input type="password" class="form-control form-control-glass" id="confirm_new_password"
                            name="confirm_new_password" required>
                    </div>
                    <button type="submit" class="btn btn-save">Đổi mật khẩu</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>