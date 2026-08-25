<?php
require_once '../config/db_connect.php';

$error = '';
$room = null;

// LUỒNG 1: XỬ LÝ KHI MỞ TRANG (LẤY DỮ LIỆU CŨ)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: manage_rooms.php");
        exit();
    }
    
    $room_id = (int)$_GET['id'];
    
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Nếu ai đó cố tình gõ ID không tồn tại trên URL
        header("Location: manage_rooms.php");
        exit();
    }
    
    $room = $result->fetch_assoc();
    $stmt->close();
}

// LUỒNG 2: XỬ LÝ KHI BẤM NÚT LƯU (CẬP NHẬT DỮ LIỆU)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = (int)$_POST['id']; // Lấy ID từ thẻ input hidden
    $room_name = trim($_POST['room_name'] ?? '');
    $room_number = trim($_POST['room_number'] ?? '');
    $capacity = (int)($_POST['capacity'] ?? 0);
    $area = (int)($_POST['area'] ?? 0);
    $location = trim($_POST['location'] ?? '');
    $equipment_details = trim($_POST['equipment_details'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');

    if (empty($image_url)) {
        $image_url = 'https://via.placeholder.com/400x200?text=No+Image';
    }

    if (empty($room_name) || empty($room_number) || empty($location)) {
        $error = "Vui lòng nhập đầy đủ Tên phòng, Mã phòng và Vị trí.";
    } elseif ($capacity <= 0 || $area <= 0) {
        $error = "Sức chứa và Diện tích phải là số nguyên dương lớn hơn 0.";
    } else {
        // Thực thi cập nhật (UPDATE) thay vì thêm mới (INSERT)
        $sql_update = "UPDATE rooms SET room_name=?, room_number=?, capacity=?, area=?, location=?, equipment_details=?, image_url=? WHERE id=?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("ssiisssi", $room_name, $room_number, $capacity, $area, $location, $equipment_details, $image_url, $room_id);
        
        if ($stmt->execute()) {
            header("Location: manage_rooms.php?msg=updated");
            exit();
        } else {
            $error = "Lỗi hệ thống: Không thể cập nhật dữ liệu. " . $conn->error;
        }
        $stmt->close();
    }
    
    // Nếu có lỗi, phải giữ lại dữ liệu user vừa gõ để render lại ra form
    $room = $_POST; 
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
            theme: { extend: { colors: { "primary": "#00288e", "surface": "#fbf8ff", "surface-container": "#eeedf7", "on-surface": "#1a1b22", "outline-variant": "#c4c5d5", "error": "#ba1a1a", "error-container": "#ffdad6" } } }
        }
    </script>
    <style>body { background-color: #f4f2fc; font-family: 'Inter', sans-serif; }</style>
</head>
<body class="text-on-surface flex items-center justify-center min-h-screen p-6">

    <div class="w-full max-w-3xl bg-surface rounded-2xl shadow-lg p-8">
        
        <div class="flex items-center gap-4 border-b border-outline-variant pb-4 mb-6">
            <a href="manage_rooms.php" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined text-[24px]">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-primary">Cập Nhật Thông Tin Phòng</h1>
                <p class="text-sm text-gray-500">Chỉnh sửa thông số cho ID: RM-<?php echo isset($room['id']) ? $room['id'] : ''; ?></p>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-error-container text-error px-4 py-3 rounded-lg mb-6 flex items-start gap-3">
                <span class="material-symbols-outlined">warning</span>
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <!-- FORM CẬP NHẬT -->
        <form action="edit_room.php" method="POST" class="flex flex-col gap-5">
            
            <!-- TRƯỜNG QUAN TRỌNG NHẤT: Bắt buộc phải có để Backend biết đang sửa phòng nào -->
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($room['id'] ?? ''); ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-gray-700">Tên phòng *</label>
                    <input type="text" name="room_name" required value="<?php echo htmlspecialchars($room['room_name'] ?? ''); ?>" 
                           class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-gray-700">Mã phòng *</label>
                    <input type="text" name="room_number" required value="<?php echo htmlspecialchars($room['room_number'] ?? ''); ?>" 
                           class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-gray-700">Sức chứa (Người) *</label>
                    <input type="number" name="capacity" min="1" required value="<?php echo htmlspecialchars($room['capacity'] ?? ''); ?>" 
                           class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-gray-700">Diện tích (m²) *</label>
                    <input type="number" name="area" min="1" required value="<?php echo htmlspecialchars($room['area'] ?? ''); ?>" 
                           class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-semibold text-gray-700">Vị trí *</label>
                <input type="text" name="location" required value="<?php echo htmlspecialchars($room['location'] ?? ''); ?>" 
                       class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-semibold text-gray-700">Thiết bị đi kèm (Cách nhau bằng dấu phẩy)</label>
                <input type="text" name="equipment_details" value="<?php echo htmlspecialchars($room['equipment_details'] ?? ''); ?>" 
                       class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-semibold text-gray-700">Đường dẫn ảnh (URL)</label>
                <input type="url" name="image_url" value="<?php echo htmlspecialchars($room['image_url'] ?? ''); ?>" 
                       class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            </div>

            <div class="flex justify-end gap-3 mt-4 pt-4 border-t border-outline-variant">
                <a href="manage_rooms.php" class="px-6 py-2 rounded-lg text-gray-600 hover:bg-gray-100 font-semibold transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="px-6 py-2 rounded-lg bg-primary text-white font-semibold hover:bg-blue-800 transition-colors shadow-md">
                    Lưu Thay Đổi
                </button>
            </div>
        </form>

    </div>
</body>
</html>