<?php
session_start();
include "dbconnect.php";

$message = "";
$message_type = "";

if (isset($_POST['submit']) && $_POST['submit'] == "register") {
    // 1. Lấy dữ liệu và làm sạch
    $username = trim($_POST['register_username']);
    $password = $_POST['register_password'];
    $confirm_password = $_POST['confirm_password'];

    // 2. Validate cơ bản
    if (empty($username) || empty($password)) {
        $message = "Vui lòng điền đầy đủ thông tin.";
        $message_type = "warning";
    } elseif ($password !== $confirm_password) {
        $message = "Mật khẩu xác nhận không khớp.";
        $message_type = "error";
    } else {
        // 3. Kiểm tra Username đã tồn tại chưa (Dùng Prepared Statement)
        $stmt = $con->prepare("SELECT UserName FROM users WHERE UserName = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "Tên đăng nhập '$username' đã được sử dụng.";
            $message_type = "error";
        } else {
            // 4. Mã hóa mật khẩu (Quan trọng!)
            // Mật khẩu sẽ được biến đổi thành chuỗi ký tự ngẫu nhiên an toàn
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // 5. Thêm vào CSDL (Chỉ định rõ cột UserName và Password)
            // Lưu ý: Cấu trúc bảng users cần có cột (UserName, Password)
            $insert_stmt = $con->prepare("INSERT INTO users (UserName, Password) VALUES (?, ?)");
            $insert_stmt->bind_param("ss", $username, $hashed_password);

            if ($insert_stmt->execute()) {
                $message = "Đăng ký thành công! Bạn có thể đăng nhập ngay.";
                $message_type = "success";
                // Tự động chuyển hướng sau 2 giây (Xử lý bằng JS bên dưới)
            } else {
                $message = "Lỗi hệ thống: " . $con->error;
                $message_type = "error";
            }
            $insert_stmt->close();
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | BookZ Store</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #0f172a;
            --accent: #d4af37;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f0f4f8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }

        /* --- BACKGROUND BLOBS --- */
        .bg-blobs {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: radial-gradient(circle at 80% 10%, rgba(212, 175, 55, 0.15), transparent 40%),
                radial-gradient(circle at 20% 90%, rgba(15, 23, 42, 0.15), transparent 40%);
        }

        /* --- GLASS CARD --- */
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 500px;
        }

        h2 {
            font-family: 'Playfair Display', serif;
            color: var(--primary);
        }

        .form-control {
            background: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 12px 15px;
            transition: 0.3s;
        }

        .form-control:focus {
            background: white;
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.15);
            border-color: var(--accent);
        }

        .btn-glass {
            background: var(--primary);
            color: white;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.2);
        }

        .btn-glass:hover {
            background: var(--accent);
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(212, 175, 55, 0.3);
        }
    </style>
</head>

<body>

    <div class="bg-blobs"></div>

    <div class="glass-card fade-in-up">
        <div class="text-center mb-4">
            <h2 class="fw-bold">Create Account</h2>
            <p class="text-muted">Join our community of book lovers</p>
        </div>

        <form action="" method="post">
            <!-- Username -->
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary small">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0 ps-0"><i class="fas fa-user text-muted"></i></span>
                    <input type="text" class="form-control" name="register_username" placeholder="Choose a username" required>
                </div>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary small">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0 ps-0"><i class="fas fa-lock text-muted"></i></span>
                    <input type="password" class="form-control" name="register_password" placeholder="Create a password" required>
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <label class="form-label fw-bold text-secondary small">Confirm Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0 ps-0"><i class="fas fa-check-circle text-muted"></i></span>
                    <input type="password" class="form-control" name="confirm_password" placeholder="Repeat your password" required>
                </div>
            </div>

            <button type="submit" name="submit" value="register" class="btn btn-glass w-100 mb-3">
                Sign Up Now
            </button>

            <div class="text-center mt-4">
                <span class="text-muted">Already have an account?</span>
                <a href="login.php" class="text-primary fw-bold text-decoration-none ms-1">Log In</a>
            </div>
        </form>
    </div>

    <!-- Script xử lý thông báo -->
    <?php if (!empty($message)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '<?php echo $message_type; ?>',
                    title: '<?php echo ($message_type == "success" ? "Success!" : "Notice"); ?>',
                    text: '<?php echo $message; ?>',
                    confirmButtonColor: '#0f172a',
                    background: 'rgba(255, 255, 255, 0.95)',
                }).then((result) => {
                    // Nếu đăng ký thành công, chuyển hướng về trang login
                    if ('<?php echo $message_type; ?>' === 'success') {
                        window.location.href = 'login.php';
                    }
                });
            });
        </script>
    <?php endif; ?>

</body>

</html>