<?php
// Danh sách thiết bị mặc định có sẵn phục vụ hiển thị checkbox
$default_equipments = ['Máy chiếu', 'Loa', 'Bảng từ', 'TV màn hình lớn', 'Micro không dây'];

// Tách chuỗi thiết bị hiện tại thành mảng để check vào checkbox tương ứng
$current_equipments = isset($room['equipment_details']) ? array_map('trim', explode(',', $room['equipment_details'])) : [];

// Lọc ra các thiết bị ngoài danh sách mặc định để điền vào ô "Khác"
$custom_arr = array_diff($current_equipments, $default_equipments);
$custom_str = !empty($custom_arr) ? implode(', ', $custom_arr) : '';
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

    <div class="w-full max-w-3xl bg-surface rounded-2xl shadow-lg p-8 my-10">
        
        <div class="flex items-center gap-4 border-b border-outline-variant pb-4 mb-6">
            <a href="index.php?controller=admin&action=manageRooms" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined text-[24px]">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-primary">Cập Nhật Thông Tin Phòng</h1>
                <p class="text-sm text-gray-500">Chỉnh sửa thông số cho ID: RM-<?php echo htmlspecialchars($room['id'] ?? ''); ?></p>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-error-container text-error px-4 py-3 rounded-lg mb-6 flex items-start gap-3">
                <span class="material-symbols-outlined">warning</span>
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>

        <!-- FORM CẬP NHẬT TRỎ TỚI ROUTER CHUNG MVC -->
        <form action="index.php?controller=admin&action=editRoom&id=<?php echo $room['id']; ?>" method="POST" enctype="multipart/form-data" class="flex flex-col gap-5">
            
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($room['id'] ?? ''); ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-gray-700">Tên phòng *</label>
                    <input type="text" name="room_name" required value="<?php echo htmlspecialchars($room['room_name'] ?? ''); ?>" 
                           class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-gray-700">Mã phòng (Chỉ nhập số) *</label>
                    <input type="number" name="room_number" required value="<?php echo htmlspecialchars($room['room_number'] ?? ''); ?>" 
                           class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary" placeholder="Ví dụ: 101">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-gray-700">Sức chứa (Người) *</label>
                    <input type="number" name="capacity" min="1" required value="<?php echo htmlspecialchars($room['capacity'] ?? ''); ?>" 
                           class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-gray-700">Diện tích (m²) *</label>
                    <input type="number" name="area" step="any" min="0.1" required value="<?php echo htmlspecialchars($room['area'] ?? ''); ?>" 
                           class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary">
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-semibold text-gray-700">Vị trí *</label>
                <input type="text" name="location" required value="<?php echo htmlspecialchars($room['location'] ?? ''); ?>" 
                       class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary">
            </div>

            <!-- PHẦN THIẾT BỊ: CHECKBOX CÓ SẴN + Ô NHẬP KHÁC -->
            <div class="flex flex-col gap-2">
                <label class="text-sm font-semibold text-gray-700">Thiết bị đi kèm</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 bg-surface-container p-4 rounded-lg border border-outline-variant">
                    <?php foreach ($default_equipments as $item): 
                        $isChecked = in_array($item, $current_equipments);
                    ?>
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="checkbox" name="equipments[]" value="<?php echo $item; ?>" <?php echo $isChecked ? 'checked' : ''; ?>
                                   class="w-4 h-4 text-primary border-outline-variant rounded focus:ring-primary">
                            <span><?php echo $item; ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="mt-1">
                    <input type="text" name="custom_equipment" value="<?php echo htmlspecialchars($custom_str); ?>" 
                           placeholder="Nhập thêm thiết bị khác (ngăn cách bằng dấu phẩy nếu có)..." 
                           class="w-full px-4 py-2 bg-surface-container border border-outline-variant rounded-lg text-sm focus:outline-none focus:border-primary">
                </div>
            </div>

            <!-- PHẦN ẢNH VÀ XEM TRƯỚC (PREVIEW) -->
            <div class="flex flex-col gap-2">
                <label class="text-sm font-semibold text-gray-700">Hình ảnh phòng họp</label>
                <div class="flex items-center gap-4">
                    <div class="w-32 h-20 bg-gray-200 rounded-lg overflow-hidden border border-outline-variant shrink-0">
                        <img id="previewImage" src="../public/<?php echo htmlspecialchars($room['image_url'] ?? 'https://via.placeholder.com/400x200'); ?>" 
                             onerror="this.src='<?php echo htmlspecialchars($room['image_url'] ?? ''); ?>'" alt="Preview" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <input type="file" name="room_image" id="roomImageInput" accept="image/png, image/jpeg, image/webp" 
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-blue-800 cursor-pointer">
                        <span class="text-xs text-gray-500 block mt-1">Chọn ảnh định dạng JPG, PNG hoặc WEBP từ thiết bị.</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-4 pt-4 border-t border-outline-variant">
                <a href="index.php?controller=admin&action=manageRooms" class="px-6 py-2 rounded-lg text-gray-600 hover:bg-gray-100 font-semibold transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="px-6 py-2 rounded-lg bg-primary text-white font-semibold hover:bg-blue-800 transition-colors shadow-md">
                    Lưu Thay Đổi
                </button>
            </div>
        </form>

    </div>

    <!-- JAVASCRIPT XEM TRƯỚC ẢNH NGAY LẬP TỨC -->
    <script>
        const imageInput = document.getElementById('roomImageInput');
        const previewImage = document.getElementById('previewImage');

        imageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>