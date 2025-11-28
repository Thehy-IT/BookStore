<?php
// File: ajax_search.php

// Bắt đầu session và kết nối CSDL
session_start();
include "dbconnect.php";

// Thiết lập header để trình duyệt hiểu đây là JSON
header('Content-Type: application/json');

// Lấy từ khóa từ request GET
$keyword_raw = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

$results = [];

// Chỉ thực hiện tìm kiếm nếu từ khóa có ít nhất 2 ký tự
if (strlen($keyword_raw) >= 2) {
    $keyword = "%{$keyword_raw}%";

    // Chuẩn bị câu truy vấn, giới hạn 5 kết quả để tối ưu
    // Lấy các cột cần thiết: PID, Title, Author để hiển thị gợi ý
    $query = "SELECT PID, Title, Author FROM products WHERE Title LIKE ? OR Author LIKE ? ORDER BY Title ASC LIMIT 5";

    $stmt = $con->prepare($query);
    if ($stmt) {
        $stmt->bind_param("ss", $keyword, $keyword);
        $stmt->execute();
        $result_set = $stmt->get_result();

        while ($row = $result_set->fetch_assoc()) {
            $results[] = $row;
        }
        $stmt->close();
    }
}

// Trả về kết quả dưới dạng JSON
echo json_encode($results);
?>