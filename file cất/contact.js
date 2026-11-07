document.addEventListener('DOMContentLoaded', function() {
    
    // ===========================================
    // HÀM CHUNG: HIỂN THỊ THÔNG BÁO (Toast)
    // ===========================================
    function showMessage(message, type = 'success') {
        const msgBox = document.getElementById('message-box');
        if (!msgBox) return; // Đảm bảo phần tử message-box tồn tại
        
        msgBox.textContent = message;
        msgBox.className = `message-box show ${type}`;
        
        setTimeout(() => {
            msgBox.className = 'message-box';
        }, 3000);
    }
    
    // ===========================================
    // HÀM XỬ LÝ ĐĂNG NHẬP / ĐĂNG XUẤT (Vẫn cần backend PHP)
    // ===========================================
    async function checkUser() {
        try {
            // Thay đổi URL này nếu cần thiết
            let res = await fetch("http://localhost/web/check_session.php"); 
            let data = await res.json();
            const authArea = document.getElementById("auth-area");

            if (!authArea) return;

            if (data.logged_in) {
                authArea.innerHTML = `
                    <span class="user-name" onclick="window.toggleDropdown()">Xin chào, ${data.user_name} ▾</span>
                    <ul class="dropdown" id="userDropdown">
                        <li><a href="profile.html">Thông tin cá nhân</a></li>
                        <li><a href="my_course.html">Khóa học của tôi</a></li>
                        <li><a href="#" id="logout-link">Đăng xuất</a></li>
                    </ul>
                `;
                document.getElementById("logout-link").addEventListener("click", async (e) => {
                    e.preventDefault();
                    await fetch("http://localhost/web/logout.php");
                    location.reload();
                });
            } else {
                authArea.innerHTML = `
                    <div class="auth-buttons">
                        <a href="login.html" class="auth-btn">Đăng nhập</a>
                        <a href="signup.html" class="auth-btn">Đăng ký</a>
                    </div>
                `;
            }
        } catch (err) {
            console.error("Lỗi khi kiểm tra session:", err);
            // Có thể thêm logic showMessage nếu lỗi nghiêm trọng
        }
    }

    // Định nghĩa toggleDropdown ở phạm vi window để có thể gọi từ inline HTML
    window.toggleDropdown = function() {
        const dropdown = document.getElementById("userDropdown");
        if (dropdown) {
             dropdown.classList.toggle("show");
        }
    }

    window.onclick = function(e) {
        if (!e.target.matches('.user-name')) {
            let dropdown = document.getElementById("userDropdown");
            if (dropdown && dropdown.classList.contains("show")) {
                dropdown.classList.remove("show");
            }
        }
    }

    checkUser();
    
    // ===========================================
    // LOGIC 1: CHỨC NĂNG TOGGLE TOPICS
    // ===========================================
    const topicHeaders = document.querySelectorAll('.topic-header');
    
    // Lấy 6 header đầu tiên (Khoa học đến Thiết kế)
    const firstSixHeaders = Array.from(topicHeaders).slice(0, 6);

    topicHeaders.forEach((header) => {
        header.addEventListener('click', function() {
            const index = Array.from(topicHeaders).indexOf(this);
            const content = this.nextElementSibling;
            
            if (index < 6) {
                // MỞ/ĐÓNG ĐỒNG THỜI cho 6 mục đầu tiên
                
                // Xác định trạng thái mục tiêu (Toggle)
                const shouldToggle = !firstSixHeaders[0].classList.contains('active');

                firstSixHeaders.forEach((currentHeader) => {
                    const currentContent = currentHeader.nextElementSibling;
                    
                    // Sử dụng toggle(className, force) để thực hiện ADD hoặc REMOVE đồng loạt
                    currentHeader.classList.toggle('active', shouldToggle);
                    currentContent.classList.toggle('show', shouldToggle);
                });

            } else {
                // MỞ/ĐÓNG ĐỘC LẬP cho các mục còn lại (nếu có)
                this.classList.toggle('active');
                content.classList.toggle('show');
            }
        });
    });

    // ===========================================
    // LOGIC 2: XỬ LÝ FORM LIÊN HỆ THỰC TẾ (Sử dụng Fetch API)
    // ===========================================
    const contactForm = document.getElementById('contact-form');
    
    if (contactForm) {
        contactForm.addEventListener('submit', async function(event) {
            event.preventDefault();
        
            const form = this;
            const submitButton = form.querySelector('button[type="submit"]');
            
            // Lấy dữ liệu và xác thực cơ bản (tránh gửi request vô ích)
            const name = document.getElementById("name").value.trim();
            const email = document.getElementById("email").value.trim();
            const message = document.getElementById("message").value.trim();
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i;
            
            if (!name || !email || !message || !emailRegex.test(email)) {
                showMessage("⚠️ Vui lòng nhập đầy đủ và đúng định dạng Email!", "error");
                return;
            }

            submitButton.disabled = true;
            submitButton.textContent = 'Đang gửi...';
        
            const formData = new FormData(form);
        
            try {
                // Đảm bảo file process_contact.php nằm cùng thư mục
                const response = await fetch('process_contact.php', { 
                    method: 'POST',
                    body: formData
                });
        
                if (response.ok) {
                    // Thành công: Đã lưu vào DB/Gửi mail (tùy thuộc backend)
                    showMessage('Thông tin của bạn đã được gửi thành công!', 'success');
                    form.reset();
                } else {
                    // Lỗi từ server (400, 500)
                    const errorData = await response.json();
                    showMessage(`Lỗi: ${errorData.message || 'Có lỗi xảy ra khi gửi.'}`, 'error');
                }
            } catch (error) {
                // Lỗi mạng
                showMessage('Lỗi kết nối máy chủ. Vui lòng kiểm tra lại mạng.', 'error');
                console.error('Error submitting form:', error);
            } finally {
                // Luôn bật lại nút gửi
                submitButton.disabled = false;
                submitButton.textContent = 'Gửi';
            }
        });
    }

}); // Kết thúc DOMContentLoaded