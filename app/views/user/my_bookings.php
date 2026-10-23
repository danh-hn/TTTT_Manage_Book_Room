<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config={theme:{extend:{"colors":{"primary":"#00288e","surface":"#fbf8ff","surface-container":"#eeedf7","surface-container-low":"#f4f2fc","surface-container-high":"#e8e7f1","on-surface":"#1a1b22","on-surface-variant":"#444653","outline-variant":"#c4c5d5","error":"#ba1a1a","error-container":"#ffdad6","on-error-container":"#93000a","on-primary":"#ffffff"}}}}
    </script>
    <style>body { background-color: #fbf8ff; font-family: 'Inter', sans-serif; }</style>
</head>
<body class="text-on-surface">
    
    <!-- Sidebar -->
    <aside class="fixed left-0 top-0 h-full w-72 bg-surface-container-low z-40 flex flex-col py-6 border-r border-outline-variant">
        <div class="px-8 mb-8 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-3xl">meeting_room</span>
            <span class="text-xl font-bold text-primary tracking-tight">RoomHub</span>
        </div>
        <nav class="flex-1 px-4 flex flex-col gap-1">
            <a class="flex items-center px-4 py-3 rounded-xl text-on-surface-variant hover:bg-surface-container-high transition-all" href="index.php?controller=user&action=index">
                <span class="material-symbols-outlined mr-4">home</span><span>Trang chủ Đặt phòng</span>
            </a>
            <a class="flex items-center px-4 py-3 rounded-xl bg-blue-100 text-blue-900 font-semibold transition-all" href="index.php?controller=user&action=myBookings">
                <span class="material-symbols-outlined mr-4">history</span><span>Lịch sử của tôi</span>
            </a>
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <div class="my-2 border-t border-outline-variant mx-4 opacity-50"></div>
            <a class="flex items-center px-4 py-3 rounded-xl text-on-surface-variant hover:bg-surface-container-high transition-all" href="index.php?controller=admin&action=dashboard">
                <span class="material-symbols-outlined mr-4">admin_panel_settings</span><span>Quản trị viên</span>
            </a>
            <?php endif; ?>
        </nav>
        <div class="px-4 pt-6 border-t border-outline-variant">
            <div class="flex items-center gap-3 px-4 py-2 bg-surface-container-highest rounded-xl">
                <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-bold">
                    <?php echo strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)); ?>
                </div>
                <div class="overflow-hidden flex-1">
                    <p class="font-bold text-sm text-on-surface truncate"><?php echo htmlspecialchars($_SESSION['full_name'] ?? ''); ?></p>
                    <p class="text-xs text-on-surface-variant truncate uppercase tracking-widest">Nhân viên</p>
                </div>
                <a href="index.php?controller=auth&action=logout" class="text-error hover:bg-error-container p-2 rounded-full transition-colors" title="Đăng xuất">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                </a>
            </div>
        </div>
    </aside>

    <div class="pl-72">
        <main class="p-10 min-h-screen">
            <div class="max-w-[1200px] mx-auto flex flex-col gap-8">
                
                <div>
                    <h1 class="text-4xl font-bold text-on-surface">Lịch sử đặt phòng</h1>
                    <p class="text-on-surface-variant mt-2">Theo dõi trạng thái phê duyệt các yêu cầu của bạn. <span class="text-primary font-semibold">Nhấn vào một dòng bất kỳ trong bảng để xem chi tiết.</span></p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-outline-variant overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-container-low text-on-surface-variant text-sm uppercase tracking-wider">
                                <th class="py-4 px-6 font-bold">Phòng</th>
                                <th class="py-4 px-6 font-bold">Thời gian họp</th>
                                <th class="py-4 px-6 font-bold">Tiêu đề / Mục đích</th>
                                <th class="py-4 px-6 font-bold text-center">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <?php
                            if (!empty($history)) {
                                foreach ($history as $row) {
                                    $start_time = date('H:i, d/m/Y', strtotime($row['start_time']));
                                    $end_time = date('H:i, d/m/Y', strtotime($row['end_time']));
                                    
                                    $now = time();
                                    $start_ts = strtotime($row['start_time']);
                                    $end_ts = strtotime($row['end_time']);
                                    $db_status = $row['status'];
                                    $expires_at = $row['expires_at'] ?? null;
                                    
                                    $status_html = '';
                                    $status_text_modal = '';

                                    if ($db_status === 'holding') {
                                        if ($expires_at && strtotime($expires_at) > $now) {
                                            $holding_id = $row['id'];
                                            $status_html = '
                                                <div class="flex flex-col items-center gap-2" onclick="event.stopPropagation();">
                                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-100 text-amber-800 font-bold text-xs animate-pulse">
                                                        <span class="material-symbols-outlined text-[14px]">lock_clock</span> Đang giữ chỗ tạm
                                                    </span>
                                                    <a href="index.php?controller=user&action=bookingDetail&holding_id=' . $holding_id . '" 
                                                       class="inline-flex items-center gap-1 px-4 py-2 rounded-xl bg-primary text-white font-bold text-xs hover:bg-blue-800 shadow-sm transition-all">
                                                        <span class="material-symbols-outlined text-[16px]">play_arrow</span> Tiếp tục đặt
                                                    </a>
                                                </div>';
                                            $status_text_modal = 'Đang giữ chỗ tạm thời';
                                        } else {
                                            $status_html = '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-200 text-gray-600 font-bold text-xs"><span class="material-symbols-outlined text-[14px]">timer_off</span> Hết hạn giữ chỗ</span>';
                                            $status_text_modal = 'Hết hạn giữ chỗ';
                                        }
                                    } elseif ($db_status === 'cancelled') {
                                        $status_html = '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-red-100 text-red-800 font-bold"><span class="material-symbols-outlined text-[16px]">cancel</span> Bị từ chối</span>';
                                        $status_text_modal = 'Bị từ chối';
                                    } elseif ($db_status === 'pending') {
                                        if ($start_ts < $now) {
                                            $status_html = '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-200 text-gray-600 font-bold"><span class="material-symbols-outlined text-[16px]">timer_off</span> Quá thời hạn</span>';
                                            $status_text_modal = 'Quá thời hạn';
                                        } else {
                                            $status_html = '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 font-bold"><span class="material-symbols-outlined text-[16px]">pending</span> Chờ duyệt</span>';
                                            $status_text_modal = 'Chờ duyệt';
                                        }
                                    } elseif ($db_status === 'approved') {
                                        if ($now < $start_ts) {
                                            $status_html = '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-100 text-green-800 font-bold"><span class="material-symbols-outlined text-[16px]">event_available</span> Đã duyệt</span>';
                                            $status_text_modal = 'Đã duyệt';
                                        } elseif ($now >= $start_ts && $now <= $end_ts) {
                                            $status_html = '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-100 text-blue-800 font-bold animate-pulse"><span class="material-symbols-outlined text-[16px]">play_circle</span> Đang diễn ra</span>';
                                            $status_text_modal = 'Đang diễn ra';
                                        } else {
                                            $status_html = '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-surface-variant text-on-surface-variant font-bold"><span class="material-symbols-outlined text-[16px]">task_alt</span> Đã hoàn thành</span>';
                                            $status_text_modal = 'Đã hoàn thành';
                                        }
                                    }
                                    ?>
                                    <tr class="border-t border-outline-variant hover:bg-blue-50/60 transition-colors cursor-pointer"
                                        onclick="showBookingDetails(
                                            '<?php echo htmlspecialchars(addslashes($row['title'] ?? '')); ?>',
                                            '<?php echo htmlspecialchars(addslashes($row['room_name'] . ' (Số phòng: ' . $row['room_number'] . ' - ' . ($row['location'] ?? 'Chưa rõ tầng') . ')')); ?>',
                                            '<?php echo $start_time; ?>',
                                            '<?php echo $end_time; ?>',
                                            '<?php echo (int)($row['participant_count'] ?? 1); ?> người',
                                            '<?php echo htmlspecialchars(addslashes($row['extra_services'] ?? 'Không có')); ?>',
                                            '<?php echo $db_status; ?>',
                                            '<?php echo $status_text_modal; ?>'
                                        )">
                                        <td class="py-4 px-6">
                                            <p class="font-bold text-primary"><?php echo htmlspecialchars($row['room_name']); ?></p>
                                            <p class="text-xs text-on-surface-variant mt-1">
                                                <span class="inline-block bg-surface-container border border-outline-variant px-2 py-0.5 rounded mr-1">
                                                    Số phòng: <?php echo htmlspecialchars($row['room_number']); ?>
                                                </span>
                                                <span class="inline-block bg-surface-container border border-outline-variant px-2 py-0.5 rounded">
                                                    <?php echo htmlspecialchars($row['location'] ?? 'Chưa rõ tầng'); ?>
                                                </span>
                                            </p>
                                        </td>
                                        <td class="py-4 px-6">
                                            <p class="font-medium"><?php echo $start_time; ?></p>
                                            <p class="text-on-surface-variant">đến <?php echo $end_time; ?></p>
                                        </td>
                                        <td class="py-4 px-6">
                                            <p class="font-medium text-on-surface line-clamp-2"><?php echo htmlspecialchars($row['title']); ?></p>
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <?php echo $status_html; ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='4' class='py-8 text-center text-on-surface-variant'>Bạn chưa có yêu cầu đặt phòng nào.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </main>
    </div>

    <!-- MODAL CHI TIẾT LỊCH SỬ ĐẶT PHÒNG -->
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
                        <p class="text-xs text-gray-500">Thông tin chi tiết yêu cầu của bạn</p>
                    </div>
                </div>
                <button onclick="closeBookingModal()" class="text-gray-400 hover:text-gray-700 transition-colors bg-white hover:bg-gray-100 rounded-full w-9 h-9 flex items-center justify-center shadow-sm border border-gray-200">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            
            <!-- Body -->
            <div class="p-6 flex flex-col gap-5 text-sm max-h-[75vh] overflow-y-auto">
                <!-- Chủ đề cuộc họp -->
                <div class="bg-blue-50/60 p-4 rounded-2xl border border-blue-100/60">
                    <p class="text-[11px] font-bold text-blue-600 uppercase tracking-wider mb-1">Chủ đề / Mục đích cuộc họp</p>
                    <p id="modalTitle" class="font-bold text-base text-gray-900 leading-snug"></p>
                </div>

                <!-- Thông tin phòng họp -->
                <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-primary shrink-0 mt-0.5 border border-gray-100">
                        <span class="material-symbols-outlined text-[18px]">meeting_room</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Phòng họp</p>
                        <p id="modalRoom" class="font-semibold text-gray-900 mt-0.5"></p>
                    </div>
                </div>

                <!-- Thời gian bắt đầu & kết thúc -->
                <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 flex flex-col gap-3">
                    <div class="flex items-center justify-between border-b border-gray-200/60 pb-2.5">
                        <span class="text-xs font-bold text-gray-500 uppercase flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-emerald-600">schedule</span> Bắt đầu
                        </span>
                        <span id="modalStart" class="font-semibold text-gray-900"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500 uppercase flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-rose-600">schedule</span> Kết thúc
                        </span>
                        <span id="modalEnd" class="font-semibold text-gray-900"></span>
                    </div>
                </div>

                <!-- Số người tham dự & Trạng thái -->
                <div class="grid grid-cols-2 gap-4 items-center">
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-orange-600 shrink-0 border border-gray-100">
                            <span class="material-symbols-outlined text-[18px]">group</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Số người</p>
                            <p id="modalCount" class="font-semibold text-gray-900 text-sm mt-0.5"></p>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 flex flex-col justify-center h-full">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Trạng thái duyệt</p>
                        <div>
                            <span id="modalStatus" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider"></span>
                        </div>
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
        function showBookingDetails(title, room, start, end, count, services, dbStatus, statusText) {
            document.getElementById('modalTitle').textContent = title || 'Không có tiêu đề';
            document.getElementById('modalRoom').textContent = room;
            document.getElementById('modalStart').textContent = start;
            document.getElementById('modalEnd').textContent = end;
            document.getElementById('modalCount').textContent = count;
            document.getElementById('modalServices').textContent = services || 'Không có yêu cầu thêm';

            const statusEl = document.getElementById('modalStatus');
            statusEl.textContent = statusText;

            if (dbStatus === 'approved') {
                statusEl.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200';
            } else if (dbStatus === 'pending') {
                statusEl.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200';
            } else if (dbStatus === 'holding') {
                statusEl.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-50 text-amber-800 border border-amber-200';
            } else {
                statusEl.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-red-50 text-red-700 border border-red-200';
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
</body>
</html>