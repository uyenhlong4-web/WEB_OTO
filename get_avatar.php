<?php
// Kết nối database
$servername = "localhost";
$username = "ten_database";
$password = "mat_khau_database";
$dbname = "ten_database_cua_ban";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
  die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy user_id từ URL
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Truy vấn để lấy hình ảnh avatar từ database
    $sql = "SELECT avatar FROM users WHERE id = $user_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Giả sử cột 'avatar' chứa dữ liệu blob (dạng nhị phân)
        $avatar_data = $row['avatar'];
        
        // Thiết lập header để trình duyệt hiểu đây là một hình ảnh
        header("Content-type: image/jpeg"); 
        
        // Trả về dữ liệu hình ảnh
        echo $avatar_data;
    } else {
        // Trả về hình ảnh mặc định nếu không tìm thấy avatar
        header("Content-type: image/png");
        readfile("path/to/default_avatar.png");
    }
}

$conn->close();
?>