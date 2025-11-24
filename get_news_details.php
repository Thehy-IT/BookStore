<?php
session_start();
include "dbconnect.php";

// Thiết lập header để trả về JSON
header('Content-Type: application/json');

// CHẶN KHÔNG CHO USER THƯỜNG VÀO
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = $con->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $news_item = $result->fetch_assoc();

    if ($news_item) {
        echo json_encode($news_item);
    } else {
        echo json_encode(['error' => 'News item not found.']);
    }
} else {
    echo json_encode(['error' => 'Invalid ID.']);
}
