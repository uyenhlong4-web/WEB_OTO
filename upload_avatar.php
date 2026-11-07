<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Chưa đăng nhập"]);
    exit;
}

$conn = new mysqli("localhost", "root", "", "auth_demo");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Kết nối thất bại"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Kiểm tra file upload
if (!isset($_FILES['avatar'])) {
    echo json_encode(["success" => false, "message" => "Không có file upload"]);
    exit;
}

$file = $_FILES['avatar'];
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$allowed = ["jpg", "jpeg", "png", "gif"];

if (!in_array(strtolower($ext), $allowed)) {
    echo json_encode(["success" => false, "message" => "Chỉ chấp nhận jpg, png, gif"]);
    exit;
}

// Tạo tên file mới tránh trùng
$newName = "avatar_" . $user_id . "_" . time() . "." . $ext;
$uploadPath = "uploads/" . $newName;

// Tạo folder nếu chưa có
if (!is_dir("uploads")) {
    mkdir("uploads", 0777, true);
}

if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
    // Lưu đường dẫn vào DB
    $sql = "UPDATE users SET avatar=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $uploadPath, $user_id);
    $stmt->execute();

    echo json_encode(["success" => true, "avatar" => $uploadPath]);
} else {
    echo json_encode(["success" => false, "message" => "Upload thất bại"]);
}
