<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <title>Đăng nhập - RoomHub</title>
</head>
<body class="bg-[#f4f2fc] flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-[#00288e] tracking-tight">RoomHub</h1>
            <p class="text-gray-500 mt-2">Đăng nhập để đặt phòng</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-100 text-red-800 p-3 rounded-lg mb-4 text-sm font-medium"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Form trỏ tới router chung public/index.php -->
        <form action="index.php?controller=auth&action=login" method="POST" class="flex flex-col gap-4">
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
            Chưa có tài khoản? <a href="index.php?controller=auth&action=register" class="text-[#00288e] font-bold hover:underline">Đăng ký ngay</a>
        </p>
    </div>
    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerText = 'visibility_off';
            } else {
                input.type = 'password';
                icon.innerText = 'visibility';
            }
        }
    </script>
</body>
</html>