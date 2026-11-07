// Hàm hiển thị thông báo trong hộp thông báo
function showMessage(message, type) {
    const messageBox = document.getElementById("message-box");
    
    // Cập nhật nội dung và kiểu thông báo (thành công hoặc lỗi)
    messageBox.textContent = message;
    messageBox.className = "message-box " + type; // success hoặc error

    // Hiển thị hộp thông báo
    messageBox.classList.add("show");

    // Tự động ẩn hộp thông báo sau 5 giây
    setTimeout(() => {
        hideMessage(); 
    }, 5000);
}

// Hàm ẩn hộp thông báo
function hideMessage() {
    const messageBox = document.getElementById("message-box");
    messageBox.classList.remove("show");
}

// Hàm kiểm tra điều kiện mật khẩu
function validatePassword(password, confirmPassword) {
    if (password !== confirmPassword) {
        return "Mật khẩu và xác nhận mật khẩu không khớp!";
    }

    if (password.length < 8) {
        return "Mật khẩu phải có ít nhất 8 ký tự!";
    }

    const regex = /^(?=.*[a-zA-Z])(?=.*\d).+$/;
    if (!regex.test(password)) {
        return "Mật khẩu phải chứa ít nhất một chữ cái và một số!";
    }

    const specialCharRegex = /[^a-zA-Z0-9]/;
    if (!specialCharRegex.test(password)) {
        return "Mật khẩu phải chứa ít nhất một ký tự đặc biệt!";
    }

    return null; 
}

// Xử lý form đăng ký
document.getElementById("register-form").addEventListener("submit", async function(event) {
    event.preventDefault();

    const name = document.getElementById("name").value;
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm-password").value;

    const passwordMessage = validatePassword(password, confirmPassword);

    if (passwordMessage) {
        showMessage(passwordMessage, "error");
    } else {
        let formData = new FormData();
        formData.append("name", name);
        formData.append("email", email);
        formData.append("password", password);

        try {
    let res = await fetch("http://localhost/web/register.php", {
        method: "POST",
        body: formData
    });

    let data = await res.json();

    if (data.success) {
        showMessage(data.message, "success");
        setTimeout(() => {
            window.location.href = "login.html";
        }, 2000);
    } else {
        showMessage(data.message, "error");
    }
} catch (err) {
    showMessage("Có lỗi kết nối server!", "error");
    console.error(err);
}

    }
});
