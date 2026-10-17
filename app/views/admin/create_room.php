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
            <!-- Đường dẫn điều hướng quay lại qua Router chung -->
            <a href="index.php?controller=admin&action=manageRooms" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined text-[24px]">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-primary">Thêm Phòng Họp Mới</h1>
                <p class="text-sm text-gray-500">Cấu hình thông số và thiết bị cho phòng mới.</p>
            </div>
        </div>

        <!-- Hiển thị thông báo lỗi từ Controller truyền sang -->
        <?php if (!empty($error)): ?>
            <div class="bg-error-container text-error px-4 py-3 rounded-lg mb-6 flex items-start gap-3">
                <span class="material-symbols-outlined">warning</span>
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>

        <!-- FORM NHẬP LIỆU TRỎ TỚI ROUTER MVC -->
        <form action="index.php?controller=admin&action=createRoom" method="POST" enctype="multipart/form-data" class="flex flex-col gap-5">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-gray-700">Tên phòng *</label>
                    <input type="text" name="room_name" required placeholder="VD: Phòng Hội Đồng" 
                           class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-gray-700">Mã phòng (Chỉ nhập số) *</label>
                    <input type="number" name="room_number" required placeholder="VD: 101" 
                           class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-gray-700">Sức chứa (Người) *</label>
                    <input type="number" name="capacity" min="1" required placeholder="VD: 20" 
                           class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-gray-700">Diện tích (m²) *</label>
                    <input type="number" name="area" step="any" min="0.1" required placeholder="VD: 45.5" 
                           class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary">
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-semibold text-gray-700">Vị trí *</label>
                <input type="text" name="location" required placeholder="VD: Tầng 3 - Tòa nhà C" 
                       class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary">
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-semibold text-gray-700">Thiết bị đi kèm (Cách nhau bằng dấu phẩy)</label>
                <input type="text" name="equipment_details" placeholder="VD: Máy chiếu, Bảng trắng, 4 Micro" 
                       class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none focus:border-primary">
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-semibold text-gray-700">Tải ảnh phòng lên từ thiết bị</label>
                <input type="file" name="room_image" accept="image/png, image/jpeg, image/webp" 
                       class="px-4 py-2 bg-surface-container border border-outline-variant rounded-lg focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-blue-800">
                <span class="text-xs text-gray-500">Hỗ trợ định dạng: JPG, JPEG, PNG, WEBP.</span>
            </div>

            <div class="flex justify-end gap-3 mt-4 pt-4 border-t border-outline-variant">
                <a href="index.php?controller=admin&action=manageRooms" class="px-6 py-2 rounded-lg text-gray-600 hover:bg-gray-100 font-semibold transition-colors">
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