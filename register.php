<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'dbconnect.php'; // Đảm bảo bạn đã có file kết nối CSDL

// Nếu người dùng đã đăng nhập, chuyển hướng họ về trang chủ
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$errors = [];
$username = $email = $fullname = $phonenumber = $address = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy và làm sạch dữ liệu
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $fullname = trim($_POST['fullname']);
    $phonenumber = trim($_POST['phonenumber']);
    $address = trim($_POST['address']);

    // --- Bắt đầu Validate ---
    if (empty($username)) {
        $errors[] = "Tên đăng nhập là bắt buộc.";
    }

    if (empty($email)) {
        $errors[] = "Email là bắt buộc.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Định dạng email không hợp lệ.";
    }

    if (empty($password)) {
        $errors[] = "Mật khẩu là bắt buộc.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Mật khẩu phải có ít nhất 6 ký tự.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Mật khẩu xác nhận không khớp.";
    }

    if (empty($fullname)) {
        $errors[] = "Họ và tên là bắt buộc.";
    }

    // Kiểm tra username hoặc email đã tồn tại chưa
    if (empty($errors)) {
        $sql = "SELECT UserID FROM users WHERE UserName = ? OR Email = ?";
        if ($stmt = $con->prepare($sql)) {
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $errors[] = "Tên đăng nhập hoặc Email đã tồn tại.";
            }
            $stmt->close();
        }
    }
    // --- Kết thúc Validate ---

    // Nếu không có lỗi, tiến hành đăng ký
    if (empty($errors)) {
        // Mã hóa mật khẩu
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (UserName, Email, Password, FullName, PhoneNumber, Address) VALUES (?, ?, ?, ?, ?, ?)";

        if ($stmt = $con->prepare($sql)) {
            $stmt->bind_param("ssssss", $username, $email, $hashed_password, $fullname, $phonenumber, $address);

            if ($stmt->execute()) {
                // Đăng ký thành công, chuyển hướng đến trang đăng nhập
                $_SESSION['flash_message'] = "Đăng ký tài khoản thành công! Vui lòng đăng nhập.";
                $_SESSION['flash_type'] = "success";
                header("location: login.php");
                exit();
            } else {
                $errors[] = "Đã có lỗi xảy ra. Vui lòng thử lại sau.";
            }
            $stmt->close();
        }
    }
    // Không cần đóng kết nối ở đây nếu các file khác còn dùng
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký Tài Khoản - BookZ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css"> <!-- File CSS tùy chỉnh của bạn -->
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4">Tạo Tài Khoản Mới</h2>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <?php foreach ($errors as $error): ?>
                                    <p class="mb-0"><?php echo $error; ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="mb-3">
                                <label for="fullname" class="form-label">Họ và Tên</label>
                                <input type="text" name="fullname" id="fullname" class="form-control"
                                    value="<?php echo htmlspecialchars($fullname); ?>" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">Tên đăng nhập</label>
                                    <input type="text" name="username" id="username" class="form-control"
                                        value="<?php echo htmlspecialchars($username); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" id="email" class="form-control"
                                        value="<?php echo htmlspecialchars($email); ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Mật khẩu</label>
                                    <input type="password" name="password" id="password" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                                    <input type="password" name="confirm_password" id="confirm_password"
                                        class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="phonenumber" class="form-label">Số điện thoại</label>
                                <input type="tel" name="phonenumber" id="phonenumber" class="form-control"
                                    value="<?php echo htmlspecialchars($phonenumber); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Địa chỉ</label>
                                <textarea name="address" id="address" class="form-control"
                                    rows="3"><?php echo htmlspecialchars($address); ?></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Đăng Ký</button>
                            </div>
                            <p class="text-center mt-3">
                                Đã có tài khoản? <a href="login.php">Đăng nhập tại đây</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

```

### Bước 3: Cải thiện Form và Logic Đăng nhập (`login.php`)

Bây giờ, tôi sẽ tạo tệp `login.php`. Điểm cải tiến quan trọng ở đây là cho phép người dùng đăng nhập bằng **cả Tên đăng
nhập hoặc Email**.

```diff