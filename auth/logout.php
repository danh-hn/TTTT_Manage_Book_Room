<?php
// Bắt buộc phải gọi session_start() để nhận diện phiên làm việc hiện tại
session_start();

// 1. Xóa toàn bộ các biến trong mảng session
$_SESSION = array();

// 2. Hủy hoàn toàn cookie của session trên trình duyệt (nếu có sử dụng)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Hủy bỏ hoàn toàn Session trên server
session_destroy();

// 4. Chuyển hướng người dùng về trang đăng nhập
header("Location: login.php?msg=logged_out");
exit();
?>