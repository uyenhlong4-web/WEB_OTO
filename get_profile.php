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
$sql = "SELECT id, name, email FROM users WHERE id=?";
$sql = "SELECT id, name, email, avatar FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(["success" => true, "user" => $row]);
} else {
    echo json_encode(["success" => false, "message" => "Không tìm thấy user"]);
}
