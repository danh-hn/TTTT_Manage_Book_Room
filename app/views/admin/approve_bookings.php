<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
// Nhận biến từ AdminController truyền sang
$pending_count = isset($pending_bookings) ? count($pending_bookings) : 0;
$approved_count = isset($approved_bookings) ? count($approved_bookings) : 0;
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
            <a class="flex items-center px-md py-md rounded-xl text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-all group" href="index.php?controller=admin&action=schedule">
                <span class="material-symbols-outlined mr-md">calendar_month</span><span class="font-medium">Lịch phòng họp</span>
            </a>
            <a class="flex items-center px-md py-md rounded-xl text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-all group" href="index.php?controller=admin&action=manageRooms">
                <span class="material-symbols-outlined mr-md">apartment</span><span class="font-medium">Quản lý phòng</span>
            </a>
            <a class="flex items-center px-md py-md rounded-xl bg-secondary-container text-on-secondary-container font-semibold transition-all group" href="index.php?controller=admin&action=approveBookings">
                <span class="material-symbols-outlined mr-md">task_alt</span><span class="font-medium">Phê duyệt</span>
            </a>
        </nav>
        <div class="px-md pt-lg border-t border-outline-variant">
            <div class="flex items-center gap-md px-md py-sm bg-surface-container-highest rounded-xl">
                <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-on-primary text-[18px]">person</span>
                </div>
                <div class="overflow-hidden">
                    <p class="font-label-md text-label-md text-on-surface truncate"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Admin'); ?></p>
                    <p class="text-[10px] text-on-surface-variant truncate uppercase tracking-widest">Quản trị viên</p>
                </div>
            </div>
        </div>
    </aside>

    <div class="pl-72">
        <main class="relative p-10 min-h-screen">
            <div class="max-w-[1400px] mx-auto flex flex-col gap-8">
                
                <!-- Header -->
                <div class="flex justify-between items-end">
                    <div>
                        <h1 class="text-4xl font-bold text-on-surface">Phê duyệt yêu cầu</h1>
                        <p class="text-on-surface-variant mt-2">Quản lý các yêu cầu đặt phòng đang chờ xử lý.</p>
                    </div>
                    <div class="bg-surface-container-high rounded-full px-4 py-2 flex items-center gap-2 shadow-sm">
                        <span class="material-symbols-outlined text-primary">notifications_active</span>
                        <span class="font-semibold text-sm uppercase tracking-wider"><?php echo $pending_count; ?> yêu cầu mới</span>
                    </div>
                </div>

                <!-- CẢNH BÁO LỖI VÀ THÀNH CÔNG -->
                <?php if (isset($_GET['msg'])): ?>
                    <?php if ($_GET['msg'] === 'error'): ?>
                        <div class="mt-2 bg-error-container text-error px-5 py-4 rounded-xl flex items-center gap-3 shadow-sm font-semibold border border-error">
                            <span class="material-symbols-outlined text-[24px]">warning</span>
                            <p>Từ chối duyệt: Phòng này đã có một lịch khác được phê duyệt trùng thời gian!</p>
                        </div>
                    <?php elseif ($_GET['msg'] === 'approved'): ?>
                        <div class="mt-2 bg-green-100 text-green-800 px-5 py-4 rounded-xl flex items-center gap-3 shadow-sm font-semibold border border-green-300">
                            <span class="material-symbols-outlined text-[24px]">check_circle</span>
                            <p>Đã phê duyệt yêu cầu đặt phòng thành công.</p>
                        </div>
                    <?php elseif ($_GET['msg'] === 'cancelled'): ?>
                        <div class="mt-2 bg-gray-100 text-gray-800 px-5 py-4 rounded-xl flex items-center gap-3 shadow-sm font-semibold border border-gray-300">
                            <span class="material-symbols-outlined text-[24px]">delete</span>
                            <p>Đã từ chối/xóa yêu cầu đặt phòng.</p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- DANH SÁCH CHỜ DUYỆT -->
                <div>
                    <h2 class="text-xl font-bold text-on-surface border-b border-outline-variant pb-2 mb-6">Yêu cầu chờ duyệt</h2>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <?php
                        if ($pending_count > 0) {
                            foreach ($pending_bookings as $row) {
                                $now = time();
                                $start_ts = strtotime($row['start_time']);
                                $time_diff = $start_ts - $now;
                                
                                $is_expired = ($time_diff < 0);
                                $is_urgent = (!$is_expired && $time_diff <= 86400);
                                
                                $start_str = date('H:i', $start_ts);
                                $end_str = date('H:i, d/m/Y', strtotime($row['end_time']));
                                
                                $border_color = 'border-outline-variant';
                                if ($is_urgent) $border_color = 'border-error';
                                if ($is_expired) $border_color = 'border-gray-300 opacity-75';
                                ?>
                                <div class="bg-white rounded-2xl p-6 shadow-sm border <?php echo $border_color; ?> relative flex flex-col gap-4">
                                    <?php if ($is_urgent): ?>
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-error rounded-l-2xl"></div>
                                    <?php elseif ($is_expired): ?>
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gray-400 rounded-l-2xl"></div>
                                    <?php endif; ?>

                                    <div class="flex justify-between items-start">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-full bg-surface-container flex items-center justify-center text-primary font-bold text-xl <?php if($is_expired) echo 'grayscale'; ?>">
                                                <?php echo strtoupper(substr($row['full_name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-on-surface"><?php echo htmlspecialchars($row['full_name']); ?></h3>
                                                <p class="text-sm text-on-surface-variant"><?php echo htmlspecialchars($row['email']); ?></p>
                                            </div>
                                        </div>
                                        
                                        <?php if ($is_expired): ?>
                                            <span class="bg-gray-200 text-gray-600 px-2 py-1 rounded text-xs font-bold uppercase flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[14px]">timer_off</span> Quá hạn
                                            </span>
                                        <?php elseif ($is_urgent): ?>
                                            <span class="bg-error-container text-error px-2 py-1 rounded text-xs font-bold uppercase flex items-center gap-1">
                                                <span class="w-2 h-2 rounded-full bg-error animate-pulse"></span> Khẩn cấp
                                            </span>
                                        <?php else: ?>
                                            <span class="text-on-surface-variant text-xs font-semibold uppercase">Bình thường</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="bg-surface-container-low rounded-xl p-4 flex flex-col gap-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-xl font-bold text-primary"><?php echo htmlspecialchars($row['room_name'] . ' (' . $row['room_number'] . ')'); ?></span>
                                            <span class="flex items-center gap-1 text-sm bg-white px-2 py-1 rounded shadow-sm">
                                                <span class="material-symbols-outlined text-[16px]">group</span> 
                                                <?php echo $row['participant_count']; ?> / <?php echo $row['capacity']; ?>
                                            </span>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4 mt-2">
                                            <div>
                                                <span class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Thời gian</span>
                                                <span class="text-sm font-medium text-on-surface flex items-center gap-1 <?php if($is_expired) echo 'text-error line-through'; ?>">
                                                    <span class="material-symbols-outlined text-[18px] text-primary">schedule</span>
                                                    <?php echo $start_str . ' - ' . $end_str; ?>
                                                </span>
                                            </div>
                                            <div>
                                                <span class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Mục đích</span>
                                                <span class="text-sm text-on-surface line-clamp-2"><?php echo htmlspecialchars($row['title']); ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex gap-4 mt-auto pt-2">
                                        <a href="index.php?controller=admin&action=processBooking&action_type=reject&id=<?php echo $row['id']; ?>" class="flex-1 py-3 rounded-xl text-center text-sm font-bold uppercase tracking-wider text-error bg-error-container/30 hover:bg-error-container transition-colors" onclick="return confirm('Bạn có chắc chắn muốn TỪ CHỐI yêu cầu này?');">
                                            <?php echo $is_expired ? 'Xóa yêu cầu' : 'Từ chối'; ?>
                                        </a>
                                        
                                        <?php if (!$is_expired): ?>
                                        <a href="index.php?controller=admin&action=processBooking&action_type=approve&id=<?php echo $row['id']; ?>" class="flex-1 py-3 rounded-xl flex items-center justify-center gap-2 text-sm font-bold uppercase tracking-wider text-white bg-primary hover:bg-blue-800 transition-colors shadow-md" onclick="return confirm('Xác nhận PHÊ DUYỆT yêu cầu này?');">
                                            <span class="material-symbols-outlined text-[18px]">check_circle</span> Phê duyệt
                                        </a>
                                        <?php else: ?>
                                        <button disabled class="flex-1 py-3 rounded-xl flex items-center justify-center gap-2 text-sm font-bold uppercase tracking-wider text-white bg-gray-400 cursor-not-allowed">
                                            <span class="material-symbols-outlined text-[18px]">block</span> Hết hạn
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo "<div class='col-span-full text-center py-10 bg-white rounded-xl shadow-sm border border-outline-variant'>";
                            echo "<span class='material-symbols-outlined text-4xl text-gray-300 mb-2'>task</span>";
                            echo "<p class='text-lg text-on-surface-variant'>Hiện tại không có yêu cầu nào đang chờ phê duyệt.</p>";
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>

                <!-- DANH SÁCH ĐÃ DUYỆT / LỊCH SỬ -->
                <div class="mt-8">
                    <h2 class="text-xl font-bold text-on-surface border-b border-outline-variant pb-2 mb-6">Lịch sử đã phê duyệt (Gần đây)</h2>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <?php
                        if ($approved_count > 0) {
                            foreach ($approved_bookings as $row) {
                                $start_ts = strtotime($row['start_time']);
                                $end_ts = strtotime($row['end_time']);
                                $now = time();
                                
                                $start_str = date('H:i', $start_ts);
                                $end_str = date('H:i, d/m/Y', $end_ts);
                                
                                $is_finished = ($end_ts < $now);
                                
                                $card_border = $is_finished ? 'border-gray-200 opacity-75' : 'border-green-200 hover:opacity-100';
                                $accent_color = $is_finished ? 'bg-gray-400' : 'bg-green-500';
                                ?>
                                <div class="bg-white rounded-2xl p-6 shadow-sm border <?php echo $card_border; ?> relative flex flex-col gap-4 transition-opacity">
                                    <div class="absolute left-0 top-0 bottom-0 w-1 <?php echo $accent_color; ?> rounded-l-2xl"></div>

                                    <div class="flex justify-between items-start">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-full <?php echo $is_finished ? 'bg-gray-100 text-gray-600' : 'bg-green-50 text-green-700'; ?> flex items-center justify-center font-bold text-xl">
                                                <?php echo strtoupper(substr($row['full_name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-on-surface"><?php echo htmlspecialchars($row['full_name']); ?></h3>
                                                <p class="text-sm text-on-surface-variant"><?php echo htmlspecialchars($row['email']); ?></p>
                                            </div>
                                        </div>

                                        <?php if ($is_finished): ?>
                                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold uppercase flex items-center gap-1 border border-gray-200">
                                                <span class="material-symbols-outlined text-[14px]">task_alt</span> Đã hoàn thành
                                            </span>
                                        <?php else: ?>
                                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold uppercase flex items-center gap-1 border border-green-200">
                                                <span class="material-symbols-outlined text-[14px]">check_circle</span> Đã duyệt
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="<?php echo $is_finished ? 'bg-gray-50' : 'bg-green-50/50'; ?> rounded-xl p-4 flex flex-col gap-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-lg font-bold <?php echo $is_finished ? 'text-gray-700' : 'text-green-800'; ?>"><?php echo htmlspecialchars($row['room_name'] . ' (' . $row['room_number'] . ')'); ?></span>
                                            <span class="flex items-center gap-1 text-sm bg-white px-2 py-1 rounded shadow-sm text-gray-600">
                                                <span class="material-symbols-outlined text-[16px]">group</span> 
                                                <?php echo $row['participant_count']; ?>
                                            </span>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4 mt-2">
                                            <div>
                                                <span class="block text-xs font-bold text-gray-500 uppercase mb-1">Thời gian</span>
                                                <span class="text-sm font-medium text-gray-800 flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[18px] <?php echo $is_finished ? 'text-gray-500' : 'text-green-600'; ?>">schedule</span>
                                                    <?php echo $start_str . ' - ' . $end_str; ?>
                                                </span>
                                            </div>
                                            <div>
                                                <span class="block text-xs font-bold text-gray-500 uppercase mb-1">Mục đích</span>
                                                <span class="text-sm text-gray-800 line-clamp-2"><?php echo htmlspecialchars($row['title']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Nút Hủy Lịch dành cho Admin nếu có sự cố thay đổi lịch -->
                                    <div class="mt-2 text-right">
                                        <a href="index.php?controller=admin&action=processBooking&action_type=reject&id=<?php echo $row['id']; ?>" class="text-xs font-bold text-error hover:underline" onclick="return confirm('CẢNH BÁO: Bạn có chắc chắn muốn HỦY lịch này không?');">
                                            Hủy lịch trình này
                                        </a>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo "<div class='col-span-full text-center py-6 bg-white rounded-xl shadow-sm border border-outline-variant'>";
                            echo "<p class='text-sm text-on-surface-variant'>Chưa có yêu cầu nào được phê duyệt gần đây.</p>";
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>
</html>