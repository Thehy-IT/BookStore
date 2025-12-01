# BookZ - Dự án Website Bán Sách

Chào mừng đến với BookZ!  
👉 [Website đã triển khai](https://bookz.free.nf/)


Đây là một dự án website thương mại điện tử được xây dựng để kinh doanh sách trực tuyến, cung cấp một nền tảng đầy đủ tính năng cho cả người dùng và quản trị viên.

## Giới thiệu

BookZ là một hiệu sách trực tuyến được phát triển bằng PHP và MySQL, mô phỏng một hệ thống e-commerce hoàn chỉnh. Người dùng có thể duyệt, tìm kiếm, xem chi tiết sách, thêm vào giỏ hàng và đặt mua. Quản trị viên có một trang quản lý riêng để kiểm soát toàn bộ hoạt động của cửa hàng.

## Tính năng chính

Dựa trên cấu trúc cơ sở dữ liệu, hệ thống bao gồm các tính năng sau:

### Dành cho Khách hàng (User)

- **Đăng ký & Đăng nhập:** Tạo tài khoản mới và quản lý thông tin cá nhân.
- **Duyệt & Tìm kiếm sản phẩm:** Tìm kiếm sách theo tên, tác giả, hoặc duyệt qua các danh mục.
- **Trang chi tiết sản phẩm:** Xem thông tin đầy đủ về sách, bao gồm mô tả, tác giả, nhà xuất bản, giá, và các đánh giá từ người mua khác.
- **Giỏ hàng (Shopping Cart):** Thêm/xóa sản phẩm và cập nhật số lượng trước khi thanh toán.
- **Danh sách yêu thích (Wishlist):** Lưu lại những cuốn sách quan tâm để mua sau.
- **Hệ thống Đặt hàng:** Quy trình thanh toán để đặt hàng, nhập địa chỉ giao hàng và chọn phương thức thanh toán.
- **Quản lý Đơn hàng:** Xem lại lịch sử các đơn hàng đã đặt và theo dõi trạng thái của chúng.
- **Đánh giá & Xếp hạng:** Viết bình luận và cho điểm những cuốn sách đã mua.
- **Mã giảm giá (Coupons):** Áp dụng mã giảm giá để được hưởng ưu đãi khi thanh toán.
- **Tin tức & Bài viết:** Đọc các bài viết, tin tức, hoặc các bài phỏng vấn liên quan đến sách và tác giả.
- **Thông báo:** Nhận thông báo về đơn hàng, khuyến mãi, và các cập nhật quan trọng khác.

### Dành cho Quản trị viên (Admin)

- **Bảng điều khiển (Dashboard):** Giao diện quản trị tổng quan để theo dõi hoạt động của website.
- **Quản lý Sản phẩm:** Thêm, sửa, xóa sách và cập nhật thông tin chi tiết, số lượng tồn kho.
- **Quản lý Đơn hàng:** Xem danh sách đơn hàng, cập nhật trạng thái (ví dụ: Chờ xử lý, Đang giao, Hoàn thành).
- **Quản lý Người dùng:** Xem thông tin và quản lý tài khoản người dùng.
- **Quản lý Tác giả:** Thêm và cập nhật thông tin, tiểu sử của các tác giả.
- **Quản lý Tin tức:** Tạo và quản lý các bài viết, tin tức trên trang web.
- **Quản lý Mã giảm giá:** Tạo và quản lý các chương trình khuyến mãi.

## Công nghệ sử dụng

- **Backend:** PHP
- **Cơ sở dữ liệu:** MySQL / MariaDB
- **Frontend:** HTML, CSS, JavaScript (Có thể có Bootstrap hoặc các thư viện khác)
- **Môi trường phát triển:** XAMPP / WAMP hoặc tương tự.

## Cấu trúc Cơ sở dữ liệu

Hệ thống sử dụng một cơ sở dữ liệu quan hệ bao gồm các bảng chính sau:

- `products`: Lưu trữ thông tin chi tiết về các cuốn sách.
- `users`: Lưu trữ thông tin tài khoản người dùng và vai trò (admin/user).
- `authors`: Lưu trữ thông tin về tác giả.
- `cart`: Lưu trữ các sản phẩm trong giỏ hàng của người dùng.
- `wishlist`: Lưu trữ danh sách sản phẩm yêu thích của người dùng.
- `orders` & `order_items`: Quản lý thông tin đơn hàng và các sản phẩm trong mỗi đơn hàng.
- `reviews`: Lưu trữ các đánh giá và xếp hạng sản phẩm từ người dùng.
- `news`: Lưu trữ các bài viết tin tức.
- `coupons`: Quản lý các mã giảm giá.
- `notifications`: Quản lý các thông báo gửi đến người dùng.

## Hướng dẫn Cài đặt

Để chạy dự án này trên máy cục bộ của bạn, hãy làm theo các bước sau:

1. **Yêu cầu:**
   - Cài đặt một môi trường server web như XAMPP hoặc WAMP.

2. **Sao chép mã nguồn:**
   - Clone hoặc tải mã nguồn của dự án vào thư mục `htdocs` (đối với XAMPP) hoặc `www` (đối với WAMP).
   - Ví dụ: `c:\xampp\htdocs\bs`

3. **Cài đặt Cơ sở dữ liệu:**
   - Khởi động Apache và MySQL từ bảng điều khiển XAMPP/WAMP.
   - Truy cập `http://localhost/phpmyadmin`.
   - Tạo một cơ sở dữ liệu mới (ví dụ: `bookstore_db`).
   - Chọn cơ sở dữ liệu vừa tạo, vào tab `Import`.
   - Chọn tệp `bookstore.sql` và thực hiện import để tạo các bảng và dữ liệu mẫu.

4. **Cấu hình kết nối:**
   - Tìm tệp cấu hình kết nối cơ sở dữ liệu trong mã nguồn (thường là `config.php` hoặc `db_connect.php`).
   - Cập nhật thông tin kết nối cho phù hợp với môi trường của bạn:
     ```php
     define('DB_SERVER', 'localhost');
     define('DB_USERNAME', 'root');
     define('DB_PASSWORD', ''); // Mật khẩu mặc định của XAMPP là rỗng
     define('DB_NAME', 'bookstore_db'); // Tên CSDL bạn đã tạo
     ```

5. **Chạy ứng dụng:**
   - Mở trình duyệt và truy cập vào `http://localhost/bs`.

6. **Tài khoản Admin:**
   - Sử dụng thông tin đăng nhập mặc định để truy cập trang quản trị:
     - **Tên đăng nhập:** `admin`
     - **Mật khẩu:** `admin123`

## 📜 Lưu ý về Giấy phép
Vui lòng đọc kỹ file [LICENSE](./LICENSE) trước khi sử dụng hoặc phân phối lại mã nguồn này.  
Mọi sản phẩm phái sinh phải ghi rõ nguồn repo gốc: https://github.com/Thehy-IT/BookStore

---

Cảm ơn bạn đã quan tâm đến dự án BookZ!
