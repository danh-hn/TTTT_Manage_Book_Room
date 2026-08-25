<?php
require_once '../config/db_connect.php';

$error = '';
$success = '';

// BẮT ĐẦU XỬ LÝ BACKEND KHI ADMIN BẤM NÚT LƯU
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Hứng dữ liệu và làm sạch
    $room_name = trim($_POST['room_name'] ?? '');
    $room_number = trim($_POST['room_number'] ?? '');
    $capacity = (int)($_POST['capacity'] ?? 0);
    $area = (int)($_POST['area'] ?? 0);
    $location = trim($_POST['location'] ?? '');
    $equipment_details = trim($_POST['equipment_details'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');

    // Nếu để trống ảnh, gán một ảnh mặc định
    if (empty($image_url)) {
        $image_url = 'https://via.placeholder.com/400x200?text=No+Image';
    }

    // 2. Ràng buộc dữ liệu (Validation)
    if (empty($room_name) || empty($room_number) || empty($location)) {
        $error = "Vui lòng nhập đầy đủ Tên phòng, Mã phòng và Vị trí.";
    } elseif ($capacity <= 0 || $area <= 0) {
        $error = "Sức chứa và Diện tích phải là số nguyên dương lớn hơn 0.";
    } else {
        // 3. Thực thi chèn vào Cơ sở dữ liệu
        $sql_insert = "INSERT INTO rooms (room_name, room_number, capacity, area, location, equipment_details, image_url, status, version) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, 'active', 1)";
        $stmt = $conn->prepare($sql_insert);
        $stmt->bind_param("ssiisss", $room_name, $room_number, $capacity, $area, $location, $equipment_details, $image_url);
        
        if ($stmt->execute()) {
            // Chuyển hướng về trang danh sách và báo thành công
            header("Location: manage_rooms.php?msg=success");
            exit();
        } else {
            $error = "Lỗi hệ thống: Không thể lưu dữ liệu. " . $conn->error;
        }
        $stmt->close();
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
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary": "#00288e",
                        "surface": "#fbf8ff",
                        "surface-container": "#eeedf7",
                        "on-surface": "#1a1b22",
                        "outline-variant": "#c4c5d5",
                        "error": "#ba1a1a",
                        "error-container": "#ffdad6"
                    },
                    fontFamily: { "sans": ["Inter", "sans-serif"] }
                }
            }
        }
    </script>
    <style>body { background-color: #f4f2fc; }</style>
</head>
<body class="text-on-surface flex items-center justify-center min-h-screen p-6">

    <div class="w-full max-w-3xl bg-surface rounded-2xl shadow-lg p-8">
        
        <div class="flex items-center gap-4 border-b border-outline-variant pb-4 mb-6">
            <a href="manage_rooms.php" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined text-[24px]">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-primary">Thêm Phòng Họp Mới</h1>
                <p class="text-sm text-gray-500">Cấu hình thông số và thiết bị cho phòng mới.</p>
            </div>
        </div>

        <!-- Hiển thị thông báo lỗi nếu có -->
        <?php if (!empty($error)): ?>
            <div class="bg-error-container text-error px-4 py-3 rounded-lg mb-6 flex items-start gap-3">
                <span class="material-symbols-outlined">warning</span>
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <!-- FORM NHẬP LIỆU -->
        <form action="create_room.php" method="POST" class="flex flex-col gap-5">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Tên phòng -->
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-gray-700">Tên phòng *</label>
                    <input type="text" name="room_name" required placeholder="VD: Phòng Hội Đồng" 
                           class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                <!-- Mã phòng -->
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-gray-700">Mã phòng (Định danh) *</label>
                    <input type="text" name="room_number" required placeholder="VD: A101" 
                           class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Sức chứa -->
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-gray-700">Sức chứa (Người) *</label>
                    <input type="number" name="capacity" min="1" required placeholder="VD: 20" 
                           class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                <!-- Diện tích -->
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-gray-700">Diện tích (m²) *</label>
                    <input type="number" name="area" min="1" required placeholder="VD: 45" 
                           class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
            </div>

            <!-- Vị trí -->
            <div class="flex flex-col gap-1">
                <label class="text-sm font-semibold text-gray-700">Vị trí *</label>
                <input type="text" name="location" required placeholder="VD: Tầng 3 - Tòa nhà C" 
                       class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            </div>

            <!-- Thiết bị -->
            <div class="flex flex-col gap-1">
                <label class="text-sm font-semibold text-gray-700">Thiết bị đi kèm (Cách nhau bằng dấu phẩy)</label>
                <input type="text" name="equipment_details" placeholder="VD: Máy chiếu, Bảng trắng, 4 Micro" 
                       class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            </div>

            <!-- Link Ảnh -->
            <div class="flex flex-col gap-1">
                <label class="text-sm font-semibold text-gray-700">Đường dẫn ảnh (URL)</label>
                <input type="url" name="image_url" placeholder="https://..." 
                       class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                <span class="text-xs text-gray-500">Nếu để trống, hệ thống sẽ sử dụng ảnh mặc định.</span>
            </div>

            <!-- Nút Action -->
            <div class="flex justify-end gap-3 mt-4 pt-4 border-t border-outline-variant">
                <a href="manage_rooms.php" class="px-6 py-2 rounded-lg text-gray-600 hover:bg-gray-100 font-semibold transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="px-6 py-2 rounded-lg bg-primary text-white font-semibold hover:bg-blue-800 transition-colors shadow-md">
                    Lưu Phòng Mới
                </button>
            </div>
        </form>

    </div>
</body>
</html>