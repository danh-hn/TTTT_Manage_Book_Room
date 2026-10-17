<html lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { "primary": "#00288e", "surface": "#fbf8ff", "surface-container": "#eeedf7", "on-surface": "#1a1b22", "outline-variant": "#c4c5d5", "error": "#ba1a1a", "error-container": "#ffdad6" } } }
        }
    </script>
    <style>body { background-color: #f4f2fc; font-family: 'Inter', sans-serif; }</style>
</head>
<body class="text-on-surface flex items-center justify-center min-h-screen p-6">

    <div class="w-full max-w-2xl bg-surface rounded-2xl shadow-lg p-8">
        
        <div class="flex items-center gap-4 border-b border-outline-variant pb-4 mb-6">
            <a href="index.php?controller=admin&action=schedule" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined text-[24px]">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-primary">Tạo Lịch Đặt Phòng Nhanh</h1>
                <p class="text-sm text-gray-500">Đăng ký lịch họp trực tiếp với tư cách quản trị viên.</p>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-error-container text-error px-4 py-3 rounded-lg mb-6 flex items-start gap-3">
                <span class="material-symbols-outlined">warning</span>
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>

        <form action="index.php?controller=admin&action=createBooking" method="POST" class="flex flex-col gap-5">
            
            <div class="flex flex-col gap-1">
                <label class="text-sm font-semibold text-gray-700">Chọn phòng họp *</label>
                <select name="room_id" required class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary">
                    <option value="">-- Chọn phòng họp --</option>
                    <?php foreach ($rooms as $room): ?>
                        <option value="<?php echo $room['id']; ?>">
                            <?php echo htmlspecialchars($room['room_name'] . ' (Sức chứa tối đa: ' . $room['capacity'] . ' người)'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-semibold text-gray-700">Tiêu đề cuộc họp / Mục đích *</label>
                <input type="text" name="title" required placeholder="VD: Họp tổng kết ban điều hành" 
                       class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-gray-700">Thời gian bắt đầu *</label>
                    <input type="datetime-local" name="start_time" required 
                           class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-gray-700">Thời gian kết thúc *</label>
                    <input type="datetime-local" name="end_time" required 
                           class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary">
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-semibold text-gray-700">Số lượng người tham gia *</label>
                <input type="number" name="participant_count" min="1" required placeholder="VD: 10" 
                       class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary">
            </div>

            <div class="flex justify-end gap-3 mt-4 pt-4 border-t border-outline-variant">
                <a href="index.php?controller=admin&action=schedule" class="px-6 py-2 rounded-lg text-gray-600 hover:bg-gray-100 font-semibold transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="px-6 py-2 rounded-lg bg-primary text-white font-semibold hover:bg-blue-800 transition-colors shadow-md">
                    Xác Nhận Đặt Phòng
                </button>
            </div>
        </form>

    </div>
</body>
</html>