<?php
// Tích hợp file kết nối CSDL
require_once '../config/db_connect.php';

// Kiểm tra xem có nhận được ID phòng từ URL hay không
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $room_id = (int)$_GET['id'];

    // 1. Truy vấn để lấy trạng thái hiện tại của phòng
    $sql_check = "SELECT status FROM rooms WHERE id = ?";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $current_status = $row['status'];

        // 2. Logic lật trạng thái (Toggle)
        // Nếu đang 'active' thì chuyển thành 'maintenance', và ngược lại
        $new_status = ($current_status === 'active') ? 'maintenance' : 'active';

        // 3. Thực thi cập nhật xuống CSDL
        $sql_update = "UPDATE rooms SET status = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $new_status, $room_id);
        
        if ($stmt_update->execute()) {
            // Có thể thêm tham số vào URL để hiện thông báo (toast) nếu muốn
            $msg = ($new_status === 'active') ? 'activated' : 'suspended';
            header("Location: manage_rooms.php?status=" . $msg);
            exit();
        }
        $stmt_update->close();
    }
    $stmt->close();
}

// Nếu không có ID hoặc xử lý xong, mặc định đẩy về trang quản lý
header("Location: manage_rooms.php");
exit();
?>