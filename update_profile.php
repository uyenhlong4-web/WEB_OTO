<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Chưa đăng nhập"]);
    exit;
}

$conn = new mysqli("localhost", "root", "", "auth_demo");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Kết nối DB thất bại: " . $conn->connect_error]);
    exit;
}

// Lấy dữ liệu
$user_id = $_SESSION['user_id'];
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Khởi tạo các biến để xây dựng câu truy vấn
$sql = "UPDATE users SET name=?, email=?";
$params = [$name, $email];
$types = "ss";
$avatarPath = null;

// Xử lý upload Avatar
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['avatar'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $allowed = ["jpg", "jpeg", "png", "gif"];

    if (in_array(strtolower($ext), $allowed)) {
        $newName = "avatar_" . $user_id . "_" . time() . "." . $ext;
        $uploadPath = "uploads/" . $newName;

        if (!is_dir("uploads")) {
            mkdir("uploads", 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $avatarPath = $uploadPath;
        }
    }
}

// Thêm mật khẩu (nếu có)
if (!empty($password)) {
    $sql .= ", password=?";
    $hashed = password_hash($password, PASSWORD_BCRYPT);
    $params[] = $hashed;
    $types .= "s";
}

// Thêm avatar (nếu có)
if ($avatarPath) {
    $sql .= ", avatar=?";
    $params[] = $avatarPath;
    $types .= "s";
}

// Hoàn thiện câu truy vấn
$sql .= " WHERE id=?";
$params[] = $user_id;
$types .= "i";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    // 🔥 QUAN TRỌNG: Cập nhật SESSION với tên mới sau khi cập nhật DB thành công
    $_SESSION['user_name'] = $name; 
    
    echo json_encode(["success" => true, "message" => "Cập nhật thành công"]);
} else {
    echo json_encode(["success" => false, "message" => "Cập nhật thất bại. Lỗi DB: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>