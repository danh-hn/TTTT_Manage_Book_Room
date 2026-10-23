<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Tổng quan hệ thống - RoomHub Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#001b79',
                        secondary: '#b3c4ff',
                        background: '#f7f8fc',
                        surface: '#ffffff',
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        body { background-color: #f7f8fc; }
        .hide-scroll::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="text-gray-800 flex h-screen overflow-hidden font-sans">

    <!-- SIDEBAR -->
    <aside class="w-72 bg-[#f4f2fc] border-r border-[#c4c5d5] flex flex-col shrink-0 py-6">
        <div class="px-8 mb-8 flex items-center gap-2">
            <span class="material-symbols-outlined text-[#00288e] text-[32px]">meeting_room</span>
            <span class="text-2xl font-bold text-[#00288e] tracking-tight">RoomHub</span>
        </div>
        <nav class="flex-1 px-4 flex flex-col gap-1 overflow-y-auto hide-scroll">
            <a class="flex items-center px-4 py-3 rounded-xl bg-[#d0e1fb] text-[#00288e] font-semibold transition-all shadow-sm" href="index.php?controller=admin&action=dashboard">
                <span class="material-symbols-outlined mr-4">dashboard</span><span class="font-medium">Tổng quan Admin</span>
            </a>
            <a class="flex items-center px-4 py-3 rounded-xl text-gray-600 hover:bg-[#e8e7f1] hover:text-gray-900 transition-all" href="index.php?controller=admin&action=schedule">
                <span class="material-symbols-outlined mr-4">calendar_month</span><span class="font-medium">Lịch phòng họp</span>
            </a>
            <a class="flex items-center px-4 py-3 rounded-xl text-gray-600 hover:bg-[#e8e7f1] hover:text-gray-900 transition-all" href="index.php?controller=admin&action=manageRooms">
                <span class="material-symbols-outlined mr-4">apartment</span><span class="font-medium">Quản lý phòng</span>
            </a>
            <a class="flex items-center px-4 py-3 rounded-xl text-gray-600 hover:bg-[#e8e7f1] hover:text-gray-900 transition-all" href="index.php?controller=admin&action=approveBookings">
                <span class="material-symbols-outlined mr-4">task_alt</span><span class="font-medium">Phê duyệt</span>
            </a>
        </nav>
        <div class="px-4 pt-6 border-t border-[#c4c5d5] mt-auto">
            <div class="flex items-center gap-3 px-4 py-2 bg-[#e3e1eb] rounded-xl">
                <div class="w-8 h-8 rounded-full bg-[#00288e] flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-white text-[18px]">person</span>
                </div>
                <div class="overflow-hidden flex-1">
                    <p class="font-bold text-sm text-gray-900 truncate"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Admin'); ?></p>
                    <p class="text-[10px] text-gray-600 truncate uppercase tracking-widest">Quản trị viên</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-background">
        
        <header class="h-20 flex items-center justify-between px-8 bg-background border-b border-gray-100">
            <div class="relative w-96">
                
            </div>
            
            <div class="flex items-center gap-5 text-gray-600">
                <a href="index.php?controller=auth&action=logout" class="hover:text-red-600 transition-colors" title="Đăng xuất">
                    <span class="material-symbols-outlined">logout</span>
                </a>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 pt-6 hide-scroll">
            
            <!-- Page Title & Actions -->
            <div class="flex items-end justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-1">Tổng quan hệ thống</h1>
                    <p class="text-sm text-gray-500 font-medium">Theo dõi trạng thái và hiệu suất sử dụng phòng họp</p>
                </div>
                <div class="flex gap-3">
                    <div class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">calendar_today</span>
                        Tháng <?php echo htmlspecialchars((string)($current_month ?? date('m'))); ?>, <?php echo htmlspecialchars((string)($current_year ?? date('Y'))); ?>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <span class="material-symbols-outlined">apartment</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-semibold mb-1">Tổng số phòng đang HĐ</p>
                        <h3 class="text-4xl font-extrabold text-gray-900"><?php echo (int)($total_rooms ?? 0); ?></h3>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                            <span class="material-symbols-outlined">book_online</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-semibold mb-1">Lượt đặt trong tháng</p>
                        <h3 class="text-4xl font-extrabold text-gray-900"><?php echo number_format((float)($monthly_bookings ?? 0)); ?></h3>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute top-0 right-0 bg-[#fff5f5] w-32 h-32 rounded-bl-full -z-0"></div>
                    <div class="flex justify-between items-start mb-6 relative z-10">
                        <div class="relative w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                            <span class="material-symbols-outlined">pending_actions</span>
                            <?php if (($pending_requests ?? 0) > 0): ?>
                                <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                            <?php endif; ?>
                        </div>
                        <a href="index.php?controller=admin&action=approveBookings" class="text-[#00288e] text-xs font-bold px-3 py-1.5 hover:underline">Duyệt ngay</a>
                    </div>
                    <div class="relative z-10">
                        <p class="text-sm text-gray-500 font-semibold mb-1">Yêu cầu chờ duyệt</p>
                        <h3 class="text-4xl font-extrabold text-gray-900"><?php echo (int)($pending_requests ?? 0); ?></h3>
                    </div>
                </div>
            </div>

            <!-- Table & Timeline Grid -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 pb-10">
                
                <!-- Col Left: Thay biểu đồ bằng Bảng dữ liệu thực -->
                <div class="xl:col-span-2 flex flex-col gap-6">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col h-full">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-900">Yêu cầu đặt phòng gần đây</h3>
                            <a href="index.php?controller=admin&action=approveBookings" class="text-sm font-semibold text-[#00288e] hover:underline">Xem danh sách chờ</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-xs uppercase tracking-wider text-gray-500 border-b border-gray-100">
                                        <th class="pb-3 pr-4 font-semibold">Người đặt</th>
                                        <th class="pb-3 px-4 font-semibold">Phòng họp</th>
                                        <th class="pb-3 px-4 font-semibold">Mục đích</th>
                                        <th class="pb-3 px-4 font-semibold">Thời gian tạo</th>
                                        <th class="pb-3 pl-4 font-semibold text-right">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm">
                                    <?php if (!empty($recent_activities)): ?>
                                        <?php foreach ($recent_activities as $act): ?>
                                            <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                                <td class="py-4 pr-4 font-semibold text-gray-900"><?php echo htmlspecialchars($act['full_name'] ?? $act['user_name'] ?? 'Khách'); ?></td>
                                                <td class="py-4 px-4 text-[#00288e] font-bold"><?php echo htmlspecialchars($act['room_name']); ?></td>
                                                <td class="py-4 px-4 text-gray-600 max-w-[150px] truncate" title="<?php echo htmlspecialchars($act['title']); ?>">
                                                    <?php echo htmlspecialchars($act['title'] ?: 'Không có tiêu đề'); ?>
                                                </td>
                                                <td class="py-4 px-4 text-gray-500"><?php echo date('d/m/Y H:i', strtotime($act['created_at'])); ?></td>
                                                <td class="py-4 pl-4 text-right">
                                                    <?php 
                                                    if ($act['status'] === 'approved') {
                                                        echo '<span class="inline-block px-3 py-1 rounded-full bg-green-100 text-green-700 font-semibold text-xs">Đã duyệt</span>';
                                                    } elseif ($act['status'] === 'pending') {
                                                        echo '<span class="inline-block px-3 py-1 rounded-full bg-orange-100 text-orange-700 font-semibold text-xs">Chờ duyệt</span>';
                                                    } elseif ($act['status'] === 'cancelled' || $act['status'] === 'rejected') {
                                                        echo '<span class="inline-block px-3 py-1 rounded-full bg-red-100 text-red-700 font-semibold text-xs">Đã hủy</span>';
                                                    } else {
                                                        echo '<span class="inline-block px-3 py-1 rounded-full bg-gray-100 text-gray-700 font-semibold text-xs">Không rõ</span>';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="py-10 text-center text-gray-500 italic">Chưa có dữ liệu đặt phòng trong hệ thống.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Col Right: Timeline (Dữ liệu thực từ DB) -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 h-full">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Hoạt động đặt phòng</h3>
                    </div>
                    
                    <div class="relative pl-3">
                        <div class="absolute left-6 top-2 bottom-2 w-[2px] bg-gray-100"></div>
                        
                        <div class="flex flex-col gap-5">
                            <?php if (!empty($recent_activities)): ?>
                                <?php foreach ($recent_activities as $act): 
                                    $time_str = date('d/m/Y H:i', strtotime($act['created_at']));
                                    
                                    if ($act['status'] === 'approved') {
                                        $icon = 'check'; $bg = 'bg-green-100'; $text = 'text-green-700'; $action = 'đã được duyệt';
                                    } elseif ($act['status'] === 'cancelled') {
                                        $icon = 'close'; $bg = 'bg-red-100'; $text = 'text-red-700'; $action = 'đã bị hủy/từ chối';
                                    } elseif ($act['status'] === 'pending') {
                                        $icon = 'hourglass_empty'; $bg = 'bg-orange-100'; $text = 'text-orange-700'; $action = 'vừa đặt';
                                    } else {
                                        $icon = 'edit'; $bg = 'bg-blue-100'; $text = 'text-blue-700'; $action = 'đã thao tác';
                                    }
                                ?>
                                <div class="relative z-10 flex gap-4">
                                    <div class="w-7 h-7 rounded-full <?php echo $bg; ?> <?php echo $text; ?> flex items-center justify-center shrink-0 shadow-[0_0_0_4px_#fff]">
                                        <span class="material-symbols-outlined text-[14px]"><?php echo $icon; ?></span>
                                    </div>
                                    <div class="bg-[#f8f9fb] p-3.5 rounded-xl w-full">
                                        <p class="text-sm text-gray-700 leading-snug">
                                            <span class="font-bold text-gray-900"><?php echo htmlspecialchars($act['full_name'] ?? $act['user_name'] ?? 'Khách'); ?></span> 
                                            <?php echo $action; ?> 
                                            <span class="font-bold text-[#00288e]"><?php echo htmlspecialchars($act['room_name']); ?></span>
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[12px]">schedule</span> <?php echo $time_str; ?>
                                        </p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-sm text-gray-500 italic">Chưa có hoạt động nào gần đây.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>
</html>