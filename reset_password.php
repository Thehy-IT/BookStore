<?php
session_start();
include "dbconnect.php";

$message = "";
$message_type = "";
$show_form = false;

// 1. Kiểm tra token
if (!isset($_GET['token'])) {
    $message = "Token không hợp lệ hoặc đã hết hạn.";
    $message_type = "error";
} else {
    $token = $_GET['token'];
    $stmt = $con->prepare("SELECT UserID FROM users WHERE reset_token = ? AND reset_token_expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $show_form = true;
        $user = $result->fetch_assoc();
        $user_id = $user['UserID'];
    } else {
        $message = "Token không hợp lệ hoặc đã hết hạn.";
        $message_type = "error";
    }
    $stmt->close();
}

// 2. Xử lý submit form
if (isset($_POST['submit']) && $show_form) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($password) || empty($confirm_password)) {
        $message = "Vui lòng nhập đầy đủ mật khẩu.";
        $message_type = "warning";
    } elseif ($password !== $confirm_password) {
        $message = "Mật khẩu xác nhận không khớp.";
        $message_type = "error";
    } else {
        // Cập nhật mật khẩu mới
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_stmt = $con->prepare("UPDATE users SET Password = ?, reset_token = NULL, reset_token_expires_at = NULL WHERE UserID = ?");
        $update_stmt->bind_param("si", $hashed_password, $user_id);

        if ($update_stmt->execute()) {
            $_SESSION['flash_message'] = "Mật khẩu của bạn đã được cập nhật thành công! Vui lòng đăng nhập lại.";
            $_SESSION['flash_type'] = "success";
            header("Location: login.php");
            exit();
        } else {
            $message = "Lỗi hệ thống, không thể cập nhật mật khẩu.";
            $message_type = "error";
        }
        $update_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password | BookZ Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: #f0f4f8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>
    <div class="bg-blobs"></div>
    <div class="glass-card" style="max-width: 500px;">
        <div class="text-center mb-4">
            <h2 class="fw-bold font-playfair">Đặt lại mật khẩu</h2>
            <p class="text-muted">Tạo một mật khẩu mới cho tài khoản của bạn.</p>
        </div>

        <?php if ($show_form) : ?>
            <form action="" method="post">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                <!-- Password -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary small">Mật khẩu mới</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 ps-0"><i class="fas fa-lock text-muted"></i></span>
                        <input type="password" class="form-control" name="password" placeholder="Tạo mật khẩu mới" required>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary small">Xác nhận mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 ps-0"><i class="fas fa-check-circle text-muted"></i></span>
                        <input type="password" class="form-control" name="confirm_password" placeholder="Nhập lại mật khẩu mới" required>
                    </div>
                </div>

                <button type="submit" name="submit" class="btn btn-glass w-100 mb-3">
                    Lưu mật khẩu mới
                </button>
            </form>
        <?php endif; ?>
    </div>

    <?php if (!empty($message)) : ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '<?php echo $message_type; ?>',
                    title: 'Thông báo',
                    text: '<?php echo addslashes($message); ?>',
                    confirmButtonColor: '#0f172a'
                }).then(() => {
                    <?php if ($message_type == 'error' && !$show_form) : ?>
                        window.location.href = 'login.php';
                    <?php endif; ?>
                });
            });
        </script>
    <?php endif; ?>

</body>

</html>
