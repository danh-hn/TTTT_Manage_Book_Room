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
                    <p class="text-on-surface-variant mt-2">Theo dõi trạng thái phê duyệt các yêu cầu của bạn.</p>
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
                                    
                                    $status_html = '';
                                    if ($db_status === 'cancelled') {
                                        $status_html = '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-red-100 text-red-800 font-bold"><span class="material-symbols-outlined text-[16px]">cancel</span> Bị từ chối</span>';
                                    } elseif ($db_status === 'pending') {
                                        if ($start_ts < $now) {
                                            $status_html = '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-200 text-gray-600 font-bold"><span class="material-symbols-outlined text-[16px]">timer_off</span> Quá thời hạn</span>';
                                        } else {
                                            $status_html = '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 font-bold"><span class="material-symbols-outlined text-[16px]">pending</span> Chờ duyệt</span>';
                                        }
                                    } elseif ($db_status === 'approved') {
                                        if ($now < $start_ts) {
                                            $status_html = '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-100 text-green-800 font-bold"><span class="material-symbols-outlined text-[16px]">event_available</span> Đã duyệt</span>';
                                        } elseif ($now >= $start_ts && $now <= $end_ts) {
                                            $status_html = '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-100 text-blue-800 font-bold animate-pulse"><span class="material-symbols-outlined text-[16px]">play_circle</span> Đang diễn ra</span>';
                                        } else {
                                            $status_html = '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-surface-variant text-on-surface-variant font-bold"><span class="material-symbols-outlined text-[16px]">task_alt</span> Đã hoàn thành</span>';
                                        }
                                    }
                                    ?>
                                    <tr class="border-t border-outline-variant hover:bg-gray-50 transition-colors">
                                        <td class="py-4 px-6">
                                            <p class="font-bold text-primary"><?php echo htmlspecialchars($row['room_name']); ?></p>
                                            <p class="text-xs text-on-surface-variant">Mã: <?php echo htmlspecialchars($row['room_number']); ?></p>
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
</body>
</html>