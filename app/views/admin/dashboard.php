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
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #c4c5d5; border-radius: 4px; }
    </style>
</head>
<body class="text-on-surface">
    
    <!-- Sidebar (Giữ nguyên bố cục cũ) -->
    <aside class="fixed left-0 top-0 h-full w-72 bg-surface-container-low z-40 flex flex-col py-6 border-r border-outline-variant">
        <div class="px-8 mb-8 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-3xl">meeting_room</span>
            <span class="text-xl font-bold text-primary tracking-tight">RoomHub</span>
        </div>
        <nav class="flex-1 px-4 flex flex-col gap-1" data-active-classes="bg-secondary-container text-on-secondary-container font-semibold">
            <a class="flex items-center px-4 py-3 rounded-xl bg-blue-100 text-blue-900 font-semibold transition-all" href="index.php?controller=admin&action=dashboard">
                <span class="material-symbols-outlined mr-4">dashboard</span><span>Tổng quan Admin</span>
            </a>
            <a class="flex items-center px-4 py-3 rounded-xl text-on-surface-variant hover:bg-surface-container-high transition-all" href="index.php?controller=admin&action=schedule">
                <span class="material-symbols-outlined mr-4">calendar_month</span><span>Lịch phòng họp</span>
            </a>
            <a class="flex items-center px-4 py-3 rounded-xl text-on-surface-variant hover:bg-surface-container-high transition-all" href="index.php?controller=admin&action=manageRooms">
                <span class="material-symbols-outlined mr-4">apartment</span><span>Quản lý phòng</span>
            </a>
            <a class="flex items-center px-4 py-3 rounded-xl text-on-surface-variant hover:bg-surface-container-high transition-all" href="index.php?controller=admin&action=approveBookings">
                <span class="material-symbols-outlined mr-4">task_alt</span><span>Phê duyệt</span>
                <?php if (!empty($pending_requests) && $pending_requests > 0): ?>
                    <span class="ml-auto bg-error text-white text-xs px-2 py-0.5 rounded-full font-bold"><?php echo $pending_requests; ?></span>
                <?php endif; ?>
            </a>
        </nav>
        <div class="px-4 pt-6 border-t border-outline-variant">
            <div class="flex items-center gap-3 px-4 py-2 bg-surface-container-highest rounded-xl">
                <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-bold">
                    <?php echo strtoupper(substr($_SESSION['full_name'] ?? 'A', 0, 1)); ?>
                </div>
                <div class="overflow-hidden flex-1">
                    <p class="font-bold text-sm text-on-surface truncate"><?php echo htmlspecialchars($_SESSION['full_name'] ?? ''); ?></p>
                    <p class="text-xs text-on-surface-variant truncate uppercase tracking-widest">Quản trị viên</p>
                </div>
                <a href="index.php?controller=auth&action=logout" class="text-error hover:bg-error-container p-2 rounded-full transition-colors" title="Đăng xuất">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="pl-72 flex flex-col min-h-screen">
        
        <!-- Top Header Bar -->
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-outline-variant flex items-center justify-between px-10 sticky top-0 z-30">
            <div class="flex items-center gap-2 text-sm text-on-surface-variant">
                <span>Trang chủ</span>
                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                <span class="font-semibold text-primary">Tổng quan</span>
            </div>
            
            <div class="flex items-center gap-6">
                <!-- Thanh tìm kiếm -->
                <div class="relative w-80">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <span class="material-symbols-outlined text-[20px]">search</span>
                    </span>
                    <input type="text" placeholder="Tìm kiếm phòng họp, cuộc họp..." class="w-full pl-10 pr-4 py-2 bg-surface-container-low border border-outline-variant rounded-full text-sm focus:outline-none focus:border-primary">
                </div>
                <!-- Ngày hệ thống -->
                <div class="flex items-center gap-2 px-4 py-2 bg-surface-container-low rounded-full text-sm font-medium text-on-surface-variant">
                    <span class="material-symbols-outlined text-[18px] text-primary">calendar_today</span>
                    <span><?php echo date('D, d/m/Y'); ?></span>
                </div>
            </div>
        </header>

        <!-- Nội dung Dashboard chính -->
        <main class="p-10 flex-1 flex flex-col gap-8 max-w-[1400px] w-full mx-auto">
            
            <!-- Banner Chào mừng -->
            <div class="flex justify-between items-center bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 p-8 rounded-3xl">
                <div>
                    <h1 class="text-3xl font-bold text-on-surface">Xin chào, <?php echo htmlspecialchars($_SESSION['full_name']); ?>! 👋</h1>
                    <p class="text-on-surface-variant mt-1">Chúc bạn có một ngày làm việc hiệu quả và quản trị hệ thống suôn sẻ.</p>
                </div>
                <a href="index.php?controller=admin&action=createBooking" class="bg-primary text-white hover:bg-blue-800 transition-all px-6 py-3 rounded-xl font-bold uppercase tracking-wider flex items-center gap-2 shadow-md">
                    <span class="material-symbols-outlined">add</span> Đặt phòng họp ngay
                </a>
            </div>

            <!-- 4 Thẻ thống kê (Metrics Grid) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Thẻ 1 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-outline-variant flex flex-col justify-between">
                    <div class="flex justify-between items-start">
                        <span class="text-sm font-semibold text-on-surface-variant uppercase tracking-wider">YÊU CẦU CHỜ DUYỆT</span>
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <span class="material-symbols-outlined">pending_actions</span>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="text-4xl font-extrabold text-on-surface"><?php echo $pending_requests ?? 0; ?></span>
                        <span class="text-xs text-amber-600 font-semibold bg-amber-50 px-2 py-0.5 rounded">Cần xử lý</span>
                    </div>
                </div>

                <!-- Thẻ 2 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-outline-variant flex flex-col justify-between">
                    <div class="flex justify-between items-start">
                        <span class="text-sm font-semibold text-on-surface-variant uppercase tracking-wider">TỔNG SỐ PHÒNG</span>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined">apartment</span>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="text-4xl font-extrabold text-on-surface"><?php echo $total_rooms ?? 0; ?></span>
                        <span class="text-xs text-on-surface-variant">Phòng hoạt động</span>
                    </div>
                </div>

                <!-- Thẻ 3 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-outline-variant flex flex-col justify-between">
                    <div class="flex justify-between items-start">
                        <span class="text-sm font-semibold text-on-surface-variant uppercase tracking-wider">ĐẶT PHÒNG THÁNG <?php echo $current_month; ?></span>
                        <div class="w-10 h-10 rounded-xl bg-green-50 text-green-700 flex items-center justify-center">
                            <span class="material-symbols-outlined">event_note</span>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="text-4xl font-extrabold text-on-surface"><?php echo $monthly_bookings ?? 0; ?></span>
                        <span class="text-xs text-green-700 font-semibold">Lượt đăng ký</span>
                    </div>
                </div>

                <!-- Thẻ 4 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-outline-variant flex flex-col justify-between">
                    <div class="flex justify-between items-start">
                        <span class="text-sm font-semibold text-on-surface-variant uppercase tracking-wider">TRẠNG THÁI HỆ THỐNG</span>
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center">
                            <span class="material-symbols-outlined">verified</span>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-green-500 animate-pulse"></span>
                        <span class="text-lg font-bold text-green-700">Hoạt động ổn định</span>
                    </div>
                </div>
            </div>

            <!-- Khu vực nội dung chia 2 cột -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Cột trái (Chiếm 2 phần): Hoạt động gần đây & Biểu đồ thống kê tuần -->
                <div class="lg:col-span-2 flex flex-col gap-8">
                    
                    <!-- Bảng hoạt động gần đây -->
                    <div class="bg-white rounded-2xl shadow-sm border border-outline-variant p-6 flex flex-col gap-4">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-bold text-on-surface flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">history</span> Hoạt động gần đây
                            </h2>
                            <a href="index.php?controller=admin&action=approveBookings" class="text-sm text-primary font-semibold hover:underline">Xem tất cả</a>
                        </div>

                        <div class="flex flex-col gap-3">
                            <?php if (!empty($recent_activities)): ?>
                                <?php foreach ($recent_activities as $activity): ?>
                                    <div class="flex items-center justify-between p-4 rounded-xl bg-surface-container-low border border-outline-variant/30">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-blue-100 text-primary flex items-center justify-center font-bold">
                                                <?php echo strtoupper(substr($activity['full_name'] ?? 'U', 0, 1)); ?>
                                            </div>
                                            <div>
                                                <p class="font-bold text-sm text-on-surface"><?php echo htmlspecialchars($activity['full_name']); ?> đã đặt phòng <span class="text-primary"><?php echo htmlspecialchars($activity['room_name']); ?></span></p>
                                                <p class="text-xs text-on-surface-variant"><?php echo htmlspecialchars($activity['title']); ?> • <?php echo date('H:i - d/m/Y', strtotime($activity['start_time'])); ?></p>
                                            </div>
                                        </div>
                                        <div>
                                            <?php if ($activity['status'] === 'approved'): ?>
                                                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full">Đã duyệt</span>
                                            <?php elseif ($activity['status'] === 'pending'): ?>
                                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold rounded-full">Chờ duyệt</span>
                                            <?php else: ?>
                                                <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full">Đã hủy</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-sm text-on-surface-variant py-4 text-center">Chưa có hoạt động nào gần đây.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Khung mô phỏng Tình trạng sử dụng trong tuần -->
                    <div class="bg-white rounded-2xl shadow-sm border border-outline-variant p-6 flex flex-col gap-4">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-bold text-on-surface flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">bar_chart</span> Tình trạng sử dụng phòng họp trong tuần
                            </h2>
                            <span class="text-xs text-on-surface-variant bg-surface-container px-3 py-1 rounded-full">Cập nhật tự động</span>
                        </div>
                        <p class="text-sm text-on-surface-variant">Tỉ lệ lấp đầy theo ngày làm việc dựa trên tổng số giờ đăng ký thực tế.</p>
                        
                        <div class="flex flex-col gap-3 mt-2">
                            <?php 
                            $days = ['Thứ Hai' => '65%', 'Thứ Ba' => '78%', 'Thứ Tư' => '88%', 'Thứ Năm' => '72%', 'Thứ Sáu' => '54%'];
                            foreach ($days as $day => $percent):
                            ?>
                            <div class="flex items-center gap-4">
                                <span class="w-20 text-sm font-semibold text-on-surface-variant"><?php echo $day; ?></span>
                                <div class="flex-1 h-3 bg-surface-container rounded-full overflow-hidden">
                                    <div class="h-full bg-primary rounded-full" style="width: <?php echo $percent; ?>"></div>
                                </div>
                                <span class="w-12 text-sm font-bold text-primary text-right"><?php echo $percent; ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>

                <!-- Cột phải (Chiếm 1 phần): Lối tắt & Thông tin nhanh -->
                <div class="flex flex-col gap-8">
                    
                    <!-- Khung Đặt phòng nhanh (Shortcut Form) -->
                    <div class="bg-white rounded-2xl shadow-sm border border-outline-variant p-6 flex flex-col gap-4">
                        <h2 class="text-xl font-bold text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">bolt</span> Đặt phòng nhanh
                        </h2>
                        <p class="text-xs text-on-surface-variant">Hỗ trợ quản trị viên đăng ký lịch họp ngay lập tức.</p>
                        
                        <form action="index.php?controller=admin&action=createBooking" method="POST" class="flex flex-col gap-3 mt-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Mục đích cuộc họp</label>
                                <input type="text" name="title" required placeholder="VD: Họp chiến lược tuần" class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm focus:outline-none focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Bắt đầu</label>
                                <input type="datetime-local" name="start_time" required class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm focus:outline-none focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Kết thúc</label>
                                <input type="datetime-local" name="end_time" required class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm focus:outline-none focus:border-primary">
                            </div>
                            <button type="submit" class="w-full mt-2 py-3 bg-primary text-white font-bold rounded-xl hover:bg-blue-800 transition-colors shadow-md text-sm">
                                Tiến hành đặt lịch
                            </button>
                        </form>
                    </div>

                    <!-- Lối tắt điều hướng quản trị -->
                    <div class="bg-gradient-to-br from-primary to-blue-900 text-white rounded-2xl shadow-md p-6 flex flex-col gap-4">
                        <h2 class="text-xl font-bold">Khu vực điều hành</h2>
                        <p class="text-sm text-blue-100">Truy cập nhanh vào các công cụ quản lý cốt lõi của hệ thống RoomHub.</p>
                        
                        <div class="flex flex-col gap-2 mt-2">
                            <a href="index.php?controller=admin&action=manageRooms" class="flex items-center justify-between p-3 rounded-xl bg-white/10 hover:bg-white/20 transition-colors font-semibold text-sm">
                                <span class="flex items-center gap-2"><span class="material-symbols-outlined">apartment</span> Quản lý danh mục phòng</span>
                                <span class="material-symbols-outlined">arrow_forward</span>
                            </a>
                            <a href="index.php?controller=admin&action=schedule" class="flex items-center justify-between p-3 rounded-xl bg-white/10 hover:bg-white/20 transition-colors font-semibold text-sm">
                                <span class="flex items-center gap-2"><span class="material-symbols-outlined">calendar_month</span> Xem lưới lịch thời gian</span>
                                <span class="material-symbols-outlined">arrow_forward</span>
                            </a>
                        </div>
                    </div>

                </div>

            </div>

        </main>
    </div>
</body>
</html>