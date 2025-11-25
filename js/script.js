// function startCountdown() {
//   const hoursElement = document.getElementById("d-hours");
//   const minutesElement = document.getElementById("d-minutes");
//   const secondsElement = document.getElementById("d-seconds");

//   if (!hoursElement || !minutesElement || !secondsElement) return;

//   let h = parseInt(hoursElement.innerText);
//   let m = parseInt(minutesElement.innerText);
//   let s = parseInt(secondsElement.innerText);

//   const speed = 600;

//   const timer = setInterval(function () {
//     s--;
//     if (s < 0) {
//       s = 59;
//       m--;
//       if (m < 0) {
//         m = 59;
//         h--;
//         if (h < 0) {
//           clearInterval(timer); // Hết giờ
//           h = 0;
//           m = 0;
//           s = 0;
//         }
//       }
//     }
//     // Cập nhật số lên giao diện
//     hoursElement.innerText = h < 10 ? "0" + h : h;
//     minutesElement.innerText = m < 10 ? "0" + m : m;
//     secondsElement.innerText = s < 10 ? "0" + s : s;
//   }, speed);
// }
// document.addEventListener("DOMContentLoaded", startCountdown);



// /* ========================  Nút Menu  ======================== */
// $(document).ready(function () {
//   var menuHideTimer;

//   // Khi di chuột VÀO nút 3 gạch
//   $("#category-menu-toggle").mouseenter(function () {
//     clearTimeout(menuHideTimer); // Hủy mọi lệnh đóng menu (nếu có)
//     $("#category").slideDown(200); // Mở menu nhanh (200ms)
//   });

//   // Khi di chuột RA KHỎI nút 3 gạch
//   $("#category-menu-toggle").mouseleave(function () {
//     // Đặt một bộ đếm thời gian để đóng menu.
//     menuHideTimer = setTimeout(function () {
//       $("#category").slideUp(200);
//     }, 200);
//   });

//   // Khi di chuột VÀO BẢNG menu
//   $("#category").mouseenter(function () {
//     clearTimeout(menuHideTimer); // Hủy lệnh đóng menu (vì chuột đã ở trong menu)
//   });

//   // Khi di chuột RA KHỎI BẢNG menu
//   $("#category").mouseleave(function () {
//     // Đóng menu NGAY LẬP TỨC
//     $("#category").slideUp(200);
//   });
// });

// /* ========================  FLASH SALE  ======================== */
// document.addEventListener("DOMContentLoaded", function () {
//   // Khởi tạo Swiper
//   var swiper = new Swiper("#flashsale_slider150976", {
//     // Số lượng sản phẩm hiển thị
//     slidesPerView: 5,
//     // Khoảng cách giữa các sản phẩm
//     spaceBetween: 10,

//     navigation: {
//       nextEl: "#fhs-tab-slider-next150976",
//       prevEl: "#fhs-tab-slider-prev150976",
//     },
//     // Tùy chỉnh cho responsive (điện thoại, máy tính bảng)
//     breakpoints: {
//       // <= 767px
//       767: {
//         slidesPerView: 2, // Hiển thị 2 sản phẩm
//         spaceBetween: 10,
//       },
//       // <= 991px
//       991: {
//         slidesPerView: 3, // Hiển thị 3 sản phẩm
//         spaceBetween: 10,
//       },
//       // <= 1199px
//       1199: {
//         slidesPerView: 4, // Hiển thị 4 sản phẩm
//         spaceBetween: 10,
//       },
//     },
//   });
//   // Đếm ngược
//   var thoiGianKetThuc = new Date().getTime() + 30 * 60 * 1000; // 30 phút
//   // Lấy các element để cập nhật
//   var hoursSpan = document.getElementById("fs_hours");
//   var minutesSpan = document.getElementById("fs_minutes");
//   var secondsSpan = document.getElementById("fs_seconds");

//   // Cập nhật đồng hồ mỗi 1 giây
//   var countdownInterval = setInterval(function () {
//     var bayGio = new Date().getTime();
//     var thoiGianConLai = thoiGianKetThuc - bayGio;

//     // Tính toán giờ, phút, giây
//     var gio = Math.floor(
//       (thoiGianConLai % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
//     );
//     var phut = Math.floor((thoiGianConLai % (1000 * 60 * 60)) / (1000 * 60));
//     var giay = Math.floor((thoiGianConLai % (1000 * 60)) / 1000);

//     // Hàm để thêm số 0 đằng trước (ví dụ: 9 -> "09")
//     function dinhDangSo(so) {
//       return so < 10 ? "0" + so : so;
//     }

//     // Hiển thị kết quả
//     if (thoiGianConLai < 0) {
//       clearInterval(countdownInterval);
//       // Khi hết giờ
//       hoursSpan.innerHTML = "00";
//       minutesSpan.innerHTML = "00";
//       secondsSpan.innerHTML = "00";
//     } else {
//       hoursSpan.innerHTML = dinhDangSo(gio);
//       minutesSpan.innerHTML = dinhDangSo(phut);
//       secondsSpan.innerHTML = dinhDangSo(giay);
//     }
//   }, 1000); // 1000ms = 1 giây
// });

// /* ========================  BXH  ======================== */
// $(document).ready(function () {
//   var menuHideTimer;
//   // Xử lý khi click vào một sách bên trái
//   $(".bestseller-item").on("click", function () {
//     var $this = $(this);
//     var $currentTabPane = $this.closest(".tab-pane");

//     // Lấy tên target (ví dụ: "vh-book-1")
//     var targetId = $this.data("target");

//     // Xóa active khỏi các mục CÙNG TAB
//     $currentTabPane.find(".bestseller-item").removeClass("active");
//     $currentTabPane.find(".bestseller-pane").removeClass("active");

//     // Thêm active cho mục vừa click
//     $this.addClass("active");

//     // Hiển thị chi tiết sách tương ứng bằng ID
//     $("#" + targetId).addClass("active");
//   });
// });
