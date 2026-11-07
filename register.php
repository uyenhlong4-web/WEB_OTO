<?php
header("Content-Type: application/json; charset=UTF-8");
require "config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Dữ liệu không hợp lệ"]);
    exit;
}

// kiểm tra email tồn tại
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetchColumn()) {
    http_response_code(409);
    echo json_encode(["success" => false, "message" => "Email đã tồn tại"]);
    exit;
}

// hash mật khẩu
$hash = password_hash($password, PASSWORD_BCRYPT);

// thêm vào DB
try {
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $hash]);

    echo json_encode(["success" => true, "message" => "Đăng ký thành công"]);
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        http_response_code(409);
        echo json_encode(["success" => false, "message" => "Email đã tồn tại"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Lỗi máy chủ"]);
    }
}
