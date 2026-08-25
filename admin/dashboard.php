<?php
// CHỐT CHẶN BẢO MẬT: Bắt buộc khởi tạo session ở dòng đầu tiên
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Kiểm tra phân quyền Admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/db_connect.php';

// 1. TÍNH TOÁN KPI: Tổng số phòng đang hoạt động
$sql_rooms = "SELECT COUNT(id) as total FROM rooms WHERE status = 'active'";
$total_rooms = $conn->query($sql_rooms)->fetch_assoc()['total'];

// 2. TÍNH TOÁN KPI: Yêu cầu chờ duyệt
$sql_pending = "SELECT COUNT(id) as total FROM bookings WHERE status = 'pending'";
$pending_requests = $conn->query($sql_pending)->fetch_assoc()['total'];

// 3. TÍNH TOÁN KPI: Lượt đặt trong tháng hiện tại
$current_month = date('m');
$current_year = date('Y');
$sql_monthly = "SELECT COUNT(id) as total FROM bookings WHERE MONTH(start_time) = ? AND YEAR(start_time) = ?";
$stmt_month = $conn->prepare($sql_monthly);
$stmt_month->bind_param("ii", $current_month, $current_year);
$stmt_month->execute();
$monthly_bookings = $stmt_month->get_result()->fetch_assoc()['total'];
$stmt_month->close();

// 4. LẤY HOẠT ĐỘNG GẦN ĐÂY (5 lượt đặt phòng mới nhất)
$sql_recent = "
    SELECT b.*, u.full_name, r.room_name 
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN rooms r ON b.room_id = r.id
    ORDER BY b.id DESC LIMIT 5
";
$result_recent = $conn->query($sql_recent);
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
            <a class="flex items-center px-md py-md rounded-xl bg-secondary-container text-on-secondary-container font-semibold transition-all group" href="dashboard.php">
                <span class="material-symbols-outlined mr-md">dashboard</span><span class="font-body-md text-body-md">Tổng quan Admin</span>
            </a>
            <a class="flex items-center px-md py-md rounded-xl text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-all group" href="schedule.php">
                <span class="material-symbols-outlined mr-md">calendar_month</span><span class="font-body-md text-body-md">Lịch phòng họp</span>
            </a>
            <a class="flex items-center px-md py-md rounded-xl text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-all group" href="manage_rooms.php">
                <span class="material-symbols-outlined mr-md">apartment</span><span class="font-body-md text-body-md">Quản lý phòng</span>
            </a>
            <a class="flex items-center px-md py-md rounded-xl text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-all group" href="approve_bookings.php">
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
        <main class="relative p-10 min-h-screen">
            <div class="max-w-[1400px] mx-auto flex flex-col gap-8">
                
                <!-- Header -->
                <div class="flex justify-between items-end">
                    <div>
                        <h1 class="text-4xl font-bold text-on-surface">Tổng quan hệ thống</h1>
                        <p class="text-on-surface-variant mt-2">Theo dõi trạng thái và hiệu suất sử dụng phòng họp</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="bg-surface-container rounded-lg px-4 py-2 flex items-center gap-2">
                            <span class="material-symbols-outlined text-on-surface-variant">calendar_today</span>
                            <span class="font-semibold text-sm">Tháng <?php echo $current_month . ', ' . $current_year; ?></span>
                        </div>
                    </div>
                </div>

                <!-- 4 Thẻ KPI Động -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-outline-variant flex flex-col gap-4">
                        <div class="flex justify-between items-start">
                            <div class="w-12 h-12 rounded-xl bg-primary-fixed flex items-center justify-center">
                                <span class="material-symbols-outlined text-on-primary-fixed text-2xl">meeting_room</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-on-surface-variant uppercase tracking-wider">Tổng số phòng</p>
                            <p class="text-4xl font-bold text-on-surface mt-1"><?php echo $total_rooms; ?></p>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-outline-variant flex flex-col gap-4">
                        <div class="flex justify-between items-start">
                            <div class="w-12 h-12 rounded-xl bg-tertiary-fixed flex items-center justify-center">
                                <span class="material-symbols-outlined text-on-tertiary-fixed text-2xl">book_online</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-on-surface-variant uppercase tracking-wider">Lượt đặt trong tháng</p>
                            <p class="text-4xl font-bold text-on-surface mt-1"><?php echo $monthly_bookings; ?></p>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-outline-variant flex flex-col gap-4">
                        <div class="flex justify-between items-start">
                            <div class="w-12 h-12 rounded-xl bg-secondary-fixed flex items-center justify-center">
                                <span class="material-symbols-outlined text-on-secondary-fixed text-2xl">data_usage</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-on-surface-variant uppercase tracking-wider">Tỷ lệ sử dụng</p>
                            <p class="text-4xl font-bold text-on-surface mt-1">68<span class="text-xl text-on-surface-variant">%</span></p>
                            <p class="text-xs text-on-surface-variant italic">*Cần API Chart.js để tính thực tế</p>
                        </div>
                    </div>

                    <div class="bg-error-container rounded-2xl p-6 shadow-sm border border-error/30 flex flex-col gap-4">
                        <div class="flex justify-between items-start">
                            <div class="w-12 h-12 rounded-xl bg-error text-white flex items-center justify-center relative">
                                <?php if($pending_requests > 0): ?>
                                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-400 rounded-full animate-ping"></span>
                                <?php endif; ?>
                                <span class="material-symbols-outlined text-2xl">pending_actions</span>
                            </div>
                            <a href="approve_bookings.php" class="text-error font-bold text-sm hover:underline">Duyệt ngay</a>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-on-error-container uppercase tracking-wider">Yêu cầu chờ duyệt</p>
                            <p class="text-4xl font-bold text-on-error-container mt-1"><?php echo $pending_requests; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Lịch sử hoạt động thực tế từ Table Bookings -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-outline-variant flex flex-col">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-on-surface">Hoạt động đặt phòng gần đây</h2>
                        <a href="approve_bookings.php" class="text-primary text-sm font-bold hover:underline">Xem tất cả</a>
                    </div>
                    
                    <div class="flex flex-col gap-4 relative">
                        <!-- Đường kẻ dọc -->
                        <div class="absolute left-6 top-2 bottom-2 w-px bg-surface-container-high z-0"></div>
                        
                        <?php
                        if ($result_recent->num_rows > 0) {
                            while ($activity = $result_recent->fetch_assoc()) {
                                // Xác định icon và màu sắc dựa trên trạng thái
                                $status = $activity['status'];
                                $icon = 'add'; $bg = 'bg-blue-100 text-blue-800'; $action_text = 'đã yêu cầu đặt';
                                
                                if ($status === 'approved') {
                                    $icon = 'check'; $bg = 'bg-green-100 text-green-800'; $action_text = 'đã được duyệt phòng';
                                } elseif ($status === 'cancelled') {
                                    $icon = 'close'; $bg = 'bg-red-100 text-red-800'; $action_text = 'đã hủy phòng';
                                }
                                ?>
                                
                                <div class="relative z-10 flex gap-4 items-start group">
                                    <div class="w-12 h-12 rounded-full <?php echo $bg; ?> flex items-center justify-center shrink-0 border-4 border-white shadow-sm">
                                        <span class="material-symbols-outlined text-[20px]"><?php echo $icon; ?></span>
                                    </div>
                                    <div class="flex-1 bg-surface-container-low p-4 rounded-xl group-hover:bg-surface-container transition-colors">
                                        <p class="text-sm text-on-surface">
                                            <span class="font-bold"><?php echo htmlspecialchars($activity['full_name']); ?></span> 
                                            <?php echo $action_text; ?> 
                                            <span class="font-bold text-primary"><?php echo htmlspecialchars($activity['room_name']); ?></span>
                                        </p>
                                        <p class="text-sm text-on-surface-variant mt-2 border-l-2 border-outline-variant pl-3 italic">
                                            "<?php echo htmlspecialchars($activity['title']); ?>"
                                        </p>
                                    </div>
                                </div>
                                
                                <?php
                            }
                        } else {
                            echo "<p class='text-on-surface-variant ml-16'>Chưa có hoạt động nào được ghi nhận.</p>";
                        }
                        ?>
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>
</html>