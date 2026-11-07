<?php
$host = "localhost";
$db   = "auth_demo";   // tên database bạn tạo trong phpMyAdmin
$user = "root";        // mặc định XAMPP là root
$pass = "";            // mật khẩu mặc định trống

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Kết nối thất bại: " . $e->getMessage()]);
    exit;
}
