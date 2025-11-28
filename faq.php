<?php
include 'header.php'; // Bao gồm header để có layout chung và kết nối CSDL
?>

<style>
    .faq-header {
        padding: 60px 0;
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.05), transparent),
            linear-gradient(225deg, rgba(212, 175, 55, 0.05), transparent);
        border-radius: 24px;
        text-align: center;
    }

    .faq-title {
        font-family: 'Playfair Display', serif;
        font-size: 3.5rem;
        font-weight: 700;
        color: var(--primary);
    }

    .accordion-item {
        background-color: transparent;
        border: 1px solid rgba(0, 0, 0, 0.05);
        margin-bottom: 1rem;
        border-radius: 15px !important;
        overflow: hidden;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .accordion-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.07);
    }

    .accordion-button {
        font-weight: 600;
        color: #334155;
        background-color: rgba(255, 255, 255, 0.5);
        box-shadow: none !important;
        padding: 1.25rem 1.5rem;
    }

    .accordion-button:not(.collapsed) {
        color: var(--primary);
        background-color: rgba(212, 175, 55, 0.1);
    }

    .accordion-button::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%230f172a'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        transition: transform .2s ease-in-out;
    }

    .accordion-button:not(.collapsed)::after {
        transform: rotate(-180deg);
    }

    .accordion-body {
        line-height: 1.7;
        color: #334155;
    }
</style>

<!-- ============== FAQ Content ==============-->
<div class="container" style="padding-top: 100px; padding-bottom: 50px;">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4"
        style="background-color: var(--glass-bg); padding: 15px; border-radius: 12px; backdrop-filter: blur(10px); border: var(--glass-border);">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Câu hỏi thường gặp</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="faq-header mb-5">
        <h5 class="text-muted text-uppercase letter-spacing-2 mb-3">Hỗ trợ</h5>
        <h1 class="faq-title">Câu Hỏi Thường Gặp</h1>
        <p class="lead text-muted col-md-6 mx-auto">Tìm câu trả lời cho các câu hỏi phổ biến nhất về dịch vụ của chúng
            tôi.</p>
    </div>

    <!-- Accordion -->
    <div class="row justify-content-center">
        <div class="col-lg-9">

            <!-- Category: Đặt Hàng & Vận Chuyển -->
            <h4 class="mb-3 mt-4 fw-bold" style="font-family: 'Playfair Display', serif;">Về Đặt Hàng & Vận Chuyển</h4>
            <div class="accordion" id="faqOrdering">
                <!-- Q1 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Làm thế nào để đặt hàng?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                        data-bs-parent="#faqOrdering">
                        <div class="accordion-body">
                            Bạn có thể dễ dàng đặt hàng bằng cách thêm sản phẩm vào giỏ hàng, sau đó tiến hành thanh
                            toán. Điền đầy đủ thông tin giao hàng và chọn phương thức thanh toán phù hợp. Cuối cùng,
                            nhấn "Hoàn tất đặt hàng" để xác nhận.
                        </div>
                    </div>
                </div>
                <!-- Q2 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            BookZ có những phương thức vận chuyển nào?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                        data-bs-parent="#faqOrdering">
                        <div class="accordion-body">
                            Chúng tôi cung cấp dịch vụ vận chuyển tiêu chuẩn trên toàn quốc. Thời gian giao hàng dự kiến
                            từ 2-5 ngày làm việc tùy thuộc vào địa chỉ của bạn. Chúng tôi thường xuyên có các chương
                            trình miễn phí vận chuyển, hãy theo dõi trang Khuyến mãi để biết thêm chi tiết.
                        </div>
                    </div>
                </div>
                <!-- Q3 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Làm cách nào để theo dõi đơn hàng của tôi?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                        data-bs-parent="#faqOrdering">
                        <div class="accordion-body">
                            Sau khi đăng nhập, bạn có thể truy cập vào mục "Đơn mua" từ menu tài khoản ở góc trên bên
                            phải. Tại đây, bạn sẽ thấy danh sách tất cả các đơn hàng đã đặt và trạng thái hiện tại của
                            chúng.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category: Thanh Toán & Khuyến Mãi -->
            <h4 class="mb-3 mt-5 fw-bold" style="font-family: 'Playfair Display', serif;">Về Thanh Toán & Khuyến Mãi
            </h4>
            <div class="accordion" id="faqPayment">
                <!-- Q4 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            BookZ chấp nhận những phương thức thanh toán nào?
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                        data-bs-parent="#faqPayment">
                        <div class="accordion-body">
                            Chúng tôi chấp nhận nhiều phương thức thanh toán linh hoạt, bao gồm:
                            <ul>
                                <li>Thanh toán khi nhận hàng (COD).</li>
                                <li>Thanh toán qua ví điện tử: Momo, ZaloPay, ShopeePay.</li>
                                <li>Thanh toán qua cổng VNPAY (hỗ trợ thẻ ATM nội địa, Visa, Master, JCB).</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Q5 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                            Làm thế nào để sử dụng mã giảm giá?
                        </button>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive"
                        data-bs-parent="#faqPayment">
                        <div class="accordion-body">
                            Tại trang "Thanh toán", bạn sẽ thấy một ô "Mã giảm giá". Hãy nhập mã của bạn vào ô này và
                            nhấn "Áp dụng". Hệ thống sẽ tự động tính toán lại tổng số tiền bạn cần thanh toán.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category: Tài Khoản & Chính Sách -->
            <h4 class="mb-3 mt-5 fw-bold" style="font-family: 'Playfair Display', serif;">Về Tài Khoản & Chính Sách</h4>
            <div class="accordion" id="faqAccount">
                <!-- Q6 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSix">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                            Tôi quên mật khẩu, làm thế nào để lấy lại?
                        </button>
                    </h2>
                    <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix"
                        data-bs-parent="#faqAccount">
                        <div class="accordion-body">
                            Tại cửa sổ Đăng nhập, bạn có thể nhấn vào liên kết "Quên mật khẩu" (chức năng này đang được
                            phát triển). Hiện tại, vui lòng liên hệ bộ phận hỗ trợ của chúng tôi để được cấp lại mật
                            khẩu mới.
                        </div>
                    </div>
                </div>
                <!-- Q7 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSeven">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                            Chính sách đổi trả của BookZ như thế nào?
                        </button>
                    </h2>
                    <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven"
                        data-bs-parent="#faqAccount">
                        <div class="accordion-body">
                            Chúng tôi chấp nhận đổi trả trong vòng 7 ngày kể từ ngày nhận hàng đối với các sản phẩm bị
                            lỗi do nhà sản xuất hoặc hư hỏng trong quá trình vận chuyển. Sản phẩm đổi trả cần còn nguyên
                            vẹn, chưa qua sử dụng. Vui lòng liên hệ với bộ phận hỗ trợ khách hàng của chúng tôi qua mục
                            "Liên hệ" ở cuối trang để được hướng dẫn chi tiết.
                        </div>
                    </div>
                </div>
                <!-- Q8 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingEight">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                            Làm thế nào để tìm một cuốn sách cụ thể?
                        </button>
                    </h2>
                    <div id="collapseEight" class="accordion-collapse collapse" aria-labelledby="headingEight"
                        data-bs-parent="#faqAccount">
                        <div class="accordion-body">
                            Bạn có thể sử dụng thanh tìm kiếm ở đầu trang bằng cách nhập tên sách, tác giả hoặc từ khóa
                            liên quan. Ngoài ra, bạn có thể duyệt sách theo các "Thể loại" có sẵn trong menu chính để
                            khám phá thêm nhiều đầu sách thú vị.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
include 'footer.php'; // Bao gồm footer
?>