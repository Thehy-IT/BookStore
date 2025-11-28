<?php
include 'header.php'; // Bao gồm header để có layout chung
?>

<style>
    .policy-header {
        padding: 60px 0;
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.05), transparent),
            linear-gradient(225deg, rgba(212, 175, 55, 0.05), transparent);
        border-radius: 24px;
        text-align: center;
    }

    .policy-title {
        font-family: 'Playfair Display', serif;
        font-size: 3.5rem;
        font-weight: 700;
        color: var(--primary);
    }

    .policy-content-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(15px);
        border: var(--glass-border);
        border-radius: 20px;
        padding: 40px;
    }

    .policy-content h2 {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        color: var(--primary);
        border-bottom: 2px solid var(--accent);
        padding-bottom: 10px;
        margin-top: 2.5rem;
        margin-bottom: 1.5rem;
    }

    .policy-content h3 {
        font-weight: 600;
        color: #334155;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        font-size: 1.25rem;
    }

    .policy-content p,
    .policy-content li {
        line-height: 1.8;
        color: #475569;
    }
</style>

<!-- ============== Policy Content ==============-->
<div class="container" style="padding-top: 100px; padding-bottom: 50px;">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4"
        style="background-color: var(--glass-bg); padding: 15px; border-radius: 12px; backdrop-filter: blur(10px); border: var(--glass-border);">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chính sách & Điều khoản</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="policy-header mb-5">
        <h5 class="text-muted text-uppercase letter-spacing-2 mb-3">Quy định & Cam kết</h5>
        <h1 class="policy-title">Chính Sách & Điều Khoản</h1>
        <p class="lead text-muted col-md-8 mx-auto">Cảm ơn bạn đã tin tưởng và mua sắm tại BookZ. Vui lòng đọc kỹ các
            chính sách dưới đây để đảm bảo quyền lợi của mình.</p>
    </div>

    <!-- Policy Details -->
    <div class="policy-content-card">
        <div class="policy-content">

            <h2>1. Chính sách Đổi trả</h2>
            <p>BookZ cam kết mang đến những sản phẩm chất lượng. Tuy nhiên, nếu có bất kỳ vấn đề nào, chúng tôi sẵn sàng
                hỗ trợ đổi trả theo các điều kiện sau:</p>
            <h3>Điều kiện đổi trả:</h3>
            <ul>
                <li>Sản phẩm bị lỗi in ấn từ nhà sản xuất (thiếu trang, trang in mờ, ngược...).</li>
                <li>Sản phẩm bị hư hỏng trong quá trình vận chuyển (rách, ướt, biến dạng).</li>
                <li>Giao sai sản phẩm, sai phiên bản so với đơn hàng đã đặt.</li>
            </ul>
            <h3>Thời gian áp dụng:</h3>
            <p>Quý khách vui lòng thông báo cho chúng tôi trong vòng <strong>07 ngày</strong> kể từ ngày nhận hàng.
            </p>
            <h3>Quy trình đổi trả:</h3>
            <ol>
                <li>Liên hệ với bộ phận Chăm sóc khách hàng qua email hoặc hotline, cung cấp mã đơn hàng và hình
                    ảnh/video
                    chứng minh tình trạng sản phẩm.</li>
                <li>Sau khi xác nhận, chúng tôi sẽ hướng dẫn bạn cách thức gửi trả hàng.</li>
                <li>BookZ sẽ gửi lại sản phẩm mới hoặc hoàn tiền 100% giá trị sản phẩm (không bao gồm phí vận chuyển ban
                    đầu) trong vòng 3-5 ngày làm việc sau khi nhận được hàng trả về.</li>
            </ol>
            <p><strong>Lưu ý:</strong> BookZ không áp dụng đổi trả với lý do "không thích", "đổi ý". Sản phẩm đổi trả
                phải còn nguyên vẹn, chưa qua sử dụng.</p>

            <h2>2. Chính sách Bảo mật thông tin</h2>
            <p>BookZ tôn trọng và cam kết bảo mật thông tin cá nhân của khách hàng.</p>
            <h3>Mục đích thu thập thông tin:</h3>
            <ul>
                <li>Xử lý và giao đơn hàng.</li>
                <li>Hỗ trợ khách hàng và giải quyết khiếu nại.</li>
                <li>Cung cấp thông tin về các chương trình khuyến mãi, sản phẩm mới (khi khách hàng đồng ý nhận tin).
                </li>
            </ul>
            <h3>Phạm vi thu thập:</h3>
            <p>Chúng tôi thu thập các thông tin: Họ tên, địa chỉ giao hàng, số điện thoại, email, lịch sử mua hàng.</p>
            <h3>Cam kết bảo mật:</h3>
            <ul>
                <li>Không bán, trao đổi hoặc chia sẻ thông tin cá nhân của khách hàng cho bất kỳ bên thứ ba nào khác,
                    ngoại trừ các đối tác vận chuyển để phục vụ việc giao hàng hoặc khi có yêu cầu của cơ quan pháp
                    luật.</li>
                <li>Dữ liệu của bạn được bảo vệ bằng các biện pháp kỹ thuật và an ninh tiên tiến.</li>
            </ul>

            <h2>3. Chính sách Vận chuyển</h2>
            <h3>Thời gian giao hàng dự kiến:</h3>
            <ul>
                <li><strong>Nội thành TP.HCM & Hà Nội:</strong> 1-2 ngày làm việc.</li>
                <li><strong>Các tỉnh thành khác:</strong> 3-5 ngày làm việc.</li>
            </ul>
            <p>Thời gian có thể thay đổi do các yếu tố khách quan (thời tiết, dịch bệnh, sự kiện...). Chúng tôi sẽ thông
                báo nếu có sự chậm trễ đáng kể.</p>
            <h3>Phí vận chuyển:</h3>
            <p>Phí vận chuyển được tính tự động tại trang thanh toán dựa trên địa chỉ và trọng lượng đơn hàng. Chúng
                tôi thường xuyên có các chương trình <strong>miễn phí vận chuyển</strong> cho các đơn hàng đạt giá trị
                tối thiểu, vui lòng theo dõi tại trang chủ.</p>

            <h2>4. Điều khoản Dịch vụ</h2>
            <h3>Trách nhiệm của người dùng:</h3>
            <ul>
                <li>Cung cấp thông tin chính xác khi đăng ký tài khoản và đặt hàng.</li>
                <li>Tự bảo mật thông tin tài khoản và mật khẩu.</li>
                <li>Không sử dụng website cho các mục đích bất hợp pháp, lừa đảo hoặc gây hại cho người khác.</li>
            </ul>
            <h3>Quyền sở hữu trí tuệ:</h3>
            <p>Mọi nội dung trên website BookZ, bao gồm logo, thiết kế, văn bản, hình ảnh... đều thuộc quyền sở hữu của
                BookZ hoặc các bên cấp phép. Mọi hành vi sao chép, sử dụng mà không có sự cho phép bằng văn bản đều là
                vi phạm.</p>
            <h3>Miễn trừ trách nhiệm:</h3>
            <p>BookZ không chịu trách nhiệm cho bất kỳ thiệt hại nào phát sinh từ việc sử dụng website hoặc sản phẩm
                không đúng cách. Chúng tôi có quyền thay đổi, cập nhật các chính sách và điều khoản này mà không cần
                báo trước.</p>

            <h2>5. Chính sách Thanh toán</h2>
            <p>Chúng tôi cung cấp các phương thức thanh toán an toàn và tiện lợi:</p>
            <ul>
                <li><strong>Thanh toán khi nhận hàng (COD):</strong> Quý khách thanh toán tiền mặt trực tiếp cho nhân
                    viên giao hàng.</li>
                <li><strong>Thanh toán qua ví điện tử:</strong> Momo, ZaloPay, ShopeePay.</li>
                <li><strong>Thanh toán qua cổng VNPAY:</strong> Hỗ trợ thẻ ATM nội địa, thẻ tín dụng/ghi nợ (Visa,
                    Mastercard, JCB).</li>
            </ul>
            <p>Tất cả các giao dịch trực tuyến đều được mã hóa và bảo mật tuyệt đối.</p>

            <hr class="my-5">
            <p class="text-center text-muted"><em>Cập nhật lần cuối: <?php echo date('d/m/Y'); ?>.</em></p>
        </div>
    </div>
</div>

<?php
include 'footer.php'; // Bao gồm footer
?>