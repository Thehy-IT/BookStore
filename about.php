<?php
// Giả định rằng bạn có một tệp header.php chứa phần đầu của trang HTML,
// bao gồm session_start(), kết nối CSDL, và thẻ <head> với các CSS cần thiết.
include 'header.php';
?>

<style>
    /* Custom styles for About Us page */
    .about-hero {
        background: url('img/about/br.png') no-repeat center center;
        background-size: cover;
        padding: 120px 0;
        position: relative;
        color: white;
    }

    .about-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
    }

    .about-hero .container {
        position: relative;
        z-index: 2;
    }

    .value-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .value-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.1) !important;
    }

    .team-member img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 4px solid var(--accent);
    }

    .team-member {
        transition: transform 0.3s ease;
    }

    .team-member:hover {
        transform: scale(1.05);
    }

    .testimonial-card {
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.07);
    }

    .testimonial-card img {
        width: 70px;
        height: 70px;
        object-fit: cover;
    }

    .section-title {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
    }
</style>

<main>
    <!-- ============== Hero Section ==============-->
    <section class="about-hero text-center">
        <div class="container fade-in-element">
            <h1 class="display-3 fw-bold font-playfair">Câu Chuyện Về BookZ</h1>
            <p class="lead fs-4 text-white-50">Nơi mỗi trang sách mở ra một thế giới mới.</p>
        </div>
    </section>

    <!-- ============== Our Story Section ==============-->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 fade-in-element">
                    <img src="img/about/s.png" class="img-fluid rounded-4 shadow-lg" alt="Reading a book">
                </div>
                <div class="col-lg-6 fade-in-element">
                    <h2 class="section-title mb-3">Sứ Mệnh Của Chúng Tôi</h2>
                    <p class="text-muted">BookZ được sinh ra từ niềm đam mê bất tận với sách và khát khao lan tỏa văn
                        hóa đọc. Chúng tôi tin rằng mỗi cuốn sách là một cánh cửa dẫn đến tri thức, sự sáng tạo và những
                        cuộc phiêu lưu kỳ thú. Sứ mệnh của chúng tôi là trở thành cầu nối, mang những tác phẩm tinh hoa
                        đến gần hơn với độc giả Việt Nam.</p>
                    <p class="text-muted">Chúng tôi không chỉ bán sách, chúng tôi tuyển chọn và giới thiệu những câu
                        chuyện đáng đọc, những kiến thức đáng giá, và những trải nghiệm đọc cao cấp cho độc giả hiện
                        đại.</p>
                    <a href="Product.php" class="btn btn-primary rounded-pill px-4 mt-3">Khám Phá Tủ Sách <i
                            class="fas fa-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- ============== Core Values Section ==============-->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5 fade-in-element">
                <h2 class="section-title">Giá Trị Cốt Lõi</h2>
                <p class="text-muted">Những nguyên tắc định hình nên BookZ.</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6 fade-in-element">
                    <div class="card h-100 border-0 shadow-sm p-4 text-center value-card">
                        <div class="mb-3">
                            <i class="fas fa-book-heart fa-3x text-warning"></i>
                        </div>
                        <h5 class="fw-bold">Đam Mê Sách</h5>
                        <p class="text-muted small">Chúng tôi là những người yêu sách, và chúng tôi muốn chia sẻ tình
                            yêu đó với bạn qua từng sản phẩm được lựa chọn cẩn thận.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 fade-in-element">
                    <div class="card h-100 border-0 shadow-sm p-4 text-center value-card">
                        <div class="mb-3">
                            <i class="fas fa-award fa-3x text-warning"></i>
                        </div>
                        <h5 class="fw-bold">Tuyển Chọn Chất Lượng</h5>
                        <p class="text-muted small">Mỗi cuốn sách trên kệ của BookZ đều được đội ngũ biên tập viên tâm
                            huyết tuyển chọn, đảm bảo về nội dung và hình thức.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 fade-in-element">
                    <div class="card h-100 border-0 shadow-sm p-4 text-center value-card">
                        <div class="mb-3">
                            <i class="fas fa-users-cog fa-3x text-warning"></i>
                        </div>
                        <h5 class="fw-bold">Trải Nghiệm Khách Hàng</h5>
                        <p class="text-muted small">Sự hài lòng của bạn là ưu tiên hàng đầu. Chúng tôi cam kết mang đến
                            dịch vụ tận tâm và trải nghiệm mua sắm tuyệt vời.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============== Meet The Team Section ==============-->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5 fade-in-element">
                <h2 class="section-title">Gặp Gỡ Đội Ngũ BookZ</h2>
                <p class="text-muted">Những con người đứng sau hành trình lan tỏa tri thức.</p>
            </div>
            <div class="row g-5 justify-content-center">
                <div class="col-lg-2 col-md-4 col-6 text-center team-member fade-in-element">
                    <img src="img/about/m0.png" class="rounded-circle mb-3 shadow" alt="Team Member 1">
                    <h5 class="fw-bold mb-1">Huỳnh Thế HY</h5>
                    <p class="text-warning fw-bold">Nhóm trưởng</p>
                </div>
                <div class="col-lg-2 col-md-4 col-6 text-center team-member fade-in-element">
                    <img src="img/about/m1.jpg" class="rounded-circle mb-3 shadow" alt="Team Member 2">
                    <h5 class="fw-bold mb-1">Lê Nhật Anh</h5>
                    <p class="text-muted">Thành viên</p>
                </div>
                <div class="col-lg-2 col-md-4 col-6 text-center team-member fade-in-element">
                    <img src="img/about/m2.jpg" class="rounded-circle mb-3 shadow" alt="Team Member 3">
                    <h5 class="fw-bold mb-1">Lê Quốc Dũng</h5>
                    <p class="text-muted">Thành viên</p>
                </div>
                <div class="col-lg-2 col-md-4 col-6 text-center team-member fade-in-element">
                    <img src="img/about/m4.jpg" class="rounded-circle mb-3 shadow" alt="Team Member 4">
                    <h5 class="fw-bold mb-1">Văn Hân</h5>
                    <p class="text-muted">Thành viên</p>
                </div>
                <div class="col-lg-2 col-md-4 col-6 text-center team-member fade-in-element">
                    <img src="img/about/m3.jpg" class="rounded-circle mb-3 shadow" alt="Team Member 5">
                    <h5 class="fw-bold mb-1">Nguyễn Vân Thiên</h5>
                    <p class="text-muted">Thành viên</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============== Testimonials Section ==============-->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5 fade-in-element">
                <h2 class="section-title">Độc Giả Nói Về BookZ</h2>
                <p class="text-muted">Niềm vui của bạn là động lực lớn nhất của chúng tôi.</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 fade-in-element">
                    <div class="testimonial-card p-4 h-100">
                        <div class="d-flex align-items-center mb-3">
                            <img src="img/about/r1.jpg" class="rounded-circle me-3" alt="Avatar">
                            <div>
                                <h6 class="fw-bold mb-0">Minh Anh</h6>
                                <small class="text-muted">Sinh viên</small>
                            </div>
                        </div>
                        <p class="fst-italic text-muted">"BookZ có một bộ sưu tập sách rất chọn lọc. Mình đã tìm thấy
                            nhiều đầu sách hay mà không có ở các nơi khác. Giao hàng cũng rất nhanh!"</p>
                    </div>
                </div>
                <div class="col-lg-4 fade-in-element">
                    <div class="testimonial-card p-4 h-100">
                        <div class="d-flex align-items-center mb-3">
                            <img src="img/about/r2.jpg" class="rounded-circle me-3" alt="Avatar">
                            <div>
                                <h6 class="fw-bold mb-0">Quốc Bảo</h6>
                                <small class="text-muted">Lập trình viên</small>
                            </div>
                        </div>
                        <p class="fst-italic text-muted">"Website rất đẹp và dễ sử dụng. Trải nghiệm mua sắm online tại
                            BookZ thực sự mượt mà. Sẽ tiếp tục ủng hộ shop."</p>
                    </div>
                </div>
                <div class="col-lg-4 fade-in-element">
                    <div class="testimonial-card p-4 h-100">
                        <div class="d-flex align-items-center mb-3">
                            <img src="img/about/r3.jpg" class="rounded-circle me-3" alt="Avatar">
                            <div>
                                <h6 class="fw-bold mb-0">Hương Giang</h6>
                                <small class="text-muted">Nhân viên văn phòng</small>
                            </div>
                        </div>
                        <p class="fst-italic text-muted">"Mình rất thích cách BookZ gói quà. Rất chỉn chu và đẹp mắt.
                            Dịch vụ chăm sóc khách hàng cũng rất nhiệt tình và chuyên nghiệp."</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============== CTA Section ==============-->
    <section class="py-5 text-center">
        <div class="container fade-in-element">
            <h2 class="section-title">Sẵn Sàng Cho Cuộc Phiêu Lưu Mới?</h2>
            <p class="text-muted mb-4">Hàng ngàn đầu sách hay đang chờ bạn khám phá. Bắt đầu hành trình tri thức của bạn
                ngay hôm nay!</p>
            <a href="Product.php" class="btn btn-warning btn-lg rounded-pill px-5">Xem Tất Cả Sách</a>
        </div>
    </section>
</main>

<?php
// Giả định bạn có tệp footer.php chứa phần chân trang và các script JS cần thiết.
include 'footer.php';
?>