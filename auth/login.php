<?php
session_start();
require_once '../config/db_connect.php';

// Nếu đã đăng nhập thì đá về trang chủ tùy theo quyền
if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role'] === 'admin' ? '../admin/dashboard.php' : '../index.php'));
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Vui lòng nhập email và mật khẩu.";
    } else {
        $stmt = $conn->prepare("SELECT id, full_name, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Xử lý song song: Chấp nhận cả mật khẩu đã hash (đăng ký mới) và mật khẩu thô (từ file mock data cũ)
            $is_password_correct = false;
            if (password_verify($password, $user['password'])) {
                $is_password_correct = true;
            } elseif ($password === $user['password']) {
                $is_password_correct = true; // Hỗ trợ account mock data '123456'
            }

            if ($is_password_correct) {
                // Khởi tạo phiên làm việc (Session)
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                
                // Điều hướng dựa trên phân quyền
                if ($user['role'] === 'admin') {
    header("Location: ../admin/dashboard.php");
} else {
    header("Location: ../index.php");
}
exit();
            } else {
                $error = "Mật khẩu không chính xác.";
            }
        } else {
            $error = "Tài khoản không tồn tại.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Đăng nhập - RoomHub</title>
</head>
<body class="bg-[#f4f2fc] flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-[#00288e] tracking-tight">RoomHub</h1>
            <p class="text-gray-500 mt-2">Đăng nhập để đặt phòng</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-100 text-red-800 p-3 rounded-lg mb-4 text-sm font-medium"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="flex flex-col gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:border-[#00288e] focus:ring-1 focus:ring-[#00288e]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Mật khẩu</label>
                <div class="relative">
                    <input type="password" name="password" id="login_password" required class="w-full pl-4 pr-10 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:border-[#00288e] focus:ring-1 focus:ring-[#00288e]">
                    <button type="button" onclick="togglePassword('login_password', 'icon_login')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-[#00288e]">
                        <span id="icon_login" class="material-symbols-outlined text-[20px]">visibility</span>
                    </button>
                </div>
            </div>
            <button type="submit" class="w-full py-3 mt-4 bg-[#00288e] text-white rounded-lg font-bold hover:bg-blue-800 transition-colors shadow-md">Đăng Nhập</button>
        </form>
        <p class="text-center text-sm text-gray-600 mt-6">
            Chưa có tài khoản? <a href="register.php" class="text-[#00288e] font-bold hover:underline">Đăng ký ngay</a>
        </p>
    </div>
</body>
</html>