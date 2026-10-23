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
<body class="bg-background font-body-md text-on-surface">
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
            <a class="flex items-center px-md py-md rounded-xl bg-secondary-container text-on-secondary-container font-semibold transition-all group" href="index.php?controller=admin&action=manageRooms">
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
                    <p class="font-label-md text-label-md text-on-surface truncate"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Admin'); ?></p>
                    <p class="text-[10px] text-on-surface-variant truncate uppercase tracking-widest">Quản trị viên</p>
                </div>
            </div>
        </div>
    </aside>

    <div class="pl-72">
        <main class="relative pt-16 bg-background min-h-screen">
            <div class="flex flex-col w-full h-full relative">
                <div class="px-margin-desktop pb-xl pt-lg relative z-10 flex flex-col gap-lg flex-1">
                    
                    <!-- Tiêu đề & Nút Thêm mới -->
                    <div class="flex items-end justify-between">
                        <div>
                            <h1 class="font-display-lg text-display-lg text-on-surface mb-xs relative inline-block">
                                Quản lý phòng
                                <span class="absolute -bottom-2 left-0 w-1/3 h-1 bg-primary rounded-full"></span>
                            </h1>
                            <p class="font-body-lg text-body-lg text-on-surface-variant">Quản lý cấu hình và trạng thái của tất cả phòng họp trong hệ thống.</p>
                        </div>
                        <a href="index.php?controller=admin&action=createRoom" class="bg-primary text-on-primary hover:bg-surface-tint hover:shadow-lg hover:shadow-primary/20 transition-all px-lg py-md rounded-xl font-label-md text-label-md uppercase tracking-wider flex items-center gap-sm shadow-md">
                            <span class="material-symbols-outlined">add</span>
                            Thêm phòng mới
                        </a>
                    </div>

                    <!-- THÊM MỚI: BỘ LỌC TÌM KIẾM ĐƯỢC TÍCH HỢP -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-outline-variant flex flex-col md:flex-row gap-4 justify-between items-center">
                        <form method="GET" action="index.php" class="flex flex-wrap items-center gap-4 w-full">
                            <input type="hidden" name="controller" value="admin">
                            <input type="hidden" name="action" value="manageRooms">

                            <div class="flex items-center gap-2">
                                <span class="font-label-md text-label-md flex items-center gap-xs text-on-surface-variant mr-4">
                                    <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                                    Bộ lọc:
                                </span>
                            </div>

                            <!-- Lọc theo Tầng / Vị trí -->
                            <div class="flex items-center gap-2 bg-surface-container-low px-3 py-2 rounded-xl border border-outline-variant focus-within:border-primary transition-colors">
                                <span class="material-symbols-outlined text-gray-500 text-[20px]">layers</span>
                                <select name="floor" onchange="this.form.submit()" class="bg-transparent text-sm font-medium focus:outline-none text-on-surface w-40 cursor-pointer">
                                    <option value="">Tất cả các tầng</option>
                                    <option value="Tầng 1" <?php echo (isset($_GET['floor']) && $_GET['floor'] === 'Tầng 1') ? 'selected' : ''; ?>>Tầng 1</option>
                                    <option value="Tầng 2" <?php echo (isset($_GET['floor']) && $_GET['floor'] === 'Tầng 2') ? 'selected' : ''; ?>>Tầng 2</option>
                                    <option value="Tầng 3" <?php echo (isset($_GET['floor']) && $_GET['floor'] === 'Tầng 3') ? 'selected' : ''; ?>>Tầng 3</option>
                                    <option value="Tầng 4" <?php echo (isset($_GET['floor']) && $_GET['floor'] === 'Tầng 4') ? 'selected' : ''; ?>>Tầng 4</option>
                                </select>
                            </div>

                            <!-- Lọc theo Trạng thái hoạt động -->
                            <div class="flex items-center gap-2 bg-surface-container-low px-3 py-2 rounded-xl border border-outline-variant focus-within:border-primary transition-colors">
                                <span class="material-symbols-outlined text-gray-500 text-[20px]">online_prediction</span>
                                <select name="status" onchange="this.form.submit()" class="bg-transparent text-sm font-medium focus:outline-none text-on-surface w-48 cursor-pointer">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="active" <?php echo (isset($_GET['status']) && $_GET['status'] === 'active') ? 'selected' : ''; ?>>Đang hoạt động</option>
                                    <option value="maintenance" <?php echo (isset($_GET['status']) && $_GET['status'] === 'maintenance') ? 'selected' : ''; ?>>Đang bảo trì / sửa chữa</option>
                                </select>
                            </div>

                            <!-- Nút Xóa Lọc & Hiển thị số lượng -->
                            <div class="flex items-center justify-between flex-1 pl-4 border-l border-outline-variant">
                                <span class="font-body-md text-body-md text-on-surface-variant bg-surface-container-highest px-3 py-1 rounded-full">
                                    Đang hiển thị: <strong class="text-primary"><?php echo count($rooms ?? []); ?></strong> phòng
                                </span>
                                <?php if (!empty($_GET['floor']) || !empty($_GET['status'])): ?>
                                    <a href="index.php?controller=admin&action=manageRooms" class="text-sm text-error hover:text-on-error-container hover:bg-error-container px-3 py-1.5 rounded-lg transition-colors font-bold flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[18px]">close</span> Xóa bộ lọc
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <!-- LƯỚI HIỂN THỊ DỮ LIỆU ĐỘNG TỪ DATABASE -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-lg pb-xl">
                        <?php
                        if (!empty($rooms)) {
                            foreach ($rooms as $row) {
                                $is_active = ($row['status'] === 'active');
                                $status_class = $is_active ? 'bg-secondary-fixed text-on-secondary-fixed' : 'bg-surface-variant text-on-surface-variant';
                                $status_text = $is_active ? 'Đang hoạt động' : 'Đang bảo trì';
                                $card_opacity = $is_active ? '' : 'opacity-75 grayscale-[0.2]';
                                
                                // Đảm bảo equipments luôn là mảng hợp lệ để không bị lỗi explode
                                $equipment_string = isset($row['equipment_details']) ? $row['equipment_details'] : '';
                                $equipments = $equipment_string !== '' ? explode(',', $equipment_string) : [];
                                ?>

                                <!-- Khối Card cho 1 Phòng -->
                                <div class="bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col relative h-[500px] <?php echo $card_opacity; ?>">
                                    
                                    <!-- Nút thao tác -->
                                    <div class="absolute top-md right-md z-20 flex gap-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="index.php?controller=admin&action=editRoom&id=<?php echo $row['id']; ?>" class="w-8 h-8 rounded-full bg-surface-container-lowest/90 text-on-surface backdrop-blur flex items-center justify-center hover:bg-primary hover:text-on-primary transition-colors shadow-sm" title="Chỉnh sửa">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </a>
                                        <!-- Gọi hàm confirm của javascript để cảnh báo việc xoá lịch -->
                                        <a href="index.php?controller=admin&action=deleteRoom&id=<?php echo $row['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa phòng này? Toàn bộ các lịch đặt liên quan đến phòng này cũng có thể bị ảnh hưởng hoặc xóa theo!');" class="w-8 h-8 rounded-full bg-surface-container-lowest/90 text-on-surface backdrop-blur flex items-center justify-center hover:bg-error hover:text-on-error transition-colors shadow-sm" title="Xóa phòng">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </a>
                                    </div>

                                    <!-- Ảnh và Header -->
                                    <div class="h-48 w-full relative shrink-0 overflow-hidden">
                                        <div class="absolute top-md left-md z-10 px-sm py-[2px] rounded <?php echo $status_class; ?> font-label-md text-label-md shadow-sm">
                                            <?php echo $status_text; ?>
                                        </div>
                                        <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" src="../public/<?php echo htmlspecialchars($row['image_url'] ?? 'https://via.placeholder.com/400x200'); ?>" onerror="this.src='<?php echo htmlspecialchars($row['image_url'] ?? 'https://via.placeholder.com/400x200'); ?>'" alt="Room Image" />
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                                        <div class="absolute bottom-md left-md right-md">
                                            <h3 class="text-white font-bold text-lg">
                                                <?php echo htmlspecialchars($row['room_name'] . ' (' . $row['room_number'] . ')'); ?>
                                            </h3>
                                            <p class="text-gray-200 text-sm flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[16px]">location_on</span> 
                                                <?php echo htmlspecialchars($row['location'] ?? 'Chưa cập nhật'); ?>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Thông số chi tiết -->
                                    <div class="p-md flex flex-col flex-1 gap-md">
                                        <div class="grid grid-cols-2 gap-sm">
                                            <div class="flex items-center gap-sm bg-surface-container-low p-sm rounded-lg">
                                                <span class="material-symbols-outlined text-primary">groups</span>
                                                <div>
                                                    <p class="font-label-md text-label-md text-on-surface-variant">Sức chứa</p>
                                                    <p class="font-body-md text-body-md text-on-surface"><?php echo $row['capacity']; ?> người</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-sm bg-surface-container-low p-sm rounded-lg">
                                                <span class="material-symbols-outlined text-tertiary">aspect_ratio</span>
                                                <div>
                                                    <p class="font-label-md text-label-md text-on-surface-variant">Diện tích</p>
                                                    <p class="font-body-md text-body-md text-on-surface"><?php echo isset($row['area']) ? $row['area'] : '0'; ?> m²</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Danh sách thiết bị -->
                                        <div class="flex-1">
                                            <p class="font-label-md text-label-md text-on-surface-variant mb-xs">Thiết bị đi kèm</p>
                                            <div class="flex flex-wrap gap-xs">
                                                <?php if (!empty($equipments)): ?>
                                                    <?php foreach ($equipments as $item): ?>
                                                        <?php if (trim($item) !== ''): ?>
                                                            <span class="inline-flex items-center gap-xs px-sm py-[2px] bg-surface-container rounded text-on-surface font-body-sm text-body-md">
                                                                <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                                                <?php echo htmlspecialchars(trim($item)); ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <span class="text-sm text-gray-400 italic">Chưa cập nhật thiết bị</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="pt-sm border-t border-surface-variant flex justify-between items-center mt-auto">
                                            <span class="font-code-sm text-code-sm text-on-surface-variant">ID: RM-<?php echo $row['id']; ?></span>
                                            
                                            <?php if ($is_active): ?>
                                                <a href="index.php?controller=admin&action=toggleRoomStatus&id=<?php echo $row['id']; ?>" class="text-error hover:bg-error-container hover:text-on-error-container px-sm py-xs rounded transition-colors font-label-md text-label-md">Chuyển sang bảo trì</a>
                                            <?php else: ?>
                                                <a href="index.php?controller=admin&action=toggleRoomStatus&id=<?php echo $row['id']; ?>" class="text-primary hover:bg-primary-container hover:text-on-primary-container px-sm py-xs rounded transition-colors font-label-md text-label-md">Kích hoạt hoạt động</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <!-- Kết thúc Card -->

                                <?php
                            }
                        } else {
                            echo "<div class='col-span-full flex flex-col items-center justify-center py-20 bg-surface-container-low rounded-2xl border border-dashed border-outline-variant'>";
                            echo "<span class='material-symbols-outlined text-gray-400 text-6xl mb-4'>search_off</span>";
                            echo "<h3 class='text-lg font-bold text-on-surface'>Không tìm thấy phòng nào</h3>";
                            echo "<p class='font-body-lg text-body-lg text-on-surface-variant mt-2'>Thử thay đổi điều kiện lọc (Tầng hoặc Trạng thái) để xem lại.</p>";
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Script hiển thị thông báo lỗi trùng số phòng từ Controller truyền về -->
    <?php if (isset($_GET['error']) && $_GET['error'] === 'duplicate_room'): ?>
        <script>
            alert("LỖI: Số phòng này đã tồn tại trong hệ thống. Vui lòng kiểm tra lại!");
            // Xóa tham số error trên URL để không báo lại nếu user F5
            window.history.replaceState(null, null, window.location.pathname + "?controller=admin&action=manageRooms");
        </script>
    <?php endif; ?>
</body>
</html>