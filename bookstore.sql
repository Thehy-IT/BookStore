
-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 05, 2016 at 07:55 PM
-- Server version: 10.0.20-MariaDB
-- PHP Version: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `u385439067_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE IF NOT EXISTS `cart` (
  `UserID` int(11) NOT NULL,
  `ProductID` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Quantity` int(5) NOT NULL,
  PRIMARY KEY (`UserID`,`ProductID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`UserID`, `ProductID`, `Quantity`) VALUES
(2, 'LIT-1', 2),
(3, 'BUS-1', 1),
(3, 'HEA-2', 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `PID` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `Title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Author` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `MRP` float NOT NULL,
  `Price` float NOT NULL,
  `Discount` int(11) DEFAULT NULL,
  `Available` int(11) NOT NULL,
  `Publisher` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Edition` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Category` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Description` text COLLATE utf8_unicode_ci,
  `Language` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `page` int(5) DEFAULT NULL,
  `weight` int(4) DEFAULT '500',
  PRIMARY KEY (`PID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`PID`, `Title`, `Author`, `MRP`, `Price`, `Discount`, `Available`, `Publisher`, `Edition`, `Category`, `Description`, `Language`, `page`, `weight`) VALUES
('LIT-1', 'Dế Mèn Phiêu Lưu Ký', 'Tô Hoài', 120000, 85000, 29, 50, 'NXB Kim Đồng', 'Tái bản 2023', 'Literature and Fiction', 'Dế Mèn Phiêu Lưu Ký là tác phẩm văn xuôi đặc sắc và nổi tiếng nhất của nhà văn Tô Hoài viết về loài vật, giành cho lứa tuổi thiếu nhi. Truyện gồm 10 chương, kể về những cuộc phiêu lưu của Dế Mèn qua thế giới muôn màu muôn vẻ của các loài vật.', 'Tiếng Việt', 250, 300),
('LIT-2', 'Số Đỏ', 'Vũ Trọng Phụng', 150000, 110000, 27, 30, 'NXB Văn Học', '2022', 'Literature and Fiction', 'Số Đỏ là một tiểu thuyết trào phúng, châm biếm xã hội Việt Nam những năm 1930-1945. Tác phẩm xoay quanh nhân vật chính là Xuân Tóc Đỏ, từ một kẻ hạ lưu bỗng chốc đổi đời, bước vào xã hội thượng lưu nhờ sự may mắn và tài bịp bợm.', 'Tiếng Việt', 350, 400),
('LIT-3', 'Hoàng Tử Bé', 'Antoine de Saint-Exupéry', 99000, 79000, 20, 100, 'NXB Nhã Nam', '2021', 'Literature and Fiction', 'Một câu chuyện triết lý sâu sắc về tình bạn, tình yêu và ý nghĩa cuộc sống, được kể qua cuộc gặp gỡ giữa một phi công và một hoàng tử bé đến từ hành tinh khác. Tác phẩm kinh điển dành cho mọi lứa tuổi.', 'Tiếng Việt', 150, 250),
('LIT-4', 'Tôi Thấy Hoa Vàng Trên Cỏ Xanh', 'Nguyễn Nhật Ánh', 135000, 95000, 30, 80, 'NXB Trẻ', '2018', 'Literature and Fiction', 'Câu chuyện cảm động về tuổi thơ ở một làng quê nghèo, xoay quanh cuộc sống của hai anh em Thiều và Tường với những rung động đầu đời, những bài học về tình yêu thương, sự bao dung và lòng trắc ẩn.', 'Tiếng Việt', 380, 420),
('LIT-20', 'Mật Mã Da Vinci', 'Dan Brown', 250000, 199000, 20, 25, 'NXB Hội Nhà Văn', '2019', 'Literature and Fiction', 'Robert Langdon, một chuyên gia biểu tượng học, bị cuốn vào một cuộc điều tra án mạng tại bảo tàng Louvre. Những manh mối bí ẩn dẫn ông đến một bí mật kinh thiên động địa được che giấu hàng thế kỷ bởi một hội kín.', 'Tiếng Việt', 650, 600),

('BIO-1', 'Muôn Kiếp Nhân Sinh', 'Nguyên Phong', 180000, 150000, 17, 60, 'NXB Tổng Hợp TPHCM', '2020', 'Biographies and Auto Biographies', 'Cuốn sách ghi lại những câu chuyện, trải nghiệm về tiền kiếp và luật nhân quả từ một doanh nhân thành đạt, giúp người đọc chiêm nghiệm về ý nghĩa của cuộc sống, tình yêu thương và sự tha thứ.', 'Tiếng Việt', 450, 500),
('BIO-2', 'Steve Jobs - Thiên Tài Lập Dị', 'Walter Isaacson', 350000, 299000, 15, 40, 'NXB Alpha Books', '2017', 'Biographies and Auto Biographies', 'Cuốn tiểu sử đầy đủ và chân thực nhất về cuộc đời của Steve Jobs, từ thời thơ ấu, những ngày đầu sáng lập Apple, cho đến khi tạo ra những sản phẩm thay đổi cả thế giới. Một câu chuyện về sự sáng tạo, đam mê và khác biệt.', 'Tiếng Việt', 700, 800),
('BIO-9', 'Nhật Ký Đặng Thùy Trâm', 'Đặng Thùy Trâm', 80000, 65000, 19, 70, 'NXB Hội Nhà Văn', '2005', 'Biographies and Auto Biographies', 'Những trang nhật ký đầy xúc động của nữ bác sĩ, liệt sĩ Đặng Thùy Trâm trong những năm tháng khốc liệt của chiến tranh. Tác phẩm là biểu tượng cho lý tưởng sống cao đẹp và tinh thần bất khuất của thế hệ trẻ Việt Nam.', 'Tiếng Việt', 320, 350),

('ACA-1', 'Lược Sử Loài Người', 'Yuval Noah Harari', 299000, 249000, 17, 55, 'NXB Tri Thức', '2018', 'Academic and Professional', 'Một cái nhìn toàn cảnh về lịch sử phát triển của loài người, từ thời kỳ đồ đá cho đến cuộc cách mạng khoa học và công nghệ. Cuốn sách giải thích cách Homo Sapiens đã thống trị hành tinh và định hình thế giới hiện đại.', 'Tiếng Việt', 550, 650),
('ACA-2', 'Súng, Vi Trùng và Thép', 'Jared Diamond', 320000, 280000, 13, 30, 'NXB Thế Giới', '2015', 'Academic and Professional', 'Lý giải tại sao các xã hội Á-Âu lại chinh phục và thống trị các nền văn minh khác, thông qua các yếu tố về địa lý, môi trường, và sự phát triển của nông nghiệp, công nghệ.', 'Tiếng Việt', 600, 700),
('ACA-10', 'Python Cho Người Mới Bắt Đầu', 'Trần Minh Tuấn', 199000, 159000, 20, 40, 'NXB Khoa Học Kỹ Thuật', '2023', 'Academic and Professional', 'Hướng dẫn chi tiết và dễ hiểu về ngôn ngữ lập trình Python, từ các khái niệm cơ bản đến các ứng dụng thực tế trong phân tích dữ liệu và phát triển web. Dành cho những ai muốn bắt đầu sự nghiệp trong ngành công nghệ.', 'Tiếng Việt', 400, 450),

('BUS-1', 'Đắc Nhân Tâm', 'Dale Carnegie', 99000, 75000, 24, 150, 'NXB Tổng Hợp TPHCM', 'Tái bản 2023', 'Business and Management', 'Cuốn sách kinh điển về nghệ thuật giao tiếp và ứng xử, giúp bạn xây dựng các mối quan hệ tốt đẹp, tạo thiện cảm và ảnh hưởng tích cực đến người khác trong công việc và cuộc sống.', 'Tiếng Việt', 320, 380),
('BUS-2', 'Từ Tốt Đến Vĩ Đại', 'Jim Collins', 150000, 120000, 20, 60, 'NXB Trẻ', '2019', 'Business and Management', 'Nghiên cứu về những yếu tố cốt lõi giúp các công ty từ chỗ chỉ "tốt" có thể vươn lên trở thành "vĩ đại" và duy trì sự thành công bền vững. Một cẩm nang quý giá cho các nhà lãnh đạo và quản lý.', 'Tiếng Việt', 400, 450),
('BUS-8', 'Nghĩ Giàu và Làm Giàu', 'Napoleon Hill', 120000, 99000, 18, 90, 'NXB Tổng Hợp TPHCM', '2021', 'Business and Management', 'Tác phẩm kinh điển về tư duy thành công, đúc kết từ cuộc phỏng vấn hơn 500 người thành công nhất nước Mỹ. Cuốn sách đưa ra 13 nguyên tắc để đạt được sự giàu có và thành công trong mọi lĩnh vực.', 'Tiếng Việt', 388, 420),
('BUS-10', 'Khởi Nghiệp Tinh Gọn', 'Eric Ries', 169000, 135000, 20, 45, 'NXB Lao Động', '2020', 'Business and Management', 'Phương pháp xây dựng và phát triển sản phẩm mới một cách nhanh chóng và hiệu quả, giảm thiểu rủi ro và lãng phí bằng cách liên tục thử nghiệm và học hỏi từ phản hồi của khách hàng.', 'Tiếng Việt', 360, 400),

('CHILD-1', 'Kính Vạn Hoa', 'Nguyễn Nhật Ánh', 500000, 450000, 10, 20, 'NXB Trẻ', 'Trọn bộ', 'Children and Teens', 'Bộ truyện dài tập kể về những cuộc phiêu lưu, những trò nghịch ngợm và những bài học cuộc sống của bộ ba Quý ròm, nhỏ Hạnh và Tiểu Long. Một tác phẩm gắn liền với tuổi thơ của nhiều thế hệ.', 'Tiếng Việt', 5400, 3000),
('CHILD-2', 'Chúc Một Ngày Tốt Lành', 'Nguyễn Nhật Ánh', 110000, 88000, 20, 50, 'NXB Trẻ', '2014', 'Children and Teens', 'Một câu chuyện dí dỏm và độc đáo về cuộc sống của các loài vật trong một khu vườn, với những triết lý nhẹ nhàng về cuộc sống, tình bạn và cách đối nhân xử thế.', 'Tiếng Việt', 300, 350),
('CHILD-6', 'Harry Potter và Hòn Đá Phù Thủy', 'J.K. Rowling', 180000, 145000, 19, 70, 'NXB Trẻ', '2017', 'Children and Teens', 'Tập đầu tiên trong series truyện kinh điển về cậu bé phù thủy Harry Potter, mở ra một thế giới phép thuật đầy kỳ diệu và một cuộc chiến không khoan nhượng giữa cái thiện và cái ác.', 'Tiếng Việt', 550, 580),

('HEA-1', 'Ẩm Thực Chay Hiện Đại', 'Lê Ngọc Anh', 220000, 180000, 18, 25, 'NXB Phụ Nữ', '2023', 'Health and Cooking', 'Tuyển tập các công thức nấu món chay sáng tạo, ngon miệng và đầy đủ dinh dưỡng, phù hợp với lối sống hiện đại. Sách hướng dẫn chi tiết cách chế biến và trình bày món ăn đẹp mắt.', 'Tiếng Việt', 200, 400),
('HEA-2', 'Yoga Cho Cuộc Sống Bận Rộn', 'Phạm Hoàng Yến', 150000, 120000, 20, 40, 'NXB Thể Dục Thể Thao', '2022', 'Health and Cooking', 'Giới thiệu các bài tập yoga đơn giản, hiệu quả, có thể thực hiện ngay tại nhà hoặc văn phòng, giúp giảm căng thẳng, cải thiện sức khỏe thể chất và tinh thần cho người bận rộn.', 'Tiếng Việt', 180, 300),
('HEA-9', 'Nấu Ăn Bằng Cả Trái Tim', 'Phan Anh (Esheep)', 250000, 225000, 10, 30, 'NXB Dân Trí', '2021', 'Health and Cooking', 'Không chỉ là sách dạy nấu ăn, đây còn là những câu chuyện truyền cảm hứng về tình yêu bếp núc. Tác giả chia sẻ những công thức từ truyền thống đến hiện đại, cùng những bí quyết để mỗi bữa ăn đều là một trải nghiệm hạnh phúc.', 'Tiếng Việt', 240, 500),

('REG-1', 'Chí Phèo', 'Nam Cao', 75000, 50000, 33, 60, 'NXB Văn Học', '2020', 'Regional Books', 'Truyện ngắn kinh điển của văn học hiện thực phê phán Việt Nam, khắc họa bi kịch của người nông dân nghèo bị xã hội tha hóa, đẩy vào con đường lưu manh và bị cự tuyệt quyền làm người.', 'Tiếng Việt', 120, 200),
('REG-2', 'Tắt Đèn', 'Ngô Tất Tố', 89000, 69000, 22, 50, 'NXB Hội Nhà Văn', '2019', 'Regional Books', 'Tác phẩm phơi bày sự tàn nhẫn của chế độ sưu thuế và sự cùng quẫn của người nông dân Việt Nam trước Cách mạng tháng Tám, qua hình ảnh chị Dậu - một người phụ nữ giàu lòng tự trọng và tình yêu thương.', 'Tiếng Việt', 250, 300),
('REG-8', 'Hà Nội Băm Sáu Phố Phường', 'Thạch Lam', 95000, 76000, 20, 40, 'NXB Đời Nay', 'Tái bản 2018', 'Regional Books', 'Tập bút ký tinh tế và đầy chất thơ về vẻ đẹp của phố phường, con người và đặc biệt là văn hóa ẩm thực của Hà Nội xưa. Một tác phẩm dành cho những ai yêu mến và muốn tìm hiểu về Thăng Long - Hà Nội.', 'Tiếng Việt', 180, 250);

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `biography` text COLLATE utf8_unicode_ci,
  `image_url` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `authors`
--
-- This will auto-populate authors from the products table
--

INSERT INTO `authors` (name) SELECT DISTINCT Author FROM products WHERE Author IS NOT NULL AND Author != '' ORDER BY Author;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `author` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_url` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `author`, `image_url`, `content`, `created_at`) VALUES
(1, 'Top 10 Sách Nên Đọc Mùa Đông Này', 'Biên tập viên BookZ', 'https://images.unsplash.com/photo-1457369804613-52c61a468e7d?q=80&w=2070&auto=format&fit=crop', '<p class=\"lead\">Khi thời tiết trở lạnh, không có gì tuyệt vời hơn là cuộn mình trong chăn ấm cùng một cuốn sách hay. Dưới đây là danh sách 10 cuốn sách mà các biên tập viên của chúng tôi đề xuất cho mùa đông này.</p>\r\n            <p>Từ những câu chuyện bí ẩn ấm cúng đến những cuốn tiểu thuyết lãng mạn cảm động, danh sách này có đủ mọi thể loại để làm hài lòng bất kỳ độc giả nào. Chúng tôi đã lựa chọn cẩn thận những tác phẩm không chỉ có nội dung hấp dẫn mà còn mang lại cảm giác ấm áp, phù hợp với không khí mùa đông.</p>\r\n            <h5>1. Thư Viện Nửa Đêm - Matt Haig</h5>\r\n            <p>Một câu chuyện đầy suy ngẫm về những lựa chọn trong cuộc sống và cơ hội thứ hai. Cuốn sách này sẽ khiến bạn phải suy nghĩ về con đường mình đã chọn và những khả năng vô tận của cuộc đời.</p>\r\n            <h5>2. Bệnh Nhân Thầm Lặng - Alex Michaelides</h5>\r\n            <p>Nếu bạn yêu thích thể loại giật gân, đây là một lựa chọn không thể bỏ qua. Một câu chuyện ly kỳ với những cú twist bất ngờ sẽ giữ bạn đọc đến tận trang cuối cùng.</p>\r\n            <blockquote class=\"blockquote fst-italic my-4 p-3 bg-light border-start border-5 border-warning\">\r\n                \'Một cuốn sách hay trên giá sách là một người bạn, dù có quay lưng lại nhưng vẫn không bao giờ quay mặt đi.\' - Khuyết danh\r\n            </blockquote>\r\n            <p>Ngoài ra, danh sách còn có những tác phẩm kinh điển, sách phát triển bản thân và cả những câu chuyện thiếu nhi ý nghĩa để bạn có thể đọc cùng gia đình. Hãy chuẩn bị một tách trà nóng và bắt đầu khám phá thế giới văn học mùa đông này!</p>', '2025-10-15 10:00:00'),
(2, 'Phỏng vấn độc quyền J.K. Rowling', 'Jane Doe', 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?q=80&w=2128&auto=format&fit=crop', '<p class=\"lead\">Chúng tôi đã có cơ hội ngồi lại với tác giả huyền thoại J.K. Rowling để trò chuyện về quá trình sáng tạo đằng sau loạt phim Harry Potter và những dự định sắp tới của bà.</p>\r\n            <p>Trong buổi phỏng vấn, J.K. Rowling đã chia sẻ những chi tiết thú vị về việc xây dựng thế giới phù thủy, từ nguồn cảm hứng ban đầu cho đến những khó khăn trong quá trình viết. Bà cũng tiết lộ một vài bí mật nhỏ về các nhân vật mà người hâm mộ chưa từng được biết đến.</p>\r\n            <p>Khi được hỏi về dự án tiếp theo, bà mỉm cười bí ẩn và nói rằng mình luôn có những câu chuyện mới để kể. \'Thế giới luôn đầy ắp những điều kỳ diệu, bạn chỉ cần biết cách tìm kiếm chúng,\' bà chia sẻ.</p>', '2025-10-12 11:30:00'),
(3, 'Sự Trỗi Dậy Của Thư Viện Số', 'John Smith', 'https://images.unsplash.com/photo-1519682337058-a94d519337bc?q=80&w=2070&auto=format&fit=crop', '<p class=\"lead\">Công nghệ đang định hình lại cách chúng ta tiếp cận và tiêu thụ văn học. Các thư viện số và sách điện tử (ebook) đang ngày càng trở nên phổ biến, mang lại sự tiện lợi chưa từng có cho độc giả.</p>\r\n            <p>Với một thiết bị đọc sách nhỏ gọn, bạn có thể mang theo cả một thư viện hàng ngàn cuốn sách. Điều này không chỉ giúp tiết kiệm không gian mà còn cho phép bạn đọc sách ở bất cứ đâu, bất cứ lúc nào. Các nền tảng như Kindle, Kobo đã thay đổi hoàn toàn thói quen đọc của nhiều người.</p>\r\n            <p>Tuy nhiên, sự trỗi dậy của sách điện tử cũng đặt ra những câu hỏi về tương lai của sách giấy truyền thống. Liệu chúng có biến mất? Hay cả hai sẽ cùng tồn tại, phục vụ những nhu cầu khác nhau của độc giả? Hãy cùng chúng tôi phân tích xu hướng này.</p>', '2025-10-08 14:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL AUTO_INCREMENT,
  `UserName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Role` varchar(20) COLLATE utf8_unicode_ci DEFAULT 'user',
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `UserName` (`UserName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `UserName`, `Password`, `Role`) VALUES
(1, 'admin', '$2y$10$z1GCPdWGeSMrTdPAAdbXiOjRAVdM14IDhjBmVtFuUwJw9l.aCBOwq', 'admin'); -- password is 'admin123'
ALTER TABLE `users` ADD `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `ProductID` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_product` (`UserID`,`ProductID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL,
  `comment` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`review_id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `customer_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `shipping_address` text COLLATE utf8_unicode_ci NOT NULL,
  `phone_number` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Pending',
  `payment_method` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `type` enum('percentage','fixed') NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `min_spend` decimal(10,2) DEFAULT 0.00,
  `expiry_date` date DEFAULT NULL,
  `usage_limit` int(11) DEFAULT 1,
  `usage_count` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Thêm một vài mã giảm giá mẫu:
INSERT INTO `coupons` (`code`, `type`, `value`, `min_spend`, `expiry_date`) VALUES
('GIAM10', 'percentage', 10.00, 200000.00, '2025-12-31'),
('KHAITRUONG', 'fixed', 50000.00, 300000.00, '2025-12-31'),
('FREESHIP', 'fixed', 30000.00, 150000.00, '2025-12-31');

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `icon` varchar(50) DEFAULT 'fas fa-bell',
  `title` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;