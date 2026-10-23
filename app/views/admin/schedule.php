<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
?>
<html lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <style>@layer base{html,body{margin:0;padding:0;}body{overscroll-behavior:none;}main>:first-child{margin-top:0!important;}main>:last-child{margin-bottom:0!important;}}::-webkit-scrollbar{display:none;}</style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">tailwind.config={theme:{extend:{"colors":{"tertiary-container":"#872d00","on-background":"#1a1b22","on-tertiary-fixed":"#380d00","on-tertiary-fixed-variant":"#802a00","on-tertiary-container":"#ffa583","surface-variant":"#e3e1eb","surface-dim":"#dad9e3","tertiary-fixed":"#ffdbce","primary-container":"#1e40af","secondary-container":"#d0e1fb","surface-container-high":"#e8e7f1","primary-fixed-dim":"#b8c4ff","on-surface-variant":"#444653","tertiary":"#611e00","inverse-surface":"#2f3037","surface-tint":"#3755c3","background":"#fbf8ff","secondary":"#505f76","on-secondary-fixed-variant":"#38485d","primary-fixed":"#dde1ff","surface-container-highest":"#e3e1eb","on-primary-fixed":"#001453","surface":"#fbf8ff","on-secondary-fixed":"#0b1c30","tertiary-fixed-dim":"#ffb59a","surface-container":"#eeedf7","outline-variant":"#c4c5d5","on-primary-fixed-variant":"#173bab","on-surface":"#1a1b22","on-primary-container":"#a8b8ff","secondary-fixed-dim":"#b7c8e1","secondary-fixed":"#d3e4fe","on-error-container":"#93000a","surface-container-low":"#f4f2fc","surface-bright":"#fbf8ff","on-tertiary":"#ffffff","on-error":"#ffffff","outline":"#757684","inverse-on-surface":"#f1f0fa","surface-container-lowest":"#ffffff","on-primary":"#ffffff","primary":"#00288e","on-secondary-container":"#54647a","error":"#ba1a1a","inverse-primary":"#b8c4ff","error-container":"#ffdad6","on-secondary":"#ffffff"},"borderRadius":{"DEFAULT":"0.125rem","lg":"0.25rem","xl":"0.5rem","full":"0.75rem"},"spacing":{"lg":"24px","xs":"4px","gutter":"20px","base":"4px","xl":"32px","sm":"8px","margin-mobile":"16px","md":"16px","margin-desktop":"40px"},"fontFamily":{"code-sm":["Inter"],"headline-lg-mobile":["Inter"],"body-lg":["Inter"],"headline-lg":["Inter"],"label-md":["Inter"],"body-md":["Inter"],"headline-md":["Inter"],"display-lg":["Inter"]},"fontSize":{"code-sm":["13px",{"lineHeight":"18px","fontWeight":"500"}],"headline-lg-mobile":["24px",{"lineHeight":"32px","fontWeight":"600"}],"body-lg":["16px",{"lineHeight":"24px","fontWeight":"400"}],"headline-lg":["32px",{"lineHeight":"40px","letterSpacing":"-0.01em","fontWeight":"600"}],"label-md":["12px",{"lineHeight":"16px","letterSpacing":"0.05em","fontWeight":"600"}],"body-md":["14px",{"lineHeight":"20px","fontWeight":"400"}],"headline-md":["20px",{"lineHeight":"28px","fontWeight":"600"}],"display-lg":["48px",{"lineHeight":"56px","letterSpacing":"-0.02em","fontWeight":"700"}]}}}}</script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet"/>
</head>
<body class="text-on-surface">
    <!-- Sidebar -->
    <aside class="fixed left-0 top-0 h-full w-72 bg-surface-container-low z-50 flex flex-col py-lg">
        <div class="px-xl mb-xl flex items-center gap-sm">
            <span class="material-symbols-outlined text-primary text-[32px]">meeting_room</span>
            <span class="font-headline-md text-headline-md text-primary tracking-tight">RoomHub</span>
        </div>
        <nav class="flex-1 px-md flex flex-col gap-xs" data-active-classes="bg-secondary-container text-on-secondary-container font-semibold">
            <a class="flex items-center px-md py-md rounded-xl text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-all group" href="index.php?controller=admin&action=dashboard">
                <span class="material-symbols-outlined mr-md">dashboard</span><span class="font-medium">Tổng quan Admin</span>
            </a>
            <a class="flex items-center px-md py-md rounded-xl bg-secondary-container text-on-secondary-container font-semibold transition-all group" href="index.php?controller=admin&action=schedule">
                <span class="material-symbols-outlined mr-md">calendar_month</span><span class="font-medium">Lịch phòng họp</span>
            </a>
            <a class="flex items-center px-md py-md rounded-xl text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-all group" href="index.php?controller=admin&action=manageRooms">
                <span class="material-symbols-outlined mr-md">apartment</span><span class="font-medium">Quản lý phòng</span>
            </a>
            <a class="flex items-center px-md py-md rounded-xl text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-all group" href="index.php?controller=admin&action=approveBookings">
                <span class="material-symbols-outlined mr-md">task_alt</span><span class="font-medium">Phê duyệt</span>
            </a>
        </nav>
        <div class="px-md pt-lg border-t border-outline-variant">
            <div class="flex items-center gap-md px-md py-sm bg-surface-container-highest rounded-xl">
                <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-on-primary text-[18px]">person</span>
                </div>
                <div class="overflow-hidden">
                    <p class="font-label-md text-label-md text-on-surface truncate"><?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
                    <p class="text-[10px] text-on-surface-variant truncate uppercase tracking-widest">Quản trị viên</p>
                </div>
            </div>
        </div>
    </aside>

    <div class="pl-72">
        <header class="h-16 bg-white/80 backdrop-blur-md border-b border-outline-variant flex items-center justify-between px-10 sticky top-0 z-40">
            <h1 class="text-xl font-bold">Chi tiết lịch đặt phòng</h1>
            <a href="index.php?controller=admin&action=createBooking" class="bg-primary text-white px-4 py-2 rounded-full text-sm font-semibold flex items-center gap-2 hover:bg-blue-800 shadow-md">
                <span class="material-symbols-outlined text-[18px]">add</span> Đặt phòng nhanh
            </a>
        </header>

        <main class="p-10 flex gap-8 min-h-[calc(100vh-4rem)]">
            
            <!-- Cột trái: Danh sách phòng -->
            <aside class="w-64 bg-white rounded-2xl shadow-sm border border-outline-variant flex flex-col overflow-hidden shrink-0">
                <div class="p-4 bg-surface-container-low border-b border-outline-variant">
                    <h2 class="font-bold text-on-surface">Danh mục phòng</h2>
                </div>
                <div class="overflow-y-auto flex-1 p-2 flex flex-col gap-2">
                    <?php if (!empty($rooms)): ?>
                        <?php foreach ($rooms as $room): ?>
                            <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-surface-container transition-colors text-left group">
                                <div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-on-surface-variant">meeting_room</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-sm text-on-surface truncate">
                                        <?php echo htmlspecialchars($room['room_name'] . ' (' . $room['room_number'] . ')'); ?>
                                    </div>
                                    <div class="text-xs text-on-surface-variant flex items-center gap-1 mt-1">
                                        <span class="material-symbols-outlined text-[14px]">group</span> <?php echo $room['capacity']; ?> người
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </aside>

            <!-- Cột phải: Lưới thời gian (Timeline) -->
            <section class="flex-1 bg-white rounded-2xl shadow-sm border border-outline-variant flex flex-col overflow-hidden">
                <!-- Điều hướng ngày -->
                <div class="p-4 flex items-center justify-between border-b border-outline-variant bg-surface-container-lowest">
                    <div class="flex items-center gap-4">
                        <h2 class="text-xl font-bold text-on-surface">Ngày <?php echo htmlspecialchars($display_date_str); ?></h2>
                        <div class="flex gap-1">
                            <a href="index.php?controller=admin&action=schedule&date=<?php echo date('Y-m-d', strtotime($current_date . ' -1 day')); ?>" class="w-8 h-8 rounded-full bg-surface-container flex items-center justify-center hover:bg-gray-200">
                                <span class="material-symbols-outlined">chevron_left</span>
                            </a>
                            <a href="index.php?controller=admin&action=schedule&date=<?php echo date('Y-m-d', strtotime($current_date . ' +1 day')); ?>" class="w-8 h-8 rounded-full bg-surface-container flex items-center justify-center hover:bg-gray-200">
                                <span class="material-symbols-outlined">chevron_right</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Khu vực vẽ lưới -->
                <div class="flex-1 overflow-auto p-4 relative">
                    <div class="min-w-[1200px]">
                        
                        <!-- Header các mốc giờ (00:00 - 24:00) -->
                        <div class="grid gap-1 mb-4 text-center text-[11px] font-bold text-on-surface-variant sticky top-0 bg-white/90 backdrop-blur z-20 py-2" style="grid-template-columns: repeat(24, minmax(0, 1fr));">
                            <div>00:00</div><div>01:00</div><div>02:00</div><div>03:00</div>
                            <div>04:00</div><div>05:00</div><div>06:00</div><div>07:00</div>
                            <div>08:00</div><div>09:00</div><div>10:00</div><div>11:00</div>
                            <div>12:00</div><div>13:00</div><div>14:00</div><div>15:00</div>
                            <div>16:00</div><div>17:00</div><div>18:00</div><div>19:00</div>
                            <div>20:00</div><div>21:00</div><div>22:00</div><div>23:00</div>
                        </div>

                        <!-- Render các dòng Timeline tương ứng với từng phòng -->
                        <?php if (!empty($rooms)): ?>
                            <?php foreach ($rooms as $room): ?>
                                <div class="relative h-24 bg-surface-container-low rounded-xl mb-4 border border-outline-variant/30 flex items-center group">
                                    
                                    <!-- Nhãn tên phòng mờ ở góc -->
                                    <span class="absolute left-4 font-bold text-gray-300 pointer-events-none truncate max-w-[200px]">
                                        <?php echo htmlspecialchars($room['room_name'] . ' (' . $room['room_number'] . ')'); ?>
                                    </span>

                                    <!-- Vẽ các cuộc họp của phòng này -->
                                    <?php 
                                    if (isset($bookings_by_room[$room['id']])) {
                                        foreach ($bookings_by_room[$room['id']] as $booking) {
                                            $start_ts = strtotime($booking['start_time']);
                                            $end_ts = strtotime($booking['end_time']);
                                            
                                            $start_minutes = (int)date('H', $start_ts) * 60 + (int)date('i', $start_ts);
                                            // Xử lý trường hợp giờ kết thúc là 00:00 của ngày hôm sau (được tính là 24:00)
                                            $end_hour = (int)date('H', $end_ts);
                                            $end_minutes_part = (int)date('i', $end_ts);
                                            $end_minutes = ($end_hour == 0 && $end_minutes_part == 0 && date('Y-m-d', $end_ts) > date('Y-m-d', $start_ts)) ? 24 * 60 : $end_hour * 60 + $end_minutes_part;
                                            
                                            // Đặt lại lưới bắt đầu từ 0h (0 phút) và tổng cộng 24h (24 * 60 phút)
                                            $grid_start_minutes = 0; 
                                            $total_grid_minutes = 24 * 60; 
                                            
                                            $safe_start = max($start_minutes, $grid_start_minutes);
                                            $safe_end = min($end_minutes, $grid_start_minutes + $total_grid_minutes);
                                            $duration = $safe_end - $safe_start;
                                            
                                            if ($duration > 0) {
                                                $left_percent = (($safe_start - $grid_start_minutes) / $total_grid_minutes) * 100;
                                                $width_percent = ($duration / $total_grid_minutes) * 100;
                                                ?>
                                                
                                                <div class="absolute top-2 bottom-2 bg-blue-100/80 hover:bg-blue-200 border-l-4 border-primary rounded shadow-sm p-2 cursor-pointer transition-colors overflow-hidden"
                                                     style="left: <?php echo $left_percent; ?>%; width: <?php echo $width_percent; ?>%;"
                                                     onclick="showBookingDetails(
                                                         '<?php echo htmlspecialchars(addslashes($booking['title'] ?? '')); ?>',
                                                         '<?php echo htmlspecialchars(addslashes($booking['full_name'] ?? 'Khách')); ?>',
                                                         '<?php echo date('H:i d/m/Y', $start_ts); ?>',
                                                         '<?php echo date('H:i d/m/Y', $end_ts); ?>',
                                                         '<?php echo htmlspecialchars(addslashes($room['room_name'] . ' (' . $room['room_number'] . ')')); ?>',
                                                         '<?php echo htmlspecialchars($booking['status'] ?? 'pending'); ?>',
                                                         '<?php echo (int)($booking['participant_count'] ?? 0); ?>',
                                                         '<?php echo htmlspecialchars(addslashes($booking['extra_services'] ?? 'Không có')); ?>'
                                                     )">
                                                    <div class="text-xs font-bold text-primary truncate"><?php echo htmlspecialchars($booking['title'] ?? 'Không tiêu đề'); ?></div>
                                                    <div class="text-[10px] text-blue-800 truncate"><?php echo htmlspecialchars($booking['full_name'] ?? 'Khách'); ?></div>
                                                </div>

                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    </div>
                </div>
            </section>
        </main>
    </div>
</body>

<!-- MODAL CHI TIẾT CUỘC HỌP (ĐÃ THIẾT KẾ LẠI ĐẸP VÀ CHUYÊN NGHIỆP HƠN) -->
    <div id="bookingModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden transform scale-95 transition-all duration-300 border border-gray-100" id="bookingModalContent">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-slate-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-primary flex items-center justify-center font-bold">
                        <span class="material-symbols-outlined text-[22px]">event_note</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Chi tiết lịch họp</h3>
                        <p class="text-xs text-gray-500">Thông tin chi tiết về phiên họp</p>
                    </div>
                </div>
                <button onclick="closeBookingModal()" class="text-gray-400 hover:text-gray-700 transition-colors bg-white hover:bg-gray-100 rounded-full w-9 h-9 flex items-center justify-center shadow-sm border border-gray-200">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            
            <!-- Body -->
            <div class="p-6 flex flex-col gap-4 text-sm max-h-[75vh] overflow-y-auto">
                <!-- Chủ đề cuộc họp -->
                <div class="bg-blue-50/60 p-4 rounded-2xl border border-blue-100/60">
                    <p class="text-[11px] font-bold text-blue-600 uppercase tracking-wider mb-1">Chủ đề cuộc họp</p>
                    <p id="modalTitle" class="font-bold text-base text-gray-900 leading-snug"></p>
                </div>

                <!-- 3 Thẻ div nằm ngang hàng: Người đặt | Phòng họp | Số người -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <!-- Người đặt -->
                    <div class="bg-gray-50 p-3.5 rounded-2xl border border-gray-100 flex flex-col justify-between">
                        <div class="flex items-center gap-1.5 text-gray-400 mb-1">
                            <span class="material-symbols-outlined text-[16px]">person</span>
                            <p class="text-[10px] font-bold uppercase tracking-wider">Người đặt</p>
                        </div>
                        <p id="modalUser" class="font-semibold text-gray-900 text-xs leading-relaxed"></p>
                    </div>

                    <!-- Phòng họp (Không bị giới hạn truncate, hỗ trợ xuống dòng tự nhiên) -->
                    <div class="bg-gray-50 p-3.5 rounded-2xl border border-gray-100 flex flex-col justify-between">
                        <div class="flex items-center gap-1.5 text-primary mb-1">
                            <span class="material-symbols-outlined text-[16px]">meeting_room</span>
                            <p class="text-[10px] font-bold uppercase tracking-wider">Phòng họp</p>
                        </div>
                        <p id="modalRoom" class="font-semibold text-gray-900 text-xs leading-relaxed"></p>
                    </div>

                    <!-- Số người -->
                    <div class="bg-gray-50 p-3.5 rounded-2xl border border-gray-100 flex flex-col justify-between">
                        <div class="flex items-center gap-1.5 text-orange-600 mb-1">
                            <span class="material-symbols-outlined text-[16px]">group</span>
                            <p class="text-[10px] font-bold uppercase tracking-wider">Số người</p>
                        </div>
                        <p id="modalCount" class="font-semibold text-gray-900 text-xs leading-relaxed"></p>
                    </div>
                </div>

                <!-- Thời gian bắt đầu & kết thúc -->
                <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 flex flex-col gap-3">
                    <div class="flex items-center justify-between border-b border-gray-200/60 pb-2.5">
                        <span class="text-xs font-bold text-gray-500 uppercase flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-emerald-600">schedule</span> Bắt đầu
                        </span>
                        <span id="modalStart" class="font-semibold text-gray-900 text-xs sm:text-sm"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500 uppercase flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-rose-600">schedule</span> Kết thúc
                        </span>
                        <span id="modalEnd" class="font-semibold text-gray-900 text-xs sm:text-sm"></span>
                    </div>
                </div>

                <!-- Trạng thái -->
                <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Trạng thái phê duyệt</p>
                        <span id="modalStatus" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider"></span>
                    </div>
                </div>

                <!-- Dịch vụ & Yêu cầu thêm -->
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Dịch vụ & Yêu cầu thêm</p>
                    <div id="modalServices" class="text-gray-700 bg-gray-50 p-3.5 rounded-2xl border border-gray-100 text-xs leading-relaxed min-h-[44px] flex items-center"></div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end">
                <button onclick="closeBookingModal()" class="px-6 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-semibold rounded-xl transition-colors shadow-sm text-sm">Đóng lại</button>
            </div>
        </div>
    </div>

    <!-- SCRIPT XỬ LÝ POPUP -->
    <script>
        function showBookingDetails(title, user, start, end, room, status, count, services) {
            document.getElementById('modalTitle').textContent = title || 'Không có tiêu đề';
            document.getElementById('modalUser').textContent = user;
            document.getElementById('modalRoom').textContent = room;
            document.getElementById('modalStart').textContent = start;
            document.getElementById('modalEnd').textContent = end;
            
            document.getElementById('modalCount').textContent = (count > 0 ? count + ' người' : 'Không rõ');
            document.getElementById('modalServices').textContent = services || 'Không có yêu cầu thêm';

            const statusEl = document.getElementById('modalStatus');
            if (status === 'approved') {
                statusEl.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-500"></span> Đã phê duyệt';
                statusEl.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200';
            } else if (status === 'pending') {
                statusEl.innerHTML = '<span class="w-2 h-2 rounded-full bg-amber-500"></span> Chờ duyệt';
                statusEl.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200';
            } else {
                statusEl.innerHTML = '<span class="w-2 h-2 rounded-full bg-gray-400"></span> ' + status;
                statusEl.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-gray-100 text-gray-700 border border-gray-200';
            }

            const modal = document.getElementById('bookingModal');
            const modalContent = document.getElementById('bookingModalContent');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            setTimeout(() => {
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        }

        function closeBookingModal() {
            const modal = document.getElementById('bookingModal');
            const modalContent = document.getElementById('bookingModalContent');
            
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 200);
        }
        
        document.getElementById('bookingModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeBookingModal();
            }
        });
    </script>

</html>