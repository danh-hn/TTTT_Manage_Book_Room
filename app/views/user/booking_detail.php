<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Lấy dữ liệu an toàn: Ưu tiên dữ liệu do Controller truyền sang, sau đó mới lấy từ POST/GET
$current_start =$start_time ?? $_POST['start_time'] ?? $_GET['start'] ?? '';
$current_end =$end_time ?? $_POST['end_time'] ?? $_GET['end'] ?? '';
$current_holding_id =$holding_id ?? $_POST['holding_id'] ?? $_GET['holding_id'] ?? '0';

// Dùng SESSION của Server để khóa cứng thời gian (Chống F5 reset)
$session_key = 'expiry_time_holding_' .$current_holding_id;
if (!isset($_SESSION[$session_key])) {
    $_SESSION[$session_key] = time() + 300; // 300 giây = 5 phút
}

// Tính số giây còn lại thực tế từ Server
$time_left = $_SESSION[$session_key] - time();
if ($time_left < 0) {$time_left = 0;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config={theme:{extend:{colors:{"primary":"#00288e","surface":"#fbf8ff","surface-container":"#eeedf7","surface-container-low":"#f4f2fc","surface-container-high":"#e8e7f1","on-surface":"#1a1b22","on-surface-variant":"#444653","outline-variant":"#c4c5d5","error":"#ba1a1a","error-container":"#ffdad6","on-error-container":"#93000a","on-primary":"#ffffff"}}}}
    </script>
    <style>body { background-color: #f4f2fc; font-family: 'Inter', sans-serif; }</style>
</head>
<body class="text-on-surface p-8">

    <div class="max-w-6xl mx-auto flex flex-col gap-6">
        
        <div class="bg-blue-50 border border-blue-200 p-4 rounded-2xl flex justify-between items-center shadow-sm">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary text-2xl">lock_clock</span>
                <p class="text-sm font-semibold text-primary">
                    Phòng <span class="font-bold"><?php echo htmlspecialchars($room['room_name'] ?? ''); ?></span> đang được giữ chỗ tạm thời cho bạn.
                </p>
            </div>
            <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-xl border border-blue-200 shadow-inner">
                <span class="material-symbols-outlined text-error text-[18px]">timer</span>
                <span class="text-xs text-gray-500">CÒN LẠI:</span>
                <span id="countdown" class="font-mono font-bold text-error text-base">00:00</span>
            </div>
        </div>

        <form action="index.php?controller=user&action=bookRoomWithDetails" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <input type="hidden" name="room_id" value="<?php echo htmlspecialchars((string)($room['id'] ?? '')); ?>">
            <!-- THẺ ẨN CHỨA DỮ LIỆU ĐỂ KHÔNG TẠO ĐƠN RÁC -->
            <input type="hidden" name="holding_id" value="<?php echo htmlspecialchars((string)$current_holding_id); ?>">

            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-outline-variant p-8 flex flex-col gap-6">
                
                <div>
                    <h1 class="text-2xl font-bold text-on-surface">Chi tiết phiên họp</h1>
                    <p class="text-sm text-on-surface-variant mt-1">Vui lòng hoàn tất thông tin để hệ thống gửi lịch tới người tham gia.</p>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-gray-700">Tên cuộc họp *</label>
                    <input type="text" name="title" required placeholder="VD: Họp rà soát tiến độ dự án..." 
                           class="px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl focus:outline-none focus:border-primary font-medium">
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-gray-700">Mục đích & Nội dung cuộc họp</label>
                    <textarea name="description" rows="3" placeholder="Nhập tóm tắt nội dung cuộc họp..." 
                              class="px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl focus:outline-none focus:border-primary text-sm"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-surface-container-low rounded-xl border border-outline-variant/30 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold">
                            <?php echo strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)); ?>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Người chủ trì</p>
                            <p class="font-bold text-sm text-on-surface"><?php echo htmlspecialchars($_SESSION['full_name'] ?? ''); ?></p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-gray-700">Mời người tham dự (Email nội bộ)</label>
                    <input type="text" name="attendees" placeholder="Nhập email cách nhau bởi dấu phẩy..." 
                           class="px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl focus:outline-none focus:border-primary text-sm">
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-semibold text-gray-700">Số người tham gia *</label>
                        <input type="number" name="participant_count" min="1" max="<?php echo htmlspecialchars((string)($room['capacity'] ?? 10)); ?>" value="1" required 
                               class="px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl focus:outline-none focus:border-primary">
                    </div>
                    
                    <!-- Khóa khung giờ, tự động điền dữ liệu an toàn -->
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-semibold text-gray-700">Thời gian bắt đầu</label>
                        <input type="datetime-local" name="start_time" value="<?php echo htmlspecialchars((string)$current_start); ?>" readonly 
                               class="px-4 py-3 bg-gray-100 border border-outline-variant rounded-xl text-gray-500 cursor-not-allowed text-sm">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-semibold text-gray-700">Thời gian kết thúc</label>
                        <input type="datetime-local" name="end_time" value="<?php echo htmlspecialchars((string)$current_end); ?>" readonly 
                               class="px-4 py-3 bg-gray-100 border border-outline-variant rounded-xl text-gray-500 cursor-not-allowed text-sm">
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-gray-700">Dịch vụ & Yêu cầu hỗ trợ bổ sung</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="flex items-center gap-2 p-3 bg-surface-container-low rounded-xl border border-outline-variant/30 cursor-pointer text-sm font-medium">
                            <input type="checkbox" name="services[]" value="Teabreak tiêu chuẩn" class="w-4 h-4 text-primary rounded"> Teabreak tiêu chuẩn
                        </label>
                        <label class="flex items-center gap-2 p-3 bg-surface-container-low rounded-xl border border-outline-variant/30 cursor-pointer text-sm font-medium">
                            <input type="checkbox" name="services[]" value="Hỗ trợ kỹ thuật IT" class="w-4 h-4 text-primary rounded"> Hỗ trợ kỹ thuật IT
                        </label>
                        <label class="flex items-center gap-2 p-3 bg-surface-container-low rounded-xl border border-outline-variant/30 cursor-pointer text-sm font-medium">
                            <input type="checkbox" name="services[]" value="Văn phòng phẩm" class="w-4 h-4 text-primary rounded"> Văn phòng phẩm
                        </label>
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-gray-700">Ghi chú cho bộ phận Lễ tân & Vận hành</label>
                    <input type="text" name="notes" placeholder="VD: Chuẩn bị thêm 2 ghế phụ, điều hòa 24 độ..." 
                           class="px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl focus:outline-none focus:border-primary text-sm">
                </div>
            </div>

            <div class="flex flex-col gap-6">
                <div class="bg-white rounded-2xl shadow-sm border border-outline-variant overflow-hidden sticky top-6">
                    <div class="h-44 w-full relative">
                        <img src="<?php echo htmlspecialchars($room['image_url'] ?? 'https://via.placeholder.com/400x200'); ?>" alt="Room" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <div class="absolute bottom-3 left-4 right-4">
                            <h3 class="text-white font-bold text-lg"><?php echo htmlspecialchars($room['room_name'] ?? ''); ?></h3>
                            <p class="text-gray-200 text-xs"><?php echo htmlspecialchars($room['location'] ?? ''); ?></p>
                        </div>
                    </div>

                    <div class="p-6 flex flex-col gap-4 text-sm">
                        <div class="flex items-center gap-2 text-green-700 bg-green-50 p-3 rounded-xl font-medium">
                            <span class="material-symbols-outlined text-[18px]">check_circle</span> Đang khóa khung giờ an toàn
                        </div>

                        <div class="flex flex-col gap-2 text-on-surface-variant border-t border-outline-variant/30 pt-4">
                            <p class="flex justify-between"><span>Sức chứa tối đa:</span> <strong class="text-on-surface"><?php echo htmlspecialchars((string)($room['capacity'] ?? '')); ?> Người</strong></p>
                            <p class="flex justify-between"><span>Diện tích:</span> <strong class="text-on-surface"><?php echo htmlspecialchars((string)($room['area'] ?? '')); ?> m²</strong></p>
                        </div>

                        <div class="mt-4 pt-4 border-t border-outline-variant flex flex-col gap-3">
                            <button type="submit" class="w-full py-3.5 bg-primary text-white font-bold rounded-xl hover:bg-blue-800 transition-colors shadow-md flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-[20px]">task_alt</span> Xác nhận & Hoàn tất đặt phòng
                            </button>
                            <a href="index.php?controller=user&action=index" class="w-full py-2.5 bg-surface-container hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors text-center text-xs">
                                Hủy bỏ (Quay lại chọn phòng)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // NGĂN TRÌNH DUYỆT GỬI LẠI FORM (RESUBMIT) KHI NHẤN F5
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        // Lấy số giây chuẩn xác từ SERVER (PHP)
        let timeLeft = <?php echo $time_left; ?>;
        const countdownEl = document.getElementById('countdown');
        const holdingId = <?php echo json_encode($current_holding_id); ?>;

        const timer = setInterval(() => {
            if (timeLeft <= 0) {
                clearInterval(timer);
                countdownEl.textContent = "00:00";
                alert('Hết thời gian giữ chỗ tạm thời! Hệ thống sẽ tự động hủy đơn và đưa bạn về trang chủ.');
                window.location.href = `index.php?controller=user&action=cancelHolding&holding_id=${holdingId}`;
                return;
            }
            
            let minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;
            
            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;
            
            countdownEl.textContent = `${minutes}:${seconds}`;
            timeLeft--;
        }, 1000);
    </script>
</body>
</html>