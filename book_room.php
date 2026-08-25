<?php
// Khởi tạo phiên làm việc để lấy ID người dùng
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Lùi 1 cấp để gọi đúng file cấu hình
require_once 'config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy ID người dùng thực tế từ phiên đăng nhập
    $user_id = $_SESSION['user_id']; 
    $room_id = (int)$_POST['room_id'];
    $title = trim($_POST['title']);
    $participant_count = (int)$_POST['participant_count'];
    $extra_services = $_POST['extra_services'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Lớp bảo vệ 1: Kiểm tra thời gian
    if (strtotime($start_time) >= strtotime($end_time)) {
        die("<script>alert('Lỗi: Thời gian kết thúc phải diễn ra sau thời gian bắt đầu.'); window.history.back();</script>");
    }

    $conn->begin_transaction();

    try {
        // Lớp bảo vệ 2: Kiểm tra sức chứa & Khóa lạc quan
        $stmt_ver = $conn->prepare("SELECT version, capacity FROM rooms WHERE id = ? AND status = 'active'");
        $stmt_ver->bind_param("i", $room_id);
        $stmt_ver->execute();
        $result_ver = $stmt_ver->get_result();
        
        if ($result_ver->num_rows === 0) {
            throw new Exception("Phòng họp không tồn tại hoặc đang trong thời gian bảo trì.");
        }
        
        $room_data = $result_ver->fetch_assoc();
        $current_version = $room_data['version'];
        $max_capacity = $room_data['capacity'];
        $stmt_ver->close();

        if ($participant_count > $max_capacity) {
            throw new Exception("Số lượng người tham gia vượt quá sức chứa tối đa của phòng.");
        }

        // Lớp bảo vệ 3: Kiểm tra giao thoa thời gian (Overlap)
        $sql_overlap = "SELECT id FROM bookings 
                        WHERE room_id = ? 
                        AND status != 'cancelled'
                        AND (start_time < ? AND end_time > ?)";
        $stmt_overlap = $conn->prepare($sql_overlap);
        $stmt_overlap->bind_param("iss", $room_id, $end_time, $start_time);
        $stmt_overlap->execute();
        $result_overlap = $stmt_overlap->get_result();
        
        if ($result_overlap->num_rows > 0) {
            throw new Exception("Khung giờ này đã có người đặt hoặc bị trùng lặp. Vui lòng chọn giờ khác.");
        }
        $stmt_overlap->close();

        // Thực thi Khóa lạc quan
        $sql_update_ver = "UPDATE rooms SET version = version + 1 WHERE id = ? AND version = ?";
        $stmt_update = $conn->prepare($sql_update_ver);
        $stmt_update->bind_param("ii", $room_id, $current_version);
        $stmt_update->execute();

        if ($stmt_update->affected_rows === 0) {
            throw new Exception("Phòng đang có sự biến động dữ liệu. Vui lòng thao tác lại để tránh ghi đè.");
        }
        $stmt_update->close();

        // CHỐT CHẶN TRẠNG THÁI: Ghi nhận lịch với status = 'pending'
        $sql_insert = "INSERT INTO bookings (user_id, room_id, title, participant_count, extra_services, start_time, end_time, status) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')"; 
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("iisisss", $user_id, $room_id, $title, $participant_count, $extra_services, $start_time, $end_time);
        $stmt_insert->execute();
        $stmt_insert->close();

        $conn->commit();
        
        // Điều hướng ngược ra ngoài thư mục gốc và báo thành công
        header("Location: ../index.php?msg=success");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Giao dịch thất bại: " . $e->getMessage() . "'); window.history.back();</script>";
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>