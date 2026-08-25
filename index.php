<?php
session_start();

// CHỐT CHẶN BẢO MẬT: Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

require_once 'config/db_connect.php';

// Lấy danh sách phòng ĐANG HOẠT ĐỘNG
$sql_rooms = "SELECT * FROM rooms WHERE status = 'active' ORDER BY room_name ASC";
$result_rooms = $conn->query($sql_rooms);
$active_rooms = [];
if ($result_rooms->num_rows > 0) {
    while($r = $result_rooms->fetch_assoc()) {
        $active_rooms[] = $r;
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config={theme:{extend:{colors:{"primary":"#00288e","surface":"#fbf8ff","surface-container":"#eeedf7","surface-container-low":"#f4f2fc","surface-container-high":"#e8e7f1","on-surface":"#1a1b22","on-surface-variant":"#444653","outline-variant":"#c4c5d5","error":"#ba1a1a","error-container":"#ffdad6","on-error-container":"#93000a","on-primary":"#ffffff"}}}}
    </script>
    <style>
        body { background-color: #fbf8ff; font-family: 'Inter', sans-serif; }
        /* Ẩn scrollbar cho modal mượt hơn */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #c4c5d5; border-radius: 4px; }
    </style>
</head>
<body class="text-on-surface">
    
    <!-- Sidebar -->
    <aside class="fixed left-0 top-0 h-full w-72 bg-surface-container-low z-40 flex flex-col py-6 border-r border-outline-variant">
        <div class="px-8 mb-8 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-3xl">meeting_room</span>
            <span class="text-xl font-bold text-primary tracking-tight">RoomHub</span>
        </div>
        <nav class="flex-1 px-4 flex flex-col gap-1">
            <a class="flex items-center px-4 py-3 rounded-xl bg-blue-100 text-blue-900 font-semibold transition-all" href="index.php">
                <span class="material-symbols-outlined mr-4">home</span><span>Trang chủ Đặt phòng</span>
            </a>
            <a class="flex items-center px-4 py-3 rounded-xl text-on-surface-variant hover:bg-surface-container-high transition-all" href="my_bookings.php">
                <span class="material-symbols-outlined mr-4">history</span><span>Lịch sử của tôi</span>
            </a>
            <?php if($_SESSION['role'] === 'admin'): ?>
            <div class="my-2 border-t border-outline-variant mx-4 opacity-50"></div>
            <a class="flex items-center px-4 py-3 rounded-xl text-on-surface-variant hover:bg-surface-container-high transition-all" href="admin/dashboard.php">
                <span class="material-symbols-outlined mr-4">admin_panel_settings</span><span>Quản trị viên</span>
            </a>
            <?php endif; ?>
        </nav>
        <div class="px-4 pt-6 border-t border-outline-variant">
            <div class="flex items-center gap-3 px-4 py-2 bg-surface-container-highest rounded-xl">
                <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-bold">
                    <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
                </div>
                <div class="overflow-hidden flex-1">
                    <p class="font-bold text-sm text-on-surface truncate"><?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
                    <p class="text-xs text-on-surface-variant truncate uppercase tracking-widest">Nhân viên</p>
                </div>
                <a href="auth/logout.php" class="text-error hover:bg-error-container p-2 rounded-full transition-colors" title="Đăng xuất">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                </a>
            </div>
        </div>
    </aside>

    <div class="pl-72">
        <main class="p-10 min-h-screen">
            <div class="max-w-[1200px] mx-auto flex flex-col gap-8">
                
                <div class="flex justify-between items-end">
                    <div>
                        <h1 class="text-4xl font-bold text-on-surface">Danh sách phòng họp</h1>
                        <p class="text-on-surface-variant mt-2">Lựa chọn không gian phù hợp cho cuộc họp của bạn.</p>
                    </div>
                    <button onclick="openBookingModal()" class="bg-primary text-white hover:bg-blue-800 transition-all px-6 py-3 rounded-xl font-bold uppercase tracking-wider flex items-center gap-2 shadow-md">
                        <span class="material-symbols-outlined">add</span> Đặt phòng nhanh
                    </button>
                </div>

                <!-- Grid Phòng -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($active_rooms as $room): ?>
                        <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all border border-outline-variant flex flex-col">
                            <div class="h-40 w-full relative">
                                <img src="<?php echo htmlspecialchars($room['image_url'] ?? 'https://via.placeholder.com/400x200'); ?>" alt="Room" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                <div class="absolute bottom-3 left-4 right-4">
                                    <h3 class="text-white font-bold text-lg"><?php echo htmlspecialchars($room['room_name']); ?></h3>
                                    <p class="text-gray-200 text-sm flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">location_on</span> <?php echo htmlspecialchars($room['location']); ?></p>
                                </div>
                            </div>
                            <div class="p-5 flex flex-col gap-4 flex-1">
                                <div class="flex justify-between items-center text-sm text-on-surface-variant">
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-primary text-[18px]">groups</span> <?php echo $room['capacity']; ?> người</span>
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-tertiary text-[18px]">aspect_ratio</span> <?php echo $room['area']; ?> m²</span>
                                </div>
                                <div class="text-sm">
                                    <p class="text-on-surface-variant mb-1 font-semibold">Thiết bị:</p>
                                    <p class="text-on-surface line-clamp-2"><?php echo htmlspecialchars($room['equipment_details']); ?></p>
                                </div>
                                <div class="mt-auto pt-4">
                                    <button onclick="openBookingModal(<?php echo $room['id']; ?>)" class="w-full py-2 bg-surface-container hover:bg-primary hover:text-white text-primary font-bold rounded-lg transition-colors border border-primary/20">
                                        Chọn phòng này
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </main>
    </div>

    <!-- MODAL ĐẶT PHÒNG (Ẩn mặc định) -->
    <div id="bookingModal" class="fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-300">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeBookingModal()"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="flex justify-between items-center p-6 border-b border-outline-variant bg-surface">
                <h2 class="text-2xl font-bold text-primary">Đăng ký mượn phòng</h2>
                <button onclick="closeBookingModal()" class="text-gray-400 hover:text-error transition-colors">
                    <span class="material-symbols-outlined text-2xl">close</span>
                </button>
            </div>

            <div class="p-6 overflow-y-auto custom-scrollbar">
                <!-- Chú ý action trỏ về book_room.php -->
                <form id="bookingForm" action="book_room.php" method="POST" class="flex flex-col gap-4">
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Chọn Phòng *</label>
                        <select name="room_id" id="room_select" required class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                            <option value="" disabled selected>-- Vui lòng chọn phòng --</option>
                            <?php foreach ($active_rooms as $room): ?>
                                <option value="<?php echo $room['id']; ?>" data-capacity="<?php echo $room['capacity']; ?>">
                                    [<?php echo htmlspecialchars($room['room_number']); ?>] <?php echo htmlspecialchars($room['room_name']); ?> (Tối đa: <?php echo $room['capacity']; ?> người)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tiêu đề cuộc họp *</label>
                        <input type="text" name="title" required placeholder="VD: Họp giao ban dự án mới" class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Số người tham gia *</label>
                            <input type="number" name="participant_count" id="participant_count" min="1" value="1" required class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                            <p id="participant_error" class="text-xs text-error mt-1 hidden"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Dịch vụ đi kèm</label>
                            <select name="extra_services" class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                                <option value="Không">Không cần thiết</option>
                                <option value="Nước suối">Chuẩn bị Nước suối</option>
                                <option value="Teabreak">Trà & Bánh (Teabreak)</option>
                                <option value="IT Support">Hỗ trợ IT (Máy chiếu/Mic)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Thời gian bắt đầu *</label>
                            <input type="datetime-local" name="start_time" id="start_time" required class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Thời gian kết thúc *</label>
                            <input type="datetime-local" name="end_time" id="end_time" required class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                            <p id="end_time_error" class="text-xs text-error mt-1 hidden"></p>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-outline-variant flex justify-end gap-3">
                        <button type="button" onclick="closeBookingModal()" class="px-6 py-2 rounded-lg text-gray-600 font-semibold hover:bg-gray-100">Hủy</button>
                        <button type="submit" id="submitBtn" class="px-6 py-2 rounded-lg bg-primary text-white font-bold hover:bg-blue-800 shadow-md">Xác Nhận Đặt Phòng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SCRIPT XỬ LÝ GIAO DIỆN VÀ VALIDATION LỚP 1 -->
    <script>
        const modal = document.getElementById('bookingModal');
        const roomSelect = document.getElementById('room_select');
        const participantInput = document.getElementById('participant_count');
        const participantError = document.getElementById('participant_error');
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const endTimeError = document.getElementById('end_time_error');
        const submitBtn = document.getElementById('submitBtn');

        // Mở Modal (Có thể auto-select phòng nếu truyền ID)
        function openBookingModal(roomId = null) {
            modal.classList.remove('hidden');
            setTimeout(() => modal.classList.remove('opacity-0'), 10);
            if(roomId) roomSelect.value = roomId;
            validateCapacity(); // Chạy check ngay nếu đã chọn phòng
        }

        // Đóng Modal
        function closeBookingModal() {
            modal.classList.add('opacity-0');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        // 1. Chặn thời gian quá khứ
        function setMinDateTime() {
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            const nowString = now.toISOString().slice(0, 16);
            startTimeInput.min = nowString;
            endTimeInput.min = nowString;
        }

        // 2. Kiểm tra tổng hợp (Sức chứa + Thời gian)
        function validateForm() {
            let isValid = true;

            // Check Sức chứa
            participantError.classList.add('hidden');
            participantInput.classList.remove('border-error');
            const selectedOption = roomSelect.options[roomSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                const roomCapacity = parseInt(selectedOption.getAttribute('data-capacity'));
                const inputParticipants = parseInt(participantInput.value);
                if (inputParticipants > roomCapacity) {
                    participantError.innerText = `Vượt quá sức chứa tối đa (${roomCapacity} người).`;
                    participantError.classList.remove('hidden');
                    participantInput.classList.add('border-error');
                    isValid = false;
                }
            }

            // Check Thời gian
            endTimeError.classList.add('hidden');
            endTimeInput.classList.remove('border-error');
            const startVal = startTimeInput.value;
            const endVal = endTimeInput.value;
            if (startVal && endVal) {
                if (new Date(endVal) <= new Date(startVal)) {
                    endTimeError.innerText = 'Thời gian kết thúc phải diễn ra sau khi bắt đầu.';
                    endTimeError.classList.remove('hidden');
                    endTimeInput.classList.add('border-error');
                    isValid = false;
                }
            }

            submitBtn.disabled = !isValid;
            submitBtn.className = isValid 
                ? "px-6 py-2 rounded-lg bg-primary text-white font-bold hover:bg-blue-800 shadow-md transition-colors" 
                : "px-6 py-2 rounded-lg bg-gray-400 text-white font-bold cursor-not-allowed";
        }

        setMinDateTime();
        
        // Lắng nghe sự kiện
        roomSelect.addEventListener('change', validateForm);
        participantInput.addEventListener('input', validateForm);
        startTimeInput.addEventListener('change', () => {
            if (startTimeInput.value) endTimeInput.min = startTimeInput.value;
            validateForm();
        });
        endTimeInput.addEventListener('change', validateForm);
    </script>
</body>
</html>