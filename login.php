<?php
session_start();
include "dbconnect.php"; // Đảm bảo file này kết nối đúng DB

$message = "";
$message_type = "";

// --- XỬ LÝ LOGIC ĐĂNG NHẬP ---
if (isset($_POST['submit']) && $_POST['submit'] == "login") {

  $username = trim($_POST['login_username']);
  $password_input = $_POST['login_password'];

  // 1. Sử dụng Prepared Statement để an toàn
  // Lưu ý: Chúng ta SELECT * để lấy cả cột Role
  if ($stmt = $con->prepare("SELECT * FROM users WHERE UserName = ?")) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();

      // 2. Kiểm tra mật khẩu (Dùng password_verify cho mật khẩu đã mã hóa)
      if (password_verify($password_input, $row['Password'])) {

        // 3. Lưu thông tin vào Session
        $_SESSION['user'] = $row['UserName'];
        $_SESSION['user_id'] = $row['UserID']; // Lưu ID để dùng cho các việc khác
        $_SESSION['role'] = $row['Role'];       // QUAN TRỌNG: Lưu quyền hạn (admin/user)

        // 4. Chuyển hướng dựa trên quyền (Role)
        if ($row['Role'] == 'admin') {
          header("Location: admin.php");
        } else {
          // Đặt một session flash để hiển thị thông báo trên trang chủ
          $_SESSION['flash_message'] = "Đăng nhập thành công! Chào mừng trở lại, " . htmlspecialchars($row['UserName']) . ".";
          $_SESSION['flash_type'] = "success";
          header("Location: index.php");
        }
        exit();
      } else {
        $message = "Mật khẩu không chính xác.";
        $message_type = "error";
      }
    } else {
      $message = "Tài khoản không tồn tại.";
      $message_type = "error";
    }
    $stmt->close();
  } else {
    $message = "Lỗi kết nối cơ sở dữ liệu.";
    $message_type = "error";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | BookZ Store</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <!-- Bootstrap 5 & Icons -->
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
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      position: relative;
    }

    /* --- BACKGROUND BLOBS --- */
    .bg-blobs {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
      background: radial-gradient(circle at 15% 15%, rgba(212, 175, 55, 0.2), transparent 40%),
        radial-gradient(circle at 85% 85%, rgba(15, 23, 42, 0.15), transparent 40%);
    }

    /* --- GLASS LOGIN CARD --- */
    .glass-card {
      background: rgba(255, 255, 255, 0.75);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.8);
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
      border-radius: 24px;
      padding: 40px;
      width: 100%;
      max-width: 450px;
      position: relative;
      z-index: 10;
    }

    h2 {
      font-family: 'Playfair Display', serif;
      color: var(--primary);
    }

    /* --- INPUTS --- */
    .form-control {
      background: rgba(255, 255, 255, 0.6);
      border: 1px solid rgba(0, 0, 0, 0.1);
      border-radius: 12px;
      padding: 12px 15px;
      backdrop-filter: blur(5px);
    }

    .form-control:focus {
      background: rgba(255, 255, 255, 0.9);
      box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.15);
      border-color: var(--accent);
    }

    /* --- BUTTON --- */
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

    .back-link {
      text-decoration: none;
      color: #64748b;
      font-size: 0.9rem;
      transition: 0.3s;
    }

    .back-link:hover {
      color: var(--primary);
    }
  </style>
</head>

<body>

  <div class="bg-blobs"></div>

  <div class="glass-card">
    <div class="text-center mb-4">
      <h2 class="fw-bold mb-1">Welcome Back</h2>
      <p class="text-muted">Please sign in to access your account</p>
    </div>

    <form action="" method="post">
      <div class="mb-3">
        <label class="form-label fw-bold text-secondary small">Username</label>
        <div class="input-group">
          <span class="input-group-text bg-transparent border-0 ps-0"><i class="fas fa-user text-muted"></i></span>
          <input type="text" class="form-control" name="login_username" placeholder="Enter your username" required>
        </div>
      </div>

      <div class="mb-4">
        <label class="form-label fw-bold text-secondary small">Password</label>
        <div class="input-group">
          <span class="input-group-text bg-transparent border-0 ps-0"><i class="fas fa-lock text-muted"></i></span>
          <input type="password" class="form-control" name="login_password" placeholder="Enter your password" required>
        </div>
      </div>

      <button type="submit" name="submit" value="login" class="btn btn-glass w-100 mb-3">
        Sign In <i class="fas fa-arrow-right ms-2"></i>
      </button>

      <div class="d-flex justify-content-between align-items-center mt-4">
        <a href="index.php" class="back-link"><i class="fas fa-arrow-left me-1"></i> Back to Home</a>
        <a href="register.php" class="text-primary fw-bold text-decoration-none">Create Account</a>
      </div>
    </form>
  </div>

  <!-- Hiển thị thông báo lỗi (nếu có) -->
  <?php if (!empty($message)): ?>
    <script>
      Swal.fire({
        icon: '<?php echo $message_type; ?>',
        title: '<?php echo ($message_type == "error" ? "Login Failed" : "Success"); ?>',
        text: '<?php echo $message; ?>',
        confirmButtonColor: '#0f172a',
        background: 'rgba(255, 255, 255, 0.95)',
        backdrop: `rgba(0,0,0,0.4)`
      });
    </script>
  <?php endif; ?>

</body>

</html>