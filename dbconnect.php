<?php
define('DB_HOST', 'localhost:3306');
define('DB_NAME', 'bookstore');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

$db = mysqli_select_db($con, DB_NAME);
if (!$db) {
    die("Failed to select database: " . mysqli_error($con));
}
