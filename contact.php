<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'dbconnect.php'; // Chỉ include dbconnect để xử lý logic

$errors = [];
$old_data = [];

// --- XỬ LÝ LOGIC KHI GỬI FORM LIÊN HỆ ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_contact'])) {
    // 1. Lấy và làm sạch dữ liệu, lưu lại dữ liệu cũ để hiển thị lại nếu có lỗi
    $name = htmlspecialchars(trim($_POST['contact_name']));
    $email = htmlspecialchars(trim($_POST['contact_email']));
    $subject = htmlspecialchars(trim($_POST['contact_subject']));
    $message = htmlspecialchars(trim($_POST['contact_message']));
    $old_data = ['name' => $name, 'email' => $email, 'subject' => $subject, 'message' => $message];

    // 2. Xác thực dữ liệu
    if (empty($name)) {
        $errors[] = 'Họ và tên không được để trống.';
    }
    if (empty($email)) {
        $errors[] = 'Email không được để trống.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Định dạng email không hợp lệ.';
    }
    if (empty($subject)) {
        $errors[] = 'Chủ đề không được để trống.';
    }
    if (empty($message)) {
        $errors[] = 'Nội dung tin nhắn không được để trống.';
    }

    // 3. Nếu không có lỗi, tiến hành xử lý
    if (empty($errors)) {
        // Chuẩn bị câu lệnh SQL để chèn dữ liệu
        $stmt = $con->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");

        if ($stmt) {
            $stmt->bind_param("ssss", $name, $email, $subject, $message);

            if ($stmt->execute()) {
                // Thành công: tạo thông báo và chuyển hướng
                $_SESSION['flash_message'] = 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất có thể.'; // Set session message
                $_SESSION['flash_type'] = 'success'; // Set session type
            } else {
                // Thất bại khi thực thi: tạo thông báo lỗi
                $_SESSION['flash_message'] = 'Đã có lỗi xảy ra. Vui lòng thử lại sau.'; // Set session message
                $_SESSION['flash_type'] = 'error'; // Set session type
            }
            $stmt->close();
        } else {
            // Thất bại khi chuẩn bị câu lệnh: tạo thông báo lỗi
            // Ghi log lỗi ở đây sẽ tốt hơn cho việc debug, ví dụ: error_log($con->error);
            $_SESSION['flash_message'] = 'Lỗi hệ thống. Không thể chuẩn bị truy vấn.';
            $_SESSION['flash_type'] = 'error';
        }
        // Chuyển hướng về trang liên hệ để hiển thị thông báo (thành công hoặc lỗi)
        header("Location: contact.php");
        exit();
    }
    // Nếu có lỗi xác thực, script sẽ tiếp tục chạy xuống dưới để hiển thị form với lỗi
}

// --- BẮT ĐẦU HIỂN THỊ GIAO DIỆN ---
include 'header.php'; // Bây giờ mới include header để hiển thị HTML

if (isset($_SESSION['flash_message'])) {
    // Ưu tiên hiển thị thông báo flash từ session (sau khi redirect)
    $swal_script = set_swal(
        $_SESSION['flash_type'],
        'Thông báo',
        $_SESSION['flash_message']
    );
    unset($_SESSION['flash_message'], $_SESSION['flash_type']); // Xóa để không hiển thị lại
} elseif (!empty($errors)) {
    // Nếu không có thông báo flash, hiển thị lỗi xác thực
    $error_message = implode('<br>', $errors);
    $swal_script = set_swal('error', 'Lỗi', $error_message);
}

?>

<style>
    /* Kế thừa các biến CSS từ header.php */
    :root {
        --primary: #0f172a;
        --accent: #d4af37;
        --glass-bg: rgba(255, 255, 255, 0.65);
        --glass-border: 1px solid rgba(255, 255, 255, 0.5);
    }

    /* Hero Section - Tái sử dụng từ deals.php */
    .contact-hero {
        margin-top: 20px;
        padding: 60px 0;
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.08), transparent),
            linear-gradient(225deg, rgba(15, 23, 42, 0.08), transparent);
        border-radius: 24px;
        margin-bottom: 50px;
        text-align: center;
    }

    .contact-title {
        font-family: 'Playfair Display', serif;
        font-size: 3.5rem;
        font-weight: 700;
        color: var(--primary);
    }

    /* Contact Info Card */
    .info-card {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: var(--glass-border);
        border-radius: 20px;
        padding: 30px;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 25px;
    }

    .info-icon {
        width: 45px;
        height: 45px;
        flex-shrink: 0;
        background: var(--primary);
        color: white;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        font-size: 1.1rem;
    }

    .info-content h6 {
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 5px;
    }

    .info-content p {
        margin-bottom: 0;
        color: #475569;
    }

    /* Contact Form */
    .contact-form-wrapper {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 15px 40px rgba(15, 23, 42, 0.08);
        border: 1px solid #e2e8f0;
    }

    .form-control-glass {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 0.85rem 1rem;
    }

    .form-control-glass:focus {
        background-color: white;
        border-color: var(--accent);
        box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
    }

    /* Map */
    .map-container {
        border-radius: 20px;
        overflow: hidden;
        height: 450px;
        border: 1px solid #e2e8f0;
    }
</style>

<!-- ============== Contact Content ==============-->
<div class="container" style="padding-top: 100px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4"
        style="background-color: var(--glass-bg); padding: 15px; border-radius: 12px; backdrop-filter: blur(10px); border: var(--glass-border);">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Liên hệ</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="contact-hero">
        <h5 class="text-muted text-uppercase letter-spacing-2 mb-3">Kết nối với chúng tôi</h5>
        <h1 class="contact-title">Thông Tin Liên Hệ</h1>
        <p class="lead text-muted col-md-7 mx-auto">Bạn có câu hỏi, góp ý hay cần hỗ trợ? Đừng ngần ngại liên hệ với
            BookZ. Chúng tôi luôn sẵn lòng lắng nghe.</p>
    </div>

    <div class="row g-5 mb-5">
        <!-- Cột trái: Thông tin liên hệ -->
        <div class="col-lg-5">
            <div class="info-card">
                <h4 class="mb-4 fw-bold" style="font-family: 'Playfair Display', serif;">Văn phòng BookZ</h4>
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="info-content">
                        <h6>Địa chỉ</h6>
                        <p> Phường Hiệp Bình chánh, Quận Thủ Đức, TP. Hồ Chí Minh</p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
                    <div class="info-content">
                        <h6>Điện thoại</h6>
                        <p>0385782400</p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-envelope"></i></div>
                    <div class="info-content">
                        <h6>Email</h6>
                        <p>hyht9083@ut.edu.vn</p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-clock"></i></div>
                    <div class="info-content">
                        <h6>Giờ làm việc</h6>
                        <p>Thứ 2 - Thứ 7: 08:00 - 17:00</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột phải: Form liên hệ -->
        <div class="col-lg-7">
            <div class="contact-form-wrapper">
                <h4 class="mb-4 fw-bold" style="font-family: 'Playfair Display', serif;">Gửi tin nhắn cho chúng tôi</h4>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label for="contact_name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control form-control-glass" id="contact_name"
                                name="contact_name" value="<?php echo $old_data['name'] ?? ''; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contact_email" class="form-label">Email</label>
                            <input type="email" class="form-control form-control-glass" id="contact_email"
                                name="contact_email" value="<?php echo $old_data['email'] ?? ''; ?>" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="contact_subject" class="form-label">Chủ đề</label>
                            <input type="text" class="form-control form-control-glass" id="contact_subject"
                                name="contact_subject" value="<?php echo $old_data['subject'] ?? ''; ?>" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="contact_message" class="form-label">Nội dung</label>
                            <textarea class="form-control form-control-glass" id="contact_message"
                                name="contact_message" rows="5"
                                required><?php echo $old_data['message'] ?? ''; ?></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="send_contact" class="btn btn-primary-glass btn-lg w-100">Gửi Tin
                                Nhắn</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bản đồ -->
    <div class="map-container mb-5">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.494668303332!2d106.6584303758801!3d10.77301015923149!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752ecb3a2339a5%3A0x8853155d65863863!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBCw6FjaCBraG9hIC0gxJBIUUctVFAuSENN!5e0!3m2!1svi!2s!4v1701072991246!5m2!1svi!2s"
            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</div>

<?php
include 'footer.php'; // Thêm footer để hoàn thiện trang

// In script SweetAlert ra cuối trang, ngay trước khi đóng body
if (!empty($swal_script)) {
    echo $swal_script;
}
?>