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
                <span class="material-symbols-outlined mr-md">dashboard</span><span class="font-body-md text-body-md">Tổng quan Admin</span>
            </a>
            <a class="flex items-center px-md py-md rounded-xl bg-secondary-container text-on-secondary-container font-semibold transition-all group" href="index.php?controller=admin&action=schedule">
                <span class="material-symbols-outlined mr-md">calendar_month</span><span class="font-body-md text-body-md">Lịch phòng họp</span>
            </a>
            <a class="flex items-center px-md py-md rounded-xl text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-all group" href="index.php?controller=admin&action=manageRooms">
                <span class="material-symbols-outlined mr-md">apartment</span><span class="font-body-md text-body-md">Quản lý phòng</span>
            </a>
            <a class="flex items-center px-md py-md rounded-xl text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-all group" href="index.php?controller=admin&action=approveBookings">
                <span class="material-symbols-outlined mr-md">task_alt</span><span class="font-body-md text-body-md">Phê duyệt</span>
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
                                    <div class="font-bold text-sm text-on-surface truncate"><?php echo htmlspecialchars($room['room_name']); ?></div>
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
                        
                        <!-- Header các mốc giờ (06:00 - 24:00) -->
                        <div class="grid gap-1 mb-4 text-center text-[11px] font-bold text-on-surface-variant sticky top-0 bg-white/90 backdrop-blur z-20 py-2" style="grid-template-columns: repeat(18, minmax(0, 1fr));">
                            <div>06:00</div><div>07:00</div><div>08:00</div><div>09:00</div>
                            <div>10:00</div><div>11:00</div><div>12:00</div><div>13:00</div>
                            <div>14:00</div><div>15:00</div><div>16:00</div><div>17:00</div>
                            <div>18:00</div><div>19:00</div><div>20:00</div><div>21:00</div>
                            <div>22:00</div><div>23:00</div>
                        </div>

                        <!-- Render các dòng Timeline tương ứng với từng phòng -->
                        <?php if (!empty($rooms)): ?>
                            <?php foreach ($rooms as $room): ?>
                                <div class="relative h-24 bg-surface-container-low rounded-xl mb-4 border border-outline-variant/30 flex items-center group">
                                    
                                    <!-- Nhãn tên phòng mờ ở góc -->
                                    <span class="absolute left-4 font-bold text-gray-300 pointer-events-none"><?php echo htmlspecialchars($room['room_number'] ?? $room['room_name']); ?></span>

                                    <!-- Vẽ các cuộc họp của phòng này -->
                                    <?php 
                                    if (isset($bookings_by_room[$room['id']])) {
                                        foreach ($bookings_by_room[$room['id']] as $booking) {
                                            $start_ts = strtotime($booking['start_time']);
                                            $end_ts = strtotime($booking['end_time']);
                                            
                                            $start_minutes = (int)date('H', $start_ts) * 60 + (int)date('i', $start_ts);
                                            $end_minutes = (int)date('H', $end_ts) * 60 + (int)date('i', $end_ts);
                                            
                                            $grid_start_minutes = 360; 
                                            $total_grid_minutes = 18 * 60; 
                                            
                                            $safe_start = max($start_minutes, $grid_start_minutes);
                                            $safe_end = min($end_minutes, $grid_start_minutes + $total_grid_minutes);
                                            $duration = $safe_end - $safe_start;
                                            
                                            if ($duration > 0) {
                                                $left_percent = (($safe_start - $grid_start_minutes) / $total_grid_minutes) * 100;
                                                $width_percent = ($duration / $total_grid_minutes) * 100;
                                                ?>
                                                
                                                <div class="absolute top-2 bottom-2 bg-blue-100/80 hover:bg-blue-200 border-l-4 border-primary rounded shadow-sm p-2 cursor-pointer transition-colors overflow-hidden"
                                                     style="left: <?php echo $left_percent; ?>%; width: <?php echo $width_percent; ?>%;"
                                                     title="<?php echo htmlspecialchars($booking['title']); ?> (<?php echo date('H:i', $start_ts) . ' - ' . date('H:i', $end_ts); ?>)">
                                                    <div class="text-xs font-bold text-primary truncate"><?php echo htmlspecialchars($booking['title']); ?></div>
                                                    <div class="text-[10px] text-blue-800 truncate"><?php echo htmlspecialchars($booking['full_name']); ?></div>
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
</html>