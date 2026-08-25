<?php
session_start();
require_once '../config/db_connect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // 1. Kiểm tra rỗng
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Vui lòng nhập đầy đủ tất cả thông tin.";
    } 
    // 2. CHỐT CHẶN BACKEND 1: Kiểm tra định dạng email chuẩn quốc tế
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Định dạng email không hợp lệ (Ví dụ đúng: ten@congty.com).";
    } 
    // 3. CHỐT CHẶN BACKEND 2: Xác thực 2 mật khẩu khớp nhau
    elseif ($password !== $confirm_password) {
        $error = "Mật khẩu nhập lại không khớp. Vui lòng kiểm tra lại.";
    } 
    // 4. Kiểm tra độ dài mật khẩu tối thiểu (Nên có để tăng cường bảo mật)
    elseif (strlen($password) < 6) {
        $error = "Mật khẩu quá ngắn. Vui lòng nhập ít nhất 6 ký tự.";
    }
    else {
        // Kiểm tra email đã tồn tại chưa
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        
        if ($stmt_check->get_result()->num_rows > 0) {
            $error = "Email này đã được sử dụng trong hệ thống.";
        } else {
            // Mã hóa mật khẩu và lưu
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user'; 

            $sql_insert = "INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ssss", $full_name, $email, $hashed_password, $role);
            
            if ($stmt_insert->execute()) {
                $success = "Tạo tài khoản thành công! Bạn có thể đăng nhập ngay bây giờ.";
            } else {
                $error = "Lỗi hệ thống: Không thể tạo tài khoản. " . $conn->error;
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <title>Đăng ký - RoomHub</title>
</head>
<body class="bg-[#f4f2fc] flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-[#00288e] tracking-tight">RoomHub</h1>
            <p class="text-gray-500 mt-2">Đăng ký tài khoản nội bộ</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-100 text-red-800 p-3 rounded-lg mb-4 text-sm font-medium flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">error</span>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-100 text-green-800 p-3 rounded-lg mb-4 text-sm font-medium flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="flex flex-col gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Họ và tên</label>
                <input type="text" name="full_name" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
                       class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:border-[#00288e] focus:ring-1 focus:ring-[#00288e]">
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email công ty</label>
                <input type="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                       class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:border-[#00288e] focus:ring-1 focus:ring-[#00288e]">
            </div>
            
            <!-- Trường Mật khẩu có nút xem -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Mật khẩu</label>
                <div class="relative">
                    <input type="password" name="password" id="reg_password" required 
                           class="w-full pl-4 pr-10 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:border-[#00288e] focus:ring-1 focus:ring-[#00288e]">
                    <button type="button" onclick="togglePassword('reg_password', 'icon_pass')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-[#00288e]">
                        <span id="icon_pass" class="material-symbols-outlined text-[20px]">visibility</span>
                    </button>
                </div>
            </div>

            <!-- Trường Nhập lại mật khẩu có nút xem -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Xác nhận mật khẩu</label>
                <div class="relative">
                    <input type="password" name="confirm_password" id="reg_confirm_password" required 
                           class="w-full pl-4 pr-10 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:border-[#00288e] focus:ring-1 focus:ring-[#00288e]">
                    <button type="button" onclick="togglePassword('reg_confirm_password', 'icon_confirm')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-[#00288e]">
                        <span id="icon_confirm" class="material-symbols-outlined text-[20px]">visibility</span>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full py-3 mt-4 bg-[#00288e] text-white rounded-lg font-bold hover:bg-blue-800 transition-colors shadow-md">Đăng Ký</button>
        </form>
        
        <p class="text-center text-sm text-gray-600 mt-6">
            Đã có tài khoản? <a href="login.php" class="text-[#00288e] font-bold hover:underline">Đăng nhập</a>
        </p>
    </div>

    <!-- Script xử lý bật/tắt hiển thị mật khẩu -->
    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerText = 'visibility_off'; // Đổi icon sang con mắt gạch chéo
            } else {
                input.type = 'password';
                icon.innerText = 'visibility'; // Trở lại con mắt bình thường
            }
        }
    </script>
</body>
</html>