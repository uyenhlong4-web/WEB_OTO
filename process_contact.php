<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Thiết lập header để phản hồi bằng JSON (cho Fetch API bên Frontend)
header('Content-Type: application/json');

// ===========================================
// THÔNG TIN CẤU HÌNH CỦA BẠN (CẦN PHẢI THAY ĐỔI)
// ===========================================
// 1. Địa chỉ Email của Quản trị viên (nơi nhận thông báo)
$admin_email = 'dacthanh0605@gmail.com'; 

// 2. Thông tin Kết nối Database (Dùng cho XAMPP/WAMP mặc định)
$db_host = 'localhost';
$db_name = 'stem-oto'; // Tên Database đã tạo
$db_user = 'root'; 
$db_pass = '';     // Mật khẩu: Thường để trống nếu dùng XAMPP/WAMP mặc định

// ===========================================
// 1. LẤY DỮ LIỆU & XÁC THỰNG
// ===========================================
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Xác thực phía server: Kiểm tra rỗng và định dạng email
if (empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($message)) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Lỗi: Dữ liệu không hợp lệ. Vui lòng điền đủ thông tin.']);
    exit;
}

// ===========================================
// 2. LƯU VÀO DATABASE (Sử dụng PDO)
// ===========================================
$is_db_success = false;
$error_message = '';

try {
    // Kết nối CSDL
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Chuẩn bị và thực thi câu lệnh INSERT
    $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$name, $email, $message]);
    $is_db_success = true;

} catch (PDOException $e) {
    $error_message = "Lỗi Database: Không thể lưu tin nhắn.";
    error_log("Database Error: " . $e->getMessage()); 
    // Ghi lỗi log nhưng không dừng chương trình, vẫn cố gắng gửi email
}

// ===========================================
// 3. GỬI EMAIL THÔNG BÁO CHO QUẢN TRỊ VIÊN
// ===========================================
$mail_body = "Yêu cầu liên hệ mới:\n\n"
           . "Họ tên: " . $name . "\n"
           . "Email: " . $email . "\n"
           . "Nội dung: " . $message;

$headers = "From: webmaster@localhost\r\n" . 
           "Reply-To: " . $email . "\r\n";
           
// Sử dụng @ để ẩn lỗi nếu hàm mail() không hoạt động trên localhost
$is_mail_success = @mail($admin_email, 'Yêu cầu liên hệ mới từ Website STEM', $mail_body, $headers);


// ===========================================
// 4. PHẢN HỒI LẠI CLIENT (Frontend)
// ===========================================
if ($is_db_success) {
    // Nếu lưu DB thành công (Ưu tiên)
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Chúng tôi đã nhận được thông tin của bạn!']);
} else {
     // Nếu cả DB và Mail đều thất bại hoặc chỉ DB thất bại
     http_response_code(500); 
     echo json_encode(['success' => false, 'message' => 'Lỗi máy chủ nội bộ. Vui lòng thử lại sau.']);
}
?>